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
$sessions_per_submissions = round($survey->session_count / $submission_count, 2);

// Report
echo "Survey: {$survey->id}\n";
echo "Active: {$survey->begin_date} to {$survey->end_date}\n";
echo "Sessions: {$survey->session_count}\n";
echo "Threshold: {$survey->threshold}\n";
echo "Submissions: $submission_count\n";
echo "Sessions / Submissions: $sessions_per_submissions\n";
echo "\n";
