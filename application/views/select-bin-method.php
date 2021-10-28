<?php require_once ('header.php'); ?>

<div class="col-xs-12">
	
<div class="page-header white-content">

	<div class="text-center">
	<h2>Select Payment Method</h2>
	<h3>You're Buying <a href="<?= auction_slug($listing) ?>"><?= $listing->listing_title ?></a></h3>
	<h4><?= get_option( 'currency_symbol' ) . number_format($binAmount,0) ?></h3>
	<br />

	
	<?php if( get_option( 'paypal_enable', 'No' ) == 'Yes' ): ?>
	<a href="/listings/bin/<?= $listing->listingID ?>-<?= create_slug($listing->listing_title) ?>?setbin=paypal" class="btn btn-warning">Pay with PayPal</a><br/>
	<?php endif; ?>

	<?php if( get_option( 'stripe_enable', 'No' ) == 'Yes' ): ?>
	<form action="/payments/binStripe" method="POST">
	<script
	  src="https://checkout.stripe.com/checkout.js" class="stripe-button"
	  data-key="<?= get_option('stripe_public') ?>"
	  data-amount="<?= (int)$binAmount*100 ?>"
	  data-description="Auction BIN #<?= $listing->listingID ?>"
	  data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
	  data-locale="auto"
	  data-zip-code="true"
	  data-currency="<?= get_option('currency_code') ?>">
	</script>
	<input type="hidden" name="binListing" value="<?= $listing->listingID ?>">
	</form>
	<?php endif; ?>

	<br/>
	<?php if( get_option( 'bank_enable', 'No' ) == 'Yes' ): ?>
	<a href="/listings/manualpayment?type=bank-transfer&listing=<?= $listing->listingID ?>" class="btn btn-primary">Bank Transfer</a>
	<?php endif; ?>

	<?php if( get_option( 'cash_enable', 'No' ) == 'Yes' ): ?>
	<br/>
	<a href="/listings/manualpayment?type=cash&listing=<?= $listing->listingID ?>" class="btn btn-info">Cash PayOut</a>
	<?php endif; ?>
 
	</div><!-- /.col-xs-12 col-md-4 col-xs-offset-0 col-md-offset-3 -->
	</div>
	
</div>

</div>

<?php require_once ('footer.php'); ?>