/*--- File-Handling ---*/

//returns the file-name by removing the extension from the given path
function get_file_name(file) {
	//remove extension
	let file_name = file.includes(".") ? file.substr(0, file.lastIndexOf(".")) : file;
	
	//remove sub-folders
	if (file_name.includes("/")) {
		let last_slash_index = file.lastIndexOf(".");
		file_name = file.substr(last_slash_index, file_name.length - last_slash_index);
	}
	
	return file_name;
}

//returns the file-extension of the given path;
//if no extension exists, returns the whole file-path
function get_file_extension(file) {
	return file.split(".").pop();
}