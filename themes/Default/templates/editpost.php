<div class="catrow">
	Edit Post in: <?php echo $this->topicName?>
</div>
<div class="contentbox">

	<div class="postBox">
		<?php
		if (isset($this->errorArray) && count($this->errorArray) > 0)
		{
				// we have errors, we need to print them. List format is probably good
		?>
		<div class="error">
			<strong>There Were Errors in Your Post</strong>
			<ul>
			<?php
				
			foreach($this->errorArray as $error)
			{
				echo '<li>'.$error.'<li>';
			}
			?>
			</ul>
		</div>
		<?php
			// end the error printing
		}
		?>
		<form action="?act=editPost&amp;id=<?php echo $_GET['id']?>" method="post" >
			<p>
				<?php secureForm(); ?>
				<input type="hidden" name="formsent" value="1" />
				Message:<br />
				<div class="postArea">
					<!--<ul class="buttons">
						<li><img src="<?php echo FORUM_ROOT?>themes/Default/images/editor/bold.png" alt="" /></li>
						<li><img src="<?php echo FORUM_ROOT?>themes/Default/images/editor/italic.png" alt="" /></li>
						<li><img src="<?php echo FORUM_ROOT?>themes/Default/images/editor/underline.png" alt="" /></li>
					</ul>-->
					<textarea name="message" class="post" rows="40" cols="20"><?php echo $this->message ?></textarea>
					<br />
					<div class="buttonwrapper">
						<input type="submit" class="button" name="submit" value="Edit Post" />
					</div>
				</div>
			</p>
		</form>
	</div>
</div>