/** @file A class for creating inline-suggestions next to the current element by
 *  trying to match the current word with any element from the provided array. */

const prevSuggestionKeyCode = 'ArrowUp'
const nextSuggestionKeyCode = 'ArrowDown'
const acceptKeyCode = 'Enter'

export default class Suggestion {
  parentElement = null;
  suggestionElement = null;
  suggestions = [];
  suggestionID = 0;
  prevWord = '';
  replace = true;
  isEmoji = false;

  init(parentEl, prevWord, suggestions, replace = true, isEmoji = false) {
    if (suggestions.length === 0) error('No suggestions provided!');

    if (this.suggestionElement !== null) {
      // error('Can not create more than one suggestion!');
      return;
    }

    this.parentElement = parentEl;
    this.suggestions = suggestions;
    this.suggestionID = 0;
    this.prevWord = prevWord;
    this.replace = replace;
    this.isEmoji = isEmoji;

    this.onKeyDown = this.onKeyDown.bind(this);
    this.onClick = this.onClick.bind(this);

    this.clear();
    this.create();
  }

  create() {
    this.suggestionElement = document.createElement('span');
    this.suggestionElement.className = "suggestion";
    if (this.replace) this.suggestionElement.classList.add("suggestion--replace");
    this.suggestionElement.setAttribute('contenteditable', 'false');
    this.updateContent();

    //insert suggestion at the current caret position
    let sel = window.getSelection();
    if (sel.getRangeAt && sel.rangeCount) {
      let range = sel.getRangeAt(0);
      range.insertNode(this.suggestionElement);
      range.collapse(true);
    }

    this.parentElement.addEventListener('keydown', this.onKeyDown);
    this.parentElement.addEventListener('click', this.onClick);
  }

  clear() {
    this.suggestionElement?.remove();
    this.suggestionElement = null;

    this.parentElement?.removeEventListener('keydown', this.onKeyDown);
    this.parentElement?.removeEventListener('click', this.onClick);
  }

  prev() {
    this.suggestionID--;
    if (this.suggestionID < 0) this.suggestionID = this.suggestions.length - 1;
    this.updateContent();
  }

  next() {
    this.suggestionID++;
    if (this.suggestionID > this.suggestions.length - 1) this.suggestionID = 0;
    this.updateContent();
  }

  accept(suggestion) {
    this.replaceCurrentWord(suggestion)
    this.clear()
  }

  onKeyDown(evt) {
    if (evt.code === prevSuggestionKeyCode) {
      evt.preventDefault();
      this.prev();
    } else if (evt.code === nextSuggestionKeyCode) {
      evt.preventDefault();
      this.next();
    } else if (evt.code === acceptKeyCode) {
      evt.preventDefault();
      this.accept(this.getSuggestion());
    } else this.clear();
  }

  onClick(evt) {
    if (evt.target === this.suggestionElement) this.accept(this.getSuggestion());
    else this.clear()
  }

  getSuggestion() {
    return this.suggestions[this.suggestionID];
  }

  setContent(content) {
    let suffix = this.suggestions.length > 1 ? ` ↓(${this.suggestionID + 1}/${this.suggestions.length})` : '';
    this.suggestionElement.innerText = content + suffix;
  }

  updateContent() {
    let text = this.getSuggestion();
    if (this.replace) text = text.replace(this.prevWord.toLowerCase(), '');
    this.setContent(text);
  }

  replaceCurrentWord(newWord) {
    this.parentElement.focus();
    let sel = document.getSelection(), prevWord = "", range, maxIterations = 100;

    while (!/[\w-]/.test(prevWord)) {
      if (--maxIterations <= 0) break;

      sel.modify("extend", "backward", "word");
      range = sel.getRangeAt(0);
      prevWord = range.toString().trim();
    }

    //remove colon from start of the word when inserting an emoji
    if (this.isEmoji) {
      sel.modify("extend", "backward", "character");
      sel.modify("extend", "backward", "character");
      range = sel.getRangeAt(0);
    }

    range.deleteContents();
    range.insertNode(document.createTextNode(newWord));
    range.collapse();
  };
}
