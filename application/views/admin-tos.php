<?php
require_once ('header.php');
?>


	<div class="page-header white-content">
		<h1>Admin -> Update TOS</h1>

		<?php require_once 'admin-menu.php'; ?>

	</div>

	<div class="white-content">
		<?php if(isset($error)) echo $error; ?>
		
		<form method="post" action="">
		<textarea name="tos" class="form-control" rows="15" cols="45"><?=$tos;?></textarea>
		<br />
		<input type="submit" name="sb" value="Save" class="btn btn-green"/>	
		</form>
		
	</div>


<?php
	require_once ('footer.php');
?>