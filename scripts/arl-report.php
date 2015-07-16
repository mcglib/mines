<?php

// Include the function library.
require_once(__DIR__ . '/../scripts/inc.php');

// Check for survey id.
if (count($argv) == 1){
	echo "Usage: php scripts/arl-report.php \$survey_id\n\n";
	die();	
}

$conf = config();

$survey_id = $argv[1];

$fields = $conf['surveys'][$survey_id]['arl-export'];

echo implode(",", array_keys($fields)) . "\n";
foreach (survey_submissions($survey_id) as $survey_submission){
	$row = array();
	foreach ($fields as $name => $callback){
		$row[] = $callback($survey_submission);
	}
	echo implode(",", $row) . "\n";
}
echo "\n";

