<?php

require_once(__DIR__ . '/inc.php');

// Check for survey id.
if (count($argv) == 1){
	echo "Usage: php scripts/import.php /full/path/to/s1.tsv\n\n";
	die();	
}

// Let's protect ourselves from ourselves.
$YES = 'YES';
$question = "About to import a new survey, If you are sure, type '$YES' to continue: ";
echo $question;

$response = trim(fgets(STDIN));
if ($YES !== $response) die(0);

survey_import($argv[1]);

