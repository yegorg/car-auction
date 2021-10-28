<?php require_once ('header.php'); ?>

<div class="col-xs-12">
	
<div class="page-header white-content">

	<div class="text-center">
	<h3>Enter <?= ucfirst($transferType) ?> Reference:</h3>
	<br />
	
	<?php if( $transferType == 'bank-transfer' ): ?>
	<h3>Bank transfer details are</h3>
	<div class="well">
	<?= nl2br(get_option( 'bank_info', 'Please configure bank details in admin panel' )) ?>
	</div><!-- /.well -->
	<?php endif; ?>

	<div class="row">
		<div class="col-xs-12 col-md-6 col-md-offset-3">
			<form method="POST">
				<textarea name="reference" rows="5" class="form-control" required="required"></textarea>
				<br/>
				<input type="submit" name="refSb" value="Save" class="btn btn-primary">
			</form>
		</div><!-- /.col-xs-12 col-md-6 col-md-offset-3 -->
	</div><!-- /.row -->

	<br />
 
	</div><!-- /.col-xs-12 col-md-4 col-xs-offset-0 col-md-offset-3 -->
	</div>
	
</div>

</div>

<?php require_once ('footer.php'); ?>