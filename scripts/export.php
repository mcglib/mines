<?php

require_once(__DIR__ . '/inc.php');

// Check for survey id.
if (count($argv) == 1){
	echo "Usage: php export.php $survey_id\n\n";
	die();	
}

echo survey_export($argv[1]);

