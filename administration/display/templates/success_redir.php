<div class="success">
	<strong><?php echo $this->message?></strong><br />
		<br />
	You are being automatically redirected, if this doesn't work, click <a href="<?php echo $this->url?>">here</a> to continue.
</div>
<script type="text/javascript">redir("<?php echo $this->url?>", 2000);</script>