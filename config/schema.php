<?php

return array(
	'status-create' => "
		create table if not exists status (
			id varchar(2) primary key,
			name varchar(32)
		);
	",
	'status-drop' => "
		drop table status;
	",
	'status-insert' => "
		insert into status 
			(id, name)
		values
			('A', 'Active'),
			('I', 'Inactive')
		;
	",
	'survey-create' => "
		create table if not exists survey (
			id varchar(16) primary key,
			status_id varchar(2),
			weight float,
			name varchar(1024),
			notes varchar(2048),
			begin_date date,
			end_date date,
			session_count int,
			threshold int,
			force_completion tinyint(1),
			show_thanks tinyint(1),
			thanks_redirect_after_seconds smallint,
			created_at datetime,
			updated_at datetime
		);
	",
	'survey-drop' => "
		drop table survey;
	",
	'survey-select' => "
		select * from survey where id = ?
	",
	'survey-insert' => "
		insert into survey
			(id, status_id, weight, name, notes, begin_date, end_date, session_count, threshold, force_completion, show_thanks, thanks_redirect_after_seconds, created_at, updated_at)
		values
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		;
	",
	'survey_question-create' => "
		create table if not exists survey_question (
			id varchar(16) primary key,
			survey_id varchar(16),
			status_id varchar(2),
			weight float,
			question varchar(1024),
			notes varchar(2048),
			use_dropdown tinyint(1),
			allow_multiple_answers tinyint(1),
			allow_other_answer tinyint(1),
			created_at datetime,
			updated_at datetime
		);
	",
	'survey_question-drop' => "
		drop table survey_question;
	",
	'survey_questions-select' => "
		select * from survey_question where survey_id = ? order by weight
	",
	'survey_question-insert' => "
		insert into survey_question
			(id, survey_id, status_id, weight, question, notes, use_dropdown, allow_multiple_answers, allow_other_answer, created_at, updated_at)
		values
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		;
	",
	'survey_answer-create' => "
		create table if not exists survey_answer (
			id varchar(16) primary key,
			survey_question_id varchar(16),
			status_id varchar(2),
			weight float,
			answer varchar(1024),
			notes varchar(2048),
			is_other tinyint(1),
			created_at datetime,
			updated_at datetime
		);
	",
	'survey_answer-drop' => "
		drop table survey_answer;
	",
	'survey_answers-select' => "
		select * from survey_answer where survey_question_id = ? order by weight
	",
	'survey_answer-insert' => "
		insert into survey_answer
			(id, survey_question_id, status_id, weight, answer, notes, is_other, created_at, updated_at)
		values
			(?, ?, ?, ?, ?, ?, ?, ?, ?)
	",
);

