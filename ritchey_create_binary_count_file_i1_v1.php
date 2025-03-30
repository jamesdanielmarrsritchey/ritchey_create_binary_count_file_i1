<?php
# Meta
// Name: Ritchey Create Binary Count File i1 v1
// Description: Create a binary count file. This file shows which binary representations where present in the file, and how many times. Returns a "TRUE" on success. Returns "FALSE" on failure.
// Notes: Optional arguments can be "NULL" to skip them in which case they will use default values.
// Arguments: Source File (required) is the file to read. Destination File (required) is the place to write the new file. Overwrite (optional) specifies whether to overwrite an existing destination file. Display Errors (optional) specifies if errors should be displayed after the function runs.
// Arguments (For Machines): source_file: file, required. destination_file: file, required. overwrite: bool, optional. display_errors: bool, optional.
# Content
if (function_exists('ritchey_create_binary_count_file_i1_v1') === FALSE){
function ritchey_create_binary_count_file_i1_v1($source_file, $destination_file, $overwrite = NULL, $display_errors = NULL){
	## Arguments
	$errors = array();
	if (@is_file($source_file) === FALSE) {
		$errors[] = 'source_file';
	}
	if ($overwrite === NULL){
		$overwrite = FALSE;
	} else if ($overwrite === TRUE){
		// Do Nothing
	} else if ($overwrite === FALSE){
		// Do Nothing
	} else {
		$errors[] = "overwrite";
	}
	if (@is_file($destination_file) === TRUE) {
		if ($overwrite === TRUE){
			unlink($destination_file);
		} else {
			$errors[] = 'destination_file';
		}
	} else if (@is_dir(@dirname($destination_file)) === TRUE){
		// Do nothing
	} else {
		$errors[] = 'destination_file';
	}
	if ($display_errors === NULL){
		$display_errors = FALSE;
	} else if ($display_errors === TRUE){
		//Do Nothing
	} else if ($display_errors === FALSE){
		//Do Nothing
	} else {
		$errors[] = "display_errors";
	}
	## Task
	if (@empty($errors) === TRUE){
		// Open file
		$handle = @fopen($source_file, 'r');
		if ($handle === FALSE) {
			$errors[] = 'handle';
			goto result;
		}
		// Create array with key foreach binary representation
		$binary_count = array();
		$binary_representations = array();
		for ($i = 0; $i < 256; $i++) {
    		$binary_representations[] = @str_pad(@decbin($i), 8, "0", STR_PAD_LEFT);
		}
		foreach ($binary_representations as &$item){
			$binary_count[$item] = 0;
		}
		unset($item);
		// Read file 1 byte at a time, and count occurrences of binary reps
		while (@feof($handle) === FALSE) {
			$byte = @fread($handle, 1);
			// If fread has reached the end of the file during the last loop, it will attempt to read again, but return false. Don't do anything at that point!
			if ($byte !== ''){
				// Convert to binary representation
				$location = realpath(dirname(__FILE__));
				require_once $location . '/dependencies/data_to_binary_representation_v1/data_to_binary_representation_v1.php';
				$byte = data_to_binary_representation_v1($byte, TRUE);
				// Update count of how many times each particular representation has occurred.
				if (isset($binary_count[$byte]) === TRUE){
					$binary_count[$byte] = $binary_count[$byte] + 1;
				} else {
					$errors[] = "binary_count: '{$byte}'";
					goto result;
				}
			}
		}
		fclose($handle);
		// Convert count to a string, including only items that exist
		$file_data = array();
		$binary_count_str = array();
		foreach ($binary_count as $key => $item) {
			if ($item > 0){
				$binary_count_str[] = "{$key}: {$item}";
			}
		}
		$binary_count_str = implode(', ', $binary_count_str);
		// Create file data
		$file_content = "Source File: " . basename($source_file) . PHP_EOL . "Binary Count: {$binary_count_str}" . PHP_EOL;
		date_default_timezone_set('UTC');
		$timestamp = time();
		$content_sha3256 = hash('sha3-256', $file_content);
		$uuid = hash('md5', "{$timestamp}{$content_sha3256}");
		$flag = '"' . ' (' . $uuid . ')';
		// Create file layout, and add data
		$file_data['file_type'] = "File Type: Ritchey File Format v1";
		$file_data['data_type'] = "Data Type: Ritchey Binary Count Data v1";
		$file_data['created'] = "Created: " . $timestamp;
		$file_data['last_modified'] = "Last Modified: " . $timestamp;
		$file_data['file_title'] = "File Title: " . pathinfo($source_file, PATHINFO_FILENAME);
		$file_data['uuid'] = "UUID: {$uuid}";
		$file_data['content_label'] = "Content:";
		$file_data['content_flag_1'] = $flag;
		$file_data['content'] = preg_replace("/(\r\n|\n|\r)$/", '', $file_content, 1);
		$file_data['content_flag_2'] = $flag;
		$file_data['content_sha3256'] = "Content SHA3-256: " . hash('sha3-256', $file_content);
		$file_data['content_sha256'] = "Content SHA-256: " . hash('sha256', $file_content);
		$file_data = implode(PHP_EOL, $file_data);
		// Write everything to destination file
		@file_put_contents($destination_file, $file_data, FILE_APPEND | LOCK_EX);
	}
	cleanup:
	## Cleanup
	// Do nothing
	result:
	## Result
	### Display Errors
	if ($display_errors === TRUE){
		if (@empty($errors) === FALSE){
			$message = @implode(", ", $errors);
			if (function_exists('ritchey_create_binary_count_file_i1_v1_format_error') === FALSE){
				function ritchey_create_binary_count_file_i1_v1_format_error($errno, $errstr){
					echo $errstr;
				}
			}
			set_error_handler("ritchey_create_binary_count_file_i1_v1_format_error");
			trigger_error($message, E_USER_ERROR);
		}
	}
	### Return
	if (@empty($errors) === TRUE){
		return TRUE;
	} else {
		return FALSE;
	}
}
}
?>