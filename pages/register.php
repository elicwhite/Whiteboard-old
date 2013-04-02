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
 File: /pages/register.php
 Author: SaroSoftware
 Date Created: 04/12/09
 Last Edited By: Eli White
 Last Edited Date: 4/12/09
 Latest Changes:
 	
 
 Comments:
	Shows the registration page

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

require_once(ROOT_PATH.'includes/classes/recaptchalib.php');

class Register extends forumPage
{

	public function __construct()
	{
		parent::__construct();
		$this->setName("Register");
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

	/**
	 * Get the display of the page
	 *
	 * @return string The page content
	 */
	public function display()
	{
		ob_start();
		
		// whether to display the registration page
		$display = true;
		
		if (isset($_GET['do']) && $_GET['do'] == "register" && $_POST['formsent'] == "1")
		{
			// the error array
			$errors = array();
			
			// usernames must be alpha numeric and between 4 and 16 chars
			$username = $_POST['username'];
			$username= $GLOBALS['super']->db->escape($username);
			
			if (!ctype_alnum($username))
				$errors[] = "Usernames must contain only alphanumerics.";
			
			if (strlen($username) < 5 || strlen($username) > 20)
				$errors[] = "Usernames must be 5-20 characters.";
			
			$checkUser = "SELECT * FROM ".TBL_PREFIX."users WHERE `username`='".$username."'";
			$checkUser = $GLOBALS['super']->db->query($checkUser);
			$checkUser = $GLOBALS['super']->db->getRowCount($checkUser);
			
			if ($checkUser != 0)
				$errors[] = "That username is already taken";
			
			// displaynames must be alpha numeric and between 4 and 16 chars
			$displayname = $_POST['displayname'];
			$displayname=$GLOBALS['super']->db->escape($displayname);
			
			if (!ctype_print($displayname))
				$errors[] = "Displaynames may not contain special characters.";
			
				
			$checkDisplay = "SELECT * FROM ".TBL_PREFIX."users WHERE `displayname`='".$displayname."'";
			$checkDisplay = $GLOBALS['super']->db->query($checkDisplay);
			$checkDisplay = $GLOBALS['super']->db->getRowCount($checkDisplay);
			
			if ($checkDisplay != 0)
				$errors[] = "That displayname is already taken";
			
			$pass1 = $_POST['password'];
			$pass2 = $_POST['password2'];
			
			// passes must match
			if ($pass1 != $pass2)
				$errors[] = "Your passwords do not match";
			// and it must be greater than 5 chars
			elseif (strlen($pass1) <= 5)
				$errors[] = "Your password must be greater than 5 characters";
				
			$email = $_POST['email'];
			// emails must be valid
			if (!preg_match("/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/",$email))
				$errors[] = "You must enter a valid email address";
						
			$resp = recaptcha_check_answer ($_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

			if (!$resp->is_valid)
				$errors[] ="The CAPTCHA wasn't entered correctly";
			
			if (count($errors) == 0)
			{
				$display = false;
				
				$pass = encrypt_password($pass1);
				// should be unneccessary
				$pass = $GLOBALS['super']->db->escape($pass);
				$email = $GLOBALS['super']->db->escape($email);
		
				$addUserSql = 
				"INSERT INTO ".TBL_PREFIX."users (
					`username`,
					`displayname`,
					`password`,
					`email`,
					`time_added`,
					`group_id`,
					`theme_id`,
					`posts_per_page`,
					`topics_per_page`,
					`read_time`,
					`show_online`
					)
					VALUES (
					'".$username."',
					'".$displayname."',
					'".$pass."',
					'".$email."',
					'".time()."',
					'2',
					'1',
					'".$GLOBALS['super']->config->postsPerPage."',
					'".$GLOBALS['super']->config->topicsPerPage."',
					'".time()."',
					'1'
					);
					";
				
				$GLOBALS['super']->db->query($addUserSql);
				
				$error = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
				
				$str = "Registered Successfully!";
				$extra = "You may now login with your new account".
				
				$error->add("message", $str);
				$error->add("extramessage", $extra);
				$error->add("url", FORUM_ROOT);
				$error->parse();
				
			}
			
		}
		
		if (isset($errors) && count($errors) > 0)
		{
			$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
			$error->add("error_message", $errors);
			$error->parse();
		}
		
		if ($display)
		{
			$register = new tpl(ROOT_PATH.'themes/Default/templates/register.php');
			$register->add("CAPTCHAHTML", recaptcha_get_html());
	
			echo $register->parse();
		}
		
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}

?>


	