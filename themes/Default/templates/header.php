<?php echo $this->DOCTYPE?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" type="image/x-icon" href="themes/Default/images/favicon.ico" /> 
		<?php
		foreach($this->STYLESHEETS as $styles)
		{
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo $styles['path']?>" />
		<?php
		}
		foreach($this->SCRIPTS as $script)
		{
		?>
		<script type="text/javascript" src="<?php echo $script['path']?>"></script>
		<?php
		}
		?>

		<title><?php echo $this->TITLE?></title>
	</head>
<body>
	<div id="container">
		<a name="top"></a>
		<div id="logostrip">
			<img src="themes/<?php echo $this->STYLESHEET?>/images/header.jpg" alt="" />
		</div>
		<div id="container2">