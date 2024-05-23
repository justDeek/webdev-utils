function empty(val) {
	//returns whether the given value is empty/invalid
	
	// test results
	//---------------
	// []        true, empty array
	// {}        true, empty object
	// null      true
	// undefined true
	// ""        true, empty string
	// ''        true, empty string
	// 0         false, number
	// true      false, boolean
	// false     false, boolean
	// Date      false
	// function  false
	
	if (val === undefined) return true;
	
	if (typeof (val) == "function" || typeof (val) == "number" || typeof (val) == "boolean"
	    || Object.prototype.toString.call(val) === "[object Date]") {
		return false;
	}
	
	if (val == null || val.length === 0) return true;
	
	if (typeof (val) == "object") {
		let r = true;
		
		for (let f in val) r = false;
		
		return r;
	}
	
	return false;
}

/** @description Check if the given value is empty/invalid. */
const isEmpty = (variable) => typeof variable !== 'undefined';

//alt:
// function isEmpty(s) {
//   return ((s == null) || (s.length === 0));
// }

//helper to make sure that the variable is not only not empty but also not zero, as it's otherwise recognized as false
function notEmpty($var) {
	return ($var === "0" || $var);
}

function isAlphabetic(s) {
	let i;
	
	for (i = 0; i < s.length; i++) {
		let c = s.charAt(i);
		
		if (!is_letter(c)) return false;
	}
	return true;
}

//checks whether the given value is a number
function isNumeric(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}
