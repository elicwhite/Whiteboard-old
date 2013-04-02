<?php
if ($this->ERRORS && count($this->ERRORS) > 0)
{
	?>
	<div class="error">
		<strong>There were errors updating your profile.</strong>
		<ul>
		<?php
		foreach($this->ERRORS as $error)
		{
			?>
			<li><?php echo $error?></li>
			<?php
		}
		
		?>
		</ul>
	</div>
	<?php
}

?>
<ul class="forum_top_links">
	<?php
	foreach($this->NAV as $menuitem)
	{
		?>
		<li <?php if ($this->ACT == $menuitem['act']) echo 'class="selected"' ?> id="<?php echo $menuitem['act']?>"><a href="?act=controlPanel&amp;page=<?php echo $menuitem['act']?>"><?php echo $menuitem['name']?></a></li>
		<?php
	}
	?>
</ul>
<div id="controlpanel">
	<form action="index.php?act=controlPanel&page=<?php echo $this->ACT?>" method="post" enctype="multipart/form-data">
	<div style="display: none;">
		<input type="hidden" name="action" value="<?php echo $this->ACT?>" />
		<?php secureForm("controlPanel"); ?>
	</div>
	<?php
	
	switch($this->ACT)
	{
		case "personal":
			require_once(ROOT_PATH."themes/Default/templates/cp_personal.php");
			break;
		case "display":
			require_once(ROOT_PATH."themes/Default/templates/cp_display.php");
			break;
		case "privacy":
			require_once(ROOT_PATH."themes/Default/templates/cp_privacy.php");
			break;
		case "acct": // account is the default page.
		default:
			require_once(ROOT_PATH."themes/Default/templates/cp_account.php");
			break;
	}
	?>
	</form>
</div>