<?php

//------ Arrays ------

/**
 * @desc Combines two values of the given array into one, where the first
 * value serves as the key and the other one as the value (if no key names are
 * given, the first two entries of that array will get combined
 * @param array      $array          - the (sub-)array containing the values to
 *                                   combine
 * @param int|string $first_keyname  - The key of the first value to combine
 * @param int|string $second_keyname - The key of the second value to combine
 * @return array - combined array
 */
function array_combineValuePair($array, $first_keyname = '',
                                $second_keyname = '') {
	$result = [];
	
	foreach($array as $key => $value) {
		if(!empty($first_keyname) && !empty($second_keyname)) {
			$result[$value[$first_keyname]] = $value[$second_keyname];
		} else {
			$result[$value[0]] = $value[1];
		}
	}
	
	return $result;
}

/**
 * @desc Retrieves all values of the given key.
 * @param $array
 * @param $key
 * @return array
 */
function array_get_values_by_key($array, $key) {
	$result = [];
	foreach($array as $k => $v) {
		if($k === $key) {
			$result[] = $v;
		}
	}
	
	return $result;
}

/**
 * @desc Lists all sub-entries of one array in another
 * from: https://gist.github.com/SeanCannon/6585889
 * @param null $array - The array to flatten.
 * @return array
 */
function array_flatten($array = null) {
	$result = [];
	
	if(!is_array($array)) {
		$array = func_get_args();
	}
	
	foreach($array as $key => $value) {
		if(is_array($value)) {
			$result = array_merge_recursive($result, array_flatten($value));
		} else {
			$result = array_merge_recursive($result, [$key => $value]);
		}
	}
	
	return $result;
}

/**
 * Flattens multi-dimensional arrays into one (also tracks depth of recursion)
 * from: https://stackoverflow.com/questions/526556/how-to-flatten-a-multi-dimensional-array-to-simple-one-in-php
 *
 * @param array $array - The array to flatten.
 * @return bool|array
 */
function array_flatten_recursive($array) {
	if(!$array) {
		return false;
	}
	$flat = [];
	$RII = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
	foreach($RII as $value) {
		$flat[] = $value;
	}
	
	return $flat;
}

/**
 * @desc Removes any empty values from an array
 * @param $array - The array to clean
 * @return array
 */
function array_remove_empty($array) {
	return array_values(
		array_filter(
			$array,
			create_function('$value', 'return $value !== "";')
		)
	);
}

/**
 * @desc Returns a list of all direct sub-entries of the given array at the
 * specified level
 * @param array $array - The array to get the sub-entries off of
 * @param int   $level - The depth to go into before retrieving the sub-entries
 *                     (1 = the immediate sub-entries of the array)
 * @return array|bool
 */
function array_list_entries($array, $level = 1) {
	if(!is_array($array)) return false;
	
	$result = [];
	
	for($i = 0; $i < $level; $i++) {
		if(is_array($array[$i])) {
			$array = $array[$i];
		}
	}
	
	if(!is_array($array)) return false;
	
	foreach($array as $key) {
		$result[] = $key;
	}
	
	return $result;
}

function arrayExists($variable) {
	return isset($variable) && is_array($variable);
}

function multi_array_key_exists($key, $array) {
	if(array_key_exists($key, $array)) {
		return true;
	} else {
		foreach($array as $nested) {
			if(is_array($nested) && multi_array_key_exists($key, $nested)) {
				return true;
			}
		}
	}
	
	return false;
}