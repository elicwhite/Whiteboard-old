<div class="mainheaderbox2">
	<?php echo $this->INFORMATION_UPDATED?>
	This page will help you re-order, edit, and delete forums.
</div>
<form action="" method="post">
<div class="centerbox">
	<?php secureForm("structForum"); ?>
	<input type="hidden" name="post" value="1" />
	<?php
	foreach($this->STRUCTUREDFORUMS as $forum)
	{
	?>
	<div class="<?php echo $forum['class']?>">
		<div class="admin_structure_buttons">
			<?php echo $forum['indent']?><input type="text" name="sortorder[<?php echo $forum['id']?>]" size="1" class="admin_sortorder" value="<?php echo $forum['sort_order']?>" /> - <a href="index.php?act=forums&sub=edit&fid=<?php echo $forum['id']?>">Edit</a>
		</div>
		<?php echo $forum['indent']?><?php echo $forum['name']?>
	</div>
	<?php
	}
	?>	
</div>
<div class="admin_structure_submit">
	<input type="submit" value="Update" class="admin_structure_submit_button" />
</div>
</form>