<?php
require_once 'header.php';
?>



	<div class="page-header white-content">
		<h1>Admin -> Listings</h1>
	</div>

	<div class="white-content">
		<?php require_once 'admin-menu.php';?>

		<?php if (isset($message)) {
	echo $message;
}
?>

		<?php if (count($listings)): ?>
		<div class="alert alert-warning"><?php echo count($listings) . ' total listings'; ?></div>

		<div class="table-responsive">
		<table class="table table-bordered table-striped" id="dataTbl">
			<thead>
				<tr>
					<th>Thumb</th>
					<th>Title</th>
					<th>Featured</th>
					<th>Status</th>
					<th>Dates</th>
                    <th>User</th>
					<th style="width:80px;">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($listings as $l): ?>
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
					    <br />
					    <small><span class="badge"><?=$l->tBids?></span> Bid <?=$l->tBids > 1 ? 's' : ''?></small>
					</td>
					<td><?=$l->featured == "N" ? '<span class="label label-default">No</span> <br/><a href="admin?make_featured=' . $l->listingID . '">Make Featured</a>' : '<span class="label label-success">Yes</span> <br/><a href="admin?disable_featured=' . $l->listingID . '">Disable Featured</a>';?></td>
					<td><?=($l->listing_status == 'inactive') ? 'Not Paid<br/>Inactive' : 'Active/Paid';?></td>
					<td>
						<?=date("jS F Y", $l->list_date)?><br />
						<?php if( $l->list_expires < time() ) {
							echo '<small>Expired</small>'; 
						}else{ ?>
						<small>Expires in <?=timespan($l->list_expires, time())?></small>
						<?php } ?>
					</td>
					<td><a href="/users/profile/<?=$l->username?>" target="_blank">
					<?=$l->username . '</a> <br/> ' . long2ip($l->ip)?></td>
					<td>
					    <?php if ($l->listing_status == 'inactive') {
	echo anchor('/admin/index/approve/' . $l->listingID, ($l->listing_status == 'inactive') ? 'Manually Activate' : '&nbsp;', ($l->listing_status == 'inactive') ? array("class" => "btn btn-xs btn-danger") : '');
}
?>
					    <br />
					    <a href="/admin/index/remove/<?=$l->listingID;?>" class="btn btn-xs btn-default" onclick="return confirm('Are you sure you want to delete this listing?')">Remove Listing</a>
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