<?php require_once ('header.php'); ?>

<div class="col-xs-12 col-md-9">
<div class="page-header white-content">
	<h3><?=_("My Account") ?></h3>
</div>

<div class="white-content">
	<?php
	if(isset($form_message)) print $form_message;
	?>
	
	<form method="post" action="" id="acc-form" accept-charset="UTF-8" enctype="multipart/form-data">
		<label>
			<?=_('Username')?>: <span class="muted"><?=_('Username is not changeable') ?></span>
		</label>
		<input type="text" name="username" value="<?php print htmlspecialchars($user->username); ?>" class="required form-control" readonly="readonly"/>
		
		<br/>
		
		<label>
			<?=_('Email Address:')?>
		</label>
		<input type="email" name="email" value="<?php print htmlspecialchars($user->email); ?>" class="required form-control" />
		
		<br/>
		
		<label>
			<?=_('Password:')?> <span class="muted"><?=_('current or a new one') ?></span>
		</label>
		<input type="password" name="password" placeholder="****" class="required form-control" />
		
		<br/>
		
		<label>
		    <?=_('Profile Picture:') ?>
		</label>
		<input type="file" name="file" class="form-control" />
		<br/>
		
		<label><?=_('About:')?> <span class="muted"><?=_('max 255 characters (no links or spam)') ?></span></label>
		<textarea name="about" rows="6" cols="45" class="form-control"><?php print htmlspecialchars($user->about); ?></textarea>
		
		<br/>
		
		<input type="submit" name="sb_signup" value="<?=_('Update') ?>" class="btn btn-info"/>
	
	</form>
	
	<div id="signup_output"></div>
	
</div>

</div>

<?php require_once 'sidebar.php'; ?>

<?php require_once ('footer.php'); ?>