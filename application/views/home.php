<?php require_once 'header.php';?>


</div><!-- ./close container to have it fluid -->
<div class="header-home hidden-xs">
    <div class="header-home-text">
        <div class="container">
            <div class="row">
                <div class="col-xs-2 col-xs-offset-1">
                   <!-- <div class="cash-image">
                        <img src="/images/cash.svg" alt="cash image" height="120" class="align-right"/>
                    </div> ./cash image -->
                </div><!-- ./col-xs-4 (cash img)-->
                <div class="col-xs-8">
                    <h1><?= get_option('homepage_heading', 'Set heading in admin') ?></h1>
                    <h2><?= get_option('homepage_subheading', 'Set subheading in admin') ?></h2>

                    <a class="btn btn-black btn-buy-homepage" href="/auctions/all/latest">Купить авто <i
                                class="glyphicon glyphicon-chevron-right yellow-text"></i></a>
                    
                </div><!-- ./col-xs-8  (homepage header text) -->

            </div><!-- ./row  -->
        </div><!-- ./container-->
    </div><!-- ./header-home-text -->
</div><!-- ./header-home -->

<div class="homepage-img">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2 col-xs-offset-0 text-center">
                <form method="GET" action="/auctions/all/latest" class="form-inline" id="search-form">
                    <input type="text" name="search_term" placeholder="Поиск в каталоге по марке, модели и др..."
                           class="form-control" size="80">
                    <input type="submit" name="sb_search" value="GO" class="btn btn-default form-control">
                </form>
            </div><!-- ./search form col -->
        </div><!-- ./row -->
    </div><!-- ./container -->
</div><!--homepage img-->


<div class="container">
    <br/>

    <h2>Новые аукционы</h2>
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
                        <h3>Buy it Now: <?=get_option('currency_symbol') ?>
                            <?php 
                            $startingAm = str_replace(array(","," ", "."), array("","","", ""), $l->starting); 
                            echo ($l->bin > $startingAm) ? $l->bin : $l->starting
                            ?>
                        </h3>
                        <h4><?=get_option('currency_symbol') . $l->starting?></h4>

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
                No auctions in database.
            </div><!-- /.well -->
        <?php }?>
    </div><!-- ./auctions-list (  LATEST ) -->

    <h2>Популярные аукционы</h2>
    <div class="auctions-list">
    <?php if (count($featured_listings)) {?>
        <ul>
            <?php foreach ($featured_listings as $l): ?>
                <li>
                    <div class="thumbnail-placeholder">
                        <div class="expiry-placeholder">
                           <?=timespan($l->list_expires, time())?> left
                        </div>
                        <a href="<?=auction_slug($l)?>">
                            <img src="<?=get_first_image($l->listingID, 'THUMB')?>"
                                 data-toggle="tooltip" data-placement="bottom"
                                 title="<?=htmlspecialchars($l->listing_title)?>">
                        </a>
                    </div>
                    <div class="auction-box-bottom">
                        <h3>Buy it Now: <?=get_option('currency_symbol') ?><?=($l->bin > $l->starting) ? $l->bin : $l->starting?></h3>
                        <h4><?=get_option('currency_symbol') . $l->starting?></h4>

                        <a href="<?=auction_slug($l)?>" data-toggle="tooltip" data-placement="right"
                           title="<?=$l->listing_title?>">
                        <?=$l->listing_title?>
                        </a>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
        <div class="clearfix"></div>
        <!-- /.clearfix -->
        <?php } else {?>
            <div class="well">
                No featured auctions in database.
            </div><!-- /.well -->
        <?php }?>
    </div><!-- ./auctions-list (  FEATURED ) -->
</div><!-- ./container -->


<?php require_once 'footer.php';?>