<?php require_once ('header.php'); ?>

<div class="span8" style="margin-left:0px;">
	
<div class="page-header">
	<h1>Join Now. It's Free!</h1>
	 
	<form method="post" action="/users/ajax_join" id="signup-form" accept-charset="UTF-8">
		<label>
			<?=_('Username') ?>:
		</label>
		<input type="text" name="username" placeholder="username" class="required"/>
		
		<br/>
		
		<label>
			<?=_('Email') ?>:
		</label>
		<input type="email" name="email" placeholder="email" class="required" />
		
		<br/>
		
		<label>
			<?=_('Password') ?>:
		</label>
		<input type="password" name="password" placeholder="****" class="required" />
		
		<br/>
		
		<input type="submit" name="sb_signup" value="<?=_('Join Now') ?>" class="btn btn-info"/>
	
	</form>
	
	<div id="signup_output_div"></div>
	
</div>

</div>

<?php require_once 'sidebar.php'; ?>

<?php require_once ('footer.php'); ?>