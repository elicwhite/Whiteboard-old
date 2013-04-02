<?php
if (count($this->FORUMS) > 0)
{
	?>
<div class="catrow">
	<?php echo $this->NAME?>
</div>
<table cellspacing="0" class="forum_row">
	<?php
	foreach($this->FORUMS as $forum)
	{
	?>
		<tr class="forum_display_row">
			<td class="icon">
				<img src="<?php echo $forum['icon']?>" alt="" />
			</td>
			<td class="forum_main">
				<div>
					<h2><a href="<?php echo $forum['link']?>"><?php echo $forum['name']?></a></h2>
					<?php echo $forum['description']?><!-- wrap in <span> -->
				</div>
				<?php
				if ($forum['hasChildren'])
				{
				?>
					<div class="subforums">
						<strong>Sub-Forums:</strong> <?php echo $forum['children']?>
					</div>
				<?php
				}
				?>
			</td>
			<td class="forum_activity">
				<div>
				<?php
				if ($forum['forumType'] != "redirect")
				{
				?>
					<span class="first"><?php echo $forum['post_activity']?> Repl<?php echo plural($forum['post_activity'],true)?></span>
					<span><?php echo $forum['topic_activity']?> Topic<?php echo plural($forum['topic_activity'])?></span>
				<?php
				}
				else
				{
				?>
					<span class="first"><?php echo $forum['redirectHits'] ?> Hit<?php echo plural($forum['redirectHits'])?></span>
				<?php
				}
				?>					
				</div>
			</td>
			<td class="forum_latest">
				<div>
					<?php
					if ($forum['forumType'] == "stdForum")
					{
					?>
					<span class="section">
						<span class="label">topic:</span>
						<span><a href="<?php echo $forum['topiclink']?>"><?php echo $forum['topic']?></a></span>
					</span>
					<span class="section">
						<span><?php echo $forum['lastPostDate']?></span>
					</span>
					<span class="section">
						<span class="label">Last post by:</span>
						<span><?php echo $forum['lastPoster']?></span>
					</span>
					<?php
					}
					elseif ($forum['forumType'] == "redirect")
					{
					?>
					Redirect
					<?php
					}
					else
					{
					?>
					<span class="section">
						<span class="label">---</span>
					</span>
					<?php
					}
					?>
				</div>	
			</td>
		</tr>
	<?php
	}
	?>
</table>
<?php
}
?>