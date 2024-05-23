<?php
require_once("php/main.php");

html_start("class=\"page\" lang=\"en\"");

//*-- Head --*
html_head_start();

//General
html_title("Website Title");
create_meta("name=\"description\" content=\"Website Description\"");
create_meta("http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"");
create_meta("http-equiv=\"x-ua-compatible\" content=\"IE=Edge,chrome=1\"");
create_meta("name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\"");
//create_link($url, "canonical");

//FavIcons
create_icon("img/favicon/apple-icon.png", "180x180", "apple-touch-icon");
create_icon("img/favicon/favicon.ico", "180x180", "shortcut icon");
create_meta("name=\"msapplication-TileColor\" content=\"#232222\"");
create_meta("name=\"theme-color\" content=\"#131315\"");

//Styles
create_css("css/mainstyle.min.css");

//Fonts
create_css("fonts/Gilroy/Gilroy.css?family=Gilroy");

html_head_end();

//*-- Body --*
html_body_start("style=\"background-color: #1e1d1d;\"");

html_div_start("class=\"content\"");

//-- Header --
html_tag_start("header", "class=\"navigation compensate-for-scrollbar\"");

echo "<!--[if lte IE 9]>
    <p class=\"browserupgrade\">You are using an <strong>outdated</strong> browser. Please <a href=\"https://browsehappy.com/\">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->";

//Nav-bar
html_tag_start("nav", "class=\"navbar header\" id=\"main-nav\"");
html_ul_start("class=\"navbar-content navigation-container fade-in\" style=\"display: none\"");

//Logo
html_li_start("class=\"logo fade-in\"");
html_div_start("class=\"nav-link\" onclick=\"page('/')\"");
html_image("img/logo.png", "Logo", "class=\"logo-icon\"");
html_h1("Title", "class=\"logo-title\"");
html_div_end();

//Projects
html_li_break("class=\"nav-elem nav-elem-link nav-projects\"");
html_div("Projects", "class=\"nav-elem-text nav-link\" onclick=\"page('/projects')\"");
html_ul_start();

//Blog
html_li_break("class=\"nav-elem nav-elem-link nav-blog\"");
html_div("Blog", "class=\"nav-link nav-elem-text\" onclick=\"page('/blog')\"");

//Contact
html_li_break("class=\"nav-elem nav-elem-link nav-contact\"");
html_div("Contact", "class=\"nav-link nav-elem-text\" onclick=\"page('/contact')\"");

//About
html_li_break("class=\"nav-elem nav-elem-link nav-about\"");
html_div("About", "class=\"nav-link nav-elem-text\" onclick=\"page('/about')\"");
html_ul_li_end();

html_nav_end(); //end of nav-bar
html_header_end(); //end of header


//-- Main (page contents get dynamically inserted here) --
html_main("", "class=\"main-container\" id=\"main\"");


//-- Footer --
html_footer_start("class=\"navbar footer\"");
html_div_start("class=\"footer-grid\"");
html_div_start("class=\"footer-grid-elem\" id=\"footer-grid-elem2\"");
html_div_start("class=\"socials\" style=\"display: none\"");

//Cookies
html_div_start("class=\"cookie-btn nav-but-wrap fade-in\"");
html_div("🍪", "class=\"menu-icon hover-target social-icon cookie\" style=\"margin: 7.5px 0;\"");
html_div_start("class=\"nav\"");
html_div_start("class=\"nav__content\"");
html_ul_start("class=\"nav__list\"");
html_li_start("class=\"nav__list-item active-nav\"");
html_paragraph("Cookies for everyone!", "class=\"cookie-popup-title\"");
html_li_break("class=\"nav__list-item\"");
html_paragraph("This website uses cookies to ensure you get the best experience on our website.", "class=\"cookie-popup-subtext\"");
html_li_break("class=\"nav__list-item\"");
html_anchor("More information", "/about#4", "", "class=\"cookie-popup-more\"");
html_li_break("class=\"nav__list-item\"");
gui_create_button("Accept", "", "cookie-popup-button", "id=\"ihavecookiesBtn\" type=\"button\" onload=\"checkCookies()\"", "toggleCookies()");
html_ul_li_end();
html_div_end(3);

//- Socials -

//Email
html_anchor_start("mailto:...", "", "class=\"social-icon fade-in\" id=\"social-icon1\"");
html_svg_start("viewBox=\"128 128 1536 1536\"");
echo "<path d=\"M1376 128q119 0 203.5 84.5t84.5 203.5v960q0 119-84.5 203.5t-203.5 84.5h-960q-119 0-203.5-84.5t-84.5-203.5v-960q0-119 84.5-203.5t203.5-84.5h960zm32 1056v-436q-31 35-64 55-34 22-132.5 85t-151.5 99q-98 69-164 69t-164-69q-46-32-141.5-92.5t-142.5-92.5q-12-8-33-27t-31-27v436q0 40 28 68t68 28h832q40 0 68-28t28-68zm0-573q0-41-27.5-70t-68.5-29h-832q-40 0-68 28t-28 68q0 37 30.5 76.5t67.5 64.5q47 32 137.5 89t129.5 83q3 2 17 11.5t21 14 21 13 23.5 13 21.5 9.5 22.5 7.5 20.5 2.5 20.5-2.5 22.5-7.5 21.5-9.5 23.5-13 21-13 21-14 17-11.5l267-174q35-23 66.5-62.5t31.5-73.5z\" />";
html_svg_end();
html_anchor_end();

//Twitter
html_anchor_start("https://twitter.com/...", "", "class=\"social-icon fade-in\" id=\"social-icon2\"");
html_svg_start("viewBox=\"128 128 1536 1536\"");
echo "<path d=\"M1408 610q-56 25-121 34 68-40 93-117-65 38-134 51-61-66-153-66-87 0-148.5 61.5t-61.5 148.5q0 29 5 48-129-7-242-65t-192-155q-29 50-29 106 0 114 91 175-47-1-100-26v2q0 75 50 133.5t123 72.5q-29 8-51 8-13 0-39-4 21 63 74.5 104t121.5 42q-116 90-261 90-26 0-50-3 148 94 322 94 112 0 210-35.5t168-95 120.5-137 75-162 24.5-168.5q0-18-1-27 63-45 105-109zm256-194v960q0 119-84.5 203.5t-203.5 84.5h-960q-119 0-203.5-84.5t-84.5-203.5v-960q0-119 84.5-203.5t203.5-84.5h960q119 0 203.5 84.5t84.5 203.5z\" />";
html_svg_end();
html_anchor_end();

//GitHub
html_anchor_start("https://github.com/...", "", "class=\"social-icon fade-in\" id=\"social-icon4\"");
html_svg_start("viewBox=\"128 128 1536 1536\"");
echo "<path d=\"M522 1352q-8 9-20-3-13-11-4-19 8-9 20 3 12 11 4 19zm-42-61q9 12 0 19-8 6-17-7t0-18q9-7 17 6zm-61-60q-5 7-13 2-10-5-7-12 3-5 13-2 10 5 7 12zm31 34q-6 7-16-3-9-11-2-16 6-6 16 3 9 11 2 16zm129 112q-4 12-19 6-17-4-13-15t19-7q16 5 13 16zm63 5q0 11-16 11-17 2-17-11 0-11 16-11 17-2 17 11zm58-10q2 10-14 14t-18-8 14-15q16-2 18 9zm964-956v960q0 119-84.5 203.5t-203.5 84.5h-224q-16 0-24.5-1t-19.5-5-16-14.5-5-27.5v-239q0-97-52-142 57-6 102.5-18t94-39 81-66.5 53-105 20.5-150.5q0-121-79-206 37-91-8-204-28-9-81 11t-92 44l-38 24q-93-26-192-26t-192 26q-16-11-42.5-27t-83.5-38.5-86-13.5q-44 113-7 204-79 85-79 206 0 85 20.5 150t52.5 105 80.5 67 94 39 102.5 18q-40 36-49 103-21 10-45 15t-57 5-65.5-21.5-55.5-62.5q-19-32-48.5-52t-49.5-24l-20-3q-21 0-29 4.5t-5 11.5 9 14 13 12l7 5q22 10 43.5 38t31.5 51l10 23q13 38 44 61.5t67 30 69.5 7 55.5-3.5l23-4q0 38 0.5 103t0.5 68q0 22-11 33.5t-22 13-33 1.5h-224q-119 0-203.5-84.5t-84.5-203.5v-960q0-119 84.5-203.5t203.5-84.5h960q119 0 203.5 84.5t84.5 203.5z\" />";
html_svg_end();
html_anchor_end();

html_div_end(3);
html_footer_end(); //end of footer
html_div_end(); //end of content

//-- Scripts --

//Global site tag (gtag.js) - Google Analytics
create_script("https://www.googletagmanager.com/gtag/js?id=UA-...-1", "async");

//General
create_script("js/main.min.js");

html_body_end();
html_end();
