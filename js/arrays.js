Array.prototype.removeByValue = function (val) {
	for (let i = 0; i < this.length; i++) {
		if (this[i] == val) {
			this.splice(i, 1);
			break;
		}
	}
};