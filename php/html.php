<?php
function html_tag($tag, $content = "", $add = "") { toAttr($add); echo "<$tag $add>$content</$tag>"; }
function html_tag_start($tag, $add = "") { toAttr($add); echo "<$tag $add>"; }
function html_tag_break($tag, $add = "") { toAttr($add); echo "</$tag><$tag $add>"; }
function html_tag_end($tag, $amount = 1) { while($amount-->0) echo "</$tag>"; }

//--- Base ---

function html_start($add = "") { toAttr($add); echo "<!DOCTYPE html><html $add>"; }
function html_end() { echo "</html>"; }

//head
function html_head_start() { echo "<head>"; }
function html_head_end($amount = 1) { while($amount-->0) echo "</head>"; }

//body
function html_body_start($add = "") { toAttr($add); echo "<body $add>"; }
function html_body_end($amount = 1) { while($amount-->0) echo "</body>"; }

//--- Head ---

//title
function html_title($title) { echo "<title>$title</title>"; }

//creates a meta-tag
function create_meta($params = "") { toAttr($params); echo "<meta $params>"; }

function create_link($href, $rel = "", $add = "") {
	echo "<link href='$href' rel='$rel' $add />";
}

//creates a css link-tag
function create_css($href, $add = "", $rel = "stylesheet", $type = "text/css") {
  $href = add_version(base_path($href));
	echo "<link href='$href' rel='$rel' type='$type' $add />";
}

function create_icon($href, $sizes = "", $rel = "icon", $type = "image/png", $add = "") {
  $href = add_version(base_path($href));
  echo "<link href='$href' rel='$rel' type='$type' sizes='$sizes' $add />";
}

//creates a script-tag
function create_script($src, $add = "", $type = "application/javascript") {
  $src = add_version(base_path($src));
	echo "<script src='$src' language='javascript' type='$type' $add></script>";
}

//--- General ---

//divider (block-element)
function html_div($content = "", $add = "") { toAttr($add); echo "<div $add>$content</div>"; }
function html_div_break($add = "") { toAttr($add); echo "</div><div $add>"; }
function html_div_start($add = "") { toAttr($add); echo "<div $add>"; }
function html_div_end($amount = 1) { while($amount-->0) echo "</div>"; }

//span (inline-element)
function html_span($content = "", $add = "") { toAttr($add); echo "<span $add>$content</span>"; }
function html_span_break($add = "") { toAttr($add); echo "</span><span $add>"; }
function html_span_start($add = "") { toAttr($add); echo "<span $add>"; }
function html_span_end($amount = 1) { while($amount-->0) echo "</span>"; }

//break
function html_break($amount = 1) { while($amount-->0) echo "<br>"; }

//--- Structural Semantics ---

//header
function html_header_start($add = "") { toAttr($add); echo "<header $add>"; }
function html_header_end($amount = 1) { while($amount-->0) echo "</header>"; }

//nav
function html_nav_start($add = "") { toAttr($add); echo "<nav $add>"; }
function html_nav_end($amount = 1) { while($amount-->0) echo "</nav>"; }

//main
function html_main($content = "", $add = "") { toAttr($add); echo "<main $add>$content</main>"; }
function html_main_start($add = "") { toAttr($add); echo "<main $add>"; }
function html_main_end($amount = 1) { while($amount-->0) echo "</main>"; }

//section
function html_section($content = "", $add = "") { toAttr($add); echo "<section $add>$content</section>"; }
function html_section_start($add = "") { toAttr($add); echo "<section $add>"; }
function html_section_end($amount = 1) { while($amount-->0) echo "</section>"; }

//aside
function html_aside($content = "", $add = "") { toAttr($add); echo "<aside $add>$content</aside>"; }
function html_aside_start($add = "") { toAttr($add); echo "<aside $add>"; }
function html_aside_end($amount = 1) { while($amount-->0) echo "</aside>"; }

//footer
function html_footer_start($add = "") { toAttr($add); echo "<footer $add>"; }
function html_footer_end($amount = 1) { while($amount-->0) echo "</footer>"; }

//--- Controls ---

//anchor
function html_anchor($content = "", $href = "", $target = "", $add = "") {
	toAttr($add);
	$href = base_path($href);
	echo "<a href='$href' target='$target' $add>$content</a>";
}
function html_anchor_start($href = "", $target = "", $add = "") {
	toAttr($add);
	$href = base_path($href);
	echo "<a href='$href' target='$target' $add>";
}
function html_anchor_end($amount = 1) { while($amount-->0) echo "</a>"; }

//image
function html_image($src, $alt = "", $add = "") {
	toAttr($add);
  $src = add_version(base_path($src));
	echo "<img src=$src alt=$alt $add />";
}

//label
function html_label($content = "", $add = "") { toAttr($add); echo "<label $add>$content</label>"; }
function html_label_start($add = "") { toAttr($add); echo "<label $add>"; }
function html_label_end($amount = 1) { while($amount-->0) echo "</label>"; }

//form
function html_form_start($name, $action, $onsubmit = "", $add = "", $method = "post") {
	toAttr($add);
	echo "<form name=\"$name\" action=\"$action\" method=\"$method\" onsubmit=\"$onsubmit\" $add>";
}
function html_form_end($amount = 1) { while($amount-->0) echo "</form>"; }

//hidden input
function html_hidden_input($name, $value) {
	echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
}

//--- Table ---

function html_table_start($add = "") { toAttr($add); echo "<table $add>"; }
function html_table_end($amount = 1) { while($amount-->0) echo "</table>"; }

//table head
function html_table_thead_break($add = "") { toAttr($add); echo "</thead><thead $add>"; }
function html_table_thead_start($add = "") { toAttr($add); echo "<thead $add>"; }
function html_table_thead_end($amount = 1) { while($amount-->0) echo "</thead>"; }

//table header
function html_table_head($content = "", $add = "") { toAttr($add); echo "<th $add>$content</th>"; }
function html_table_head_break($add = "") { toAttr($add); echo "</th><th $add>"; }
function html_table_head_start($add = "") { toAttr($add); echo "<th $add>"; }
function html_table_head_end($amount = 1) { while($amount-->0) echo "</th>"; }

//table body
function html_table_tbody_break($add = "") { toAttr($add); echo "</tbody><tbody $add>"; }
function html_table_tbody_start($add = "") { toAttr($add); echo "<tbody $add>"; }
function html_table_tbody_end($amount = 1) { while($amount-->0) echo "</tbody>"; }

function html_table_row_col_break($td_add = "", $tr_add = "") { echo "</td></tr><tr $tr_add><td $td_add>"; }
function html_table_row_col_start($td_add = "", $tr_add = "") { echo "<tr $tr_add><td $td_add>"; }
function html_table_row_col_end($amount = 1) { while($amount-->0) echo "</td></tr>"; }

function html_table_row_col_option($content = "", $td_class = "", $td_add = "", $tr_add = "") {
	echo "<tr $tr_add><td colspan=\"2\" class=\"align-left $td_class\" $td_add>$content";
}

//table horizontal divider
function html_table_hr($add = "") { toAttr($add); echo "<hr $add>"; }

//table row
function html_table_row($content = "", $add = "") { toAttr($add); echo "<tr $add>$content</tr>"; }
function html_table_row_break($tr_add = "") { echo "</tr><tr $tr_add>"; }
function html_table_row_start($add = "") { toAttr($add); echo "<tr $add>"; }
function html_table_row_end($amount = 1) { while($amount-->0) echo "</tr>"; }

//table column
function html_table_col($content = "", $add = "") { toAttr($add); echo "<td $add>$content</td>"; }
function html_table_col_break($td_add = "") { echo "</td><td $td_add>"; }
function html_table_col_start($add = "") { toAttr($add); echo "<td $add>"; }
function html_table_col_end($amount = 1) { while($amount-->0) echo "</td>"; }

//--- List ---

function html_ul_li_start($li_add = "", $ul_add = "") { echo "<ul $ul_add><li $li_add>"; }
function html_ul_li_end($amount = 1) { while($amount-->0) echo "</li></ul>"; }

function html_ol_li_start($li_add = "", $ol_add = "") { echo "<ol $ol_add><li $li_add>"; }
function html_ol_li_end($amount = 1) { while($amount-->0) echo "</li></ol>"; }

//unordered list
function html_ul($content = "", $add = "") { toAttr($add); echo "<ul $add>$content</ul>"; }
function html_ul_break($add = "") { toAttr($add); echo "</ul><ul $add>"; }
function html_ul_start($add = "") { toAttr($add); echo "<ul $add>"; }
function html_ul_end($amount = 1) { while($amount-->0) echo "</ul>"; }

//ordered list
function html_ol($content = "", $add = "") { toAttr($add); echo "<ol $add>$content</ol>"; }
function html_ol_break($add = "") { toAttr($add); echo "</ol><ol $add>"; }
function html_ol_start($add = "") { toAttr($add); echo "<ol $add>"; }
function html_ol_end($amount = 1) { while($amount-->0) echo "</ol>"; }

//list-item
function html_li($content = "", $add = "") { toAttr($add); echo "<li $add>$content</li>"; }
function html_li_break($add = "") { toAttr($add); echo "</li><li $add>"; }
function html_li_start($add = "") { toAttr($add); echo "<li $add>"; }
function html_li_end($amount = 1) { while($amount-->0) echo "</li>"; }

//--- Text ---

//headings
function html_h1($text = "", $add = "") { toAttr($add); echo "<h1 $add>$text</h1>"; }
function html_h2($text = "", $add = "") { toAttr($add); echo "<h2 $add>$text</h2>"; }
function html_h3($text = "", $add = "") { toAttr($add); echo "<h3 $add>$text</h3>"; }
function html_h4($text = "", $add = "") { toAttr($add); echo "<h4 $add>$text</h4>"; }
function html_h5($text = "", $add = "") { toAttr($add); echo "<h5 $add>$text</h5>"; }
function html_h6($text = "", $add = "") { toAttr($add); echo "<h6 $add>$text</h6>"; }

//paragraph
function html_paragraph($text = "", $add = "") { toAttr($add); echo "<p $add>$text</p>"; }
function html_paragraph_start($add = "") { toAttr($add); echo "<p $add>"; }
function html_paragraph_end($amount = 1) { while($amount-->0) echo "</p>"; }

//bold
function html_strong($content = "", $add = "") { toAttr($add); echo "<strong $add>$content</strong>"; }
function html_strong_start($add = "") { toAttr($add); echo "<strong $add>"; }
function html_strong_end($amount = 1) { while($amount-->0) echo "</strong>"; }

//italic ("emphasis")
function html_em($content = "", $add = "") { toAttr($add); echo "<em $add>$content</em>"; }
function html_em_start($add = "") { toAttr($add); echo "<em $add>"; }
function html_em_end($amount = 1) { while($amount-->0) echo "</em>"; }

//--- Misc ---

function html_svg_start($add = "") { toAttr($add); echo "<svg xmlns=\"http://www.w3.org/2000/svg\" $add>"; }
function html_svg_end($amount = 1) { while($amount-->0) echo "</svg>"; }