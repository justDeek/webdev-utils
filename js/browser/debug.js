/*---Debugging---*/

/** @description Shorthand for console.log(). */
var log = console.log.bind(window.console)
/** @description Shorthand for console.warn(). */
var warn = console.warn.bind(window.console)
/** @description Shorthand for console.error(). */
var error = console.error.bind(window.console)

//alt. approach:
// function log(...params) {
// 	if (empty(params)) params = "- unspecified -";
// 	console.log(params);
// }

//shows an alert pop-up that automatically converts numbers to string format
function echo(...params) {
	if (is_numeric(params)) params = params.toString();
	alert(params);
}

function log_json(output) {
	console.log(JSON.stringify(output));
}
