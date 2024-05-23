//returns the value of the given query parameter by name
function getQueryParameter(name) {
	let url = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "";
	
	if (empty(url)) url = window.location.href;
	
	name = name.replace(/[\[\]]/g, "\\$&");
	let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"), results = regex.exec(url);
	
	if (!results) return null;
	if (!results[2]) return "";
	
	return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function setQueryParameter(param, value) {
	let remove = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
	baseUrl = [location.protocol, "//", location.host, location.pathname].join("");
	urlQueryString = document.location.search;
	let newParam = key + "=" + value, params = "?" + newParam;
	
	// If the "search" string exists, then build params from it
	if (urlQueryString) {
		keyRegex = new RegExp("([\?&])" + key + "[^&]*");
		
		// If param exists already, update it
		if (urlQueryString.match(keyRegex) !== null) {
			params = urlQueryString.replace(keyRegex, !remove ? "$1" + newParam : "");
		} else { // Otherwise, add it to end of query string
			params = urlQueryString + "&" + newParam;
		}
	}
	
	window.history.replaceState({}, "", baseUrl + params);
}

//returns the hash-parameter in the current url
function getHashParameter() {
	let hasharr = window.location.href.split("#");
	let hash = "";
	
	if (hasharr.length > 1) hash = hasharr[1];
	if (!empty(hash)) hash = "#" + hash;
	
	return hash;
}

// Read a page's GET URL variables and return them as an associative array.
function getUrlVars() {
	let vars = [], hash;
	let hashes = window.location.href.slice(window.location.href.indexOf("?") + 1).split("&");
	
	for (let i = 0; i < hashes.length; i++) {
		hash = hashes[i].split("=");
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	
	return vars;
}

//removes any hashed anchor-link from the current url
function removeHash() {
	history.pushState("", document.title, window.location.pathname + window.location.search);
}

// Method that checks that the browser supports the HTML5 File API
function browserSupportFileUpload() {
	let isCompatible = false;
	
	if (window.File && window.FileReader && window.FileList && window.Blob) isCompatible = true;
	
	return isCompatible;
}

// Method that reads and processes the selected file
function upload(evt, targetFunc) {
	if (!browserSupportFileUpload()) {
		showPopup("error", "Dieser Browser oder diese Browserversion unterstützt nicht die benötigten Datei-APIs!");
	} else {
		let data = null;
		let file = evt.target.files[0];
		let reader = new FileReader();
		reader.readAsText(file);
		
		reader.onload = function (event) {
			let csvData = event.target.result;
			data = $.csv.toArrays(csvData);
			
			if (data && data.length > 0) {
				// alert(data.length + '- Reihen erfolgreich importiert.');
				window[targetFunc](data);
			} else {
				showPopup("error", "Keine Daten importiert!");
			}
		};
		
		reader.onerror = function () {
			showPopup("error", "Unable to read " + file.fileName);
		};
	}
}
