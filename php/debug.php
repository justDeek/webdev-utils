<?php

//--- Debug ---

//throws separated messages to the ErrorLog
function logm() {
	$message = "";
	$args = func_get_args();
	
	if(!notEmpty($args)) $args = "Test";
	
	for($i = 0; $i < count($args); $i++) {
		$arg = $args[$i];
		
		if(is_array($arg)) {
			logm_arr_rec($arg);
			continue;
		}
		
		$message = $i === 0 ? $arg : "$message | $arg";
	}
	
	error_log($message);
}

function logm_detail() {
	$message = "";
	$args = func_get_args();
	
	for($i = 0; $i < count($args); $i++) {
		$arg = $args[$i];
		
		if(is_array($arg)) {
			logm_arr_rec($arg);
			continue;
		}
		
		$message = $i === 0 ? $arg : "$message | $arg";
	}
	
	trigger_error($message);
}

//throws notices for each element in an array and sub-arrays
//not supposed to be used directly; use logm() instead, which is shorter,
// automatically detects arrays and allows to add indefinite parameters
function logm_arr_rec($array, $arrkey = "") {
	if(!is_array($array)) {
		errorm("Given parameter isn't an array!");
	}
	
	if(!isset($result)) {
		$result = "array(" . count($array) . ")";
		if(notEmpty($arrkey)) $result .= ": $arrkey";
	}
	
	foreach($array as $key => $val) {
		//cascade results
		if(notEmpty($result)) logm($result);
		$result = "";
		
		if(is_array($val)) {
			logm_arr_rec($val, $key);
			logm("----");
		} else {
			$result .= "|- " . $key . ": " . $val . " | ";
		}
	}
	
	logm($result);
}

/** Used to debug internal error's on both the log file and browser console */
function debugm($message) {
	logm($message);
	echo "<script>log('$message');</script>";
}

function errorm($message = "Test") {
	throw new Exception($message);
}

/**
 * Simple helper to debug to the console
 *
 * @param $data    object, array, string $data
 * @param $context string  Optional a description.
 * @return string
 */
function log_console($data, $context = 'Test') {
	ob_start();
	$output = 'console.info( \'' . $context . ':\' );';
	$output .= 'console.log(' . json_encode($data) . ');';
	$output = sprintf('<script>%s</script>', $output);
	
	echo $output;
	ob_end_clean();
	
	return;
}


//--- Benchmark ---

function microtime_float() {
	list($usec, $sec) = explode(' ', microtime());
	
	return (float)$usec;
}

function benchmark_time() {
	global $benchmark_start;
	
	return number_format(microtime_float() - $benchmark_start, 4);
	
	//format: min:sec:mill:micro
	//  return date("i:s:v:u",microtime_float() - $benchmark_start);
}

function benchmark_memory() {
	global $memory_start;
	
	return memory_get_usage() - $memory_start / (1024 * 1024);
}

function benchmark($filepath, $desc = "") {
	global $doBenchmark;
	
	if(!$doBenchmark) return;
	
	$result = !empty($desc) ? " - " . $desc : "";
	logm(current_file($filepath) . $result . ": " . benchmark_time() . " | memory: " . benchmark_memory());
}