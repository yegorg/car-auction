<?php require_once 'header.php';?>

<div class="col-xs-12 col-md-9">

<div class="page-header white-content">
	<h3><?=_('My Balance');?> - <?=get_balance()?></h3>
	<span class="badge">Payouts</span> We will clear every outstanding balance by PayPal every week.
</div>

<div class="white-content">
	<form method="POST" class="form-inline">
		PayPal Email <small>(where you get paid)</small>
		<input type="text" name="payment_email" class="form-control" value="<?=$payout_email?>" required="required">
		<input type="submit" name="sb_save_payment_email" class="form-control btn btn-default" value="Save Payout Email">
	</form>
</div>

<div class="white-content">
	<?php
if (isset($msg)) {
	echo $msg;
}
?>

	<?php if (!count($tx)): ?>
	<div class="well">There are no auctions with status sold to show.</div>
	<?php endif;?>

	<div class="table-responsive">
	<span class="badge">Scheduled TBP</span> - this means the bidder paid us and you'll also get paid next processing round.
	<br /><br />

	<table class="table table-bordered table-hover table-striped">
	    <thead>
	        <tr>
	        	<th><?=_('ID')?>
	            <th><?=_('AUCTION')?></th>
	            <th><?=_('BUYER')?></th>
	            <th><?=_('DATE')?></th>
	            <th><?=_('AMOUNT')?></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php
foreach ($tx as $b) {

	$status = ($b->txStatus == 'Pending') ? 'Scheduled TBP' : 'You were paid';

	echo '<tr>
		   <td><a href="' . auction_slug($b) . '">
			<img src="' . get_first_image($b->listingID, 'THUMB') . '" width="100">
		   </a></td>
		   <td><a href="' . auction_slug($b) . '">' . $b->listing_title . '</a></td>
		   <td>
		   		<a href="/users/profile/' . url_title($b->username) . '">' . $b->username . '</a><br />
		   		<small>
		   		<a class="a-underline" href="/users/message/' . $b->userID . '">Send Message</a> |
		   		<a class="a-underline" href="mailto:' . $b->email . '">' . $b->email . '</a></small>
		   </td>
		   <td>' . date("jS F Y", $b->txDate) . '</td>
		   <td>
		   		' . get_option('currency_symbol') . number_format($b->amount, 0) . '<br/>
		   		<span class="badge">' . $status . '</span>
		   </td>
		</tr>';
}
?>
	    </tbody>
	</table>
	</div>

</div>

</div>

<?php require_once 'sidebar.php';?>

<?php require_once 'footer.php';?>