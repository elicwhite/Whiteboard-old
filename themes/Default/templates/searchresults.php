<?php
// The URL containing the search query, allows the change of type
$url = "?act=search&q=".urlencode($this->QUERY);
?>
<div id="search_page">
	<!--<ul class="forum_top_links">
		<li class="selected"><a href="<?php echo $url?>&type=all">All Results</a></li>
		<li><a href="<?php echo $url?>&type=titles">Topic Titles</a></li>
		<li><a href="<?php echo $url?>&type=posts">Posts</a></li>
		<li><a href="<?php echo $url?>&type=users">Users</a></li>
	</ul>-->
	<div class="foruminfo_box">Search Results for: <?php echo $this->QUERY?></div>
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
		<li><?php echo $this->numResults; ?> Results</li>
	</ul>
	<div id="results">
		
		<?php
		
		foreach($this->RESULTS as $result)
		{
			if ($result["type"] == "topic")
			{
				$topicposter = $this->functions->getUser($result["user_id"],true);
				$lastposter = $this->functions->getUser($result["last_user_id"], true);
				?>
				<div class="searchresult typetopic">
					<div class="leftinfo">
						<ul>
							<li><strong>Topic:</strong> <a href="?act=tdisplay&amp;id=<?php echo $result['id']?>"><?php echo $result["name"]?></a></li>
							<li><strong>Posted:</strong> <?php echo $this->functions->formatDate($result["time_added"])?></li>
							<li><strong>By:</strong> <?php echo $topicposter ?></li>
						</ul>
					</div>
					<div class="rightinfo">
						<ul>
							<li><strong>Replies:</strong> <?php echo $result["post_count"]?></li>
							<li><strong>Latest Reply By:</strong> <?php echo $lastposter ?></li>
							<li><strong>On:</strong> <?php echo $this->functions->formatDate($result["time_modified"]) ?></li>
						</ul>
					</div>
				</div>
				<?php
			}
			elseif($result["type"] == "post")
			{
				$poster = $this->functions->getUser($result["user_id"], true);
				?>
				<div class="searchresult typepost">
					<div class="leftinfo">
						<ul>
							<li><strong>Post By:</strong> <?php echo $poster?></li>
							<li><strong>In Topic:</strong> <a href="?act=tdisplay&amp;id=<?php echo $result['post_count']?>"><?php echo $result["name"]?></a></li>
							<li><strong>On</strong> <?php echo $this->functions->formatDate($result["time_added"])?></li>
						</ul>
					</div>
					<div class="rightinfo">
						 <?php echo dotdotdot($result['message'],150); ?>
					</div>
				</div>
				<?php
			}
			elseif ($result["type"] == "user")
			{
				// posterid is mapped as the avatarid for users
				$avatar = $this->functions->getImage($result["user_id"]);
				$poster = $this->functions->getUser($result["id"]);
				$posterName = $this->functions->formatUser($poster['groupname'], $poster["color"], $poster['id'], $poster['username'], "", false);
				
				
				?>
				<div class="searchresult typeuser">
					<a href="?act=member&amp;id=<?php echo $poster['id']?>"><img class="avatar" src="<?php echo $avatar["url"]?>" alt="<?php echo $avatar["name"]?>" /></a>
					<div class="leftinfo">
						<ul>
							<li><strong>Username:</strong> <?php echo $posterName ?></li>
							<?php
							if ($result["displayname"] != "")
							{
							?>
							<li><strong>Displayname:</strong> <?php echo $result["displayname"]?></li>
							<?php
							}
							?>
							<li><strong>Registered:</strong> <?php echo $this->functions->formatDate($result["time_added"])?></li>
						</ul>
					</div>
					<div class="rightinfo">
						<ul>
							<li>Send Message</li>
							<li>Send E-mail</li>
						</ul>
						
					</div>
				</div>
				<?php
			}
		}
		?>		
	</div>
</div>