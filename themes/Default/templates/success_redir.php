<div class="success">
	<strong><?php echo $this->message?></strong><br />
		<br />
		<?php
		
		if (isset($this->extramessage))
		{
			echo $this->extramessage;
			?>
			<br />
			<?php
		}			
		?>
	You are being automatically redirected, if this doesn't work, click <a href="<?php echo $this->url?>">here</a> to continue.
</div>
<script type="text/javascript">redir("<?php echo $this->url?>");</script>