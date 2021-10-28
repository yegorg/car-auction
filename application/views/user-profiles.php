<?php require_once 'header.php';?>

<div class="col-xs-12 col-md-9">

<div class="page-header white-content">
	<?php if (isset($error)) {
	echo $error;
} else {
	if (!is_object($user)) {
		echo _('-User details could not be fetched or user does not exist');
	} else {
		?>

	<div class="pull-left">
	<?php if (!empty($user->photo)): ?>
		<br />
        <img src="/uploads/<?=$user->photo?>" alt="" width="48" height="48" style="margin:0 10px 0 0"/>
    <?php else: ?>
        <img src="/img/nophoto.jpg" alt="" width="48" height="48" style="float:left;padding:5px 10px 5px 0px;" />
    <?php endif;?>
    </div>
    <div class="pull-left">
	<h1><?php echo $user->username ?> <a href="/users/message/<?=$user->userID?>" style="font-size:16px;">(<?=_('Contact User')?>)</a></h1>
	</div>
	<div class="pull-right">
	<br />
	<h4><?php echo _("Bids made: ") . $tbids . _('<br/>Listings Started: ') . $tl; ?></h4>
	</div>
	<div style="clear:both;"></div>
	<?php echo ($user->about == '') ? '' : '<br/><div class="well">' . $user->about . '</div>'; ?>

</div>

<div class="white-content">
	<h2>User Started Auctions</h2>
</div>

<div class="white-content">
	<?php
if ($listings) {
			?>
		<?php
foreach ($listings as $l):
			?>

        <div class="row">
        <div class="col-xs-4">
            <a href="<?=auction_slug($l)?>">
            <img src="<?=get_first_image($l->listingID, 'THUMB')?>" class="img-responsive" alt="">
            </a>
        </div>
        <!-- /.col-xs-4 -->
        <div class="col-xs-8">
            <h3><a href="<?=auction_slug($l)?>"><?=$l->listing_title?></a></h3>
            <br /><span class="glyphicon glyphicon-time"></span>
                    <span class="muted">Expires in <?=(now() > $l->list_expires) ? 'Closed' : timespan(now(), $l->list_expires)?></span>

            <br/><br/>

            <?php
//get latest bid
			$last_bid = $this->db->query("(SELECT amount FROM bids WHERE bid_listing = " . $l->listingID . "
            								ORDER BY bidID DESC LIMIT 1)");
			$last_bid = $last_bid->row();

			printf("<span class='glyphicon glyphicon-tag'></span> Starting BID: $%.2f", $l->starting_);

			echo ' <span class="glyphicon glyphicon-repeat"></span> Current BID: ';
			echo (!$last_bid) ? 'No Bids' : printf("$%.2f", $last_bid->amount);

			printf(" <span class='glyphicon glyphicon-bookmark'></span> BIN: $%.2f</h5>", $l->bin);

			?>

        </div>
        </div><!--row -->

        <div style="clear:both;height:20px;"></div>
        <hr/>
        <?php
endforeach;
			?>
		<?php
} else {
			print _('This user did not create any listings');
		}
	}
}
?>
</div>

</div>

<?php require_once 'sidebar.php';?>

<?php require_once 'footer.php';?>