<?php

require_once(__DIR__ . '/inc.php');

$conf = config();

// Let's protect ourselves from ourselves.
$YES = 'YES';
$question = "All existing MINES database will be permanently deleted during the install process.\n";
$question .= "If you are sure, type '$YES' to continue: ";
echo $question;

$response = trim(fgets(STDIN));
if ($YES !== $response) die(0);

// Drop defined surveys.
foreach ($conf['surveys']() as $id => $survey){
	db_exec($survey['drop'], array());
}

// Drop survey schema.
$drops = array(
	schema_config('survey_answer-drop'),
	schema_config('survey_question-drop'),
	schema_config('survey-drop'),
	schema_config('status-drop'),
);
foreach ($drops as $sql){
	db_exec($sql, array());
}

// Create survey schema.
$creates = array(
	schema_config('status-create'),
	schema_config('status-insert'),
	schema_config('survey-create'),
	schema_config('survey_question-create'),
	schema_config('survey_answer-create'),
);
foreach ($creates as $sql){
	db_exec($sql, array());
}

// Install defined surveys.
foreach ($conf['surveys']() as $id => $survey){
	survey_import(__DIR__ . '/../' . $survey['import']);
	db_exec($survey['create'], array());
}

