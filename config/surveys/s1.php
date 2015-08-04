<?php

return array(
	// Survey, questions, and answers.
	'import' => 'config/surveys/s1.tsv',

	// Custom CSS file.
	'css' => 'static/styles.css',

	// Create statement for survey submissions table.
	// Use the survey_id as the table name.
	'create' => "
		create table s1 (
			id int auto_increment primary key,
			status_id varchar(2),
			s1q1 varchar(16),
			s1q1_other varchar(1024),
			s1q2 varchar(16),
			s1q2_other varchar(1024),
			s1q3 varchar(16),
			s1q4 varchar(16),
			s1q5 varchar(256),
			url varchar(1024),
			ip varchar(15),
			user_agent varchar(1024),
			created_at datetime,
			updated_at datetime
		);
	",

	// Drop statement statement for survey submissions table.
	'drop' => "
		drop table if exists s1
	",

	// Insert statement for survey submissions table.
	'insert' => "
		insert
		into s1 (id, status_id, s1q1, s1q1_other, s1q2, s1q2_other, s1q3, s1q4, s1q5, url, ip, user_agent, created_at, updated_at)
		values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
	",

	// Select all statement for survey sumissions table.
	'select' => "
		select * from s1
	",

	// Define how each submitted form value should be validated.
	// $value contains the argument being validated. $args contains all submitted arguments.
	'validators' => array(
		'id' => function($value, $args){
			return preg_match('/^s\d+$/', $value) ? '' : 'Invalid id.';
		},
		'url' => function($value, $args){
			return preg_match('/^https?:\/\/[a-zA-Z0-9].*/', $value) ? '' : 'Invalid URL.';
		},
		's1q1' => function($value, $args){
			return preg_match('/^s\d+q\d+a\d+$/', $value) ? '' : 'Invalid answer for question 1.';
		},
		's1q1:other' => function($value, $args){
			if (@$args['s1q1'] != 's1q1a9') return '';
			return $value != '' ? '' : 'Please specify an other answer for question 1.';
		},
		's1q2' => function($value, $args){
			return preg_match('/^s\d+q\d+a\d+$/', $value) ? '' : 'Invalid answer for question 2.';
		},
		's1q2:other' => function($value, $args){
			if (@$args['s1q2'] != 's1q2a15') return '';
			return $value != '' ? '' : 'Please specify an other answer for question 2.';
		},
		's1q3' => function($value, $args){
			return preg_match('/^s\d+q\d+a\d+$/', $value) ? '' : 'Invalid answer for question 3.';
		},
		's1q4' => function($value, $args){
			return preg_match('/^s\d+q\d+a\d+$/', $value) ? '' : 'Invalid answer for question 4.';
		},
		's1q5' => function($value, $args){
			foreach ($value as $arg_value){
				if (!preg_match('/^s\d+q\d+a\d+$/', $arg_value)) return 'Invalid answer for question 5.';
			}
			return '';
		},
	),

	// Define the values that should be supplied to the prepared insert statement above.
	// $args will contain submitted form data.
	'insert-args' => array(
		'id' => function($args){
			return null;
		},
		'status_id' => function($args){
			return 'A';
		},
		's1q1' => function($args){
			return @$args['s1q1'];
		},
		's1q1_other' => function($args){
			return @$args['s1q1:other'];
		},
		's1q2' => function($args){
			return @$args['s1q2'];
		},
		's1q2_other' => function($args){
			return @$args['s1q2:other'];
		},
		's1q3' => function($args){
			return @$args['s1q3'];
		},
		's1q4' => function($args){
			return @$args['s1q4'];
		},
		's1q5' => function($args){
			return implode(',', @$args['s1q5']);
		},
		'url' => function($args){
			return @$args['url'];
		},
		'ip' => function($args){
			return @$_SERVER['REMOTE_ADDR'];
		},
		'user_agent' => function($args){
			return @$_SERVER['HTTP_USER_AGENT'];
		},
		'created_at' => function($args){
			return date('Y-m-d H:i:s');
		},
		'updated_at' => function($args){
			return date('Y-m-d H:i:s');
		},
	),

	// Define how each field is mapped to ARL report requirements
	'arl-export' => array(
		'TimeCreated' => function($row){
			return $row->created_at;
		},
		'ResourceURL' => function($row){
			return $row->url;
		},
		'IPAddress' => function($row){
			return $row->ip;
		},
		'UserAgent' => function($row){
			return $row->user_agent;
		},
		'UserGroup' => function($row){
			return $row->s1q1;
		},
		'UserGroupOtherText' => function($row){
			return $row->s1q1_other;
		},
		'Affiliation' => function($row){
			return $row->s1q2;
		},
		'AffiliationOtherText' => function($row){
			return $row->s1q2_other;
		},
		'Location' => function($row){
			return $row->s1q3;
		},
		'Purpose' => function($row){
			return $row->s1q4;
		},
		'Reason' => function($row){
			return $row->s1q5;
		},
	),
);

