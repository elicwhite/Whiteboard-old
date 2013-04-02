<?php
foreach($this->POSTS as $post)
{
?>
<div class="post">
	<div class="datainfo">
		<span class="date"><?php echo $post['postdate']?></span>
		<?php
		if ($post['canEdit'] || $post['canDelete'])
		{
			?>
			<span class="moderation">
				<?php
				if ($post['canEdit'])
					echo '<a href="?act=editPost&amp;id='.$post['id'].'">Edit</a>';
					
				if ($post['canEdit'] && $post['canDelete'])
					echo ' - ';
					
				if ($post['canDelete'])
					echo '<a href="?act=deletePost&amp;id='.$post['id'].'">Delete</a>';
				?>
			</span>
			<?php			
		}
		
		?>
	</div>
	
	<div class="userinfo">
		<?php 
		// only display the avatar if it actually exists/links somewhere
		if ($post['avatar']['url'] != "")
		{
		?>
		<img class="avatar" src="<?php echo $post['avatar']['url']?>" alt="<?php echo $post['avatar']['name']?>" />
		<?php
		}
		?>
		<div class="leftinfo">
			<strong class="username"><?php echo $post['poster']?></strong>
			<span class="usergroup" style="color: <?php echo $post['groupcolor']?>"><?php echo $post['group']?></span><br />
			Joined: <?php echo $post['joindate']?>
		</div>
		<div class="rightinfo">
			Posts: <?php echo $post['postcount']?>
		</div>
		
		<!--<ul class="info">
			<li class="username"><?php echo $post['username']?></li>
			<li><span class="usergroup" style="color: <?php echo $post['groupcolor']?>"><?php echo $post['group']?></span></li>
			<li><a href="http://registration.com">Joined: <?php echo $post['joindate']?></a></li>
			<li>Posts: <?php echo $post['postcount']?></li>
		</ul>-->
	</div>
	<div class="postbox">
		<?php echo $post['message'];
		
		if ($post['editDate'] != 0)
		{
			?>
			<hr />
			<?php
			echo "Last Edited: ".$post['editDate'];
		}
		
		if($post['signature'] != "")
		{
		?>
		<div class="signature">
			<?php echo $post['signature'] ?>
		</div>
		<?php
		}
		?>
	</div>
	<div class="clearer"></div>
</div>
<?php
}
?>