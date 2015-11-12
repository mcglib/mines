<?php

// Include the function library.
require_once(__DIR__ . '/../scripts/inc.php');

// Load the config array.
$conf = config();

// Check if URL exists.
if (!isset($_REQUEST['url'])) redirect_and_die($conf['default-redirect-url']);

// Get a survey.
$survey = survey(@$_REQUEST['id']);
if (!$survey) redirect_and_die($_REQUEST['url']);

?>
<?php echo template_open(array(
	'title' => 'Library Survey: Thank you!',
	'css' => survey_config($survey->id, 'css'),
	'head' => '<meta http-equiv="refresh" content="' . $survey->thanks_redirect_after_seconds . '; url=' . $_REQUEST['url'] . '"/>',
)); ?>
<div class="survey">
	<div class="survey-logos">
		<div class="survey-logo"><img src="static/logo.png"/></div>
		<div class="survey-mines-logo"><img src="static/mines-logo.png"/></div>
		<div class="clear"></div>
	</div>
	<div class="survey-name">
		<?php echo $survey->name; ?>
	</div>
	<div class="survey-notes">
		<div class="thank-you">
			<div class="thank-you-title">
				Thank you!
			</div>
			<div class="thank-you-message">
				You will automatically be redirected to the resource you requested in <?php eko($survey->thanks_redirect_after_seconds); ?> seconds.<br/>
				Or you can click here: <a href="<?php eko($_REQUEST['url']); ?>"><?php eko($_REQUEST['url']); ?></a>
			</div>
		</div>
	</div>
</div>
<?php echo template_close(); ?>

