<?php require_once ('header.php'); ?>

<div class="col-xs-12">
	
<div class="page-header white-content">
	<h1>Congratulations!</h1>
	
	<div class="alert alert-success">
		<h3>You've successfully bought <?= $listing->listing_title ?></h3>
		You may now <a href="/users/message/<?= $listing->list_uID ?>" class="btn btn-default">Contact <?= $user->username  ?></a> OR <a href="mailto:<?=$user->email ?>" class="btn btn-default">Email</a> to arrange delivery details.
	</div><!-- /.alert alert-success -->

</div>

</div>

<?php require_once ('footer.php'); ?>