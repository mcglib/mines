<?php

// Include the function library.
require_once(__DIR__ . '/../scripts/inc.php');

// Check for survey id.
if (count($argv) == 1){
	echo "Usage: php arl-report.php \$survey_id\n\n";
	die();	
}

// Prepare arguments.
$survey_id = $argv[1];

// Get field definitions.
$fields = survey_config($survey_id, 'arl-export');

// Print header.
echo implode(",", array_keys($fields)) . "\n";

// Loop on each row.
foreach (survey_submissions($survey_id) as $survey_submission){
	$row = array();

	// Loop on each field, calling the defined callback for that field.
	foreach ($fields as $name => $callback){
		$row[] = $callback($survey_submission);
	}
	echo implode(",", $row) . "\n";
}
echo "\n";

