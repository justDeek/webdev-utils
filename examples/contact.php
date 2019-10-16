<?php
require_once("../php/main.php");

$name = $_POST['name'] ?? "";
$email = $_POST['email'] ?? "";
$message = $_POST['message'] ?? "";

html_div_start("c: main-content");
html_div_start("c: contact-container");

//has submitted?
if(!empty($name)) {
	if(sendMail("Contact Request", $message, $email)) {
		html_div_start("c: sent-title");
		html_h2("Message has been sent.");
		html_div_end();
		html_div("Thank you for your submission! Depending on your request, we'll get back to you soon.",
			"c: sent-message");
	}
} else {
	html_form_start("contact-form", "", "return check_contact_form();", "autocomplete: off");
	html_div_start("c: form-entry form-name");
	gui_create_text_input("", "name", "Name", "name-input", "minlength: 3 | required | autofocus");
	html_div_break("c: form-entry form-email");
	gui_create_text_input("", "email", "Email-Address", "name-input", "minlength:3 | required", "", "email");
	html_div_break("c: form-entry form-message");
	gui_create_textarea("", "message", "minlength: 3 | maxlength = 2000 | placeholder: Message | required | oninput:check_message();");
	html_div_break("c: form-entry form-submit");
	gui_create_submit();
	html_div_end();
	html_form_end();
}
html_div_end(2);
?>

<script>
	function check_message() {
		if ($(".form-message input").val().length >= 2000)
			alert("Message shouldn't exceed 2000 characters! Please keep it brief.");
	}
	
	function check_contact_form() {
		/* add further input checks on submission and return false when one of them failed to prevent the site from reloading */
		
		return true;
	}
</script>
