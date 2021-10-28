<?php require_once ('header.php'); ?>

<div class="container">
<div class="row">

<div class="col-xs-6 col-xs-offset-3">
<div class="page-header white-content">
	<h1>Contact</h1>
</div>
</div>

<div class="col-xs-6 col-xs-offset-3">
<div class="white-content">

<form method="POST" action="/home/contactajax" id="contact-form">
<dl>
<dt>Your Name</dt>
<dd><input type="text" name="yname" id="yname" class="form-control" /></dd>
<dt>Your Email</dt>
<dd><input type="email" name="yemail" id="yemail" class="form-control" /></dd>
<dt>Subject</dt>
<dd><input type="text" name="ysubject" id="ysubject" class="form-control" /></dd>
<dt>Message</dt>
<dd><textarea name="ymessage" rows="10" id="ymessage" class="form-control"></textarea></dd>
<dt>&nbsp;</dt>
<dd><input type="submit" name="sb" value="Contact Us" class="btn btn-primary form-control"></dd>
</dl>
</form>

<div id="contact_output_div"></div>

</div>
</div>

</div><!--./row-->
</div><!--./container-->


<?php require_once ('footer.php'); ?>