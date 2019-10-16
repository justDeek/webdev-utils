/*---Debugging---*/

//creates a log-entry in the browser console
function log(...params) {
	if (empty(params)) params = "- unspecified -";
	console.log(params);
}

//shows an alert pop-up that automatically converts numbers to string format
function echo(...params) {
	if (is_numeric(params)) params = params.toString();
	alert(params);
}

function log_json(output) {
	console.log(JSON.stringify(output));
}