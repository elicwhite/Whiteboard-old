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
 File: /pages/login.php
 Author: SaroSoftware
 Date Created: 04/12/09
 Last Edited By: Eli White
 Last Edited Date: 9/11/09
 Latest Changes:
 	
 
 Comments:
	Shows the login page
	
Latest Changes:
	9/11/09 - Post Login always redirects to the forum root.

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

class Login extends forumPage
{

	public function __construct()
	{
		
		parent::__construct();
		$this->setName("Login");
		
		// we validate here so it effects the entire page load.
		if (isset($_GET['do']) && $_GET['do'] == "login" && isset($_POST['username']) && isset($_POST['password']))
		{
			$username = $_POST['username'];
			$password = $_POST['password'];
			$remember = (isset($_POST['remember']) && $_POST['remember'] == "1");
			$GLOBALS['super']->user->checkLogin($username, $password, $remember);
			
			if ($GLOBALS['super']->user->isLogged())
			{
				$updatequery = "UPDATE ".TBL_PREFIX."users SET `last_login` = ".time()." WHERE `id` = ".$GLOBALS['super']->user->id;
				$GLOBALS['super']->db->query($updatequery);
			}
		}
		elseif (isset($_GET['do']) && $_GET['do'] == "out")
		{
			$GLOBALS['super']->user->logout();
		}
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
		
		$display = true;
		
		if (isset($_GET['do']) && $_GET['do'] == "login")
		{
			// if we logged in successfully.
			if ($GLOBALS['super']->user->isLogged())
			{
				$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
				$success->add("message", "Logged In Successfully");
				// we want to redirect to the page we were at before
				
				if (!isset($_SESSION['loginredirect']))
					$redirect = FORUM_ROOT;
				else
					$redirect = $_SESSION['loginredirect'];

				$success->add("url", $redirect);
				
				$display = false;
				
				echo $success->parse();	
						
				// and erase that var
				unset($_SESSION['loginredirect']);
				
				
			}
			else 
			{
				$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
				$error->add("error_message", "Incorrect Username or Password");
				echo $error->parse();	
			}
		}
		elseif(isset($_GET['do']) && $_GET['do'] == "out")
		{
			$display = false;
			
			$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
			$success->add("message", "Logged Out Successfully");
			$success->add("url", FORUM_ROOT);
			echo $success->parse();	
		}
		
		if ($display)
		{
			// We want to save the page we were on before, so we can go back to it
			if (isset($_SERVER['HTTP_REFERER']) && !isset($_SESSION['loginredirect']))
			{
				$_SESSION['loginredirect'] = $_SERVER['HTTP_REFERER'];
			}
			
			
			$login = new tpl(ROOT_PATH.'themes/Default/templates/login.php');
			echo $login->parse();	
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}
?>