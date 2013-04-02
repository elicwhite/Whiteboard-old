<div id="navbar">
	<ul>
		<?php
		foreach($this->MENU as $menuitem)
		{
		?>
		<li <?php if ($this->act == $menuitem['act']) echo 'class="selected"' ?> id="<?php echo $menuitem['act']?>"><a href="<?php echo $menuitem['url']?>"><?php echo $menuitem['name']?></a></li>
		<?php
		}
		?>
	</ul>
</div>