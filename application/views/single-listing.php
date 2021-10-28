<?php require_once 'header.php';?>

<br />

<ul class="breadcrumb">
    <li><a href="<?=base_url()?>">Home</a></li>
    <li><a href="<?=base_url()?>auctions/all/latest">Auctions</a></li>
    <li><a href="<?=base_url()?>auctions/<?=$l->slug?>/latest"><?=$l->category?></a></li>
    <li class="active"><?=$l->listing_title?></li>
</ul>

<div class="white-content">
    <div class="row">
        <div class="col-xs-12 col-md-6">
        <a href="<?= get_first_image($l->listingID) ?>" class="gallery">
            <img src="<?=get_first_image($l->listingID)?>"  alt="" class="img-responsive">
        </a>
            <br />
            <div class="row">
            <?php foreach (array_slice($att, 1) as $p): ?>
                <div class="col-xs-3 small-thumb">
                <a href="<?=base_url() . 'uploads/' . $p->att_file?>" class="gallery">
                    <img src="<?=base_url() . 'uploads/small-' . $p->att_file?>" class="img-responsive"
                 alt="">
                 </a>
                </div>
                <!-- /.col-xs-3 -->
            <?php endforeach;?>
            </div>

            <h2>Bidding history</h2>
            <?php if ($all_bids->num_rows()): ?>
            <div class="table-responsive">
                <table class="table table-hover table-alt table-striped">
                    <thead>
                    <tr>
                        <th>Bidder</th>
                        <th>Amount</th>
                        <th>Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($all_bids->result() as $bid) {?>
                    <tr>
                        <td><a href="/users/profile/<?=$bid->username?>"><?=$bid->username?></a></td>
                        <td><?=get_option('currency_symbol')?><?=number_format($bid->amount)?></td>
                        <td>
                            <?=timespan(time(), $bid->bid_date)?> ago
                        </td>
                    </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
            <?php else: ?>
                No bids yet. Be the first!
            <?php endif;?>


            <h2>Auction Comments</h2>
            <div id="movID" style="display:none;"><?=$l->listingID;?></div>
            <?php if (is_user_logged_in()): ?>
                <?php
echo form_open('/listings/ajax_comment', ['class' => 'form', "id" => 'comment-form'], ['listID' => $l->listingID]);
echo form_textarea(['name' => 'comment', 'cols' => 24, 'rows' => 6,
	'class' => 'form-control required', 'placeholder' => 'Comment on this auction..']);
echo '<br/>';
echo form_submit('sbComment', _('Submit comment'), 'class="btn btn-warning"');
echo form_close();
?>
                <div id="comment_output"></div>

                <hr/>

            <?php else: ?>
                <?=_('Please <a href="/?login=yes">login</a> to comment')?>
            <?php endif;?>

            <?php if (isset($comments) AND count($comments)): ?>
            <ul class="user_comments">
                <?php
$i = 0;
foreach ($comments->result() as $c):
	$i++;
	$border = ($i % 2 == 1) ? '' : 'orange';
	?>

																																																				                                                                                        <li da-lastID="<?php echo $c->commID; ?>" class="<?=$border?>">
																																																				                                                                                            <a href="/users/profile/<?php echo url_title($l->username); ?>">
																																																				                                                                                                <?php if (!empty($l->photo) and file_exists('/uploads/' . $l->photo)): ?>
																																																				                                                                                                    <img src="/uploads/<?=$l->photo?>" alt="" width="48" height="48"
																																																				                                                                                                         style="float:left;padding:5px 10px 5px 0px;"/>
																																																				                                                                                                <?php else: ?>
                                <img src="/img/nophoto.jpg" alt="" width="48" height="48"
                                     style="float:left;padding:5px 10px 5px 0px;"/>
                            <?php endif;?>
                        </a>
                        <span class="comment_author"><b
                                    class="icon-user"></b> <?php echo '<a href="/users/profile/' . url_title($c->username) . '">' . $c->username . '</a>'; ?>
                            - <b class="icon-calendar"></b><em><?php echo date("jS F Y H:ia", $c->comm_date); ?></em></span>
                        <div class="comment_content"><?php echo nl2br(wordwrap($c->comment, 80, '<br/>', true)); ?></div>
                        <div style="clear:both;"></div>
                        <?php if ($owns_listing == 'yes') {?>
                            <a href="javascript:void(0);" class="remove_c btn btn-default btn-xs"
                               id="rem_<?=$c->commID;?>">Remove Comment</a>
                        <?php }?>
                    </li>
                    <hr/>

                <?php endforeach;?>
                <?php endif;?>

        </div><!-- main photo-->


        <div class="col-xs-12 col-md-6">
            <h3 class="listing-title"><?php echo $l->listing_title ?></h3>
            <!-- /.alert alert-warning -->

            <div class="listing-status">
                <?php if ($l->list_expires < time()): ?>

                <?php else: ?>

                <?php if( $l->sold == 'N' ) : ?>
                    <div class="live"><span class="glyphicon glyphicon-record"></span> LIVE</div>
                <?php else: ?>
                    <div class="sold"><span class="glyphicon glyphicon-ok-circle"></span> BIN ON <?= date('jS F Y', $l->sold_date)?></div>
                   <div class="date"> | Price <?= get_option('currency_symbol') . number_format($l->sold_price,0) ?></div> 
                <?php endif; ?>
                
                <?php if( $l->sold == 'N' ) : ?>
                <div class="date">|</div>
                <div class="date">
                    <?php echo date('jS F Y', $l->list_date) ?>
                    | <i class="glyphicon glyphicon-dashboard"></i> <?=timespan($l->list_expires, time())?>
                </div>
                <?php endif; ?>
                <div class="date">|</div>

                <?php endif;?>
                <div class="date"> By <a href="<?=base_url()?>users/profile/<?=$l->username?>"><?=$l->username?></a></div>
            </div>
            <!-- /.listing-status live -->

         <div class="sharing_buttons">

            <div class="addthis_toolbox addthis_default_style ">
            <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
            <a class="addthis_button_tweet"></a>
            <a class="addthis_button_pinterest_pinit"></a>
            <a class="addthis_counter addthis_pill_style"></a>
            </div>
            <script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js#pubid=xa-5092861a53f31e62"></script>
             
            </div>

            <?php if (isset($hide_bid)) {?>
                <div class="well">
                    <h3>Public Auction Closed</h3>
                </div>
            <?php } else {?>
                <div class="row">
                <div class="col-xs-12 col-md-8">
                    <div class="pull-left"><h3 class="text-error"><?=$last_bid;?></h3></div>
                    <div class="pull-right"><h3 class="text-info"><?=$bid_count . _(' bids');?></div>

                    <div class="clearfix"></div>
                    <!-- /.clearfix -->

                    <br />

                    <form method="post" action="/listings/bid/<?=$l->listingID;?>" class="form-horizontal">
                        <?php echo _("Enter ") . ' ' . $last_bid_plus . ' ' . _('or more'); ?>

                        <div class="input-group ">
                        <span class="input-group-addon"><?= get_option('currency_symbol') ?></span>    
                        <input type="number" step="any" value="<?=preg_replace('/[^\d+]/', '', $last_bid_plus)?>" class="form-control" name="bid_amount" required/>
                        </div>
                        <br/>
                        <input type="submit" name="sb_bid" value="<?php echo _('Place your Bid'); ?>"
                               class="btn btn-xlarge btn-block btn-primary"/>
                        <br/>
                    </form>

                    <a class="btn btn-default btn-xlarge btn-block" href="/listings/bin/<?= $l->listingID ?>-<?= create_slug( $l->listing_title ) ?>">
                    <?php echo _('Buy it now for : '); ?>
                    <?php 
                    $lastBidAmount = filter_var( $last_bid, FILTER_SANITIZE_NUMBER_INT );

                    $lastBidAmount = str_replace( array( ",", ".", "$", " " ), array( "","","","" ), $lastBidAmount );


                    if( (int)$lastBidAmount > $l->bin ) {
                        echo $last_bid;
                    }else{
                        echo get_option('currency_symbol') . $l->bin;
                    }
                    ?>

                    </a>

                </div><!-- /.col-xs-12 col-md-8 -->
                </div><!-- /.row -->

                <br />
                <h2>Description</h2>
                <?php echo nl2br($l->listing_description) ?>

            <?php }?>

        </div>
        <!-- /.col-xs-12 col-md-4 -->
    </div>




    </div><!-- ./listing-title -->
</div><!-- ./white-content -->

<?php require_once 'footer.php';?>