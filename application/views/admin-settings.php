<?php require_once ('header.php'); ?>


<div class="page-header white-content">
	<h1>Admin -> Payment Settings</h1>
	<?php require_once 'admin-menu.php'; ?>
</div><!-- ./page-header white-content -->

<form method="post" action="" class="form form-horizontal">

<div class="white-content">
<div class="row">
	<div class="col-xs-4 col-md-3">
	<dl>
		<dt>Success Percentage <br/><small><span class="text-muted">% of amount you earn from each sale</span></small></dt>
		<dd>
		<div class="input-group">
			<input type="number" step="1" name="sitefee" required="required" value="<?= get_option( 'sitefee', 1 ) ?>" class="form-control"><span class="input-group-addon">%</span>
		</div>
		</dd>
	</dl>
	</div><!-- /.col-xs-6 col-md-3 -->
	
	<div class="col-xs-4 col-md-3">
	<dl>
		<dt>Payouts are sent by you
		<br />
		<small>
		<span class="text-muted">when you send users pending payments</span>
		</small>
		</dt>
		<dd>
			<input class="form-control" type="text" name="payouts_period" value="<?= get_option('payouts_period', 'Every Friday') ?>">
		</dd>
	</dl>
	</div><!-- /.col-xs-6 col-md-3 -->

	<div class="col-xs-4 col-md-3">
	<dl>
		<dt><sup style="color:orange">new</sup> Currency ISO Code
		<br />
		<small>
		<span class="text-muted"><a href="https://www.xe.com/iso4217.php" target="_blank">See ISO Code List</a></span>
		</small>
		</dt>
		<dd>
			<input class="form-control" type="text" name="currency_code" value="<?= get_option('currency_code') ?>">
		</dd>
	</dl>
	</div><!-- /.col-xs-6 col-md-3 -->

	<div class="col-xs-4 col-md-3">
	<dl>
		<dt><sup style="color:orange">new</sup> Currency Symbol
		<br />
		<small>
		<span class="text-muted">enter currency symbol</span>
		</small>
		</dt>
		<dd>
			<input class="form-control" type="text" name="currency_symbol" value="<?= get_option('currency_symbol') ?>">
		</dd>
	</dl>
	</div><!-- /.col-xs-6 col-md-3 -->

</div><!-- /.row -->
</div><!-- ./white-content -->

<div class="white-content">

	<?php if(isset($form_message)) print $form_message; ?>
	
	
	<div class="row">
	<div class="col-xs-12 col-md-6">
		
		<dl>
			<dt><label><sup style="color:orange">new</sup> Listing Duration:</label></dt>
				<dd>
					<select name="listing_duration_days">
						<?php for( $i = 1; $i<= 31; $i++ ): ?>
							<option value="<?= $i ?>" <?php if($i == $days) echo 'selected' ?>><?= $i ?></option>
						<?php endfor;?>
					</select>
					<select name="listing_duration_string">
						<option value="Days" <?php if('Days' == $ds) echo 'selected' ?>>Days</option>
						<option value="Months" <?php if('Months' == $ds) echo 'selected' ?>>Months</option>
						<option value="Years" <?php if('Years' == $ds OR 'Year' == $ds) echo 'selected' ?>>Years</option>
					</select>
				</dd>
			<dt><label>Listing Fee:</label></dt><dd> <input type="text" name="listing_fee" value="<?=get_option('listing_fee')?>" class="form-control"/></dd>
			<dt><label>Featured Fee:</label></dt><dd> <input type="text" name="featured_fee" value="<?=get_option('featured_fee')?>" class="form-control"/></dd>
			<dt><label>PayPal Email:</label></dt><dd> <input type="text" name="paypal_email" value="<?=get_option('paypal_email')?>" class="form-control"/></dd>
			<dt><label>Enable Paypal:</label></dt>
			<dd> 
				<input type="radio" name="paypal_enable" value="Yes" <?=get_option('paypal_enable') == 'Yes' ? 'checked' : ''; ?>/> Yes 
				<input type="radio" name="paypal_enable" value="No" <?=get_option('paypal_enable') == 'No' ? 'checked' : ''; ?>/> No	
			</dd>
			<dt>&nbsp;</dt><dd><input type="submit" name="sb" value="Update Payment Settings" class="btn btn-green"/></dd>
			
		</dl>
		
	</div><!-- left side form -->
	<div class="col-xs-12 col-md-6">
		<dl>
			<dt><label>Stripe Private Key:</label></dt><dd> <input type="text" name="stripe_private" value="<?=get_option('stripe_private')?>" class="form-control"/></dd>
			<dt><label>Stripe Public Key:</label></dt><dd> <input type="text" name="stripe_public" value="<?=get_option('stripe_public')?>" class="form-control"/></dd>
			<dt><label>Enable Stripe:</label></dt>
				<dd>
				<input type="radio" name="stripe_enable" value="Yes" <?=get_option('stripe_enable') == 'Yes' ? 'checked' : ''; ?>/> Yes 
				<input type="radio" name="stripe_enable" value="No" <?=get_option('stripe_enable') == 'No' ? 'checked' : ''; ?>/> No
				</dd>
		</dl>

		<dl>
			<dt><sup style="color:orange">new</sup> Enable Bank Transfer?</dt>
			<dd>
				<input type="radio" name="bank_enable" value="Yes" <?=get_option('bank_enable') == 'Yes' ? 'checked' : ''; ?>/> Yes 
				<input type="radio" name="bank_enable" value="No" <?=get_option('bank_enable') == 'No' ? 'checked' : ''; ?>/> No
			</dd>

			<dt><sup style="color:orange">new</sup> Enable Cash Payout?</dt>
			<dd>
				<input type="radio" name="cash_enable" value="Yes" <?=get_option('cash_enable') == 'Yes' ? 'checked' : ''; ?>/> Yes 
				<input type="radio" name="cash_enable" value="No" <?=get_option('cash_enable') == 'No' ? 'checked' : ''; ?>/> No
			</dd>

			<dt><sup style="color:orange">new</sup> Bank Transfer Info <small><span class="text-muted">account info, bank name, etc so users will transfer money to it</span></small></dt>
			<textarea class="form-control" rows="4" name="bank_info"><?= get_option('bank_info') ?></textarea>
		</dl>

	</div><!-- right side form -->
	</div><!-- ./row -->
	
</div><!-- ./white-content -->
</form><!-- ./form -->

<?php require_once ('footer.php'); ?>