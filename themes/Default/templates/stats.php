<?php

if ($GLOBALS['super']->user->isLogged() && $GLOBALS['super']->user->can("ViewBoard"))
{
	?>
	<div id="markread"><a href="index.php?act=markread">Mark all Forums as Read</a></div>
	<?php
}
?>
<div id="footerwrapper"> 	
	<table cellspacing="0" class="stat_row" id="first">
		<tr>
			<td class="stat_icon">

				<img src="themes/<?php echo "Default"?>/images/icon_stats.gif" alt="" />
			</td>
			<td class="stat_main">
				<div>
					We Have a Total of <?php echo $this->MEMBERS?> Member<?php echo plural($this->MEMBERS)?>,<br />
					Who Have Made a Total of <?php echo $this->POSTS?> Repl<?php echo plural($this->POSTS, true)?> in <?php echo $this->TOPICS?> Topic<?php echo plural($this->TOPICS)?>.<br />
					We Would Like to Welcome our Newest Member: <?php echo $this->NEWESTMEMBER?><br />

					<!--<hr />
					More Forum Statistics-->
				</div>
			</td>
		</tr>
		<tr>
			<td class="stat_icon">

				<img src="themes/<?php echo "Default"?>/images/icon_users.gif" alt="" />
			</td>
			<td class="stat_main">
				<div>
					There is Currently <?php echo $this->NUMUSERSONLINE ?> Member<?php echo plural($this->NUMUSERSONLINE)?> and <?php echo $this->GUESTSONLINE ?> Guest<?php echo plural($this->GUESTSONLINE)?> Viewing the Forums<br />
					<?php if ($this->NUMUSERSONLINE >0)
					{
					?>
					<hr />
					<?php
						foreach($this->USERSONLINE as $index=>$user)
						{
							// if we are the last one, we don't want to display a comma
							$sep = ", ";
							if ($index == count($this->USERSONLINE)-1)
							{
								$sep = "";
							}
							echo $user.$sep;
						}
					}
					?>
					<hr />
					<a href="?act=online">Detailed List</a>
				</div>
			</td>
		</tr>
	</table>
</div>