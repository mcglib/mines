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

// Open a file handle.
$output = fopen("php://output", 'w');

// Print header.
fputcsv($output, array_keys($fields));

// Loop on each row.
foreach (survey_submissions($survey_id) as $survey_submission){
	$row = array();

	// Loop on each field, calling the defined callback for that field.
	foreach ($fields as $name => $callback){
		$row[] = $callback($survey_submission);
	}

	fputcsv($output, $row);
}
echo "\n";

