<?php require_once 'header.php';?>

<div class="col-xs-12 col-md-9">

<div class="page-header white-content">
	<h3><?=_('Bids you made');?></h3>
</div>

<div class="white-content">
	<?php if (!isset($msg)) {
	?>
	<div class="table-responsive">
	<table class="table table-bordered table-hover table-striped">
	    <thead>
	        <tr>
	        	<th><?=_('THUMB')?>
	            <th><?=_('AUCTION')?></th>
	            <th><?=_('OWNER')?></th>
	            <th><?=_('DATE')?></th>
	            <th><?=_('AMOUNT')?></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php
foreach ($bids as $b) {

		echo '<tr>
						   <td><a href="' . auction_slug($b) . '">
							<img src="' . get_first_image($b->listingID, 'THUMB') . '" width="100">
						   </a></td>
	                       <td><a href="' . auction_slug($b) . '">' . $b->listing_title . '</a></td>
	                       <td><a href="/users/profile/' . url_title($b->username) . '">' . $b->username . '</a></td>
	                       <td>' . date("jS F Y", $b->bid_date) . '</td>
	                       <td><span class="badge">'.$b->bid_type.'</span><br/>' . get_option('currency_symbol') . number_format($b->amount, 0) . '</td>
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