<?php require_once 'header.php';?>

<div class="col-xs-12 col-md-9">

<div class="page-header white-content">
	<h3><?=_('Offers Received');?>
</div>

<div class="white-content">

	<?php if (!isset($msg)) {
	?>
	<div class="table-responsive">
	<table class="table table-bordered table-hover table-striped">
	    <thead>
	        <tr>
	        	<th><?=_('THUMB')?>
	            <th><?=_('TO LISTING')?></th>
	            <th><?=_('FROM')?></th>
	            <th><?=_('DATE')?></th>
	            <th><?=_('AMOUNT')?></th>
	            <th>&nbsp;</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php
foreach ($bids as $b) {
		if (!isset($complete[$b->listingID])) {
			$complete[$b->listingID] = false;
		}

		echo '<tr>
							<td><a href="' . auction_slug($b) . '">
							<img src="' . get_first_image($b->listingID, 'THUMB') . '" width="100">
						   </a></td>
	                       <td><a href="' . auction_slug($b) . '">' . $b->listing_title . '</a></td>
	                       <td><a href="/users/profile/' . url_title($b->username) . '">' . $b->username . '</a></td>
	                       <td>' . date("jS F Y", $b->bid_date) . '</td>
	                       <td>' . get_option( 'currency_symbol' ) . number_format($b->amount,0) . '</td>
	                       <td>';

		if ($b->sold == 'N') {
			echo '<a href="/users/offers/reject/' . $b->bidID . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure you want to DELETE this bid?\')">Reject</a><br/>';
		} else {
			if (!$complete[$b->listingID]) {
				echo 'COMPLETE<br/>
                          ' . date("jS F Y", $b->sold_date);
				$complete[$b->listingID] = true;
			}
		}

		echo '</td>
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