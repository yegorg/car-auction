<?php require_once ('header.php'); ?>

<div class="col-xs-12">
	
<div class="page-header white-content">
	<h1>Admin Login.</h1>
</div>

<div class="white-content">
	<?php if(isset($form_message)) print $form_message; ?>
	
	<div class="row">
	<div class="col-xs-6">
	<form method="post" action="/admin/login" class="form">
		<input type="text" name="u" placeholder="username" class="form-control" /><br/>
		<input type="password" name="p" placeholder="****" class="form-control" /><br/>
		<input type="submit" name="sbLogin" value="Login" class="btn btn-default"/>
	</form>
	</div>
	</div>
	
</div>

</div>

<?php #require_once 'sidebar.php'; ?>

<?php require_once ('footer.php'); ?>