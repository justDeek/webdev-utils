<?php

//--- GUI ---

function gui_create_text_input(
	$value = "", $name = "", $placeholder = "", $class = "", $add = "", $maxlength = "60", $type = "text",
	$readonly = false, $enclosure = "div") {
	$read_only = $readonly ? "readonly" : "";
	$class .= $readonly ? "read-only" : "";
	toAttr($add);
	
	if(empty($maxlength)) $maxlength = "60";
	if($type === "tel") {
		$add .= " pattern=\"[0-9]*\" title=\"Only numbers are allowed.\"";
	}
	
	echo "<$enclosure class='text-container' style=\"display: flex;\">";
	
	echo "<input type='$type' width='100%'
               onDrag='return false' onDrop='return false'
							 name='$name' value='$value' placeholder='$placeholder'
							 maxlength='$maxlength' class='$class' $add $read_only />";
	echo "</$enclosure>";
}

//if the width of the number input is supposed to be as long as the other option-fields
// the width in $add should be set to 180px and the td outside the call to this function
// needs to have text-align: left; in order to display properly
function gui_create_number_input($name = "", $id = "", $value = "", $min = "", $max = "", $add = "", $appendZero = true,
                                 $revolve = true, $steps = 1) {
	if(empty($appendZero)) $appendZero = "false";
	
	$append = $appendZero ? "oninput='AppendZero(this);'" : "";
	toJS($appendZero, $revolve, $steps);
	toAttr($add);
	
	html_span_start("class='text-container'");
	
	//actual number input
	echo "<input type='number' id='$id' name='$name' value='$value' min='$min' max='$max' $add $append onwheel='prepMouseWheelHandler(\"btnspin-input\")'/>";
	
	html_span_end();
}

function gui_create_textarea($value = "", $name = "", $add = "") {
	toAttr($add);
	echo "<textarea name='$name' $add>$value</textarea>";
}

function gui_create_textarea_start($name = "", $rows = "", $add = "") {
	toAttr($add);
	echo "<textarea name='$name' rows='$rows' $add>";
}

function gui_create_textarea_end() {
	echo "</textarea>";
}

//radio-button
function gui_create_radio_button(
	$value = "radio1", $name = "radio", $text = "", $class = "", $add = "", $isChecked = false, $lbl_add = "",
	$div_add = ""
) {
	toAttr($add);
	if($isChecked) $add .= " checked";
	
	html_tag_start('label', "class='radio-container' $lbl_add");
	html_div($text, "class='radio-label' $div_add");
	gui_create_text_input($value, $name, "", $class, $add, "", "radio");
	html_span_start("class='radio-button'");
	html_div("", "class='radio-inner'");
	html_span_end();
	html_tag_end("label");
}

//checkbox
function gui_create_checkbox(
	$value = "checkbox1", $name = "checkbox", $id = "checkbox", $isChecked = false, $add = "",
	$noticeSaved = false, $lbl_class = "", $lbl_add = "", $span_add = "", $span_class_add = "", $enclosure = "label"
) {
	toAttr($add);
	if($isChecked) $add .= " checked";
	
	if($noticeSaved) html_span_start();
	
	html_tag_start($enclosure, "class='checkbox-container $lbl_class' $lbl_add");
	echo "<input name='{$name}' value='{$value}' type='checkbox' $add />";
	echo "<span class='checkbox-button $span_class_add' $span_add><div class='checkbox-inner'></div></span>";
	html_tag_end($enclosure);
	
	if(!$noticeSaved) return;
	
	html_span_end();
	html_span("saved.", "class=\"notice-saved\"");
	$json_id = "#$id";
	toJS($json_id);
	
	echo '<script>
    let chkbox = $($json_id);
    let notice = chkbox.parents(3).find(".notice-saved");
    let timeout_handle;
    
    chkbox.change(function() {
      clearTimeout(timeout_handle);
      notice.finish().fadeIn(0).css("display", "flex");
      
      timeout_handle = setTimeout(() => notice.fadeOut(500), 1500);
    });
  </script>';
}

//select
function gui_create_select_start($name = "", $id = "", $onchange = "", $size = "", $add = "", $enclosure = "div", $enclosure_add = "") {
	toAttr($add);
	//can't use oninput-event because it messes with miscdests
	echo "<$enclosure class='drop-down-container' $enclosure_add>";
	echo "<select name='$name' id='$id' onchange='$onchange' size='{$size}' $add >";
}

function gui_create_option($value = "", $text = "", $isSelected = false, $add = "") {
	toAttr($add);
	if($isSelected) $add .= "selected";
	//$text = shorten($text, 20);
	echo "<option $add value='$value'>$text</option>";
}

function gui_create_select_end($enclosure = "div") {
	echo "</select>";
	echo "</$enclosure>";
}

function gui_create_button($content = "", $add = "", $onclick = "", $type = "button") {
	toAttr($add);
	echo "<button type='$type' $add onclick='$onclick'>$content</button>";
}

function gui_create_file_input($add = "", $type = "file") {
	toAttr($add);
	echo "<input type='$type' $add />";
}

function gui_create_label($for, $text = "", $add = "") {
	toAttr($add);
	echo "<label for='$for' $add>$text</label>";
}

function gui_create_submit($add = "") {
	toAttr($add);
	echo "<input class=\"input-submit\" name=\"Submit\" type=\"submit\" value=\"Submit\" $add />";
}