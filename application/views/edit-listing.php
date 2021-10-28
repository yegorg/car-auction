<?php require_once 'header.php';?>

<form action="/users/updatelisting" id="update-listing" method="POST" enctype="multipart/form-data">
<div class="row">
	<div class="col-xs-8 white-content">
		<h3>Edit Auction</h3>
		<?= isset( $msg ) ? $msg : ''; ?>
		<hr />
		<p>Auction Title</p>
		<input type="text" name="auction_title" value="<?=$l->listing_title?>" class="form-control form-underline" required>
		<br />

		<div class="row">
			<div class="col-xs-4">
				<p>Start Price</p>
				<input name="starting" type="number" step="any" value="<?=$l->starting_?>" class="form-control form-underline" required>
			</div>
			<div class="col-xs-4">
				<p>Reserve Price</p>
				<input name="reserve" type="number"  value="<?=$l->reserve?>" class="form-control form-underline" required>
			</div>
			<div class="col-xs-4">
				<p>Buy It Now</p>
				<input name="bin" type="number"  value="<?=$l->bin?>" class="form-control form-underline">
			</div>
		</div><!-- pricing row -->
		<br />
		<p>Auction Description</p>
		<textarea name="auction_description" cols="30" rows="16" class="form-control wysiwyg" required><?=$l->listing_description?></textarea>

	</div><!-- ./col-xs-8 -->

    <div class="col-xs-4">
	<div class="white-content">
		<h4>Category</h4>
		<hr />
        <select name="category" id="category" class="form-control" required>
            <option value=""> -- Select One --</option>
            <?php
			foreach ($cats as $c) {
				$isSelected = ($c->catID == $l->list_catID) ? ' selected' : '';
				printf('<option value="%d"%s>%s</option>', $c->catID, $isSelected, $c->category);
			}
			?>
        </select>
        <!-- /#category.form-control -->
	</div><!-- ./white-content -->
	</div><!-- ./col-xs-4 -->

    <div class="col-xs-4">
	<div class="white-content">
		<h4>Photos</h4>
		<hr />
		<p>Minimum 3 photos (at least 370x370 in size) required.</p>
        <input type="file" name="p[]" class="uploadAuctionPhotos form-control multi" accept="jpg|jpeg|png" multiple>
        <hr />
        <div class="row">
        <?php foreach ($att as $p): ?>
            <div class="col-xs-3 small-thumb">
            	<a href="/users/remove_att/<?= $p->attachID ?>"><span class="badge">X</span></a>
            	<a href="<?=base_url() . 'uploads/' . $p->att_file?>" target="_blank">
                	<img src="<?=base_url() . 'uploads/small-' . $p->att_file?>" class="img-responsive" alt="">
                </a>
            </div>
            <!-- /.col-xs-3 -->
        <?php endforeach;?>
        </div><!-- /.row ( existent photos ) -->
        <!-- /#uploadAuctionPhotos -->
	</div><!-- ./white-content -->
	</div><!-- ./col-xs-4 -->

	<div class="col-xs-4">
	<div class="white-content">
		<h4>Save Auction</h4>
		<hr />
        <button type="submit" class="btn btn-primary btn-lg" name="sbSave">Save Auction</button>
        <hr />
        <!-- /#submit -->
	</div><!-- ./white-content -->
	</div><!-- ./col-xs-4 -->


</div><!-- ./row -->
</form>

<?php require_once 'footer.php';?>