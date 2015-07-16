<?php

return array(
	// Survey, questions, and answers.
	'import' => 'config/surveys/s1.tsv',

	// Custom CSS file.
	'css' => 'static/styles.css',

	// SQL statements for survey submissions
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
	'drop' => "
		drop table if exists s1
	",
	'insert' => "
		insert
		into s1 (id, status_id, s1q1, s1q1_other, s1q2, s1q2_other, s1q3, s1q4, s1q5, url, ip, user_agent, created_at, updated_at)
		values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
	",
	'select' => "
		select * from s1
	",

	// Define the values that should be supplied to the prepared insert statement above.
	// $args will contain submitted form data.
	'insert-args' => array(
		null,
		'A',
		@$args['s1q1'],
		@$args['s1q1:other'],
		@$args['s1q2'],
		@$args['s1q2:other'],
		@$args['s1q3'],
		@$args['s1q4'],
		implode(',', @$args['s1q5'] ?: array()),
		@$args['url'],
		@$_SERVER['REMOTE_ADDR'],
		@$_SERVER['HTTP_USER_AGENT'],
		date('Y-m-d H:i:s'),
		date('Y-m-d H:i:s'),
	),

	// Define regex validators
	'validator-definitions' => array(
		'id' => array('/^s\d+$/', 'Invalid id.'),
		'url' => array('/^https?:\/\/[a-zA-Z0-9].*/', 'Invalid URL.'),
		'answer' => array('/^s\d+q\d+a\d+$/', 'Invalid answer for question %s.'),
		'other-answer' => array('/^.*$/', 'Invalid other answer for question %s.'),
	),

	// Define how each submitted form value should be validated.
	'validators' => array(
		'id' => array('id', array()),
		'url' => array('url', array()),
		's1q1' => array('answer', array('%s' => 1)),
		's1q1:other' => array('other-answer', array('%s' => 1)),
		's1q2' => array('answer', array('%s' => 2)),
		's1q2:other' => array('other-answer', array('%s' => 2)),
		's1q3' => array('answer', array('%s' => 3)),
		's1q4' => array('answer', array('%s' => 4)),
		's1q5' => array('answer', array('%s' => 5)),
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
			return '"' . $row->s1q5 . '"';
		},
	),
);

