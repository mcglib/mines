<?php

require_once(__DIR__ . '/inc.php');

// Check for survey id.
if (count($argv) == 1){
	echo "Usage: php import.php /full/path/to/s1.tsv\n\n";
	die();	
}

$survey_id = basename($argv[1], '.tsv');
$survey_exists = survey($survey_id) ? true : false;

// Let's protect ourselves from ourselves.
$YES = 'YES';

if ($survey_exists){
	$question = "About to re-import this survey, If you are sure, type '$YES' to continue: ";
} else {
	$question = "About to import a new survey, If you are sure, type '$YES' to continue: ";
}

echo $question;

$response = trim(fgets(STDIN));
if ($YES !== $response) die(0);

if ($survey_exists){
	db_exec(schema_config('survey_answer-delete'), array($survey_id));
	db_exec(schema_config('survey_question-delete'), array($survey_id));
	db_exec(schema_config('survey-delete'), array($survey_id));
}

survey_import($argv[1]);

