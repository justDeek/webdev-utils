/** @description Get the previous item in an array and loop around to the end when at the start of this array. */
Array.prototype.prev = function prev(item) {
  var prevID = this.indexOf(item) - 1
  if (prevID < 0) prevID = this.length - 1
  return this[prevID];
};

/** @description Get the next item in an array and loop around to the beginning when at the end of this array. */
Array.prototype.next = function next(item) {
  var nextID = this.indexOf(item) + 1;
  if (nextID > (this.length - 1)) nextID = 0;
  return this[nextID];
};

Array.prototype.removeByValue = function(val) {
	for (let i = 0; i < this.length; i++) {
		if (this[i] == val) {
			this.splice(i, 1);
			break;
		}
	}
};
