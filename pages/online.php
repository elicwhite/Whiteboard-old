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
 File: /pages/online.php
 Author: SaroSoftware
 Date Created: 04/11/09
 Last Edited By: Eli White
 Last Edited Date: 5/4/09
 Latest Changes:
 	5/4/09 - Online list is now unique by sessionIds. Allows multiple people from the same IP
 		to be on at the same time.
 
 Comments:
	Shows the online list

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
class Online extends forumPage
{

	public function __construct()
	{
		parent::__construct();
		$this->setName("Online List");
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
		
		if (!$GLOBALS['super']->user->can("ViewBoard"))
		{
			echo $GLOBALS['super']->user->noPerm();
		}
		else 
		{
			global $onlineHelper;
			// We are assuming that there will always be one user online
			// The person viewing the page?
			
			
			list($guestCount, $userCount, $info) = $onlineHelper->generate();
			
				
			$tpl = new tpl(ROOT_PATH.'themes/Default/templates/onlinelist.php');
			
			$tpl->add("online",$info);
			$tpl->add("guestcount",$guestCount);
			$tpl->add("usercount",$userCount);
			
			echo $tpl->parse();	
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	
	function getJS()
	{
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/onlinelist.js");
		
		return $js;
	}
}

?>


	