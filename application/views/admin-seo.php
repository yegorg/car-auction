<?php require_once ('header.php'); ?>


<div class="page-header white-content">
	<h1>Admin -> SEO</h1>
	<?php require_once 'admin-menu.php'; ?>
</div>

<div class="white-content">

	<?php if(isset($form_message)) print $form_message; ?>
	
	<form method="post" action="" class="form form-horizontal">
	<dl>
		<dt><label>SEO Title :</label></dt><dd> <input type="text" name="seo_title" value="<?= get_option('seo_title') ?>" class="form-control"/></dd>
		<dt><label>SEO Description :</label></dt><dd> <input type="text" name="seo_description" value="<?= get_option('seo_description') ?>" class="form-control"/></dd>
		<dt><label>SEO Keywords: <span class="muted">(separated by comma)</span></label></dt><dd> <textarea name="seo_keywords" rows="5" class="form-control"><?= get_option('seo_keywords') ?></textarea></dd>
		<dt>&nbsp;</dt><dd><input type="submit" name="sb" value="Update" class="btn btn-green" class="form-control"/></dd>
	</dl>
	</form>
	
</div>


<?php require_once ('footer.php'); ?>