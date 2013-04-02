<table class="info">
	<tr>
		<td class="info">
			<strong>Theme</strong>
		</td>
		<td class="input">
			<input type="hidden" name="post" value="form" />
			<select name="theme">
				<option value="Default" disabled="disabled">Default</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Date Format</strong><br />
			Insert a date formatting string following PHP's <a href="http://php.net/manual/en/function.date.php">date function.</a><br />
			<em>Board Default:</em> "<?php echo $GLOBALS['super']->config->dateFormat;?>"
		</td>
		<td class="input">
			<input type="text" value="<?php echo $this->USERINFO->date_format?>" name="dateformat" /><br />
		</td>
	</tr>

	<tr>
		<td class="info">
			<strong>Time Format</strong><br />
			Insert a time formatting string following PHP's <a href="http://php.net/manual/en/function.date.php">date function.</a><br />
			<em>Board Default:</em> "<?php echo $GLOBALS['super']->config->timeFormat;?>"
		</td>
		<td class="input">
			<input type="text" value="<?php echo $this->USERINFO->time_format?>" name="timeformat" /><br />
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Time Zone</strong><br />
			<em>Board Default:</em> "<?php echo $GLOBALS['super']->config->timeZone;?>"
		</td>
		<td class="input">
			<?php
			
			$timezones = DateTimeZone::listIdentifiers();
			echo '<select name="timezone">';
				foreach ( $timezones as $timezone )
				{
					?>
					<option value="<?php echo $timezone ?>" <?php echo ($this->USERINFO->time_zone == $timezone ? 'selected="selected" ' : '')?>><?php echo $timezone ?></a>
					<?php
				}
			echo '</select>';

?>
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Posts Per Page</strong><br />
			The number of posts to display on each topic page.
		</td>
		<td class="input">
			<input type="text" name="postsperpage" value="<?php echo $this->USERINFO->posts_per_page?>" />
		</td>
	</tr>
	<tr>
		<td class="info">
			<strong>Topics Per Page</strong><br />
			The number of topics to display on each forum page.
		</td>
		<td class="input">
			<input type="text" name="topicsperpage" value="<?php echo $this->USERINFO->topics_per_page?>" />
		</td>
	</tr>
	<tr>
		<td colspan="2" class="blue"><input type="submit" class="submit" name="submit" value="Submit" /></td>
	</tr>
</table>