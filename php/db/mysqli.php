<?php
/**
 * @desc Connect to the database and store the db_link object
 * @param string $db_name
 */
function db_connect($db_name = "") {
	if(empty($db_name)) $db_name = DB_NAME;
	$conn = new mysqli("p:" . DB_HOST, DB_USER, DB_PW, $db_name);
	
	if($conn->connect_errno) {
		error_log("Connection to Database $db_name for user " . DB_USER . " failed", 0);
		return;
	}
	
	//store connection in the current session
	$_SESSION['conn'] = $conn;
}

/**
 * @desc Closes the database connection
 * @param object $conn - The connection to the persistent database object
 */
function db_disconnect($conn) {
	if(!$conn->close()) {
		error_log("Closing the database connection failed. The mysqli-error is: $conn->connect_errno - $conn->connect_error",
			0);
	}
}

/**
 * @desc Runs a given query on the database
 * @param string $query
 * @param string $type_string
 * @param array  $value_array
 */
function db_query($query, $type_string = "", $value_array = []) {
	$conn = $_SESSION['conn'];
	if(!$conn) error_log("No connection to the database established!");
	
	mysqli_query_prepared($conn, $query, $type_string, $value_array);
}

/**
 * @desc Returns any values found with the given query
 * @param array|string $fields
 * @param string       $from
 * @param string       $where
 * @param string       $add
 * @param string       $type_string
 * @param array        $value_array
 */
function db_select($fields, $from, $where = "", $add = "", $type_string = "", $value_array = []) {
	if(is_array($fields)) $fields = implode(',', $fields); //stringify when providing an array
	
	$query = "SELECT $fields FROM $from";
	if(!empty($where)) $query .= " WHERE $where";
	if(!empty($add)) $query .= " $add";
	
	db_query($query, $type_string, $value_array);
}

/**
 * @desc Run an INSERT query on the database
 * @param string       $table
 * @param array|string $fields
 * @param string       $type_string
 * @param array        $value_array
 */
function db_insert($table, $fields, $type_string = "", $value_array = []) {
	if(is_array($fields)) $fields = implode(',', $fields); //stringify when providing an array
	$field_count = substr_count($fields, ',');
	
	$query = "INSERT INTO $table ($fields) VALUES (";
	while($field_count-->0) $query .= "?,";
	$query .= "?)";
	
	db_query($query, $type_string, $value_array);
}

/**
 * @desc Run an UPDATE query on the database
 * @param string       $table
 * @param array|string $fields
 * @param string       $type_string
 * @param array        $value_array
 * @param string       $where
 * @param string       $add
 */
function db_update($table, $fields, $type_string = "", $value_array = [], $where = "", $add = "") {
	if(!is_array($fields)) $fields = explode(',', $fields); //arrayify when providing an string
	
	$query = "UPDATE $table Set ";
	foreach($fields as $field) $query .= "$field=?,";
	$query = substr($query, 0, -1); //remove last comma
	
	if(!empty($where)) $query .= " WHERE $where";
	if(!empty($add)) $query .= " $add";
	
	db_query($query, $type_string, $value_array);
}

/**
 * @desc Run a DELETE FROM query on the database
 * @param string $from
 * @param string $where
 */
function db_delete($from, $where = "") {
	$query = "DELETE FROM $from";
	if(!empty($where)) $query .= " WHERE $where";
	
	db_query($query, "", []);
}

// ----------------------------General Functions-----------------------------------------------

/**
 * @desc executes an SQL-SELECT-Query as prepared statement
 * @param object $conn             - The connection to the persistent database object
 * @param string $sql_query_string - SQL-SELECT-Query (* is invalid)
 * @param string $type_string      - Contains a string with all types of the values in $value_array
 * @param array  $value_array      - Is an array with the input values for the SQL-Query (can contain 0 elements)
 * @param string $result_type      - determines whether the return value is numeric, associative or both (not
 *                                 implemented yet)
 * @return array|null - contains x-rows (outer array -numeric) and y-columns (inner array - numeric/associative/both)
 *                                 of the SQL-SELECT
 */
function mysqli_select_prepared($conn, $sql_query_string, $type_string = "", $value_array = [],
                                $result_type = 'MYSQLI_ASSOC') {
	if(is_null($conn)) {
		error_log("Preparing an sql-query failed the link is null for this SQL-statement: $sql_query_string", 0);
		
		return null;
	}
	
	$stmt = $conn->prepare($sql_query_string);
	
	if(!$stmt) {
		error_log("Preparing the SQL-statement failed: $sql_query_string | The mysqli-error is: $conn->errno - $conn->error",
			0);
		
		return null;
	}
	
	$reference_array[] = &$type_string;
	
	for($i = 0; $i < count($value_array); $i++)
		$reference_array[] = &$value_array[$i];
	
	if(count($value_array) > 0 && !call_user_func_array([$stmt, 'bind_param'], $reference_array)) {
		error_log("Binding params to a SQL-statement failed. $sql_query_string | The mysqli-error is: $conn->errno - $conn->error",
			0);
		
		return null;
	}
	
	if(!$stmt->execute()) {
		error_log("Executing a SQL-statement failed. $sql_query_string | The mysqli-error is: $conn->errno - $conn->error",
			0);
		error_log("SQL-statement Error is: $stmt->errno - $stmt->error", 0);
		
		return null;
	}
	
	$row = mysqli_select_helpfnc_bind_result_array($stmt, $result_type);
	$result_array = null;
	$i = 0;
	
	while($stmt->fetch()) {
		$result_array[$i] = mysqli_select_helpfnc_get_copy($row);
		$i++;
	}
	
	$stmt->close();
	
	return $result_array;
}

/**
 * @desc Executes a SELECT-SQL-Query
 * @param object $conn             - The connection to the persistent database object
 * @param string $sql_query_string - SQL-SELECT-Query (* is invalid)
 * @param array  $key              - Is an numeric array that contains the key and the value
 * @param string $type_string      - Contains a string with all types of the values in $value_array
 * @param array  $value_array      - Is an array with the input values for the SQL-Query
 * @return array - Returns an associative array with the key as key
 */
function mysqli_select_key_val_prepared($conn, $sql_query_string, $key, $type_string = "", $value_array = []) {
	if(is_null($conn)) {
		error_log("Preparing an sql-query failed the link is null for this SQL-statement: $sql_query_string", 0);
		
		return null;
	}
	
	$result = mysqli_select_prepared($conn, $sql_query_string, $type_string, $value_array, "MYSQLI_ASSOC");
	$result_assoc = [];
	
	for($i = 0; $i < count($result); $i++) {
		if(isset($result_assoc[$result[$i][$key[0]]]) && is_array($result_assoc[$result[$i][$key[0]]])) {
			array_push($result_assoc[$result[$i][$key[0]]], $result[$i][$key[1]]);
		} else {
			if(isset($result_assoc[$result[$i][$key[0]]])) {
				$result_assoc[$result[$i][$key[0]]] = [$result_assoc[$result[$i][$key[0]]], $result[$i][$key[1]]];
			} else {
				$result_assoc[$result[$i][$key[0]]] = $result[$i][$key[1]];
			}
		}
	}
	
	return $result_assoc;
}

/**
 * @desc Creates and Executes a SQL-SELECT-Statement
 * @param object $conn                      - The connection to the persistent database object
 * @param array  $operartion_array          - (Is a 2d-Array) - The inner part must contain the following values
 *                                          operator => <, >, =, <=, >=, LIKE, NOT LIKE value_1 => Is the left operand
 *                                          of operation type_v1 => is the type of value_1, can be s,i,b,d value_2 =>
 *                                          Is the right operand of operation type_v2 => is the type of value_2, can be
 *                                          s,i,b,d followup => AND, OR, NOR
 * @param string $sql_query_string          - Is the first part of the Select-Statement | example: SELECT test FROM
 *                                          test_table WHERE
 * @param string $sql_query_string_end_part - Is the end part of a SELECT-Query | example: GROUP BY test ASC
 * @param string $result_type               - determines whether the return value is numeric, associative or both (not
 *                                          implemented yet)
 * @return array|null - contains x-rows (outer array -numeric) and y-columns (inner array - numeric/associative/both)
 *                                          of the SQL-SELECT
 */
function mysqli_select_prepared_builder($conn, $operartion_array, $sql_query_string,
                                        $sql_query_string_end_part = "", $result_type = 'MYSQLI_ASSOC') {
	$type_string = "";
	$value_array = [];
	$index = 0;
	$unskipped = 0;
	
	foreach($operartion_array as $operation_row) {
		$unskipped++;
		
		if(empty($operation_row["value_1"]) || empty($operation_row["value_2"])) {
			$unskipped -= 1;
			continue;
		}
		
		if(isset ($operation_row["value_1"])) {
			$operand_1 = $operation_row["value_1"];
		} else {
			error_log("Canceled the Select-Function: Operand 1 is not set.");
			
			return null;
		}
		
		if(isset ($operation_row["value_2"]) && isset ($operation_row["type_v2"])) {
			array_push($value_array, $operation_row["value_2"]);
			$type_string .= $operation_row["type_v2"];
			$operand_2 = "?";
		} else if(isset ($operation_row["value_2"]) && !isset ($operation_row["type_v2"])) {
			$operand_2 = $operation_row["value_2"];
		} else {
			error_log("Canceled the Select-Function: Operand 2 is not set.");
			
			return null;
		}
		
		if(!isset($operation_row["followup"]) && $index + 1 < count($operartion_array)) {
			error_log("Canceled the Select-Function: There is a missing followup in the operation-array.");
			
			return null;
		}
		
		if(isset($operation_row["function_v1"])) {
			$operand_1 = $operation_row["function_v1"] . "($operand_1)";
		}
		
		if(isset($operation_row["function_v2"])) {
			$operand_2 = $operation_row["function_v2"] . "($operand_2)";
		}
		
		$sql_query_string .= " $operand_1 " . $operation_row["operator"]
		                     . " $operand_2 " . $operation_row["followup"] . " ";
		$index++;
	}
	
	if($unskipped == $index) $sql_query_string .= " 1 = 1 ";
	
	$sql_query_string .= $sql_query_string_end_part;
	
	return mysqli_select_prepared($conn, $sql_query_string, $type_string, $value_array, $result_type);
}

/**
 * @desc Executes a SQL-Query (SELECT is no allowed)
 * @param object $conn             - The connection to the persistent database object
 * @param string $sql_query_string - Can execute every query except SELECT
 * @param string $type_string      - Contains a string with all types of the values in $value_array
 * @param array  $value_array      - Is an array with the input values for the SQL-Query
 * @return bool - Returns false if the function fails and returns true if it executes successful
 */
function mysqli_query_prepared($conn, $sql_query_string, $type_string = "", $value_array = []) {
	if(is_null($conn)) {
		error_log("Preparing an sql-query failed the link is null for this SQL-statement: $sql_query_string", 0);
		
		return null;
	}
	
	$stmt = $conn->prepare($sql_query_string);
	
	if($stmt) {
		$reference_array[] = &$type_string;
		
		for($i = 0; $i < count($value_array); $i++) {
			$reference_array[] = &$value_array[$i];
		}
		
		if(count($value_array) > 0 && !call_user_func_array([$stmt, 'bind_param'], $reference_array)) {
			error_log("Binding params to a SQL-statement failed: $sql_query_string | The mysqli-error is: $conn->errno - $conn->error",
				0);
			
			return false;
		}
		
		if(!$stmt->execute()) {
			error_log("Executing a SQL-statement failed: $sql_query_string | The mysqli-error is: $conn->errno - $conn->error",
				0);
			
			return false;
		}
		
		$stmt->close();
		
		return true;
	}
	
	error_log("Preparing the SQL-statement failed: $sql_query_string | The mysqli-error is: $conn->errno - $conn->error",
		0);
	
	return false;
}

/**
 * @desc Executes an INPUT-SQL-Query to insert multiple rows dynamically to the row count
 * @param object $conn             - The connection to the persistent database object
 * @param string $sql_query_string - INSERT-String ot insert one row
 * @param string $type_string      - Contains a string with all types of the values in $value_array (can also be only
 *                                 for one row)
 * @param array  $value_array      - Is an array with the input values for the SQL-Query (can be 2d or 1d)
 * @return bool - Returns false if the function fails and returns true if it executes successful
 */
function mysqli_insert_multiple_rows_prepared($conn, $sql_query_string, $type_string, $value_array) {
	if(is_array(reset($value_array))) {
		$value_array = mysqli_insert_helpfnc_merge_array($value_array);
	}
	
	$count_value_array = count($value_array);
	$len_type_string = strlen($type_string);
	
	if($len_type_string != $count_value_array && $count_value_array % $len_type_string == 0) {
		$type_string = mysqli_insert_helpfnc_create_type_string($type_string, $count_value_array / $len_type_string);
	}
	
	$sql_query_string_multiple_rows = mysqli_insert_helpfnc_create_multiple_rows($sql_query_string,
		$count_value_array / $len_type_string);
	
	return mysqli_query_prepared($conn, $sql_query_string_multiple_rows, $type_string, $value_array);
}

// ----------------------------Utility Functions-----------------------------------------------

/**
 * @desc Utility function to automatically bind columns from selects in prepared statements to an 2d-array
 * @param object $stmt        - contains the execute statement object
 * @param        $result_type - determines whether the return value is numeric, associative or both (not implemented
 *                            yet)
 * @return array - contains  x-rows (outer array -numeric) and y-columns (inner array - numeric/associative/both) of
 *                            the SQL-SELECT
 */
function mysqli_select_helpfnc_bind_result_array($stmt, $result_type) {
	$meta = $stmt->result_metadata();
	$result = [];
	$index = 0;
	
	while($field = $meta->fetch_field()) {
		if($result_type === "MYSQLI_ASSOC") {
			$result[$field->name] = null;
			$params[] = &$result[$field->name];
		} else {
			if($result_type === "MYSQLI_NUM") {
				$result[$index] = null;
				$params[] = &$result[$index];
				$index++;
			}
		}
	}
	
	call_user_func_array([$stmt, 'bind_result'], $params);
	
	return $result;
}

/**
 * @desc Utility function to create a copy of an array of references
 * @param array $row - array of references
 * @return array - copy of array of references
 */
function mysqli_select_helpfnc_get_copy($row) {
	return array_map(create_function('$a', 'return $a;'), $row);
}

/**
 * @desc Utility function to merge a 2 dimensional array so that it is one dimensional array
 * @param array $value_array - is a 2 dimensional array
 * @return array A one dimensional array
 */
function mysqli_insert_helpfnc_merge_array($value_array) {
	return call_user_func_array('array_merge', $value_array);
}

/**
 * @desc Utility function to create the full type string for the prepared insert
 * @param string $type_string - is the original type string for one line to insert
 * @param int    $row_count   - is the number of rows you want to put into the database
 * @return string $type_string_return - is the changed type string
 */
function mysqli_insert_helpfnc_create_type_string($type_string, $row_count) {
	$type_string_return = "";
	
	for($i = 0; $i < $row_count; $i++) {
		$type_string_return .= $type_string;
	}
	
	return $type_string_return;
}

/**
 * @desc Utility function to create add rows to an insert statement so that the count is equal to the row count
 * @param string $sql_query_string - INSERT-String to insert one line into a table
 * @param int    $row_count        - is the number of rows you want to put into the database
 * @return string $sql_query_string - is the changed input string
 */
function mysqli_insert_helpfnc_create_multiple_rows($sql_query_string, $row_count) {
	$sql_subs_str = substr($sql_query_string, strrpos($sql_query_string, "("), strripos($sql_query_string, ")"));
	
	for($i = 1; $i < $row_count; $i++) {
		$sql_query_string .= ", " . $sql_subs_str;
	}
	
	return $sql_query_string;
}