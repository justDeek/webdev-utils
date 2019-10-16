<?php

/** Checks if there's a session currently active. */
function has_session() {
	return session_id() !== "" || isset($_SESSION) || session_status() == PHP_SESSION_ACTIVE;
}

//detect if in Internet Explorer/Edge
function browser_detect_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT'])) return false;
	
	return contains_any($_SERVER['HTTP_USER_AGENT'], 'MSIE', 'Windows', 'Trident', 'Edge');
}

//detect if in Firefox
function browser_detect_ff() {
	if(!isset($_SERVER['HTTP_USER_AGENT'])) return false;
	
	return strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false;
}

function getRealIpAddress() {
	//check ip from shared internet
	if(!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
	
	return !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
}