<?php

require_once(__DIR__ . '/../scripts/inc.php');

$messages = array();
$actions = array(
	'submit' => 'Submit',
	'cancel' => 'Not now, thanks!',
);

$conf = config();

$survey = survey(@$_REQUEST['id']);
if (!$survey) redirect_and_die($_REQUEST['url']);

if ($actions['submit'] == @$_POST['submit']){
	$messages = survey_submit($_REQUEST['id'], $_POST);

	if (!count($messages)){
		redirect_and_die($survey->show_thanks ? $_POST['url'] : 'thanks.php');
	} 
}

if ($actions['cancel'] == @$_POST['cancel']){
	if (!$survey->force_completion){
		redirect_and_die($_POST['url']);
	}
}
?>
<?php echo template_open(array(
	'title' => 'Library Survey',
	'css' => $conf['surveys'][$survey->id]['css'],
)); ?>
<script>
	function display_other(select){
		var other_selected = select.options[select.selectedIndex].getAttribute("data-is_other") == '1';
		select.form[select.name + ':other'].style.display = other_selected ? 'inline-block' : 'none';
	}
</script>
<form method="post">
	<input type="hidden" name="id" value="<?php eko(@$_REQUEST['id']); ?>" />
	<input type="hidden" name="url" value="<?php eko(@$_REQUEST['url']); ?>" />
	<div class="survey">
		<div class="survey-name"><?php eko($survey->name); ?></div>
		<div class="survey-notes"><?php eko($survey->notes); ?></div>
		<?php if (count($messages)): ?>
			<div class="messages">
				<ul >
					<?php foreach ($messages as $key => $message): ?>
						<li class="message"><?php eko($message); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
		<div class="questions">
			<?php foreach (questions($survey) as $question): ?>
				<div class="question">
					<div class="question-text">
						<?php eko($question->question); ?>:
					</div>
					<div class="answers">
						<?php if ($question->use_dropdown): ?>
							<select name="<?php eko($question->id); ?>"<?php echo $question->allow_multiple_answers ? ' multiple' : ''; ?><?php echo $question->allow_other_answer ? ' onchange="display_other(this);"' : ''; ?>>
								<?php foreach (answers($question) as $i => $answer): ?>
									<?php if ($i == 0): ?>
										<option value="" data-is_other="0"></option>
									<?php endif; ?>
									<option value="<?php eko($answer->id); ?>" data-is_other="<?php echo $answer->is_other; ?>"><?php eko($answer->answer); ?></option>
								<?php endforeach; ?>
							</select>
							<?php if ($question->allow_other_answer): ?>
								<input style="display:none;" type="textfield" name="<?php eko($question->id . ':other'); ?>" value="<?php eko(@$_POST[$question->id . ':other']); ?>"/> 
							<?php endif; ?>
						<?php else: ?>
							<?php foreach (answers($question) as $answer): ?>
								<?php if ($question->allow_multiple_answers): ?>
									<?php $checked = in_array($answer->id, @$_REQUEST[$question->id] ?: array()) ; ?>
									<input type="checkbox" name="<?php eko($question->id); ?>[]" value="<?php eko($answer->id); ?>"<?php eko($checked ? ' checked' : ''); ?>>
								<?php else: ?>
									<?php $checked = @$_POST[$question->id] == $answer->id; ?>
									<input type="radio" name="<?php eko($question->id); ?>" value="<?php eko($answer->id); ?>"<?php eko($checked ? ' checked' : ''); ?>>
								<?php endif; ?>
								<?php eko($answer->answer); ?>
								<?php if ($question->allow_other_answer && $answer->is_other): ?>
									<input type="textfield" name="<?php eko($question->id . ':other'); ?>" value="<?php eko($_POST[$question->id . ':other']); ?>"/> 
								<?php endif; ?>
								<br/>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="controls">
		<input type="submit" name="submit" value="<?php eko($actions['submit']); ?>" />
		<?php if (!$survey->force_completion): ?>
			<input type="submit" name="cancel" value="<?php eko($actions['cancel']); ?>" />
		<?php endif; ?>
	</div>
</form>
<?php echo template_close(); ?>

