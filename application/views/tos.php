<?php require_once ('header.php'); ?>

<div class="span8" style="margin-left:0px;">
	
<div class="page-header white-content">
	<h1><?=_('Terms Of Service') ?></h1>
</div>

<div class="white-content">	
	<?php if(isset($tos) AND count($tos)) print $tos->tos; ?>
</div>

</div>


<?php require_once ('footer.php'); ?>