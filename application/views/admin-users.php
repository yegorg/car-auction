<?php
require_once ('header.php');
?>



	<div class="page-header white-content">
		<h1>Admin -> Members</h1>

		<?php require_once 'admin-menu.php'; ?>
	</div>

	<div class="white-content">
		<?php if(count($users)) : ?>
		<div class="alert alert-warning"><?php echo count($users) . ' members in total'; ?></div>
		
		<div class="table-responsive">
		<table class="table table-bordered table-striped" id="dataTbl">
			<thead>
				<tr>
					<th>ID</th>
					<th>IP Addr</th>
					<th>Username</th>
					<th>Email</th>
					<th>About</th>
					<th>Remove</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($users as $m) : ?>
			
				<tr>
					<td><?=$m->userID?></td>
					<td><?=long2ip($m->ip)?></td>
					<td><?=$m->username?></td>
					<td><?=($m->email)?></td>
					<td><?=$m->about?></td>
					<td><a href="/admin/users/remove/<?=$m->userID;?>"><b class="glyphicon glyphicon-remove"></b></a></td>
				</tr>
			
				<?php endforeach; ?>
			</tbody>
		</table>
		</div>
		
		<?php endif; ?>
	</div>


<?php
	require_once ('footer.php');
?>