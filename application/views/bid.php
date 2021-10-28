<?php require_once ('header.php'); ?>

<div class="col-xs-8">
	
<div class="page-header white-content">
	<h1><?= _('Your Bid') ?>.</h1>
</div>

<div class="white-content">
	<div class="well">
	    <?php 
	    if(empty($message)) {
	       echo _('You are about to submit a bid. Please make sure you have the funds backing up this bid or legal action 
                    might be taken against you by our website and/or the seller.');
        ?>
            <br/><br/>
            <form method="post" action="/listings/bid/<?=$listingID; ?>/confirm" class="form-horizontal">
                <input type="hidden" value="<?=$bid_amount?>" class="input-medium" name="bid_amount"/>
                <input type="submit" name="sb_bid" value="<?php echo _('I agree and I confirm my BID'); ?>" class="btn btn-medium btn-green" />
            </form>
        <?php    
	    }else{
	       echo _($message);
	    }    
	    ?>
	</div>
	
</div>

</div>

<?php require_once 'sidebar.php'; ?>

<?php require_once ('footer.php'); ?>