<?php

function substr_min_max($str, $start, $end) {
  return substr($str, $start, $end - $start);
}

function substr_between($str, $from = "", $to = "", $end_offset = 0) {
  if (empty($str)) return "";

  $from     = !empty($from) ? $from : substr($str, 0, 1);
  $tmp      = empty($to) ? strrpos($str, $from) : strpos($str, $from);
  $startpos = !empty($from) ? $tmp + strlen($from) : strpos($str, $from);
  $to       = !empty($to) ? $to : substr($str, strlen($str) - 1, strlen($str));

  $sub    = substr($str, $startpos, strlen($str));
  $endpos = strpos($sub, $to) + $end_offset;

  return substr($sub, 0, $endpos);
}

//returns an array of all numbers in a string
function extract_numbers($string) {
  preg_match_all('/([\d]+)/', $string, $match);
  return $match[0];
}

//returns the first number in a string
function extract_number($string) {
  preg_match_all('/([\d]+)/', $string, $match);
  return $match[0][0];
}

//abbreviates the given text by replacing the middle part, only
//if the length of the text exceeds the give length
function shorten($text, $length = 10, $endOffset = 3) {
	$offset = $length > 3 ? 3 : 0;
	$actualLength = strlen($text);
	$result = $text;

	if($actualLength > $length - $offset) {
		$firstPart = substr($text, 0, $length - ($offset + 2));
		$lastPart = substr($text, $actualLength - $endOffset);
		$result = "$firstPart...$lastPart";
	}

	return $result;
}

//removes all whitespaces from a string
function trim_all($str) {
	return preg_replace('/\s+/', '', $str);
}

function obfuscateEmail($email) {
  $em   = explode("@", $email);
  $name = implode(array_slice($em, 0, count($em) - 1), '@');
  $len  = floor(strlen($name) / 2);

  $domain = substr_between($email, "@", ".");
  $dotPos = strpos($email, ".");
  $tld    = substr($email, $dotPos);

  $obf_domain = $domain;

  if (strlen($domain) > 3) {
    $obf_domain = substr($domain, 0, 1) . "***";
    $obf_domain .= substr($domain, strlen($domain) - 1, strlen($domain));
  }

  return substr($name, 0, $len) . str_repeat('*', $len) . "@" . $obf_domain . $tld;
}

//prepends a zero to a number if it's smaller than 10
function padNum($num) {
  if (intval($num) < 10) $num = "0$num";

  return $num;
}