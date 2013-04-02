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
 File: /pages/controlpanel.php
 Author: SaroSoftware
 Date Created: 9/22/09
 Last Edited By: Eli White
 Last Edited Date: 9/22/09
 Latest Changes:
	9/22/09 - Started design and creation
 
TODO:

	
 Comments:
	Account Settings
		Display Name
		Password
		Email
		Avatar
		Signature
	Personal Settings
		First Name
		Last Name
		Gender
		Country
		BirthDate (Day, Month, Year)
	Display Settings
		Date Format
		Time Format
		Time Zone
		Posts per page
		Topics per page
	Privacy Settings
		Show Online
		Send Digests

 -----*/


//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}
if( ! defined("INCLUDED")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

class ControlPanel extends forumPage 
{

	
	/**
	 * Constructor for display topics
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setName("User Control Panel");
	}
	
	/**
	 * Returns the calendar's breadcrumbs
	 *
	 * @return string The breadcrumb string
	 */
	public function breadCrumb()
	{
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->getName();
	}
	
	function getJS()
	{
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/controlPanel.js");
		
		return $js;
	}
	
	/**
	 * Get the display of the page
	 *
	 * @return string The page content
	 */
	public function display()
	{
		ob_start();
		
		if (!$GLOBALS['super']->user->can("ViewBoard") || !$GLOBALS['super']->user->isLogged())
		{
			echo $GLOBALS['super']->user->noPerm();
		}
		else 
		{
			// Get the page they are on
			if (!isset($_GET['page']))
				$action = "acct";
			else
				$action = $_GET['page'];
			
			// Our error array (from validation);
			$errors = null;
			
			// If a form has been submitted
			if (isset($_POST['submit']) && $_POST['submit'] == "Submit" && isSecureForm())
			{
				$errors = $this->validateForm();
			}
					
			//if (isset($errors) && count($errors) != 0)
			//{
						
				// Build the navigation array
				$navitems[] = array("name"=>"Account Settings", "act"=>"acct");
				$navitems[] = array("name"=>"Personal Settings", "act"=>"personal");
				$navitems[] = array("name"=>"Display Settings", "act"=>"display");
				$navitems[] = array("name"=>"Privacy Settings", "act"=>"privacy");
				
				
				$content = new tpl(ROOT_PATH.'themes/Default/templates/controlpanel.php');
				$content->add("ERRORS", $errors);
				
				$content->add("NAV", $navitems);
				$content->add("ACT", $action);
				
				$content->add("USERINFO", $GLOBALS['super']->user);
				
				$content->parse();
			//}
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Validates the different form's that are posted through the control panel
	 *
	 */
	private function validateForm()
	{
		$errors = array();
		if ($_POST['action'] == "acct") // If they are on the account page
		{
			// Get the variables we care about
			$displayname = (isset($_POST['displayname'])) ? $_POST['displayname'] : "";
			$email = (isset($_POST['email'])) ? $_POST['email'] : "";
			$signature = (isset($_POST['signature'])) ? $_POST['signature'] : "";
			
			$oldpass = (isset($_POST['old_password'])) ? $_POST['old_password'] : "";
			$newpass = (isset($_POST['new_password'])) ? $_POST['new_password'] : "";
			//die($signature);
			
			// Check that the display name isn't taken. Only check if we are actually changing the displayname
			if ($displayname != $GLOBALS['super']->user->displayname)
			{
				$query = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."users WHERE `displayname`='".$displayname."'");
				if ($GLOBALS['super']->db->getRowCount($query) > 0)
				{
					$errors[] = "That display name is taken.";
				}
			}
			
			if ($email != $GLOBALS['super']->user->email)
			{
				$query = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."users WHERE `email`='".$email."'");
				if ($GLOBALS['super']->db->getRowCount($query) > 0)
				{
					$errors[] = "That E-mail is already binded to another account.";
				}
			}
			
			$changePass = false;
			$pass = encrypt_password($oldpass);
			$newpass = encrypt_password($newpass);
			
			// If the old password isn't an empty string, then we are trying to change it
			if ($oldpass != "")
			{
				$username = $GLOBALS['super']->db->escape($GLOBALS['super']->user->username);
				$query = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."users WHERE `username`='".$username."' AND `password`='".$pass."'");
				
				$id = $GLOBALS['super']->db->fetch_result($query);
				if ($id != $GLOBALS['super']->user->id)
					$errors[] = "To change your password, you must provide your old password.";
				elseif (strlen($newpass) <= 5)
					$errors[] = "Your new password must be greater than 5 characters.";
				else
				{
					// No problems here, lets update our password
					$changePass = true;
				}
			}
			
			$newAvatar = false;
			// Check if they uploaded an image for a new avatar
			if (isset($_FILES["new_avatar"]) && $_FILES['new_avatar']['name'] != "" && is_uploaded_file($_FILES['new_avatar']['tmp_name']))
			{
		
				$name = $_FILES['new_avatar']['name'];
				$path = pathinfo($name);
				$size = filesize($_FILES['new_avatar']['tmp_name']);
				$info = getimagesize($_FILES['new_avatar']['tmp_name']);
				
				if (!in_array($path["extension"], array("jpg","jpeg","gif","png")))
				{
					$errors[] = "Avatars may only have the extension .jpg, .jpeg, .gif, or .png.";
				}
				elseif ($size >= 100*1024)
				{
					$errors[] = "Avatars must be less than 100KB.";
				}
				elseif ($info[0] > 60 || $info[1] > 60)
				{
					// First two elements in the array are width and height
					$erorrs[] = "Avatars cannot be larger than 60px by 60px";
				}
				else 
				{
					// No problems with the new avatar, flag it for upload
					$newAvatar = true;
				}
			}
			
			// If there are no errors, set the new values
			if (count($errors) == 0)
			{
				$GLOBALS['super']->user->displayname = $displayname;
				$GLOBALS['super']->user->email = $email;
				$GLOBALS['super']->user->signature = $signature;
				if ($changePass)
				{
					$GLOBALS['super']->user->password = $newpass;
				}
				if ($newAvatar)
				{
					// New location is ROOT_PATH/images/USERIDNUMBER
					$info = pathinfo($_FILES['new_avatar']['name']);
					$newPath =  "images/avatar_".$GLOBALS['super']->user->id.".".$info["extension"];
					move_uploaded_file($_FILES['new_avatar']['tmp_name'], ROOT_PATH.$newPath);
					
					// Now we need to add the image to the image table, then link it in the user table
					$GLOBALS['super']->db->query("INSERT INTO ".TBL_PREFIX."images (`name`, `url`) VALUES ('Avatar', '".FORUM_ROOT.$newPath."')");
					
					// Image id
					$id = $GLOBALS['super']->db->fetch_lastid();
					
					$GLOBALS['super']->user->avatar = $id;
					//$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."users SET `avatar`=".$id." WHERE `id` = ".$GLOBALS['super']->user->id." LIMIT 1");
				}
			}
		}
		elseif ($_POST['action'] == "personal") // If they are on the account page
		{
			// Get the variables we care about
			$fname = (isset($_POST['fname'])) ? $_POST['fname'] : "";
			$lname = (isset($_POST['lname'])) ? $_POST['lname'] : "";
			$gender = (isset($_POST['gender'])) ? $_POST['gender'] : "";
			$country = (isset($_POST['country'])) ? $_POST['country'] : "";
			$birth_month = (isset($_POST['birth_month'])) ? $_POST['birth_month'] : 0;
			$birth_day = (isset($_POST['birth_day'])) ? $_POST['birth_day'] : 0;
			$birth_year = (isset($_POST['birth_year'])) ? $_POST['birth_year'] : 0;
			
			// Check that the gender is in our white list
			if (!in_array($gender, array("m", "f")))
			{
				$errors[] = "You have specified an incorrect gender.";
			}
			
			// If the birthmonth is not between 1 and 12
			if (!($birth_month >= 1 && $birth_month <= 12))
			{
				$errors[] = "You have specified an incorrect birth month.";
			}
			
			// if we are within the current year
			if (!($birth_year >= 1930 && $birth_year <= intval(date("Y"))))
			{
				$errors[] = "You have specified an incorrect birth year.";
			}
			
			// if we are at a day that existed in the specified month and day
			if (!($birth_day >= 1 && $birth_day <= cal_days_in_month(CAL_GREGORIAN, $birth_month, $birth_year)))
			{
				$errors[] = "You have specified an incorrect birth day.";
			}
			// If there are no errors, set the new values
			if (count($errors) == 0)
			{
				$GLOBALS['super']->user->firstname = $fname;
				$GLOBALS['super']->user->lastname = $lname;
				$GLOBALS['super']->user->gender = $gender;
				$GLOBALS['super']->user->country = $country;
				
				$GLOBALS['super']->user->birth_day = $birth_day;
				$GLOBALS['super']->user->birth_month = $birth_month;
				$GLOBALS['super']->user->birth_year = $birth_year;
				
			}
		}
		elseif ($_POST['action'] == "display")
		{
			$dateformat = (isset($_POST['dateformat'])) ? $_POST['dateformat'] : "";
			$timeformat = (isset($_POST['timeformat'])) ? $_POST['timeformat'] : "";
			$timezone = (isset($_POST['timezone'])) ? $_POST['timezone'] : "";
			$postsperpage = (isset($_POST['postsperpage'])) ? intval($_POST['postsperpage']) : 0;
			$topicsperpage = (isset($_POST['topicsperpage'])) ? intval($_POST['topicsperpage']) : 0;
			
			// Check that the gender is in our white list
			if (!in_array($timezone, DateTimeZone::listIdentifiers()))
			{
				$errors[] = "You have specified an invalid timezone.";
			}
			
			if ($postsperpage <= 0)
				$errors[] = "You must show at least 1 post per page.";
				
				
			if ($topicsperpage <= 0)
				$errors[] = "You must show at least 1 post per page.";
				
			if (count($errors) == 0)
			{
				$GLOBALS['super']->user->date_format = $dateformat;
				$GLOBALS['super']->user->time_format = $timeformat;
				$GLOBALS['super']->user->time_zone = $timezone;
				$GLOBALS['super']->user->posts_per_page = $postsperpage;
				$GLOBALS['super']->user->topics_per_page = $topicsperpage;
			}
		}
		elseif ($_POST['action'] == "privacy")
		{
			$showonline = (isset($_POST['show_online'])) ? $_POST['show_online'] : 1;
			$senddigests = (isset($_POST['send_digests'])) ? $_POST['send_digests'] : 0;
			
			if (!in_array($showonline, array(1, 0)))
				$errors[] = "You must choose whether to show yourself on the online list.";
				
			if (!in_array($senddigests, array(1, 0)))
				$errors[] = "You must choose whether to recieve digests.";
			
			if (count($errors) == 0)
			{
				$GLOBALS['super']->user->show_online = $showonline;
				$GLOBALS['super']->user->send_digests = $senddigests;
			}
		}
		
		if (count($errors) == 0)
		{
			$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
			$success->add("message","Profile updated successfully!");
			$success->add("url","index.php?act=controlPanel&page=".$_POST['action']);
			echo $success->parse();
		}
		
		return $errors;
	}
}
?>