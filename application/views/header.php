<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>
    <?php
    if (isset($seo_title)) {
    	echo $seo_title . ' - ' . get_option('seo_title');
    } else {
    	echo get_option('seo_title');
    }
    ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link href='https://fonts.googleapis.com/css?family=Oswald:400' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700">
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <link href="<?php echo base_url(); ?>bootstrap3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/featherlight.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/featherlight.gallery.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/sweetalert.css" />
    
    <script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="<?php echo base_url(); ?>bootstrap3/js/bootstrap.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js//jquery.validate.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.form.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.lettering.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>js/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jQuery.MultiFile.min.js
"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/detect_swipe/2.1.1/jquery.detect_swipe.min.js"></script>
    <script src="<?php echo base_url(); ?>js/featherlight.js"></script>
    <script src="<?php echo base_url(); ?>js/featherlight.gallery.js"></script>
    <script src="<?php echo base_url(); ?>js/sweetalert.min.js"></script>


    <script type="text/javascript" src="<?php echo base_url(); ?>js/ajax.js"></script>

	<!--[if gte IE 9]>
	  <style type="text/css">
	    .gradient {
	       filter: none;
	    }
	  </style>
	<![endif]-->

    <?php
if (isset($_GET['login'])) {
	?>
    <script>
    $(function() {
        $("#login").modal('show');
    });
    </script>
    <?php
}
?>
    <?php
if (isset($_GET['signup'])) {
	?>
    <script>
    $(function() {
        $("#join").modal('show');
    });
    </script>
    <?php
}
?>

    <?=get_option('analytics_code');?>


    <?php
if ($img = get_option('header_image')) {
	echo '<style>.homepage-img { background-image: url(uploads/' . $img . ') !important; }</style>';
}
?>

<?php
// show notification of new bid/message if this user is logged in and has either of them
if( $notifications = get_user_notifications(  ) ) {
    echo $notifications;
}
?>


</head>
<body>

<!-- modal login -->
<div id="login" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog white-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><i class="icon icon-user"></i> Login</h3>
  </div><!-- modal-header -->
  <div class="modal-body">
    <?php if (isset($login_message)) {
	echo $login_message;
}
?>
    <form method="post" action="/users/login" class="form" id="login-form">
        <input type="text" name="uname" placeholder="username" class="form-control" /><br/>
        <input type="password" name="upwd" placeholder="****" class="form-control" /><br/>
        <input type="submit" name="sbLogin" value="<?=_('Login')?>" class="btn btn-black"/>
        <a href="/home/lostpassword" class="btn btn-default">Lost Password</a>
        <br /><br />
        Don't have an Account? <a href="/?signup=yes">Create one</a>
    </form>
    <br />
    <div id="login_output_div"></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div><!-- .modal dialog -->
</div><!-- .modal login -->

<!-- modal signup -->
<div id="join" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
<div class="modal-dialog white-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><i class="icon icon-lock"></i> Signup</h3>
  </div>
  <div class="modal-body">
    <form method="post" action="/users/ajax_join" id="signup-form" accept-charset="UTF-8">
        <label>
            <?=_('Username')?>:
        </label>
        <input type="text" name="username" placeholder="username" class="required form-control"/>

        <br/>

        <label>
            <?=_('Email')?>:
        </label>
        <input type="email" name="email" placeholder="email" class="required form-control" />

        <br/>

        <label>
            <?=_('Password')?>:
        </label>
        <input type="password" name="password" placeholder="****" class="required form-control" />

        <br/>

        <input type="submit" name="sb_signup" value="<?=_('Join Now')?>" class="btn btn-black"/>

    </form>

    <br /><br />
        Already have an Account? <a href="/?login=yes">Login</a>

    <br />
    <div id="signup_output_div"></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
</div>

<div class="over-top">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <?= get_option( 'top_text', '<span style="color: #fed135;font-weight: 700;">#1</span>
                The best auction website - Pay only when you sell' ) ?>
            </div><!-- ./ left over-top -->
        </div>
    </div>
</div>

<div class="top <?=!is_home() ? 'top-inner' : ''?>">
<div class="container">
<nav class="navbar">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="/" class="navbar-brand visible-xs">
        Navigation
      </a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <div class="site-title visible-lg pull-left-lg">
        <a href="/">
            <?php if (get_option('site_logo')): ?>
                <img src="<?=base_url()?>uploads/<?=get_option('site_logo')?>" alt="phpauctions" class="logo"/>
            <?php else: ?>
                <img src="<?=base_url()?>images/auction.svg" alt="phpauctions logo" class="logo"/>
            <?php endif;?>
            <h2 class="logo"><?php echo (get_option('website_title')) ? get_option('website_title') : 'Авто аукцион'; ?></h2>
        </a>
    </div>
      <ul class="nav navbar-nav pull-right-lg">
         <li>
            <a href="/"><?=_('Главная')?></a>
        </li>
        <li class="dropdown">
            <a href="/auctions/all" class="dropdown-toggle" data-toggle="dropdown"><?=_('Аукционы')?><b class="caret"></b></a>
            <ul class="dropdown-menu">
            <li><a href="/auctions/all/featured">Популярные</a></li>
            <li class="divider"></li>
            <li><a href="/auctions/all/latest">Последние</a></li>
            <li><a href="/auctions/all/ending">Скоро заканчиваются</a></li>
            <li><a href="/auctions/all/hot">Горячие аукционы</a></li>
            <li class="divider"></li>
            <?php foreach (get_categories() as $c): ?>
                <li>
                    <a href="/auctions/<?=$c->slug?>/latest"><?=$c->category?></a>
                </li>
            <?php endforeach;?>
            </ul>
        </li>
        <li><a href="/users/newlisting"><b class="glyphicon glyphicon-bullhorn"></b> Start an Auction</a></li>
        <?php if (is_user_logged_in()): ?>
             <li>
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                Мой аккаунт (<?=get_balance()?>)
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><?php echo anchor(base_url() . 'users', _('<i class="glyphicon glyphicon-chevron-right"></i> My Profile')); ?></li>
                <li><?php echo anchor(base_url() . 'users/balance', _('<i class="glyphicon glyphicon-chevron-right"></i> My Balance')); ?></li>
                <li><?php echo anchor(base_url() . 'users/mylistings', _('<i class="glyphicon glyphicon-chevron-right"></i> My Listings')); ?></li>
                <li><?php echo anchor(base_url() . 'users/inbox', _('<i class="glyphicon glyphicon-chevron-right"></i> Messages')); ?></li>
                <li><?php echo anchor(base_url() . 'users/bids', _('<i class="glyphicon glyphicon-chevron-right"></i> Bids Made')); ?></li>
                <li><?php echo anchor(base_url() . 'users/offers', _('<i class="glyphicon glyphicon-chevron-right"></i> Offers Received')); ?></li>
                <li><?php echo anchor(base_url() . 'users/logout', _('<i class="glyphicon glyphicon-chevron-right"></i> Logout')); ?></li>
              </ul>
            </li>
        <?php else: ?>
            <li><a href="#login" role="button" data-toggle="modal"><?=_('Вход')?></a></li>
            <li><a href="#join" role="button" data-toggle="modal"><?=_('Регистрация')?></a></li>
        <?php endif;?>
        <li><a href="/contact">Контакты</a></li>
        </ul>
        <div class="clearfix"></div>
    </div><!-- /.navbar-collapse -->
</nav>
</div><!-- container top head (logo + nav) -->
</div><!-- ./top -->


<div class="container">