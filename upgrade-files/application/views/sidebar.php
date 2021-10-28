<div class="col-xs-12 col-md-3">
    <?php
if (is_admin()) {
	echo '<div class="white-content">';
	print 'Hey Admin, you can go to <br/><a href="/admin" class="text-danger" style="text-decoration: underline">Admin Panel</a>';
	echo '</div>';
}
?>

	<?php
if (is_user_logged_in()):
?>
    <div class="white-content">
	<ul class="nav navbar">
	<li><?php echo anchor(base_url() . 'users', '<i class="glyphicon glyphicon-chevron-right"></i>' . _('My Profile')); ?></li>
	<li><?php echo anchor(base_url() . 'users/balance', '<i class="glyphicon glyphicon-chevron-right"></i>' . _('My Balance')); ?></li>
	<li><?php echo anchor(base_url() . 'users/mylistings', '<i class="glyphicon glyphicon-chevron-right"></i>' . _('My Listings')); ?></li>
	<li><?php echo anchor(base_url() . 'users/inbox', '<i class="glyphicon glyphicon-chevron-right"></i>' . _('Messages')); ?></li>
	<li><?php echo anchor(base_url() . 'users/won', '<i class="glyphicon glyphicon-chevron-right"></i>' . _('Auctions Won')); ?></li>
	<li><?php echo anchor(base_url() . 'users/bids', '<i class="glyphicon glyphicon-chevron-right"></i>' . _('Bids Made')); ?></li>
	<li><?php echo anchor(base_url() . 'users/offers', '<i class="glyphicon glyphicon-chevron-right"></i>' . _('Offers Received')); ?></li>
	<li><?php echo anchor(base_url() . 'users/logout', '<i class="glyphicon glyphicon-chevron-right"></i>' . _('Logout')); ?></li>
	</ul>

	<br />
	<center>
	<a href="/users/profile/<?php echo UsersModel::current_username($this->session->userdata('loggedIn')) ?>" class="btn btn-xs btn-default">View My Profile</a>
	</center>
	<?php else: ?>
	
	<div class="white-content">
	<h3><b class="icon-user" style="margin-top:7px;"></b> Login</h3>
	<?php 
	$CI =& get_instance();
	if ($CI->session->userdata( 'login_message' )) {
	echo $CI->session->userdata( 'login_message' );
	$CI->session->set_userdata( 'login_message',  null);
	}
	?>
	<form method="post" action="/users/loginform" class="form">
		<input type="text" name="uname" placeholder="username" class="form-control" /><br/>
		<input type="password" name="upwd" placeholder="****" class="form-control" /><br/>
		<input type="submit" name="sbLogin" value="<?=_('Login')?>" class="btn btn-primary btn-block"/><br>
		<a href="/?signup=yes" class="btn btn-default btn-block"><?=_('Sign Up')?></a>
	</form>
	</div>

	<?php endif;?>

	<hr/>

	<div class="clearfix"></div>
    </div>
	<!-- /.clearfix -->
</div>