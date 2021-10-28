<?php require_once 'header.php';?>

<div class="col-xs-12 col-md-8">

<div class="page-header white-content">
	<h3><?=_('Send a Message to ')?> <a href="/users/profile/<?=$recUsername?>"><?=$recUsername?></a></h3>

	<?php
if (isset($form_message)) {
	print $form_message;
}

?>

	<form method="post" action="" id="acc-form" accept-charset="UTF-8">

		<label><?=_('Subject')?>:</label>
		<input type="text" name="subject" placeholder="<?=_('message subject')?>" class="form-control" value="<?php if (isset($reply_subject)) {
	echo $reply_subject;
}
?>"/>


		<label><?=_('Message')?>:</label>
		<textarea name="body" rows="6" cols="40" class="form-control" placeholder="<?=_('message body')?>"></textarea>

		<br/><br/>
		<input type="submit" name="sb_msg" value="<?=_('Contact User')?>" class="btn btn-green"/>

	</form>

</div>

</div>

<?php require_once 'sidebar.php';?>

<?php require_once 'footer.php';?>