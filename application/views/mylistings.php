<?php require_once 'header.php';?>

<div class="col-xs-12 col-md-9">

<div class="white-content">
	<h3><?=_('My Listings')?>.</h3>
</div>

<div class="white-content">
	<?php
echo "<h4>" . _("You have started") . " " . $listings_count . " " . _(' auctions') . "</h4><hr />";

if (isset($_GET['added'])) {
	echo '<div class="alert alert-warning">Your listing was successfully added!</div>';
}

if (isset($_GET['added_featured'])) {
	echo '<div class="alert alert-warning">Your listing was successfully set as featured!</div>';
}

if ($listings_count) {
	?>

    <div class="table-responsive">
	<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th width="80"><?=('Thumb')?></th>
        <th width="300"><?=('Title')?></th>
        <th width="100"><?=('Price')?></th>
        <th width="120"><?=('Dates')?></th>
        <th><?=('Sold')?></th>
        <th><?=('Relist')?></th>
        <th><b class="icon-edit"></b></th>
    </tr>
    </thead>
        <?php

	foreach ($listings as $l):

		$featured_or_not = $l->featured == 'Y' ? 'Featured' : '<a href="/payments/setfeatured/' . $l->listingID . '" style="color:#ffffff;text-decoration:none;font-size:12px">' . _('Get Featured for ' . get_option(
			'currency_symbol') . get_option('featured_fee')) . '</a>';

		if ($l->featured == 'Y') {
			$featured_or_not = '<span class="label label-info">Featured</span>';
		} else {
			$featured_or_not = '<div class="btn-group">';
			$featured_or_not .= '<a href="javascript:void(0);" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span> ' . _('Get Featured for ' . get_option('currency_symbol') . get_option('featured_fee')) . '</a>';
			$featured_or_not .= '<ul class="dropdown-menu">';

			if (get_option('paypal_enable') == 'Yes') {
				$featured_or_not .= '<li><a href="/payments/setfeatured/' . $l->listingID . '" class="btn btn-xs btn-warning" style="margin-left:10px;width: 120px;">PayPal</a></li>';
			} // paypal btn if enabled

			if (get_option('stripe_enable') == 'Yes') {
				$featured_or_not .= '<li class="divider"></li><li>';

				$featured_or_not .= '<form action="/payments/stripefeatured" method="POST" style="margin-left:10px;">
																										                                          <script
																										                                            src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
																										                                            data-key="' . get_option('stripe_public') . '"
																										                                            data-amount="' . (get_option('featured_fee') * 100) . '"
																										                                            data-name="Featured Payment"
																					                                                                data-currency="' . get_option('currency_code') . '"
																										                                            data-description="Featured Fee">
																										                                          </script>
																										                                          <input type="hidden" name="listingID" value="' . $l->listingID . '" />
																										                                        </form>';

				$featured_or_not .= '</li>';
			} // stripe btn if enabled

			$featured_or_not .= '</ul></div>';

		}

		$sold = ($l->sold == 'N') ? 'No' : 'Yes';
		$sale_date = ($l->sold_date != null) ? $l->sold_date : 'N/A';

		echo '<tr>';
		echo '<td><a href="' . auction_slug($l) . '">
																			<img src="' . get_first_image($l->listingID, 'THUMB') . '" width="100"></a></td>';
		echo '<td>
																				             <a href="' . auction_slug($l) . '">
																				             ' . $l->listing_title . '</a>
																				             <br />
																				             ' . $featured_or_not . '
																				          </td>';
		echo '<td><h4>' . get_option('currency_symbol') . $l->bin . '</h4></td>';
		echo '<td>' . $l->list_date . '<br/>
															<i class="glyphicon glyphicon-dashboard"></i>' . timespan(strtotime($l->list_expires), time()) . '</td>';
		echo '<td><span class="badge">' . $sold . '</span><br />' . $sale_date . '</td>';
		echo '<td>' . sprintf($l->payLink, _('Relist')) . '</td>';
		echo '<td>' . sprintf($l->editl, _('Edit')) . '</td>';
		echo '</tr>';
	endforeach;
	?>
    <tbody>
	</tbody>

	</table>
    </div>
	<?php
} else {
	echo _("No listings for you.");
}
?>

</div>

</div>

<?php require_once 'sidebar.php';?>

<?php require_once 'footer.php';?>