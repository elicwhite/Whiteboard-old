<table cellspacing="0" class="topic_rows">
	<tr>
		<th class="icon">
		</th>
		<th class="forum_main">
			Topic Title
		</th>
		<th class="forum_activity">

			Topic Starter
		</th>
		<th class="forum_activity">
			Statistics
		</th>
		<th class="forum_latest">
			Latest Post
		</th>
		<?php
		if($this->canMod)
		{
		?>
		<th class="forum_mod">
			Mod
		</th>
		<?php
		}
		?>
	</tr>
	<?php
	foreach ($this->TOPICS as $topic)
	{
	?>
		<tr class="forum_row">
			<td class="icon">
				<img src="<?php echo $topic['image']?>" alt="" />
			</td>
			<td class="forum_main">
				<a href="<?php echo $topic['link'] ?>" title="<?php echo $topic['preview']?>">
					<span class="topicname">
						<?php echo $topic['name']?>
					</span>
					<!--<span style="display: none;" class="preview">
						<?php echo $topic['preview']?>
					</span>-->
				</a>
			</td>
			<td class="forum_activity">
				<div>
					<?php echo $topic['starter']?>
				</div>
			</td>
			<td class="forum_activity">
				<div>
					<?php echo $topic['replies']?> Replies<br />
					<?php echo $topic['views']?> Views
				</div>
			</td>
			<td class="forum_latest">
				<div>
					<span class="section">
						<span class="label">Last post by:</span>
						<span><?php echo $topic['lastposter']?></span>
					</span>

					<span class="section">
						<span><a href="<?php echo $topic['link']?>&amp;page=last"><?php echo $topic['lastpostdate']?></a></span>
					</span>
				</div>
			</td>
			<?php
			if ($topic['canDelete'])
			{
				?>
			<td class="forum_mod">
				<span><a href="index.php?act=deleteTopic&amp;id=<?php echo $topic['id'] ?>">Delete</span>
			</td>
			<?php
			}
			?>
		</tr>
	<?php
	}
	?>
</table>