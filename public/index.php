<?php

// Include the function library.
require_once(__DIR__ . '/../scripts/inc.php');

// Load the config array.
$conf = config();

// Prepare arguments.
$survey_id = @$_GET['id'] ? $_GET['id'] : $conf['default-survey-id'];
$url = @$_GET['url'] ? $_GET['url'] : redirect_and_die($conf['default-redirect-url']);

if ($conf['enable-force-survey']){
	// If URL begins with uppercase HTTP, this will force the survey to be displayed.
	// We are relying on this obscure hack in order to leave the request URL undisturbed.
	$force_survey = strpos($url, 'HTTP') === 0 ? true : false;
	if ($force_survey){
		redirect_and_die("survey.php?id=$survey_id&url=" . urlencode($url));
	}
}

// Verify if application is enabled or within the configured date-range.
if (!$conf['enabled'] || !today_is_between($conf['begin_date'], $conf['end_date'])) redirect_and_die($url);

// Get a survey.
$survey = survey($survey_id);

// Increment the survey's count.
survey_increment_count($survey);

// Is it time to present this survey?
if (survey_is_presentable($survey)){
	redirect_and_die("survey.php?id=$survey_id&url=" . urlencode($url));
}

// If we reach this point, redirect to the e-resource.
redirect_and_die($url);

