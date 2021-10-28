<?php
require_once 'header.php';
?>


	<div class="page-header white-content">
		<h1>Admin -> Categories</h1>

		<?php require_once 'admin-menu.php';?>

	</div>

	<div class="white-content">

	   <?=$msg?>

		<div class="row">
			<div class="col-xs-12">
				<div class="col-md-6">
					<form method="POST" class="form-inline">
					<label><i class="glyphicon glyphicon-plus-sign"></i> New Category </label>
					<input type="text" name="new_category" value="" placeholder="Enter Category Name" class="form-control">
					<input type="submit" name="sb_new_category" value="Create Category" class="form-control">
					</form>
				</div><!-- /.col-md-4 -->
			</div><!-- /.col-xs-12 -->
		</div><!-- /.row  (create category form )-->

		<br />

		<?php if (count($cats)): ?>
		<div class="alert alert-warning"><?php echo count($cats) . ' total categories'; ?></div>

		<div class="table-responsive">
		<table class="table table-bordered table-striped" id="dataTbl">
			<thead>
				<tr>
					<th>ID</th>
					<th>Category</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cats as $c): ?>

				<tr>
					<td><?=$c->catID?></td>
					<td>
					<?php if ($action == 'update' && $id == $c->catID): ?>
						<form method="POST" class="form-inline" action="/admin/categories">
							<input type="hidden" name="id" value="<?=$c->catID?>" />
							<input type="text" name="update_category" value="<?=$c->category?>"  class="form-control">
						<input type="submit" name="sb_update_category" value="Update Category" class="form-control">
						</form>
					<?php else: ?>
						<a href="/auctions/<?=$c->slug?>/latest" target="_blank"><?=$c->category?></a>
					<?php endif;?>
					</td>
					<td>
						<a href="/admin/categories/update/<?=$c->catID;?>">
							<b class="glyphicon glyphicon-edit"></b>
						</a>
						<a href="/admin/categories/remove/<?=$c->catID;?>">
							<b class="glyphicon glyphicon-remove"></b>
						</a>
					</td>
				</tr>

				<?php endforeach;?>
			</tbody>
		</table>
		</div>

		<?php else: ?>

			- no categories -

		<?php endif;?>
	</div>


<?php
require_once 'footer.php';
?>