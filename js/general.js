/*--- General ---*/

//- read or keep track of the current time -
// let dt    = new Date();
// let date  = dt.toLocaleDateString();
// let time  = dt.toLocaleTimeString();
// let ftime = time.replace(/:/g, '-'); //formated time (14:06:13 -> 13-06-13)

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

//helper to make sure that the variable is not only not empty but also not zero, as it's otherwise recognized as false
function not_empty($var) {
	return ($var === "0" || $var);
}

function is_empty(s) {return ((s == null) || (s.length === 0));}

function is_whitespace(s) {
	let i;
	
	if (is_empty(s)) return true;
	
	for (i = 0; i < s.length; i++) {
		let c = s.charAt(i);
		
		if (whitespace.indexOf(c) == -1) return false;
	}
	return true;
}

function is_filename(s) {
	let i;
	
	for (i = 0; i < s.length; i++) {
		let c = s.charAt(i);
		
		if (!is_filename_char(c)) return false;
	}
	return true;
}

function is_filename_char(c) {
  return (((c >= '0') && (c <= '9')) || ((c >= 'a') && (c <= 'z')) || ((c >= 'A') && (c <= 'Z')) || (c == '_') || (c == '-'));
}

function is_digit(c) { return ((c >= "0") && (c <= "9")); }

function is_letter(c) {
	return (((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) || (c == " ") || (c == "&")
	        || (c == "'") || (c == "(") || (c == ")") || (c == "-") || (c == "/"));
}

function is_alphabetic(s) {
	let i;
	
	for (i = 0; i < s.length; i++) {
		let c = s.charAt(i);
		
		if (!is_letter(c)) return false;
	}
	return true;
}

//checks whether the given value is a number
function is_numeric(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function is_alphanumeric(s) {
	let i;
	
	for (i = 0; i < s.length; i++) {
		let c = s.charAt(i);
		
		if (!(is_letter(c) || is_digit(c))) return false;
	}
	return true;
}

//validates the provided email address by checking it's pattern
function is_valid_email(email) {
	let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(String(email).toLowerCase());
}

// getElementById wrapper
function $id(id) {
	return document.getElementById(id);
}

//returns the n'th occurrence of a string in another string
function nth_index(baseString, subString, occurrence) {
	return baseString.split(subString, occurrence).join(subString).length;
}


/*--- Misc ---*/

//stops the event propagation with respect to older browsers like < IE11
function stop_propagation(evt) {
	evt = (evt) ? evt : window.event;
	
	if (evt && evt.stopPropagation) evt.stopPropagation();
	else if (window.event) evt.cancelBubble = true;
	else if (window.$.Event.prototype) window.$.Event.prototype.stopPropagation();
	
	return false;
}

/*--- Google Analytics ---*/

/*
window.dataLayer = window.dataLayer || [];

function gtag() {
  if (typeof window.dataLayer !== 'undefined')
    window.dataLayer.push(arguments);
}
gtag('js', new Date());
*/