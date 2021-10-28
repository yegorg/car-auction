<?php require_once ('header.php'); ?>

<div class="col-xs-12">
	
<div class="page-header white-content">

	<div class="text-center">
	<h2>Select Payment Method</h2>
	<h3>All auctions are charged <?= get_option( 'currency_symbol' ) . get_option( 'listing_fee' ) ?></h3>

	<br />

	
	<?php if( get_option( 'paypal_enable', 'No' ) == 'Yes' ): ?>
	<a href="/payments" class="btn btn-warning">Pay with PayPal</a><br/>
	<?php endif; ?>

	<?php if( get_option( 'stripe_enable', 'No' ) == 'Yes' ): ?>
	<form action="/payments/stripe" method="POST">
	<script
	  src="https://checkout.stripe.com/checkout.js" class="stripe-button"
	  data-key="<?= get_option('stripe_public') ?>"
	  data-amount="<?= get_option('listing_fee')*100 ?>"
	  data-description="Listing Fee"
	  data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
	  data-locale="auto"
	  data-zip-code="true"
	  data-currency="<?= get_option('currency_code') ?>">
	</script>
	</form>
	<?php endif; ?>

	<br />
 
	</div><!-- /.col-xs-12 col-md-4 col-xs-offset-0 col-md-offset-3 -->
	</div>
	
</div>

</div>

<?php require_once ('footer.php'); ?>