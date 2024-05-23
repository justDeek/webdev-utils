/*--- File-Handling ---*/

//returns the file-name by removing the extension from the given path
function getFileName(file) {
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
function getFileExtension(file) {
	return file.split(".").pop();
}

function is_filename(s) {
  let i;

  for (i = 0; i < s.length; i++) {
    let c = s.charAt(i);

    if (!is_filename_char(c)) return false;
  }
  return true;
}

function isFilenameChar(c) {
  return (((c >= '0') && (c <= '9')) || ((c >= 'a') && (c <= 'z')) || ((c >= 'A') && (c <= 'Z')) || (c == '_') || (c == '-'));
}
