<?php require_once ('header.php'); ?>


<div class="page-header white-content">
	<h1>Admin -> Mail Server</h1>
	<?php require_once 'admin-menu.php'; ?>
</div><!-- ./page-header white-content -->

<div class="white-content">

<?php if(isset($form_message)) print $form_message; ?>

<form method="post" action="" class="form form-horizontal">

<dl>
<dt>Send email with:</dt>
<dd>
<input type="radio" name="mail_type" value="smtp" <?php if( get_option('mail_type', 'mail') == 'smtp' ) {echo 'checked';} ?>> <strong>SMTP</strong> ( recommended )<br>
<input type="radio" name="mail_type" value="mail" <?php if( get_option('mail_type', 'mail') == 'mail' ) {echo 'checked';} ?>> <strong>Mail()</strong> function ( depends on host for delivery )
</dd>
<br>
<dt>SMTP Server Address:</dt>
<dd>
<input type="text" name="smtp_address" value="<?php echo get_option('smtp_address') ?>" class="form-control">
</dd>
<br>
<dt>SMTP Port:</dt>
<dd>
<input type="number" name="smtp_port" value="<?php echo get_option('smtp_port') ?>" class="form-control">
</dd>
<br>

<dt>SMTP Username/Email:</dt>
<dd>
<input type="text" name="smtp_user" value="<?php echo get_option('smtp_user') ?>" class="form-control">
</dd>
<br>

<dt>SMTP Password:</dt>
<dd>
<input type="password" name="smtp_password" value="<?php echo get_option('smtp_password') ?>" class="form-control">
</dd>	
</dl>

<dt>&nbsp;</dt>
<dd>
	<input type="submit" name="sb" class="btn btn-primary btn-block" value="Save Settings"><br>
	<a href="/admin/testmail" class="btn btn-info btn-block" target="_blank">Send Test Mail to <?php echo get_option('contact_email') ?> (Save Settings First)</a><br><br>

	<center><a href="/admin/config">Configure Contact Email</a></center>
</dd>

</div><!-- ./white-content -->
</form><!-- ./form -->

<?php require_once ('footer.php'); ?>