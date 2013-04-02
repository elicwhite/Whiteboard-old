<ul class="forum_top_links">
	<li><?php echo $this->topic_count?></li>
	<li><?php echo $this->post_count?></li>
</ul>
<div class="foruminfo_box">
	<?php echo $this->forum_name?>
				
	<?php if ($this->hasDescription)
	{
		?>
		<br />
		<span class="description"><?php echo $this->forum_description?></span>
	<?php
	}
	?>
	
</div>
<ul class="pagination">
	<li>Pages: <?php echo $this->page_count?></li>
	<?php
	foreach ($this->PAGINATION as $page)
	{
	?>
		<li <?php if ($this->curpage == $page['value']) echo 'class="current"' ?>><a href="<?php echo $page['link']?>"><?php echo $page['value']?></a></li>
	<?php
	}
	?>
	
</ul>
<ul class="forum_links">
	<?php
	foreach($this->LINKS as $link)
	{
	?>
	<li><a href="<?php echo $link['url'] ?>"><?php echo $link['name']?></a></li>
	<?php
	}
	?>
</ul>

<div class="clearer"></div>