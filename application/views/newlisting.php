<?php require_once 'header.php';?>

<form action="/users/addlisting" id="add-listing" method="POST" enctype="multipart/form-data">
<div class="row">
	<div class="col-xs-12 col-md-8 white-content">
		<h3>Start an Auction</h3>
		<hr />
		<p>Auction Title</p>
		<input type="text" name="auction_title" placeholder="Item name" class="form-control form-underline" required>
		<br />

		<div class="row">
			<div class="col-xs-4">
				<p>Start Price</p>
				<input name="starting" type="number" placeholder="500" class="form-control form-underline" required>
			</div>
			<div class="col-xs-4">
				<p>Reserve Price</p>
				<input name="reserve" type="number" placeholder="1500" class="form-control form-underline" required>
			</div>
			<div class="col-xs-4">
				<p>Buy It Now</p>
				<input name="bin" type="number" placeholder="2500" class="form-control form-underline">
			</div>
		</div><!-- pricing row -->
		<br />
		<p>Auction Description</p>
		<textarea name="auction_description" cols="30" rows="11" class="form-control wysiwyg" required></textarea>

	</div><!-- ./col-xs-8 -->

    <div class="col-xs-12 col-md-4">
	<div class="white-content">
		<h4>Category</h4>
		<hr />
        <select name="category" id="category" class="form-control" required>
            <option value=""> -- Select One --</option>
            <?php foreach ($cats as $c) {
	printf('<option value="%d">%s</option>', $c->catID, $c->category);
}
?>
        </select>
        <!-- /#category.form-control -->
	</div><!-- ./white-content -->
	</div><!-- ./col-xs-4 -->

    <div class="col-md-4 col-xs-12">
	<div class="white-content">
		<h4>Photos</h4>
		<hr />
		<p>Minimum 3 photos (at least 370x370 in size) required.</p>
        <input type="file" name="p[]" class="uploadAuctionPhotos form-control multi" accept="jpg|jpeg|png" multiple>
        <!-- /#uploadAuctionPhotos -->
	</div><!-- ./white-content -->
	</div><!-- ./col-xs-4 -->

	<div class="col-md-4 col-xs-12">
	<div class="white-content">
		<h4>Submit Auction</h4>
		<hr />
        <button type="submit" class="btn btn-primary btn-lg">Submit Auction</button>
        <hr />
        <!-- /#submit -->
	</div><!-- ./white-content -->
	</div><!-- ./col-xs-4 -->


</div><!-- ./row -->
</form>

<?php require_once 'footer.php';?>