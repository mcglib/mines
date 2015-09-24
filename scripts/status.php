<?php

// Include the function library.
require_once(__DIR__ . '/../scripts/inc.php');

// Check for survey id.
if (count($argv) == 1){
	echo "Usage: php status.php \$survey_id\n\n";
	die();	
}

// Prepare arguments.
$survey_id = $argv[1];

// Get a survey.
$survey = survey($survey_id);

// Get submission count.
$submission_count = count(survey_submissions($survey_id));

// Current date
$date = date("Y-m-d H:m:s");

// Determine sessions divided by submissions
$sessions_per_submissions = round($survey->session_count / $submission_count, 2);

// Determine last-status file path
$LAST_REPORT_FILE_PATH = __DIR__ . '/../tmp/last-status.txt';

// Report
$current_report = <<<EOS
Status Date and Time: 	{$date}
Survey: 		{$survey->id}
Active: 		{$survey->begin_date} to {$survey->end_date}
Sessions: 		{$survey->session_count}
Threshold: 		{$survey->threshold}
Submissions: 		{$submission_count}
Sessions / Submissions: {$sessions_per_submissions}
EOS;

$last_report = '';
if (file_exists($LAST_REPORT_FILE_PATH)){
	$last_report = file_get_contents($LAST_REPORT_FILE_PATH);
}

// Write the current report to $LAST_STATUS_FILE_PATH.
file_put_contents($LAST_REPORT_FILE_PATH, $current_report);

echo <<<EOS
*CURRENT REPORT*
{$current_report}

*LAST REPORT*
{$last_report}

EOS;

