<?php require_once 'header.php';?>

<div class="col-xs-12 col-md-9">

<div class="page-header white-content">
	<h3><?=_('Auctions you have won');?></h3>
</div>

<div class="white-content">
	<?php 
	if( isset( $_GET[ 'justpaid' ] ) )  {
		echo '<div class="alert alert-success">Payment successfully recorded.</div>';
	}
	?>

	<?php if (!isset($msg) OR empty( $msg )) {
	?>
	<div class="table-responsive">
	<table class="table table-bordered table-hover table-striped">
	    <thead>
	        <tr>
	        	<th><?=_('THUMB')?>
	            <th><?=_('AUCTION')?></th>
	            <th><?=_('DATE')?></th>
	            <th><?=_('AMOUNT')?></th>
	            <th>&nbsp;</th>
	        </tr>
	    </thead>
	    <tbody>
	     <?php
		foreach ($auctionsWon->result() as $aw) {

		$payLink = '';

		if( $aw->isPaid == 'Yes' ) {
			$payLink = '<a href="'.$aw->downloadUrl.'" class="btn btn-danger" target="_blank">Download</a>';
		}else{

			if( get_option( 'paypal_enable', 'No' ) == 'Yes' ) {
				$payLink .= '<a href="/payments/auction_payments_paypal?listing='.$aw->listingID.'" class="btn btn-warning">Pay Via PayPal</a><br>';
			}

			if( get_option( 'stripe_enable', 'No' ) == 'Yes' ) {
			$payLink .= '<form action="/payments/auction_payments_stripe/stripePayment" method="POST">
						<script
						  src="https://checkout.stripe.com/checkout.js" class="stripe-button"
						  data-key="'.get_option('stripe_public').'"
						  data-amount="'.(int)($aw->wonAmount*100).'"
						  data-description="Auction Won #'.$aw->listingID.'"
						  data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
						  data-locale="auto"
						  data-zip-code="true"
						  data-currency="'.get_option('currency_code').'">
						</script>
						<input type="hidden" name="listingID" value="'.$aw->listingID.'">
						</form>';
			}

		}

		echo '<tr>
						   <td><a href="' . auction_slug($aw) . '">
							<img src="' . get_first_image($aw->listingID, 'THUMB') . '" width="100">
						   </a></td>
	                       <td><a href="' . auction_slug($aw) . '">' . $aw->listing_title . '</a></td>
	                       <td>' . date("jS F Y", $aw->list_expires) . '</td>
	                       <td><span class="badge">'.$aw->wonAmount.get_option('currency_symbol').'</td>
	                       <td>'.$payLink.'</td>
	                   </tr>';

	}
	?>
	    </tbody>
	</table>
	</div>
	<?php } else {echo $msg;}?>

</div>

</div>

<?php require_once 'sidebar.php';?>

<?php require_once 'footer.php';?>