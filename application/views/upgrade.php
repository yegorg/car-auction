<?php require_once ('header.php'); ?>

<div class="col-xs-12">
	
<div class="page-header white-content">
	<h1>Upgrade from v1.3 to v1.4</h1>
	
	<strong><span class="label label-danger">Attention</span></strong> 
	If you have done modifications to any of the following files, they'll be OVERWRITTEN:
	<br/><br/>
	<h4>Step1. Copy and overwrite the files from folder <strong>upgrade-files/*</strong> to the following</h4>
	<br/><br/>

<pre>
Controllers 
- application/controllers/cron.php 
- application/controllers/payments.php
- application/controllers/users.php
- application/controllers/home.php

Views
- application/views/sidebar.php
- application/views/user-won.php
- application/views/upgrade.php
</pre>

	<h4>Step2. Configure <strong>CronJob to Run Every 5 minutes</strong> with the following command:</h4><br>
<pre>
<?php echo PHP_BINDIR; ?>/php <?php echo getcwd(); ?>/index.php cron pickWinners >/dev/null
</pre>
You can see a lot of tutorials on youtube for example on 
<a href="http://www.youtube.com/watch?v=bmBjg1nD5yA" target="_blank">how to setup a cronjob in cpanel.</a>
	<br><br>

	<h4>Step3. Upgrade database by clicking the button below</strong></h4>
	<br/>
	<a href="?confirm=true" class="btn btn-danger">Upgrade database</a>

</div>

</div>

<?php require_once ('footer.php'); ?>