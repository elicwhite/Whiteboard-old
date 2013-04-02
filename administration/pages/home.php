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
 File: /administration/pages/home.php
 Author: SaroSoftware
Date Created: 07/05/2007
 Last Edited By: Eli White
 Last Edited Date: 5/6/09
 Latest Changes:
 	- Converted to class format
 
 Comments:
	Creates the forum index

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
class Home extends adminPage
{
	
	
	
	public function __construct()
	{
		parent::__construct();
		$this->setName("Home");
	}
	
	
	public function children()
	{
		$children[] = array('text' => 'Info', 'act' => 'info', 'redirect' => 'No');
		$children[] = array('text' => 'Board', 'act' => 'board', 'redirect' => 'Yes', 'link' => FORUM_ROOT);
		//$children[] = array('text' => 'License', 'act' => 'license', 'redirect' => 'No');
		//$children[] = array('text' => 'Statistics', 'act' => 'statistics', 'redirect' => 'No');
		
		return $children;
	}
	
	public function setSub($sub)
	{
		switch ($sub)
		{
			case "License":
				break;
			case "Statistics":
				break;
			// Info and everything else
			default:
				require_once(ADMIN_PATH.'pages/home_info.php');
	    		$this->sub = new HomeInfo();
				break;
		}
		return $this->sub->getName();
	}
	
	/**
	 * Gets the content/main bulk of the page
	 *
	 * @return string The content
	 */
	public function display()
	{
		ob_start();
		
		echo $this->sub->display();
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
}

?>