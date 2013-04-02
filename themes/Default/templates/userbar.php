<div id="mainheaderbox">
	<span id="usertext_left">
		<span id="greeting">Welcome:</span> <span id="username">
		<?php
		if($this->LOGGED)
		{
		?>
		<a href="?act=member&amp;id=<?php echo $this->USERID?>"><?php echo $this->USERNAME_DISPLAYNAME?></a>
		<?php
		}
		else 
		{
			echo $this->USERNAME_DISPLAYNAME;
		}
		?>
		</span>
		
	</span>
	<?php
	if ($this->LOGGED)
	{
		?>
	<span id="usertext_right">
		<a href="?act=controlPanel">Control Panel</a><!-- - Private Messages-->
	</span>
	<?php
	}
	?>
	<div class="clearer"></div>
</div>
<div id="breadcrumbs"><?php echo $this->BREADCRUMBS?></div>
<form action="" method="get">
	<div id="searchbar">
		<input type="hidden" name="act" value="search" />
		<input type="text" name="q" class="searchbox" />
		<input type="submit" value="Search" class="searchsubmit" />
	</div>
</form>
<div class="clearer"></div>