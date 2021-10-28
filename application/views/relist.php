<?php require_once 'header.php';?>

<div class="col-xs-12 col-md-8">

<div class="page-header white-content">
	<h1>Relisting Payment.</h1>
</div>

<div class="white-content">
	<h3>Choose payment method:</h3>

	<?php if (get_option('paypal_enable') == "Yes"): ?>
	<p>
		<a href="/payments/index" class="btn btn-large btn-warning" style="margin-left: 10px;">PayPal</a>
	</p>
	<?php endif;?>

	<?php if (get_option('stripe_enable') == "Yes"): ?>
	<form action="/payments/stripe" method="POST" style="margin-left:10px;">
	  <script
	    src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
	    data-key="<?=get_option('stripe_public')?>"
	    data-amount="<?=get_option('listing_fee') * 100?>"
	    data-name="Payment"
	    data-currency="<?=get_option('currency_code')?>"
	    data-description="Listing Fee">
	  </script>
	  <input type="hidden" name="listingID" value="<?php echo $listingID ?>" />
	</form>
	<?php endif;?>

</div>

</div>

<?php require_once 'sidebar.php';?>

<?php require_once 'footer.php';?>