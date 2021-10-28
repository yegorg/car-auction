</div><!--container-->

<br/>
<div id="footer">
    <div class="container">
    
    <div class="row">

	    <div class="col-xs-6 col-md-9">
	    	<br />
			<ul class="footer-nav">
				<li><a href="<?php echo base_url(); ?>"><?=_('Home</a>') ?></li>
				<li><a href="<?php echo base_url(); ?>/home/tos"><?=_('Terms of Service') ?></a></li>
				<li><a href="<?= get_option('fb_url') ?>" target="_blank"><?=_('Facebook</a>') ?></li>
				<li><a href="<?= get_option('tw_url') ?>" target="_blank"><?=_('Twitter</a>') ?></li>
			</ul>
			<br />
			&copy; <?= date("Y") ?> All rights reserved
		</div>	<!-- ./nav -->

		<div class="col-xs-6 col-md-3">
			<br />
			<?php $CI =& get_instance(); ?>
			<?= $CI->Stats->listings_open(); ?> Auctions<br />
			<?= $CI->Stats->members_count(); ?> Registered Members
		</div><!-- right stats -->
		<div class="col-xs-12">
		Icons made by <a href="http://www.flaticon.com/authors/prosymbols" title="Prosymbols">Prosymbols</a> &amp; <a href="http://www.flaticon.com/authors/roundicons" title="Roundicons">Roundicons</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
		</div>

</div><!-- ./#footer -->
</body>
</html>