<?php
require_once 'header.php';
?>



<div class="page-header white-content">
	<h1>Admin -> Transactions</h1>
	<div class="well">
	When clearing out payments the 1st step is to generate a PayPal MassPay File!<br />
	After that, pay your users and click on "Set All Cleared" to update status to "Cleared".
	</div>
	<a href="/admin/generate_mass_pay" class="btn btn-black" target="_blank">Generate Mass Pay</a>
	<a href="/admin/tx?all_cleared=true" class="btn btn-default" onclick="return confirm('Are you sure? This will mark all pending payments to cleared. Make sure you have generated the mass pay file!')">Set All Cleared</a>
</div>

<div class="white-content">
	<?php require_once 'admin-menu.php';?>

	<?php if (isset($msg)) { echo $msg; }?>

<?php if (count($transactions)): ?>
<div class="alert alert-warning"><?php echo count($transactions) . ' total transactions'; ?></div>

<div class="table-responsive">
<table class="table table-bordered table-striped" id="dataTbl">
<thead>
<tr>
	<th class="col-md-1">Thumb</th>
	<th class="col-md-2">Title</th>
	<th class="col-md-1">Status</th>
	<th class="col-md-1">Ref</th>
	<th class="col-md-1">Seller</th>
	<th class="col-md-1">Buyer</th>
	<th class="col-md-2">Date</th>
	<th class="col-md-2">Amount</th>
    <th class="col-md-1">Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($transactions as $l): ?>
<tr>
	<td>
	    <a href="<?=auction_slug($l)?>" target="_blank">
	    	<img src="<?=get_first_image($l->listingID, 'THUMB')?>" width="100">
	    </a>
	</td>
	<td>
		<a href="<?=auction_slug($l)?>" target="_blank">
	    	<?=$l->listing_title?>
	    </a>
	</td>
	<td><?= ($l->txStatus == 'Pending') ? 'Sold<br/><span class="label label-default">Seller TBP</span>': '<span class="label label-success">'.$l->txStatus.'</span>'; ?></td>
	<td><b>Type:</b> <?=$l->paidwith ?><br/><b>Ref:</b> <?= is_null( $l->ref ) ? '--' : $l->ref; ?></td>
	<td>
		<a href="/users/profile/<?= $l->sellerUsername ?>"><?= $l->sellerUsername ?></a>
		<br />
		<small>
			<a href="mailto:<?= $l->sellerEmail ?>" class="a-underline"><?= $l->sellerEmail ?></a><br/>
			<a href="/users/message/<?= $l->sellerID ?>" class="a-underline">Message</a>
		</small>
	</td>
	<td>
		<a href="/users/profile/<?= $l->buyerUsername ?>"><?= $l->buyerUsername ?></a>
		<br />
		<small>
			<a href="mailto:<?= $l->buyerEmail ?>" class="a-underline"><?= $l->buyerEmail ?></a><br/> 
			<a href="/users/message/<?= $l->buyerID ?>" class="a-underline">Message</a>
		</small>
	</td>
	<td>
		<?= date( 'jS F Y H:ia', $l->txDate ) ?>
	</td>
	<td>
		Total: <?= get_option( 'currency_symbol' ) . number_format( $l->originalAmount ) ?> <br />
		User: <?= get_option( 'currency_symbol' ) . number_format( $l->amount ) ?>
		<br />
		<?= str_repeat('-', 10) ?><br />
		Fee Earned: <?= get_option( 'currency_symbol' ) . number_format( $l->originalAmount-$l->amount ) ?>
	</td>
	<td>
	    <br />
	    <a href="/admin/tx?remove=<?=$l->txID;?>" class="btn btn-xs btn-default" onclick="return confirm('Are you sure you want to delete this TRANSACTION?')">Remove</a>
    </td>
</tr>

<?php endforeach;?>
</tbody>
</table>
</div>

<?php endif;?>
</div>


<?php
require_once 'footer.php';
?>