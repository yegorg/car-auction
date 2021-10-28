<?php require_once ('header.php'); ?>

<div class="col-xs-12">
	
<div class="page-header white-content">
	<h1>Page not found! 404 Error.</h1>
	The page you are looking for was not found. 
	Go to <?php echo anchor(base_url(), 'Home'); ?> or <?php echo anchor(base_url() . 'auctions/new', 'Start an Auction'); ?>
</div>

</div>

<?php require_once ('footer.php'); ?>