<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" type="image/x-icon" href="themes/Default/images/favicon.ico" /> 
		<?php
		foreach($this->stylesheets as $style)
		{
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo $style['path']?>" />
		<?php
		}
		foreach($this->scripts as $script)
		{
		?>
		<script type="text/javascript" src="<?php echo $script['path']?>"></script>
		<?php
		}
		?>

		<title><?php echo $this->title?></title>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<img src="<?php echo FORUM_ROOT?>themes/Default/images/header.jpg" alt="" />
			</div>
			<div id="navbar">
				<ul class="adminpages">
					<?php
					foreach($this->mainmenu as $main)
					{
						
						$selected = ($main['act'] == $this->curact) ? 'class="selected"' : "";
						$link = '?act='.$main['act'];
						if (isset($main['link']) && $main['redirect'] == "Yes")
						{
							$link = $main['link'];
						}
					?>
						<li <?php echo $selected?>><a href="<?php echo $link?>"><?php echo $main['text']?></a></li>
					<?php
					}
					?>
				</ul>
				<ul id="topnav2">
					<?php
					foreach($this->submenu as $sub)
					{
						$selected = ($sub['text'] == $this->cursub) ? 'class="selected"' : "";
						$link = '?act='.$this->curact.'&amp;sub='.$sub['act'];
						if (isset($sub['link']) && $sub['redirect'] == "Yes")
						{
							$link = $sub['link'];
						}
					?>
						<li <?php echo $selected ?>><a href="<?php echo $link ?>"><?php echo $sub['text']?></a></li>
					<?php
					}
					?>
				</ul>
			</div>
			<div id="container2">