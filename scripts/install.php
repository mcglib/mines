<?php

require_once(__DIR__ . '/inc.php');
$conf = config();

if (false){
	// Drop defined surveys.
	foreach ($conf['surveys'] as $id => $survey){
		db_exec($survey['drop'], array());
	}

	// Drop survey schema.
	$drops = array(
		$conf['schema']['survey_answer-drop'],
		$conf['schema']['survey_question-drop'],
		$conf['schema']['survey-drop'],
		$conf['schema']['status-drop'],
	);
	foreach ($drops as $sql){
		db_exec($sql, array());
	}
}

// Create survey schema.
$creates = array(
	$conf['schema']['status-create'],
	$conf['schema']['status-insert'],
	$conf['schema']['survey-create'],
	$conf['schema']['survey_question-create'],
	$conf['schema']['survey_answer-create'],
);
foreach ($creates as $sql){
	db_exec($sql, array());
}

// Install defined surveys.
foreach ($conf['surveys'] as $id => $survey){
	survey_import(__DIR__ . '/../' . $survey['import']);
	db_exec($survey['create'], array());
}

