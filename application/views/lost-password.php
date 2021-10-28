<?php require_once ('header.php'); ?>

<div class="col-xs-12 col-md-8">
	
<div class="page-header white-content">
	<h1>Lost password</h1>
</div>

<div class="white-content">
	<?php echo $msg; ?>
	<form method="POST">
	<div class="input-group">
	<input type="email" name="ea" class="form-control" placeholder="Enter email address" required/>
	<span class="input-group-btn">
	<input type="submit" class="btn btn-default" value="Send Reset Email">
	</span>
	</div>
	</form>
</div>

</div>

<?php require_once 'sidebar.php'; ?>

<?php require_once ('footer.php'); ?>