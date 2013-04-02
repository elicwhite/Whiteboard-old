<div class="catrow">
	Topic Review (Reverse Order)
</div>
<div class="contentbox" style="padding: 0;">
	<table class="review">
	<?php
	foreach($this->POSTS as $post)
	{
	?>
		<tr>
			<td class="reviewdate" colspan="2"><?php echo $post['date']?></td>
		</tr>
		<tr>
			<td class="reviewuser"><?php echo $post['user']?></td>
			<td class="reviewmessage"><?php echo $post['message']?></td>
		</tr>
	<?php
	}
	?>
		<tr>
			<td class="reviewdate final" colspan="2">Click <a href="<?php echo $this->topicurl?>">Here</a> to View the Original Thread.</td>
		</tr>
	</table>
</div>