<?php

// Bypass APC bug: https://bugs.php.net/bug.php?id=58982
ini_set('apc.cache_by_default', 0);

// Helpers
set_error_handler(function($severity, $message, $file, $line){
    if (!(error_reporting() & $severity)) return; // This error code is not included in error_reporting

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($exception){
	try {
		$conf = config();

		if ($conf['production'] &&  !is_command_line()){
			$exception_path = "/tmp/mines-exception." . $exception->getCode();

			if ($conf['exception-email-once'] && file_exists($exception_path)) redirect_and_die(@$_REQUEST['url']);

			$to = implode(', ', $conf['admins']);
			$subject = $conf['exception-email']['subject'];
			$message = strtr($conf['exception-email']['message'], array(
				'%path' => $exception_path,
				'%exception' => $exception->__toString(),
			));

			mail($to, $subject, $message);

			file_put_contents($exception_path, $message);

			redirect_and_die(@$_REQUEST['url']);
		} else {
			print_r($exception);
		}
	} catch (Exception $e){
		echo 'Sorry, something went wrong.';
	}
});

function is_command_line(){
        return php_sapi_name() == "cli";
}

// A function for returning a fairly complex key to be used with the $GLOBALS array.
// A ludicrous attempt at avoiding collisions.
function globals_key($suffix){
	$key = __DIR__ . '/' . __FILE__ . '.' . $suffix;

	return $key;
}

function validate($args, array $validators){
	$result = array();

	foreach ($validators as $key => $callback){
		$value = @$args[$key];
		$result[] = $callback($value, $args);
	}

	return array_filter($result);
}

function today_is_between($begin_date, $end_date){
	$begin_date = str_replace('-', '', $begin_date);
	$end_date = str_replace('-', '', $end_date);
	$today = date('Ymd');

	if ($begin_date && !$end_date) return $begin_date <= $today;
	if (!$begin_date && $end_date) return $today <= $end_date;

	return $begin_date <= $today && $today <= $end_date;
}

// Config
function config(){
	$PATH = __DIR__ . '/../config/config.php';
	$key = globals_key(__FUNCTION__);
	
	if (!isset($GLOBALS[$key])){
		$config = include($PATH);
		$GLOBALS[$key] = $config[gethostname()];
	}

	return $GLOBALS[$key];
}

function schema_config($schema_key = null){
	$key = globals_key(__FUNCTION__);
	
	if (!isset($GLOBALS[$key])){
		$conf = config();
		$GLOBALS[$key] = $conf['schema']();
	}

	return $key ? $GLOBALS[$key][$schema_key] : $GLOBALS[$key];
}

function survey_config($survey_id, $survey_key = null){
	$key = globals_key(__FUNCTION__);
	
	if (!isset($GLOBALS[$key])){
		$conf = config();
		$GLOBALS[$key] = $conf['surveys']();
	}

	return $survey_key ? $GLOBALS[$key][$survey_id][$survey_key] : $GLOBALS[$key][$survey_id];
}

// Database
function db(){
	$key = globals_key(__FUNCTION__);

	if (!isset($GLOBALS[$key])){
		$conf = config();

		$dsn = 'mysql:host=' . $conf['dbms']['host'] . ';dbname=' . $conf['dbms']['database'] . ';charset=utf8';
		$pdo = new PDO($dsn, $conf['dbms']['user'], $conf['dbms']['pass']);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$GLOBALS[$key] = $pdo;
	}

	return $GLOBALS[$key];
}

function db_exec($sql, array $args){
	$args = array_values($args);

	$query = db()->prepare($sql);
	$query->execute($args);
	return $query;
}

function db_rows($sql, array $args){
	return db_exec($sql, $args)->fetchAll();
}

function db_row($sql, array $args){
	return db_exec($sql, $args)->fetch();
}

function survey($id){
	$conf = config();
	$sql = schema_config('survey-select');
	$args = array($id);

	return db_row($sql, $args);
}

function survey_insert(array $args){
	$conf = config();
	$sql = schema_config('survey-insert');
	db_exec($sql, $args);
}

function questions(stdClass $survey){
	$conf = config();
	$sql = schema_config('survey_questions-select');
	$args = array($survey->id);

	return db_rows($sql, $args);
}

function question_insert(array $args){
	$conf = config();
	$sql = schema_config('survey_question-insert');
	db_exec($sql, $args);
}

function answers(stdClass $question){
	$conf = config();
	$sql = schema_config('survey_answers-select');
	$args = array($question->id);

	return db_rows($sql, $args);
}

function answer_insert(array $args){
	$conf = config();
	$sql = schema_config('survey_answer-insert');
	db_exec($sql, $args);
}

function survey_increment_count(stdClass $survey){
	$survey->session_count++;

	$conf = config();
	$sql = schema_config('survey-update-count');
	$args = array($survey->session_count, $survey->id);

	db_exec($sql, $args);
}

function survey_is_presentable(stdClass $survey){
	if ($survey->status_id != 'A') return false;
	if (!today_is_between($survey->begin_date, $survey->end_date)) return false;
	if ($survey->session_count % $survey->threshold != 0) return false;

	return true;
}

function survey_submit($survey_id, $args){
	$conf = config();
	$def = survey_config($survey_id);

	$messages = validate($args, $def['validators']);

	if (count($messages)) return $messages;

	$insert_args = array();
	foreach ($def['insert-args'] as $callback){
		$insert_args[] = $callback($args);
	}

	$result = db_exec($def['insert'], $insert_args);

	return array();
}

function survey_submissions($survey_id){
	$conf = config();

	$sql = survey_config($survey_id, 'select');
	$args = array($survey_id);
	$query = db_exec($sql, $args);

	return $query->fetchAll();
}

function survey_export($id){
	$result = '';

	$survey = survey($id);
	$survey_keys = array_keys((array)$survey);

	$result .= "#SURVEY\n";
	$result .= implode("\t", $survey_keys) . "\n";
	$result .= implode("\t", (array)$survey) . "\n";

	$questions = questions($survey);
	$question_keys = array_keys((array)$questions[0]);

	$result .= "#QUESTIONS\n";
	$result .= implode("\t", $question_keys) . "\n";
	foreach ($questions as $question) $result .= implode("\t", (array)$question) . "\n";

	$result .= "#ANSWERS\n";
	foreach ($questions as $i => $question){
		$answers = answers($question);
		if (0 == $i){
			$answer_keys = array_keys((array)$answers[0]);
			$result .= implode("\t", $answer_keys) . "\n";
		}
		foreach ($answers as $answer) $result .= implode("\t", (array)$answer) . "\n";
	}

	return $result;
}

function survey_import($file){
	$sections = array(
		'#SURVEY' => array(),
		'#QUESTIONS' => array(),
		'#ANSWERS' => array()
	);
	$section = null;

	foreach (file($file, FILE_IGNORE_NEW_LINES) as $i => $line){
		$line = rtrim($line);
		if ('' == $line) continue;

		$fields = explode("\t" , $line);

		if (in_array(trim($fields[0]), array_keys($sections))){
			$section = trim($fields[0]);
			continue;
		}

		if (isset($sections[$section])) $sections[$section][] = $fields;
	}

	foreach ($sections as $section => $rows){
		foreach ($rows as $i => $row){
			if (0 == $i){
				 $fields = $row;
			} else {
				$args = array();
				foreach ($fields as $i => $field){
					$args[$field] = @$row[$i];
				}

				if (!$args['created_at']) $args['created_at'] = date('Y-m-d H:m:s');
				if (!$args['updated_at']) $args['updated_at'] = date('Y-m-d H:m:s');

				if ($section == '#SURVEY') survey_insert($args);
				if ($section == '#QUESTIONS') question_insert($args);
				if ($section == '#ANSWERS') answer_insert($args);
			}
		}
	}
}

// Web
function template_open(array $parts){
	if (!isset($parts['css'])) $parts['css'] = 'static/styles.css';
	if (!isset($parts['head'])) $parts['head'] = '';

	return <<<EOS
<!DOCTYPE html>
<html>
	<head>
		<title>{$parts['title']}</title>
		<link rel="stylesheet" type="text/css" href="{$parts['css']}" />
		{$parts['head']}
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	</head>
	<body>
EOS;
}

function template_close(){
	return <<<EOS
	</body>
</html>
EOS;
}

function eko($string){
	echo htmlspecialchars($string);
}

function redirect_and_die($url){
	if (!is_command_line()) header("location: $url");
	die();
}

