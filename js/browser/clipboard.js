/*--- Clipboard ---*/

function copyInput(target) {
	let parent = target.parentNode;
	let input = parent.nextSibling;
	
	input.select();
	document.execCommand("Copy");
	let infoSpan = $(parent).find("#clipboard-info");
	$(infoSpan).addClass("show");
	
	clearSelection();
}

function copyToClipboard(value) {
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val(value).select();
	document.execCommand("copy");
	$temp.remove();
}