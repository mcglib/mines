<?php

require_once(__DIR__ . '/../scripts/inc.php');

$conf = config();

$survey = survey(@$_REQUEST['id']);
if (!$survey) redirect_and_die($_REQUEST['url']);

?>
<?php echo template_open(array(
	'title' => 'Library Survey: Thank you!',
	'css' => $conf['surveys'][$survey->id]['css'],
	'head' => '<meta http-equiv="refresh" content="' . $survey->thanks_redirect_after_seconds . '; url=' . $_REQUEST['url'] . '"/>',
)); ?>
<div class="thank-you">
	<div class="thank-you-title">
		Thank you!
	</div>
	<div class="thank-you-message">
		You will automatically be redirected to the resource you requested in <?php eko($survey->thanks_redirect_after_seconds); ?> seconds.<br/>
		Or you can click here: <a href="<?php eko($_REQUEST['url']); ?>"><?php eko($_REQUEST['url']); ?></a>
	</div>
</div>
<?php echo template_close(); ?>

