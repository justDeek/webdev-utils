//A simplified and modernized alternative to jQuery
//based on cash-dom (https://www.npmjs.com/package/cash-dom v.8.1.5)

'use strict';

//#region setup
//shortcuts
const doc = document;
const win = window;
const docEle = doc.documentElement;

//arrays
const isArray = Array.isArray;
const arrFilter = Array.prototype.filter;
const arrIndexOf = Array.prototype.indexOf;
const arrMap = Array.prototype.map;
const arrPush = Array.prototype.push;
const arrSlice = Array.prototype.slice;
const arrConcat = Array.prototype.concat;
const arrSome = Array.prototype.some;

//elements
const createElement = doc.createElement.bind(doc);
const div = createElement('div');

//props
const style = div.style;
const vendorsPrefixes = ['webkit', 'moz', 'ms'];
const scriptAttributes = ['type', 'src', 'nonce', 'noModule'];
const propMap = {
  class: 'className',
  contenteditable: 'contentEditable',
  for: 'htmlFor',
  readonly: 'readOnly',
  maxlength: 'maxLength',
  tabindex: 'tabIndex',
  colspan: 'colSpan',
  rowspan: 'rowSpan',
  usemap: 'useMap'
};

//styles
const numericProps = {
  animationIterationCount: true,
  columnCount: true,
  flexGrow: true,
  flexShrink: true,
  fontWeight: true,
  gridArea: true,
  gridColumn: true,
  gridColumnEnd: true,
  gridColumnStart: true,
  gridRow: true,
  gridRowEnd: true,
  gridRowStart: true,
  lineHeight: true,
  opacity: true,
  order: true,
  orphans: true,
  widows: true,
  zIndex: true
};

//regex
const regex = {
  html: /<.+>/,
  id: /^#([-\w\u00C0-\uFFFF=$]+)$/,
  tag: /^\w+/,
  class: /\.([-\w\u00C0-\uFFFF]+)/g,
  splitValues: /\S+/g,
  fragment: /^\s*<(\w+)[^>]*>/,
  singleTag: /^<(\w+)\s*\/?>(?:<\/\1>)?$/,
  checkable: /radio|checkbox/i,
  cssVariable: /^--/,
  dashAlpha: /-([a-z])/g,
  HTMLCDATA: /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,
  scriptType: /^$|^module$|\/(java|ecma)script/i,
  JSONString: /^\s+|\s+$/,
  eventsMouse: /^(mouse|pointer|contextmenu|drag|drop|click|dblclick)/i,
}

//events
const eventsNamespace = '___ce';
const eventsNamespacesSeparator = '.';
const eventsFocus = {focus: 'focusin', blur: 'focusout'};
const eventsHover = {mouseenter: 'mouseover', mouseleave: 'mouseout'};
//#endregion

class ExDOM {
  //#region constructor
  constructor(selector, context) {
    if (!selector) return;
    if (isExDOM(selector)) return selector;
    let eles = selector;

    if (isString(selector)) {
      let ctx = context || doc;
      eles = regex.id.test(selector) && isDocument(ctx)
        ? ctx.getElementById(selector.slice(1).replace(/\\/g, '')) : regex.html.test(selector)
          ? parseHTML(selector) : isExDOM(ctx)
            ? ctx.find(selector) : isString(ctx)
              ? dom(ctx).find(selector) : find(selector, ctx);
      if (!eles) return;
    } else if (isFunction(selector)) return ready(selector);
    if (eles.nodeType || eles === win) eles = [eles];
    this.length = eles.length;

    for (let i = 0, l = this.length; i < l; i++) this[i] = eles[i];
  }

  //#endregion

  //#region iterate
  /** Return the index of the element in its parent if an element or selector isn't provided. Returns index within element or selector if it is. */
  indexOf(selector) {
    let child = selector ? dom(selector)[0] : this[0];
    let collection = selector ? this : dom(child).parent().children();
    return arrIndexOf.call(collection, child);
  };

  /** Return all elements or the one at the specified index. */
  get(index) {
    if (isUndefined(index)) return arrSlice.call(this);
    index = Number(index);
    return this[index < 0 ? index + this.length : index];
  };

  /** Return a collection with the element at index. */
  eq = (index) => dom(this.get(index));
  /** Return a collection containing only the first element. */
  first = () => this.eq(0);
  /** Return a collection containing only the last element. */
  last = () => this.eq(-1);
  /** Iterate over a collection with callback ( index, element ). The callback function may exit iteration early by returning false. */
  each = (callback) => each(this, callback);

  /** Return a new collection, mapping each element with callback ( index, element ). */
  map = (callback) => dom(arrConcat.apply([], arrMap.call(this, (ele, i) => callback.call(ele, i, ele))));
  /** Return a new collection with elements taken from start to end. */
  slice = (start, end) => dom(arrSlice.call(this, start, end));
  /** Return a collection with cloned elements. */
  clone = () => this.map((i, ele) => ele.cloneNode(true));
  //#endregion

  //#region compare
  /** Return whether the provided selector, element or collection matches any element in the collection. */
  is(comparator) {
    let compare = getCompareFunction(comparator);
    return arrSome.call(this, (ele, i) => compare.call(ele, i, ele));
  };

  /** Reduce the set of matched elements to those that have a descendant that matches the selector or DOM element. */
  has(selector) {
    let comparator = isString(selector) ? (i, ele) => find(selector, ele).length : (i, ele) => ele.contains(selector);
    return this.filter(comparator);
  };

  /** Filter out any elements not matching the given collection/selector. */
  not(comparator) {
    let compare = getCompareFunction(comparator);
    return this.filter((i, ele) => (!isString(comparator) || isElement(ele)) && !compare.call(ele, i, ele));
  };
  //#endregion

  //#region modify
  /** Insert content or elements before the collection. */
  before = (...args) => this.#insertSelectors(args, this, false, true);
  /** Insert content or elements after the collection. */
  after = (...args) => this.#insertSelectors(args, this, false, false, false, true, true);

  /** Insert collection before specified element. */
  insertBefore = (...args) => this.#insertSelectors(args, this, true, true);
  /** Insert collection after specified element. */
  insertAfter = (...args) => this.#insertSelectors(args, this, true, false, false, false, false, true);

  /** Prepend content or elements to the each element in collection. */
  prepend = (...args) => this.#insertSelectors(args, this, false, true, true, true, true);
  /** Prepend elements in a collection to the target element(s). */
  prependTo = (...args) => this.#insertSelectors(args, this, true, true, true, false, false, true);

  /** Append the given content or elements to each element in the collection. */
  append = (...args) => this.#insertSelectors(args, this, false, false, true);
  /** Add the elements in the collection to the target element(s). */
  appendTo = (...args) => this.#insertSelectors(args, this, true, false, true);

  /** Wrap a structure around each element. */
  wrap = (selector) => {
    return this.each((i, ele) => {
      let wrapper = dom(selector)[0];
      dom(ele).wrapAll(!i ? wrapper : wrapper.cloneNode(true));
    });
  };

  /** Wrap a structure around all children. */
  wrapInner = (selector) => {
    return this.each((i, ele) => {
      let $ele = dom(ele);
      let contents = $ele.contents();
      contents.length ? contents.wrapAll(selector) : $ele.append(selector);
    });
  };

  /** Wrap a structure around all elements. */
  wrapAll = (selector) => {
    let structure = dom(selector);
    let wrapper = structure[0];
    while (wrapper.children.length)
      wrapper = wrapper.firstElementChild;
    this.first().before(structure);
    return this.appendTo(wrapper);
  };

  /** Remove the wrapper from all elements. */
  unwrap = () => {
    this.parent().each((i, ele) => {
      if (ele.tagName === 'BODY') return;
      let $ele = dom(ele);
      $ele.replaceWith($ele.children());
    });
    return this;
  }

  /** Return a new collection with the element(s) added to the end. */
  add = (selector, context) => dom(unique(this.get().concat(dom(selector, context).get())));

  /** Empty the elements interior markup. */
  empty = () => this.each((i, ele) => {
    while (ele.firstChild) ele.removeChild(ele.firstChild);
  });

  /** Remove each element (or only the ones that match the selector) from the DOM. */
  detach(comparator) {
    filtered(this, comparator).each((i, ele) => {
      if (ele.parentNode) ele.parentNode.removeChild(ele);
    });
    return this;
  };

  /** Remove each element (or only the ones that match the selector) from the DOM and remove all their event listeners. */
  remove = (comparator) => {
    filtered(this, comparator).detach().off();
    return this;
  };

  /** Replace collection elements with the provided new content. */
  replaceWith = (selector) => this.before(selector).remove();

  /** Replace the matching elements with the ones from this collection.\
   This is similar to replaceWith(), but with the source and target reversed. */
  replaceAll = (selector) => {
    dom(selector).replaceWith(this);
    return this;
  };

  #insertElement = (anchor, target, left, inside, evaluate) => {
    if (inside) anchor.insertBefore(target, left ? anchor.firstChild : null);
    else {
      if (anchor.nodeName === 'HTML') anchor.parentNode.replaceChild(target, anchor);
      else anchor.parentNode.insertBefore(target, left ? anchor : anchor.nextSibling);
    }
    if (evaluate) this.#evalScripts(target, anchor.ownerDocument);
  }

  #insertSelectors = (selectors, anchors, inverse, left, inside, reverseLoop1, reverseLoop2, reverseLoop3) => {
    each(selectors, (si, selector) => {
      each(dom(selector), (ti, target) => {
        each(dom(anchors), (ai, anchor) => {
          let anchorFinal = inverse ? target : anchor;
          let targetFinal = inverse ? anchor : target;
          let indexFinal = inverse ? ti : ai;
          this.#insertElement(anchorFinal, !indexFinal ? targetFinal : targetFinal.cloneNode(true), left, inside, !indexFinal);
        }, reverseLoop3);
      }, reverseLoop2);
    }, reverseLoop1);
    return anchors;
  }

  #evalScripts = (node, doc) => {
    let collection = dom(node);
    collection.filter('script').add(collection.find('script')).each((i, ele) => {
      if (regex.scriptType.test(ele.type) && docEle.contains(ele)) {
        let script_1 = createElement('script');
        script_1.text = ele.textContent.replace(regex.HTMLCDATA, '');
        each(scriptAttributes, (i, attr) => {
          if (ele[attr]) script_1[attr] = ele[attr];
        });
        doc.head.insertBefore(script_1, null);
        doc.head.removeChild(script_1);
      }
    });
  }
  //#endregion

  //#region traverse
  /** Return the closest matching selector up the DOM tree. */
  closest(comparator) {
    let filtered = this.filter(comparator);
    if (filtered.length) return filtered;
    let $parent = this.parent();
    if (!$parent.length) return filtered;
    return $parent.closest(comparator);
  }

  /** Return the selector match descendants from the first element in the collection. */
  find = (selector) => dom(unique(pluck(this, (ele) => find(selector, ele))));

  /** Return the collection that results from applying the filter selector/method. */
  filter = (comparator) => {
    let compare = getCompareFunction(comparator);
    return dom(arrFilter.call(this, (ele, i) => compare.call(ele, i, ele)));
  };

  /** Get all child elements or only the ones matching the given selector. */
  children = (comparator) => filtered(dom(unique(pluck(this, (ele) => ele.children))), comparator);
  /** Get all sibling elements or only the ones matching the given selector. */
  siblings = (comparator) => filtered(dom(unique(pluck(this, (ele) => dom(ele).parent().children().not(ele)))), comparator)

  /** Return the previous adjacent elements. */
  prev = (comparator, _all, _until) => filtered(dom(unique(pluck(this, 'previousElementSibling', _all, _until))), comparator);
  /** Return all the previous elements. */
  prevAll = (comparator) => this.prev(comparator, true);
  /** Return all the previous elements, until the provided selector matches. */
  prevUntil = (until, comparator) => this.prev(comparator, true, until);

  /** Return the next adjacent elements. */
  next = (comparator, _all, _until) => filtered(dom(unique(pluck(this, 'nextElementSibling', _all, _until))), comparator);
  /** Return all the next elements. */
  nextAll = (comparator) => this.next(comparator, true);
  /** Return all the next elements, until the provided selector matches. */
  nextUntil = (until, comparator) => this.next(comparator, true, until);

  /** Return a collection of elements that are parent of the elements. */
  parent = (comparator) => filtered(dom(unique(pluck(this, 'parentNode'))), comparator);
  /** Return a collection of elements that are parents of the elements. Optionally filtering by given selector. */
  parents = (comparator, _until) => filtered(dom(unique(pluck(this, 'parentElement', true, _until))), comparator);
  /** Return a collection of elements that are parents of the elements, until a provided selector matches. Optionally filtering by given selector. */
  parentsUntil = (until, comparator) => this.parents(comparator, until);
  //#endregion

  //#region value
  /** Return an inputs value. If a value is supplied, set all inputs in collection's value to the value argument. */
  val(value) {
    return !arguments.length ? this[0] && getValue(this[0]) : this.each((i, ele) => {
      let isSelect = ele.multiple && ele.options;
      if (isSelect || regex.checkable.test(ele.type)) {
        let eleValue_1 = isArray(value) ? arrMap.call(value, String) : (isNull(value) ? [] : [String(value)]);
        if (isSelect) each(ele.options, (i, option) => option.selected = eleValue_1.indexOf(option.value) >= 0, true);
        else ele.checked = eleValue_1.indexOf(ele.value) >= 0;
      } else ele.value = isUndefined(value) || isNull(value) ? '' : value;
    });
  }

  /** Return the HTML text of the first element in the collection or set the HTML if provided. */
  html(html) {
    if (!arguments.length) return this[0] && this[0].innerHTML;
    if (isUndefined(html)) return this;
    let hasScript = /<script[\s>]/.test(html);
    return this.each(function (i, ele) {
      if (!isElement(ele)) return;
      if (hasScript) dom(ele).empty().append(html);
      else ele.innerHTML = html;
    });
  }

  /** Return the inner text of the first element in the collection or set the text if textContent is provided. */
  text(text) {
    if (isUndefined(text)) return this.get().map((ele) => isElement(ele) || isText(ele) ? ele.textContent : '').join('');
    return this.each((i, ele) => {
      if (!isElement(ele)) return;
      ele.textContent = text;
    });
  }

  /** Get the children of each element in the set of matched elements, including text and comment nodes.\
   Useful for selecting elements in friendly iframes. */
  contents = () => dom(unique(pluck(this, (ele) => { return ele.tagName === 'IFRAME' ? [ele.contentDocument] : (ele.tagName === 'TEMPLATE' ? ele.content.childNodes : ele.childNodes); })));
  //#endregion

  //#region attributes
  /** Without attrValue, return the attribute value of the first element in the collection.\
   With attrValue, set the attribute value of each element of the collection. */
  attr(attr, value) {
    if (!attr) return;

    if (isString(attr)) {
      if (arguments.length < 2) {
        if (!this[0] || !isElement(this[0])) return;
        let value_1 = this[0].getAttribute(attr);
        return isNull(value_1) ? undefined : value_1;
      }
      if (isUndefined(value)) return this;
      if (isNull(value)) return this.removeAttr(attr);

      return this.each((i, ele) => {
        if (!isElement(ele)) return;
        ele.setAttribute(attr, value);
      });
    }
    for (let key in attr) this.attr(key, attr[key]);
    return this;
  }

  /** Remove an attribute from each element.\
   Accepts space-separated attrName for removing multiple attributes. */
  removeAttr(attr) {
    let attrs = getSplitValues(attr);
    return this.each((i, ele) => {
      if (!isElement(ele)) return;
      each(attrs, (i, a) => ele.removeAttribute(a));
    });
  };
  //#endregion

  //#region properties
  /** Return a property value when just property is supplied.\
   Set a property when property and value are supplied, and set multiple properties when an object is supplied. */
  prop(prop, value) {
    prop = propMap[prop] || prop;
    if (arguments.length < 2) return this[0] && this[0][prop];
    return this.each((i, ele) => ele[prop] = value);
  };

  /** Remove a property from each element. */
  removeProp = (prop) => this.each((i, ele) => delete ele[propMap[prop] || prop]);
  //#endregion

  //#region classes
  /** Adds the className class to each element in the collection.\
   Accepts space-separated className for adding multiple classes */
  addClass = (cls) => this.toggleClass(cls, true);
  /** Return the boolean result of checking if any element in the collection has the className attribute. */
  hasClass = (cls) => !!cls && arrSome.call(this, (ele) => isElement(ele) && ele.classList.contains(cls));

  /** Remove the given className from each element.\
   Accepts space-separated className for adding multiple classes.\
   Providing no arguments will remove all classes. */
  removeClass(cls) {
    return arguments.length ? this.toggleClass(cls, false) : this.attr('class', '');
  }

  /** Add or remove the className from each element based on if the element already has the class.\
   Accepts space-separated classNames for toggling multiple classes, and an optional force boolean to ensure classes are added (true) or removed (false). */
  toggleClass(cls, force) {
    let classes = getSplitValues(cls);
    let isForce = !isUndefined(force);
    return this.each((i, ele) => {
      if (!isElement(ele)) return;
      each(classes, (i, c) => {
        if (isForce) force ? ele.classList.add(c) : ele.classList.remove(c);
        else ele.classList.toggle(c);
      });
    });
  };
  //#endregion

  //#region data
  /** Without arguments, returns an object mapping all the data-* attributes to their values.\
   With a key, return the value of the corresponding data-* attribute.\
   With both a key and value, set the value of the corresponding data-* attribute to value.\
   Multiple data can be set when an object is supplied. */
  data(name, value) {
    if (!name) {
      if (!this[0]) return;
      let datas = {};
      for (let key in this[0].dataset) datas[key] = this.#getData(this[0], key);
      return datas;
    }
    if (isString(name)) {
      if (arguments.length < 2) return this[0] && this.#getData(this[0], name);
      if (isUndefined(value)) return this;
      return this.each(function (i, ele) { this.#setData(ele, name, value); });
    }
    for (let key in name) this.data(key, name[key]);
    return this;
  }

  #getData(ele, key) {
    let value = ele.dataset[key] || ele.dataset[camelCase(key)];
    if (regex.JSONString.test(value)) return value;
    return attempt(JSON.parse, value);
  }

  #setData(ele, key, value) {
    value = attempt(JSON.stringify, value);
    ele.dataset[camelCase(key)] = value;
  }
  //#endregion

  //#region visibility
  /** Show the elements. */
  show = () => this.toggle(true);
  /** Hide the elements. */
  hide = () => this.toggle(false);

  /** Hide or show the elements. */
  toggle = (force) => this.each((i, ele) => {
    if (!isElement(ele)) return;
    let hidden = isHidden(ele);
    let show = isUndefined(force) ? hidden : force;
    if (show) {
      ele.style.display = ele['___cd'] || '';
      if (isHidden(ele)) ele.style.display = this.#getDefaultDisplay(ele.tagName);
    } else if (!hidden) {
      ele['___cd'] = computeStyle(ele, 'display');
      ele.style.display = 'none';
    }
  });

  #defaultDisplay = {};

  #getDefaultDisplay(tagName) {
    if (this.#defaultDisplay[tagName]) return this.#defaultDisplay[tagName];
    let ele = createElement(tagName);
    doc.body.insertBefore(ele, null);
    let display = computeStyle(ele, 'display');
    doc.body.removeChild(ele);
    return this.#defaultDisplay[tagName] = display !== 'none' ? display : 'block';
  }
  //#endregion

  //#region size
  /** Return or set the width of the element. */
  width = (value) => this.#size('Width', value);
  /** Return or set the height of the element. */
  height = (value) => this.#size('Height', value);

  /** Return the width of the element + padding. */
  innerWidth = (includeMargins) => this.#innerSize('Width', false, includeMargins);
  /** Return the height of the element + padding. */
  innerHeight = (includeMargins) => this.#innerSize('Height', false, includeMargins);

  /** Return the outer width of the element. Includes margins if includeMargings is set to true. */
  outerWidth = (includeMargins) => this.#innerSize('Width', true, includeMargins);
  /** Return the outer height of the element. Includes margins if includeMargings is set to true. */
  outerHeight = (includeMargins) => this.#innerSize('Height', true, includeMargins);

  #size(prop, value) {
    let propLC = prop.toLowerCase();
    if (!this[0]) return isUndefined(value) ? undefined : this;
    if (!value || !arguments.length) {
      if (isWindow(this[0])) return this[0].document.documentElement["client".concat(prop)];
      if (isDocument(this[0])) return getDocumentDimension(this[0], prop);
      return this[0].getBoundingClientRect()[propLC] - getExtraSpace(this[0], prop === 'Width');
    }
    let valueNumber = parseInt(value, 10);
    return this.each((i, ele) => {
      if (!isElement(ele)) return;
      let boxSizing = computeStyle(ele, 'boxSizing');
      ele.style[propLC] = this.#getSuffixedValue(propLC, valueNumber + (boxSizing === 'border-box' ? getExtraSpace(ele, prop === 'Width') : 0));
    });
  }

  #innerSize = (prop, outer, includeMargins) => {
    if (!this[0]) return;
    if (isWindow(this[0])) return outer ? this[0]["inner".concat(prop)] : this[0].document.documentElement["client".concat(prop)];
    if (isDocument(this[0])) return getDocumentDimension(this[0], prop);
    return this[0]["".concat(outer ? 'offset' : 'client').concat(prop)] + (includeMargins && outer ? computeStyleInt(this[0], "margin".concat(!outer ? 'Top' : 'Left')) + computeStyleInt(this[0], "margin".concat(!outer ? 'Bottom' : 'Right')) : 0);
  };
  //#endregion

  //#region styling
  /** Return a CSS property value when just the property is supplied.\
   Set a CSS property when property and value are supplied.\
   Set multiple properties when an object is supplied.\
   Properties will be autoprefixed if needed for the user's browser. */
  css(prop, value) {
    let isCSSVar = isCSSVariable(prop);
    prop = this.#getPrefixedProp(prop, isCSSVar);
    if (arguments.length < 2) return this[0] && computeStyle(this[0], prop, isCSSVar);
    if (!prop) return this;
    value = this.#getSuffixedValue(prop, value, isCSSVar);

    return this.each((i, ele) => {
      if (!isElement(ele)) return;
      if (isCSSVar) ele.style.setProperty(prop, value);
      else ele.style[prop] = value;
    });
  }

  #prefixedProps = {};

  #getPrefixedProp(prop, isVariable) {
    if (isVariable === void 0) {
      isVariable = isCSSVariable(prop);
    }
    if (isVariable) return prop;

    if (!this.#prefixedProps[prop]) {
      let propCC = camelCase(prop);
      let propUC = "".concat(propCC[0].toUpperCase()).concat(propCC.slice(1));
      let props = ("".concat(propCC, " ").concat(vendorsPrefixes.join("".concat(propUC, " "))).concat(propUC)).split(' ');

      each(props, (i, p) => {
        if (p in style) {
          this.#prefixedProps[prop] = p;
          return false;
        }
      });
    }

    return this.#prefixedProps[prop];
  }

  #getSuffixedValue(prop, value, isVariable) {
    if (isVariable === void 0) isVariable = isCSSVariable(prop);
    return !isVariable && !numericProps[prop] && isNumeric(value) ? "".concat(value, "px") : value;
  }
  //#endregion

  //#region events
  _guid = 1;

  /** Add an event listener to each element.\
   Accepts space-separated eventName for listening to multiple events.\
   Event is delegated if delegate is supplied. */
  on = (eventFullName, selector, data, callback, _one) => {
    let _this = this;
    if (!isString(eventFullName)) {
      for (let key in eventFullName) this.on(key, selector, data, eventFullName[key], _one);
      return this;
    }
    if (!isString(selector)) {
      if (isUndefined(selector) || isNull(selector)) {
        selector = '';
      } else if (isUndefined(data)) {
        data = selector;
        selector = '';
      } else {
        callback = data;
        data = selector;
        selector = '';
      }
    }
    if (!isFunction(callback)) {
      callback = data;
      data = undefined;
    }
    if (!callback) return this;

    each(getSplitValues(eventFullName), (i, eventFullName) => {
      let _a = parseEventName(eventFullName), nameOriginal = _a[0], namespaces = _a[1];
      let name = getEventNameBubbling(nameOriginal);
      let isEventHover = (nameOriginal in eventsHover);
      let isEventFocus = (nameOriginal in eventsFocus);
      if (!name) return;

      _this.each((i, ele) => {
        if (!isElement(ele) && !isDocument(ele) && !isWindow(ele)) return;
        let finalCallback = (event) => {
          if (event.target["___i".concat(event.type)]) return event.stopImmediatePropagation();
          if (event.namespace && !hasNamespaces(namespaces, event.namespace.split(eventsNamespacesSeparator))) return;
          if (!selector && ((isEventFocus && (event.target !== ele || event.___ot === name)) || (isEventHover && event.relatedTarget && ele.contains(event.relatedTarget)))) return;
          let thisArg = ele;

          if (selector) {
            let target = event.target;
            while (!matches(target, selector)) {
              if (target === ele) return;
              target = target.parentNode;
              if (!target) return;
            }
            thisArg = target;
          }
          Object.defineProperty(event, 'currentTarget', {
            configurable: true,
            get: () => thisArg
          });
          Object.defineProperty(event, 'delegateTarget', {
            configurable: true,
            get: () => ele
          });
          Object.defineProperty(event, 'data', {
            configurable: true,
            get: () => data
          });
          let returnValue = callback.call(thisArg, event, event.___td);
          if (_one) removeEvent(ele, name, namespaces, selector, finalCallback);
          if (returnValue === false) {
            event.preventDefault();
            event.stopPropagation();
          }
        };
        finalCallback.guid = callback.guid = (callback.guid || this._guid++);
        addEvent(ele, name, namespaces, selector, finalCallback);
      });
    });
    return this;
  }

  /** Remove an event listener from each element.\
   Accepts space-separated eventName for removing multiple events listeners.\
   Remove all event listeners if called without arguments. */
  off = (eventFullName, selector, callback) => {
    let _this = this;
    if (isUndefined(eventFullName)) {
      this.each((i, ele) => {
        if (!isElement(ele) && !isDocument(ele) && !isWindow(ele)) return;
        removeEvent(ele);
      });
    } else if (!isString(eventFullName)) {
      for (let key in eventFullName) this.off(key, eventFullName[key]);
    } else {
      if (isFunction(selector)) {
        callback = selector;
        selector = '';
      }

      each(getSplitValues(eventFullName), (i, eventFullName) => {
        let _a = parseEventName(eventFullName), nameOriginal = _a[0], namespaces = _a[1];
        let name = getEventNameBubbling(nameOriginal);
        _this.each((i, ele) => {
          if (!isElement(ele) && !isDocument(ele) && !isWindow(ele)) return;
          removeEvent(ele, name, namespaces, selector, callback);
        });
      });
    }
    return this;
  };

  /** Add an event listener to each element that only triggers once for each element.\
   Accepts space-separated eventName for listening to multiple events.\
   Event is delegated if a delegate is supplied. */
  once = (eventFullName, selector, data, callback) => this.on(eventFullName, selector, data, callback, true);

  /** Trigger the supplied event on elements in collection. Data can be passed along as the second parameter. */
  trigger = (event, data) => {
    if (isString(event)) {
      let _a = parseEventName(event), nameOriginal = _a[0], namespaces = _a[1];
      let name_1 = getEventNameBubbling(nameOriginal);
      if (!name_1) return this;
      let type = regex.eventsMouse.test(name_1) ? 'MouseEvents' : 'HTMLEvents';
      event = document.createEvent(type);
      event.initEvent(name_1, true, true);
      event.namespace = namespaces.join(eventsNamespacesSeparator);
      event.___ot = nameOriginal;
    }
    event.___td = data;
    let isEventFocus = (event.___ot in eventsFocus);

    return this.each((i, ele) => {
      if (isEventFocus && isFunction(ele[event.___ot])) {
        ele["___i".concat(event.type)] = true; // Ensuring the native event is ignored
        ele[event.___ot]();
        ele["___i".concat(event.type)] = false; // Ensuring the custom event is not ignored
      }
      ele.dispatchEvent(event);
    });
  };

  /** Call the provided callback method on DOMContentLoaded. */
  ready = (callback) => {
    let cb = () => setTimeout(callback, 0, dom);
    if (document.readyState !== 'loading') cb();
    else document.addEventListener('DOMContentLoaded', cb);
    return this;
  }

  /** Call the 'focus' event on the elements or subscribe to it by providing a callback method. */
  onFocus = (callback) => this.#evt('focus', callback);
  /** Call the 'blur' event on the elements or subscribe to it by providing a callback method. */
  onBlur = (callback) => this.#evt('blur', callback);

  /** Call the 'click' event on the elements or subscribe to it by providing a callback method. */
  onClick = (callback) => this.#evt('click', callback);
  /** Call the 'mousedown' event on the elements or subscribe to it by providing a callback method. */
  onMousedown = (callback) => this.#evt('mousedown', callback);
  /** Call the 'mouseup' event on the elements or subscribe to it by providing a callback method. */
  onMouseup = (callback) => this.#evt('mouseup', callback);

  /** Call the 'change' event on the elements or subscribe to it by providing a callback method. */
  onChange = (callback) => this.#evt('change', callback);
  /** Call the 'select' event on the elements or subscribe to it by providing a callback method. */
  onSelect = (callback) => this.#evt('select', callback);
  /** Call the 'submit' event on the elements or subscribe to it by providing a callback method. */
  onSubmit = (callback) => this.#evt('submit', callback);

  /** Call the 'keydown' event on the elements or subscribe to it by providing a callback method. */
  onKeydown = (callback) => this.#evt('keydown', callback);
  /** Call the 'keypress' event on the elements or subscribe to it by providing a callback method. */
  onKeypress = (callback) => this.#evt('keypress', callback);
  /** Call the 'keyup' event on the elements or subscribe to it by providing a callback method. */
  onKeyup = (callback) => this.#evt('keyup', callback);

  //potential other events: focusin, focusout, resize, scroll, dblclick, mousemove, mouseover, mouseout, mouseenter, mouseleave, contextmenu

  #evt = (evt, callback) => {
    if (callback) return this.on(evt, callback);
    return this.trigger(evt);
  };
  //#endregion
}

//#region initialization
const init = (selector, context) => new ExDOM(selector, context);
win['dom'] = init;
//#endregion

//#region helpers
const find = (selector, context) => {
  let isFragment = isDocumentFragment(context);
  return !selector || (!isFragment && !isDocument(context) && !isElement(context))
    ? []
    : !isFragment && regex.class.test(selector)
      ? context.getElementsByClassName(selector.slice(1).replace(/\\/g, ''))
      : !isFragment && regex.tag.test(selector)
        ? context.getElementsByTagName(selector)
        : context.querySelectorAll(selector);
}

const attempt = (fn, arg) => {
  try {
    return fn(arg);
  }
  catch (_a) {
    return arg;
  }
}

const camelCase = (str) => str.replace(regex.dashAlpha, (match, letter) => letter.toUpperCase());

//value- and type-checking
const isExDOM = (value) => value instanceof ExDOM;
const isWindow = (value) => !!value && value === value.window;
const isDocument = (value) => !!value && value.nodeType === 9;
const isDocumentFragment = (value) => !!value && value.nodeType === 11;
const isElement = (value) => !!value && value.nodeType === 1;
const isText = (value) => !!value && value.nodeType === 3;
const isFunction = (value) => typeof value === 'function';
const isString = (value) => typeof value === 'string';
const isUndefined = (value) => value === undefined;
const isNull = (value) => value === null;
const isNumeric = (value) => !isNaN(parseFloat(value)) && isFinite(value);
const isHidden = (ele) => computeStyle(ele, 'display') === 'none';
const isCSSVariable = (prop) => regex.cssVariable.test(prop);
const isPlainObject = (value) => {
  if (typeof value !== 'object' || value === null) return false;
  let proto = Object.getPrototypeOf(value);
  return proto === null || proto === Object.prototype;
}

const each = (arr, callback, _reverse) => {
  if (_reverse) {
    let i = arr.length;
    while (i--) if (callback.call(arr[i], i, arr[i]) === false) return arr;
  } else if (isPlainObject(arr)) {
    let keys = Object.keys(arr);
    for (let i = 0, l = keys.length; i < l; i++) {
      let key = keys[i];
      if (callback.call(arr[key], key, arr[key]) === false) return arr;
    }
  } else {
    for (let i = 0, l = arr.length; i < l; i++)
      if (callback.call(arr[i], i, arr[i]) === false) return arr;
  }
  return arr;
}

const pluck = (arr, prop, deep, until) => {
  let plucked = [];
  let isCallback = isFunction(prop);
  let compare = until && getCompareFunction(until);
  for (let i = 0, l = arr.length; i < l; i++) {
    if (isCallback) {
      let val_1 = prop(arr[i]);
      if (val_1.length) arrPush.apply(plucked, val_1);
    } else {
      let val_2 = arr[i][prop];
      while (val_2 != null) {
        if (until && compare(-1, val_2)) break;
        plucked.push(val_2);
        val_2 = deep ? val_2[prop] : null;
      }
    }
  }
  return plucked;
}

const getValue = (ele) => ele.multiple && ele.options
  ? pluck(arrFilter.call(ele.options, (option) => option.selected && !option.disabled && !option.parentNode.disabled), 'value')
  : ele.value || '';

const getSplitValues = (str) => isString(str) ? str.match(regex.splitValues) || [] : [];

const computeStyle = (ele, prop, isVariable) => {
  if (!isElement(ele)) return;
  let style = win.getComputedStyle(ele, null);
  return isVariable ? style.getPropertyValue(prop) || undefined : style[prop] || ele.style[prop];
}

const computeStyleInt = (ele, prop) => parseInt(computeStyle(ele, prop), 10) || 0;
const getExtraSpace = (ele, xAxis) => computeStyleInt(ele, "border".concat(xAxis ? 'Left' : 'Top', "Width")) + computeStyleInt(ele, "padding".concat(xAxis ? 'Left' : 'Top')) + computeStyleInt(ele, "padding".concat(xAxis ? 'Right' : 'Bottom')) + computeStyleInt(ele, "border".concat(xAxis ? 'Right' : 'Bottom', "Width"));
const getDocumentDimension = (doc, dimension) => Math.max(doc.body["scroll".concat(dimension)], docEle["scroll".concat(dimension)], doc.body["offset".concat(dimension)], docEle["offset".concat(dimension)], docEle["client".concat(dimension)]);

const matches = (ele, selector) => {
  let matches = ele && (ele['_matches'] || ele['webkitMatchesSelector'] || ele['msMatchesSelector']);
  return !!matches && !!selector && matches.call(ele, selector);
}

const getCompareFunction = (comparator) => {
  return isString(comparator)
    ? (i, ele) => matches(ele, comparator) : isFunction(comparator)
      ? comparator : isExDOM(comparator)
        ? (i, ele) => comparator.is(ele) : !comparator
          ? () => false : (i, ele) => ele === comparator;
}

const unique = (arr) => arr.length > 1 ? arrFilter.call(arr, (item, index, self) => arrIndexOf.call(self, item) === index) : arr;

const filtered = (collection, comparator) => !comparator ? collection : collection.filter(comparator);

const parseHTML = (html) => {
  if (!isString(html)) return [];
  if (regex.singleTag.test(html)) return [createElement(RegExp.$1)];
  let container = div;
  container.innerHTML = html;

  return dom(container.childNodes).detach().get();
}

const getEventNameBubbling = (name) => eventsHover[name] || eventsFocus[name] || name;

const parseEventName = (eventName) => {
  let parts = eventName.split(eventsNamespacesSeparator);
  return [parts[0], parts.slice(1).sort()]; // [name, namespace[]]
}

const getEventsCache = (ele) => ele[eventsNamespace] = (ele[eventsNamespace] || {});

const addEvent = (ele, name, namespaces, selector, callback) => {
  let eventCache = getEventsCache(ele);
  eventCache[name] = (eventCache[name] || []);
  eventCache[name].push([namespaces, selector, callback]);
  ele.addEventListener(name, callback);
}

const hasNamespaces = (ns1, ns2) => {
  return !ns2 || !arrSome.call(ns2, (ns) => ns1.indexOf(ns) < 0);
}

const removeEvent = (ele, name, namespaces, selector, callback) => {
  let cache = getEventsCache(ele);
  if (!name) for (name in cache) removeEvent(ele, name, namespaces, selector, callback);
  else if (cache[name]) {
    cache[name] = cache[name].filter((_a) => {
      let ns = _a[0], sel = _a[1], cb = _a[2];
      if ((callback && cb.guid !== callback.guid) || !hasNamespaces(ns, namespaces) || (selector && selector !== sel)) return true;
      ele.removeEventListener(name, cb);
    });
  }
}
//#endregion
