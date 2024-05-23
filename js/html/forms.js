//sets the cursor to the end of text input fields on the start of the page by default
function setCursorToEnd(input) {
  if (input.createTextRange) {
    //in IE
    let FieldRange = input.createTextRange();
    FieldRange.moveStart("character", input.value.length);
    FieldRange.collapse();
    FieldRange.select();
  } else {
    //in Firefox/Opera/Chrome/Safari
    input.focus();
    let length = input.value.length;
    input.setSelectionRange(length, length);
  }
}

function clearSelection() {
  if (document.selection) {
    document.selection.empty();
  } else if (window.getSelection) {
    window.getSelection().removeAllRanges();
  }
}


/*--- Checkboxes ---*/

//returns whether a checkbox is checked or not
function isChecked(id) {
  return $("#" + id).is(":checked");
}


/*--- Validatation ---*/

//validates the provided email address by checking it's pattern
function isValidEmail(email) {
  let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}
