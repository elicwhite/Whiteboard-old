<?php
/**
 * Copyright Eli White & SaroSoftware 2010
 * 
 * This file is part of WhiteBoard.
 * 
 * WhiteBoard is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * WhiteBoard is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with WhiteBoard.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
/*-----
File: /install/includes/page1.php
Author: SaroSoftware
Date Created: 03/10/2007
Last Edited By: Eli White
Last Edited Date: 3/8/09

Comments:
Configuration settings page

-----*/
require_once('includes/header.php');
require_once('install.php');

$errors = array();

// Check if form is CSRF protected and was sent
if (isSecureForm("step1") && isset($_POST['formsent']) && $_POST['formsent'] == "1")
{
	// validate form	
	$install = new Installer();
	if (!$install->install())
	{
		// We've got errors
		$errors = $install->errors;
	}
	else 
	{
		// we have no errors
		header('Location: page2.php');
	}
	
}
?>
<div class="mainheaderbox">
	WhiteBoard Installer - Configuration Options
</div>
<div class="mainheaderbox2">
	Complete these settings and click the install button to continue.
</div>
<?php

if (count($errors) > 0)
{
	?>
	<div class="error">
		<strong>Errors have occured.</strong><br />
		<ul class="errors">
		<?php
		foreach($errors as $error)
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
<div class="catrow">Board Settings</div>
<form action="" method="post">
	<div class="contentbox">
		<table class="info">
			<tr>
				<td class="info">
					<strong>MySQL Host</strong>
				</td>
				<td class="input">
					<?php secureForm("step1"); ?>
					<input type="hidden" name="formsent" value="1" />
					<input type="text" name="dbhost" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>MySQL User Name</strong>
				</td>
				<td class="input">
					<input type="text" name="dbusername" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>MySQL Password</strong>
				</td>
				<td class="input">
					<input type="text" name="dbpass" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>MySQL Database Name</strong>
				</td>
				<td class="input">
					<input type="text" name="dbname" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Table Prefix</strong><br />
					The prefix for the sql tables. This allows you to run multiple copies of the discussion board on the same database.
				</td>
				<td class="input">
					<input type="text" name="dbprefix" />
				</td>
			</tr>
			<tr class="divider">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="info">
					<strong>Admin Username</strong><br />
				</td>
				<td class="input">
					<input type="text" name="username" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Admin Displayname</strong><br />
				</td>
				<td class="input">
					<input type="text" name="displayname" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Admin Password</strong><br />
					The theme you want this forum and it's sub forums to use.
				</td>
				<td class="input">
					<input type="password" name="password" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Admin Email</strong><br />
				</td>
				<td class="input">
					<input type="text" name="email" />
				</td>
			</tr>
			<tr class="divider">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="info">
					<strong>Forum Name</strong><br />
				</td>
				<td class="input">
					<input type="text" name="forumname" />
				</td>
			</tr
			<tr>
				<td class="info">
					<strong>Forum URL</strong><br />
					The full url to the forum folder with a trailing slash. Ex: http://dev.powerwd.net/forum/
				</td>
				<td class="input">
					<input type="text" name="root" value="" />
				</td>
			</tr>
		</table>
		
	
	</div>
	<input class="submit" type="submit" value="Install!" /> <input class="submit" type="reset" value="Reset" />
</form>
<?php
require_once('includes/footer.php');
?>