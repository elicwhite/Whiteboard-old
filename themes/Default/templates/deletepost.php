<div class="catrow">
	Delete Post in: <?php echo $this->topicName?>
</div>
<div class="contentbox">

	<div class="postBox">
		<strong>Are you sure you want to delete the following post?</strong><br /><br />
		<p>
			<?php echo $this->message ?>
		</p>
		<br /><br />
		<form action="?act=deletePost&amp;id=<?php echo $_GET['id']?>" method="post">
			<p>
				<?php secureForm("deletePost"); ?>
				<input type="hidden" name="formsent" value="1" />
				<input type="submit" class="button" name="submit" value="Delete Post" />
			</p>
		</form>
	</div>
</div>