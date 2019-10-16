<?php

/*--- Globals ---*/

function define_global($name, $value) {
	define($name, $value);
	toJS($value);
	echo "<script>const $name = $value;</script>";
}

//base
define_global("VERSION", 1); //for versioning/cache-invalidation
define_global("URL", "https://xxx.xxx.xxx"); //reference to the main URL
define_global("BASE_URL", "/"); //default: "/"
define_global("ROOT_PATH", $_SERVER["DOCUMENT_ROOT"] . "/");

//database
define_global("DB_HOST", "localhost");
define_global("DB_USER", "root");
define_global("DB_PW", "root");
define_global("DB_NAME", "website");


/*--- Inclusion ---*/

function require_multi($prefix, $files, $suffix = ".php") {
	if(!is_array($files)) error_log('Given $files value is not an array!');
	foreach($files as $file) require_once($prefix . $file . $suffix);
}

require_multi("", ["arrays", "browser", "debug", "gui", "html", "strings"]);
require_multi("PHPMailer/", ["PHPMailer", "SMTP", "Exception"]);
use PHPMailer\PHPMailer\PHPMailer;

//connect to the database
require_multi("db/", ["mysqli"]);
db_connect();


/*--- Pathfinding ---*/

//modifies the given (or current) path to start from the projects root
//and for calling any resource under any working directory
function base_path($path = "") {
	//skip when providing a non-local or absolute path
	if(startsWithAny($path, "http", "www", "mailto", "/")) return $path;
	
	return empty($path) ? BASE_URL : BASE_URL . $path;
}

function add_version($path = "") {
	//don't add versioning to full URL's
	if(startsWith($path, "http") || startsWith($path, "www")) return $path;
	
	return $path . "?v=" . VERSION;
}


/*--- General ---*/

function notEmpty($var) {
	return ($var === "0" || $var);
}

//check if a variable is set and is of given value
function issetval($variable, $value) {
	return isset($variable) && $variable == $value;
}

//check if a variable is set and is not of given value
function issetnotval($variable, $value) {
	return isset($variable) && $variable != $value;
}

//check if a variable is set and equals the given values
function issetvals($variable, $values) {
	if(!is_array($values)) return issetval($variable, $values);
	$isTrue = true;
	if(isset($values)) {
		foreach($values as $value) {
			if($variable != $value) $isTrue = false;
		}
		
		return $isTrue;
	} else return false;
}

//validate the variable's value
function val($variable) {
	return $variable ?? null;
}

function exists($arg) {
	return isset($arg) || empty($arg);
}

function at($stack, $needle) {
	if(is_array($stack)) return array_search($needle, array_values($stack));
	
	return strpos($stack, $needle);
}

//checks whether the given stack contains the needle
function contains($stack, $needle) {
	if(is_array($stack)) return in_array($needle, $stack);
	if(is_file($stack)) $stack = file_get_contents($stack);
	
	return strpos($stack, $needle) !== false;
}

function contains_any($stack, ...$needles) {
	$doesContain = false;
	
	foreach($needles as $needle)
		$doesContain = $doesContain || contains($stack, $needle);
	
	return $doesContain;
}

function contains_all($stack, ...$needles) {
	$doesContain = false;
	
	foreach($needles as $needle)
		$doesContain = $doesContain && contains($stack, $needle);
	
	return $doesContain;
}

function startsWith($haystack, $needle) {
	return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
}

function startsWithAny($haystack, ...$needles) {
	$doesContain = false;
	
	foreach($needles as $needle)
		$doesContain = $doesContain || substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
	
	return $doesContain;
}

function endsWith($haystack, $needle) {
	return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

function alert($message = "Test") {
	toJS($message);
	echo "<script type=\"text/javascript\">
		      alert($message);
		    </script>";
}

//caps multiple variables to a min value
function cap_min($min, &...$vals) {
	foreach($vals as &$val) {
		$val = max($val, $min);
	}
}

//caps multiple variables to a max value
function cap_max($max, &...$vals) {
	foreach($vals as &$val) {
		$val = min($val, $max);
	}
}

/**
 * Formats the given variables to be used directly as parameters for JavaScript functions
 * @param mixed ...$args - The variables to convert
 */
function toJS(&...$args) {
	foreach($args as &$arg) {
		//make sure that a given number isn't misrepresentated as a boolean value
		if($arg === 0) $arg = '0';
		if($arg === 1) $arg = '1';
		
		//only add captions if there're none to begin with
		if(!startsWithAny($arg, "\"", '\'')) $arg = json_encode($arg);
	}
}

/**
 * Converts a given list to html attributes
 * @param $add - The list to convert
 */
function toAttr(&$add) {
	$result = "";
	
	//list of supported shortcodes
	$supported = [
		"c" => "class",
		"i" => "id",
		"n" => "name",
		"t" => "type",
		"s" => "style"
	];
	
	//collect parts delimited by pipes
	$parts = contains($add, "|") ? explode("|", $add) : [$add];
	
	foreach($parts as $part) {
		$part = trim($part);
		
		//if a shortcode is present, extract it and format the attribute
		if(contains($part, ":")) {
			list($attr, $val) = explode(":", $part);
			
			//get the full name of the shortcode
			if(contains(array_keys($supported), $attr)) $attr = $supported[$attr];
			
			//convert any existing double-quotes to single ones to nest them
			$val = str_replace("\"", "'", $val);
			$val = trim($val);
			$part = "$attr=\"$val\"";
		}
		
		$result .= "$part ";
	}
	
	$add = trim($result);
}


//------ Utilities & Helpers ------

/**
 * @desc Display any variable with neat formatting (especially for Single- and
 * Multi-Dimensional Arrays)
 * @param mixed  $args  - Any variable to prettify
 * @param string $tag   - Optionaly specify a tag
 * @param bool   $debug - If execution should stop afterwards
 */
function prettyPrint($args = "<br/>", $debug = false, $tag = 'pre') {
	echo "<$tag>" . print_r($args, true) . "</$tag>";
	if($debug) {
		die();
	}
}

/**
 * Pass over the __FILE__ magic constant to return the file name without extension of the current file.
 */
function current_file($filepath) {
	return substr_min_max($filepath, strrpos($filepath, "/") + 1, strrpos($filepath, "."));
}

/**
 * Creates a random and time based token
 *
 * @param $length - How many characters long the random part of the token should be
 * @throws \Exception
 * @return string
 */
function generateToken($length) {
	$token = bin2hex(random_bytes($length));
	
	return $token;
}


/*--- Misc ---*/

function sendMail($subject, $message, $sender = null, $receiver = null, $search_for = [], $replace_with = []) {
	//enter a base address if the $sender or $receiver hasn't been specified
	$default_sender = "xxx@xxx.xx";
	
	//Input
	$sender = $sender ?? $default_sender;
	$receiver = $receiver ?? $default_sender;
	
	//use an html file as email-template and replace certain code snippets with the dynamic content
	$file_path = "../templates/email/email-base.html";
	
	$html = file_get_contents($file_path);
	$html = str_replace("{{Subject}}", $subject, $html);
	$html = str_replace("{{Message}}", $message, $html);
	$html = str_replace("{{Sender}}", $sender, $html);
	
	//also make additional modifications if provided
	if(count($search_for) !== 0) {
		for($i = 0; $i < count($search_for); $i++) {
			$search = contains($search_for[$i], "{{") ? $search_for[$i] : "{{" . $search_for[$i] . "}}";
			$html = str_replace($search, $replace_with[$i], $html);
		}
	}
	
	$mail = new PHPMailer(true);
	
	$succeeded = false;
	try {
//    date_default_timezone_set('Etc/UTC');
		
		// - example using MailJet as the provider -
		
		//server
		$mail->isSMTP();
//    $mail->SMTPDebug = 4;
		$mail->Host = 'in-v3.mailjet.com';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = "xxx";
		$mail->Password = "xxx";
		
		//Recipients
		$mail->setFrom($sender);
		$mail->addAddress($receiver);
		$mail->addReplyTo($sender);
		//Content
		$mail->CharSet = 'UTF-8';
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $html;
		
		//remove the subject from the alt.-body so that it won't get displayed twice
		$altMessage = str_replace($subject, "", $message);
		$mail->AltBody = $subject . "\n\n" . strip_tags($altMessage);
		
		$mail->send();
		$succeeded = true;
		
		logm("[Mail has been sent from $sender to $receiver] - Subject: $subject");
	} catch(Exception $e) {
		logm("[!Failed to send mail from $sender to $receiver] - Mailer Error: $mail->ErrorInfo");
	}
	
	// - optionally store the provided data in a contact table of the db to keep track of the sent mails (in case something went wrong)
	//$values = [$sender, $receiver, $subject, $message, $succeeded];
	//db_insert("contact", "sender, receiver, subject, message, success", "ssssi", $values);
	
	return $succeeded;
}
