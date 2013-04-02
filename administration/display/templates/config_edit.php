<div class="mainheaderbox2">
	Use this page to edit board wide configuration settings.
</div>

<form action="" method="post" class="forumedit">
	<div class="catrow"><?php echo $this->Title?></div>
	<div class="contentbox">
		
		<table class="info">
			<tr>
				<td class="info">
					<strong>Site Name</strong>
				</td>
				<td class="input">
					<?php secureForm(); ?>
					<input type="hidden" name="post" value="update" />
					<input class="text" type="text" value="<?php echo $this->SiteName?>" name="site_name" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Crumb Seperator</strong><br />
					The seperator used in the page bread crumbs. ex: "&amp;raquo;" makes "&raquo;"
				</td>
				<td class="input">
					<input class="text" type="text" value="<?php echo $this->Seperator?>" name="crumb_seperator" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Time Zone</strong><br />
					The default time zone of the forum. Users can change their own values.
					Current is: "<?php echo $this->TimeZone?>"
				</td>
				<td class="input">
					<select name="time_zone" class="select">
						<?php 
						foreach($this->Zones as $key=>$zone)
						{
						?>
						<option value="<?php echo $key?>" <?php if ($this->TimeZone == $zone) echo 'selected="selected"'?>><?php echo $zone?></option>
						<?php
						}
						?>
					</select>

				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Date Format</strong><br />
					The format to use for dates on the board. See the <a href="http://php.net/manual/en/function.date.php">PHP Manual</a> for more information
				</td>
				<td class="input">
					<input class="text" type="text" value="<?php echo $this->DateFormat?>" name="date_format" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Time Format</strong><br />
					The format to use for times on the board. See the <a href="http://php.net/manual/en/function.date.php">PHP Manual</a> for more information
				</td>
				<td class="input">
					<input class="text" type="text" value="<?php echo $this->TimeFormat?>" name="time_format" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Posts Per Page</strong><br />
					The number of posts to display per page in a topic.
				</td>
				<td class="input">
					<input class="text" type="text" value="<?php echo $this->PostsPerPage?>" name="posts_per_page" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Topics Per Page</strong><br />
					The number of topics to display per page in a forum.
				</td>
				<td class="input">
					<input class="text" type="text" value="<?php echo $this->TopicsPerPage?>" name="topics_per_page" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Online List Offset</strong><br />
					The number of seconds between automatic updates on the online list.
				</td>
				<td class="input">
					<input class="text" type="text" value="<?php echo $this->OnlineOffset?>" name="online_offset" />
				</td>
			</tr>
		</table>
			
		<div class="clearer"></div>
	</div>
	<p class="submit">
		<input type="submit" class="submit" name="submit" value="Submit" />
	</p>
</form>