/** @description Determine if an array contains one or more items from another array.
 * @return {boolean} true|false If haystack contains at least one item from arr. */
String.prototype.contains = function contains(...arr) {
  return arr.some(v => this.includes(v));
}

/** @description Convert this string to PascalCase. */
String.prototype.toPascalCase = function toPascalCase() {
  return this?.replace(/(\w)(\w*)/g, (_, g1, g2) => g1.toUpperCase() + g2.toLowerCase());
}

//Replace all occurrences of a string in another string
String.prototype.replaceAll = function(search, replacement) {
	return this.replace(new RegExp(search, "g"), replacement);
};

//returns the n'th occurrence of a string in another string
function nthIndex(baseString, subString, occurrence) {
  return baseString.split(subString, occurrence).join(subString).length;
}

function isLetter(c) {
  return (((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) || (c == " ") || (c == "&")
    || (c == "'") || (c == "(") || (c == ")") || (c == "-") || (c == "/"));
}

function isAlphanumeric(s) {
  let i;

  for (i = 0; i < s.length; i++) {
    let c = s.charAt(i);

    if (!(is_letter(c) || is_digit(c))) return false;
  }
  return true;
}

function isWhitespace(s) {
  let i;

  if (is_empty(s)) return true;

  for (i = 0; i < s.length; i++) {
    let c = s.charAt(i);

    if (whitespace.indexOf(c) == -1) return false;
  }
  return true;
}

function isDigit(c) {
  return ((c >= "0") && (c <= "9"));
}

function createExcerpt(content, length = 140) {
  return content
    .split('\n')
    .slice(0, 6)
    .map((str) => str.replace(/<\/?[^>]+(>|$)/g, '').split('\n'))
    .flat()
    .join(' ')
    .substring(0, length);
};
