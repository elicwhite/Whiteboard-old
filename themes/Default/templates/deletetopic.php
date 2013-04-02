<div class="catrow">
	Delete Topic in: <?php echo $this->forumName?>
</div>
<div class="contentbox">

	<div class="postBox">
		Are you sure you want to delete the topic: <?php echo $this->topicName ?><br /><br />
		<form action="?act=deleteTopic&amp;id=<?php echo $_GET['id']?>" method="post">
			<p>
				<?php secureForm(); ?>
				<input type="hidden" name="formsent" value="1" />
				<input type="submit" class="button" name="submit" value="Delete Topic" />
			</p>
		</form>
	</div>
</div>