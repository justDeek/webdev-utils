/** @file Class for creating a dictionary for efficient
 *  lookup of all previously provided words. */

const minWordLength = 5;
const maxSuggestions = 3;

export default class Dictionary {
  constructor() {
    this.vocabulary = new Map();
  }

  update(text) {
    if (text === undefined) return;
    const words = text.toLowerCase().split(/[^\w-]+/)
    for (const wordID in words) this.add_word(words[wordID])
    this.sortByOccurrence();
  }

  add_word(str, sort = false) {
    //ignore duplicate words in direct succession
    if (this.prevWord === str) return;
    this.prevWord = str;

    const word = str.toLowerCase().trim()
    if (word.length < minWordLength || /[^a-z]/gi.test(word)) return;

    if (this.vocabulary.has(word)) this.vocabulary.set(word, this.vocabulary.get(word) + 1);
    else this.vocabulary.set(word, 1);

    if (sort) this.sortByOccurrence();
  }

  sortByOccurrence() {
    this.vocabulary = new Map([...this.vocabulary.entries()].sort((a, b) => b[1] - a[1]))
  }

  find_suggestion(str) {
    const target = str.toLowerCase()
    let matches = [];

    this.vocabulary.forEach((val, key) => {
      if (matches.length >= maxSuggestions) return;
      if (key !== target && key.startsWith(target)) matches.push(key);
    });

    return matches;
  }
}
