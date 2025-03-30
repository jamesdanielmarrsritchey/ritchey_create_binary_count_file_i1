<?php
$location = realpath(dirname(__FILE__));
require_once $location . '/ritchey_create_binary_count_file_i1_v1.php';
$return = ritchey_create_binary_count_file_i1_v1("{$location}/temporary/example_input_file.txt", "{$location}/temporary/example_bcf_file.rff", NULL, TRUE);
if ($return == TRUE){
	echo "TRUE" . PHP_EOL;
} else {
	echo "FALSE" . PHP_EOL;
}
?>