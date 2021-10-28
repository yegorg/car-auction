<?php require_once ('header.php'); ?>

<div class="col-xs-12 col-md-9">
	
<div class="page-header white-content">
	<h3><?= _('Message Center') ;?></a></h3>
</div>

<div class="white-content">
	<?php if(!isset($msg)) { ?>
	<div class="table-responsive">
	<table class="table table-bordered table-hover table-striped">
	    <thead>
	        <tr>
	            <th><?=('Subject')?></th>
	            <th><?=('From')?></th>
	            <th><?=('Date')?></th>
	            <th><?=('Reply')?></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php
	        foreach($messages as $b) {
	            
	            echo '<tr>
	                       <td><a role="button" href="/users/read_message/'.$b->msgID.'" data-target="#myModal">'.$b->subject.'</a></td>
	                       <td><a href="/users/profile/'.url_title($b->username).'">'.$b->username.'</a></td>
	                       <td>'.date("jS F Y", $b->msg_date).'</td>
	                       <td><a href="/users/message/'.$b->fromID.'/replyto/'.$b->msgID.'">Reply</a></td>
	                   </tr>';
	        }
	        ?>
	    </tbody>
	</table>
	</div>
	
	<!-- Modal -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog white-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?=_('Message body')?>:</h3>
  </div>
  <div class="modal-body">
    
  </div>
  <div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?=_('Close') ?></button>
  </div>
</div>
</div>
	
	<?php }else{ echo $msg; } ?>
	
</div>

</div>

<?php require_once 'sidebar.php'; ?>

<?php require_once ('footer.php'); ?>