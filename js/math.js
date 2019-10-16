//returns a random value between given range
function rnd(min, max) {
	return Math.floor(Math.random() * (max - min + 1) + min);
}

function roundToBase(x, base) {
	return Math.ceil(x / base) * base;
}