<?php require_once ('header.php'); ?>


<div class="page-header white-content">
	<h1>Admin -> Configuration</h1>
	<?php require_once 'admin-menu.php'; ?>
</div>

<div class="white-content">

	<?php if(isset($form_message)) print $form_message; ?>
	
	<form method="post" action="" class="form form-horizontal" enctype="multipart/form-data">
	<dl>
		<dt><label>Website Title: <span class="muted">appears on header</span> :</label></dt><dd> <input type="text" name="website_title" value="<?= get_option('website_title') ?>" class="form-control"/></dd>
		<dt><label><sup style="color:orange">new</sup> Top Text: <span class="muted">appears above header</span></label></dt><dd> <textarea name="top_text" rows="5" class="form-control"><?= get_option('top_text', '<span style="color: #fed135;font-weight: 700;">#1</span>
                The best auction website - Pay only when you sell') ?></textarea></dd>
        <dt><label>Website Title: <span class="muted">appears on header</span> :</label></dt><dd> <input type="text" name="website_title" value="<?= get_option('website_title') ?>" class="form-control"/></dd>
		
		<dt><label><sup style="color:orange">new</sup> Homepage Heading:</label></dt><dd> <textarea name="homepage_heading" rows="5" class="form-control"><?= get_option('homepage_heading') ?></textarea></dd>

        <dt><label><sup style="color:orange">new</sup> Homepage SubHeading:</label></dt><dd> <textarea name="homepage_subheading" rows="5" class="form-control"><?= get_option('homepage_subheading') ?></textarea></dd>

		<dt><label>Analytics Code: <span class="muted">(separated by comma)</span></label></dt><dd> <textarea name="analytics_code" rows="5" class="form-control"><?= get_option('analytics_code') ?></textarea></dd>
		<dt><label>Contact Email :</label></dt><dd> <input type="text" name="contact_email" value="<?=get_option('contact_email')?>" class="form-control"/></dd>
		<dt><label>Facebook URL :</label></dt><dd> <input type="text" name="fb_url" value="<?=get_option('fb_url')?>" class="form-control"/></dd>
		<dt><label>Twitter URL :</label></dt><dd> <input type="text" name="tw_url" value="<?=get_option('tw_url')?>" class="form-control"/></dd>
		<dt><label>Website Logo :</label></dt><dd> <input type="file" name="file" value="" class="form-control" /></dd>
		<dt><label>Homepage Header Image :</label></dt><dd> <input type="file" name="header_image" value="" class="form-control" /></dd>
		<dt>&nbsp;</dt><dd><input type="submit" name="sb" value="Update" class="btn btn-green"/></dd>
	</dl>
	</form>
	
</div>


<?php require_once ('footer.php'); ?>