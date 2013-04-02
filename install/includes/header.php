<?php 
ob_start();
// need this for CSRF protection.
session_start();
require_once('../includes/functions/functions.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" type="image/x-icon" href="themes/Default/images/favicon.ico" /> 
		<link rel="stylesheet" type="text/css" href="style.css" />

		<title>WhiteBoard Installer Page <?php echo $page?></title>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<img src="http://dev.powerwd.net/forum/themes/Default/images/header.jpg" alt="" />
			</div>
			<div id="container2">