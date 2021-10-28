<?php require_once 'header.php';?>

	<br />

	<ul class="breadcrumb">
	<?php foreach ($breadcrumbs as $burl => $btitle): ?>
		<?php $isActive = end($breadcrumbs) == $btitle ? 'active' : '';?>
	    <li class="<?=$isActive?>">
	    	<?php if ($isActive != 'active') {?>
	    		<a href="<?=$burl?>"><?=$btitle?></a>
	    	<?php } else {?>
	    		<?=$btitle?>
	    	<?php }?>
	    </li>
	<?php endforeach;?>
		<li class="pull-right" style="margin-right: 20px;">
		<div class="btn-group">
			<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    			Filter By <span class="caret"></span>
  			</button>
			<ul class="dropdown-menu">
	            <li><a href="/auctions/<?=$category_slug?>/featured">Featured <?=$filter_category?></a></li>
	            <li><a href="/auctions/<?=$category_slug?>/latest">Latest <?=$filter_category?></a></li>
	            <li><a href="/auctions/<?=$category_slug?>/ending">Ending <?=$filter_category?></a></li>
	            <li><a href="/auctions/<?=$category_slug?>/hot">Hot <?=$filter_category?></a></li>
	            <li class="divider"></li><!-- /.divider -->
	            <li>
	            	<a href="/auctions/<?=$category_slug?>/latest?price=asc">Price ASC</a>
	            </li>
	            <li>
	            	<a href="/auctions/<?=$category_slug?>/latest?price=desc">Price DESC</a>
	            </li>
            </ul>
        </div>
		</li>
	</ul>

    <h2 class="type-title"><?=$category?> Auctions</h2>

    <div class="auctions-list">
    <?php if (count($listings)) {?>
        <ul>
            <?php foreach ($listings as $l): ?>
                <li>
                    <div class="thumbnail-placeholder">
                        <div class="expiry-placeholder">
                           <?=timespan($l->list_expires, time())?> left
                        </div>
                        <a href="<?=auction_slug($l)?>">
                            <img src="<?=get_first_image($l->listingID, 'THUMB')?>"
                                 data-toggle="tooltip" data-placement="bottom"
                                 title="<?=htmlspecialchars($l->listing_title)?>" style="max-height: 100%; width: auto;">
                        </a>
                    </div>
                    <div class="auction-box-bottom">
                        <h3>Buy it Now: <?=get_option('currency_symbol') ?><?=($l->bin > $l->starting_) ? $l->bin : number_format($l->starting_)?></h3>
                        <h4><?=get_option('currency_symbol') . number_format($l->starting_)?></h4>

                        <a href="<?=auction_slug($l)?>" data-toggle="tooltip" data-placement="right"
                           title="<?=$l->listing_title?>">
                            <?= substr($l->listing_title, 0, 49) ?>
                            <?= strlen($l->listing_title) > 50 ? '...' : ''; ?>
                        </a>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
        <div class="clearfix"></div>
        <!-- /.clearfix -->
        <?php } else {?>
            <div class="well">
                No auctions to show.
            </div><!-- /.well -->
        <?php }?>
    </div><!-- ./auctions-list (  LATEST ) -->

</div><!-- ./container -->


<?php require_once 'footer.php';?>