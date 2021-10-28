<?php require_once ('header.php'); ?>

<div class="col-xs-12 col-md-8">
	
<div class="page-header white-content">
	<h1>Reset password</h1>
</div>

<div class="white-content">
	<?php 
	echo $msg; 
	?>
	<form method="POST">
	
	<input type="password" name="pn" class="form-control" placeholder="new password" required/>
	<input type="password" name="pc" class="form-control" placeholder="confirm password" required/>
	
	<input type="submit" class="btn btn-default" value="Reset">
	</form>

</div>

</div>

<?php require_once 'sidebar.php'; ?>

<?php require_once ('footer.php'); ?>