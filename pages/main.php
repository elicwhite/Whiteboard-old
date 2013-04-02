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
 File: /pages/main.php
 Author: SaroSoftware
 Date Created: 04/24/2007
 Last Edited By: Eli White
 Last Edited Date: 4/8/09
 Latest Changes:
 	- Converted to class format
	- Added sub forums and re-styled redirect forums
 
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
class Main extends forumPage
{

	public function __construct()
	{
		parent::__construct();
		$this->setName($GLOBALS['super']->config->siteName);
	}

	public function onlineName()
	{
		return "Board Index";
	}
	
	/**
	 * Get the display of the page
	 *
	 * @return string The page content
	 */
	public function display()
	{
		ob_start();
		
		if ($GLOBALS['super']->user->can("ViewBoard"))
		{
			// We need to get all the main categories
			$cat_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `is_cat`='1' ORDER BY `sort_order` ASC";
			$cat_query = $GLOBALS['super']->db->query($cat_sql);
			
			// and loop through them
			while ($cat = $GLOBALS['super']->db->fetch_assoc($cat_query))
			{
				// Run our class that displays all of the forums by using the ShowForums class and pass it our "Category"
				require_once(ROOT_PATH.'includes/classes/forum_display.php');
				$displayForum = new showForums($cat['id'], $cat['name']);
				// we want to echo it, not return it
				$displayForum->display(false);
			}
		}	
		else 
		{
			// they aren't allowed to view the board, show an error
			echo $GLOBALS['super']->user->noPerm();
		}
		

			
		// We need to get some counts to display information in the bottom statistics
		$memberCount = "SELECT COUNT(id) FROM ".TBL_PREFIX."users";
		$memberCount = $GLOBALS['super']->db->query($memberCount);
		$memberCount = $GLOBALS['super']->db->fetch_result($memberCount, 0);
		
		$topicCount = "SELECT COUNT(id) FROM ".TBL_PREFIX."topics";
		$topicCount = $GLOBALS['super']->db->query($topicCount);
		$topicCount = $GLOBALS['super']->db->fetch_result($topicCount, 0);
		
		$postCount = "SELECT COUNT(id) FROM ".TBL_PREFIX."posts";
		$postCount = $GLOBALS['super']->db->query($postCount);
		$postCount = $GLOBALS['super']->db->fetch_result($postCount, 0);
		
		$newestMember = "SELECT id
			FROM ".TBL_PREFIX."users 
			ORDER BY time_added DESC LIMIT 1";
		$newestMember = $GLOBALS['super']->db->query($newestMember);
		$newestMember = $GLOBALS['super']->db->fetch_result($newestMember);
		$newestMember = $GLOBALS['super']->functions->getUser($newestMember, true);
		
		
		$stats = new tpl(ROOT_PATH.'themes/Default/templates/stats.php'); // include the content tpl file
		$stats->add("MEMBERS", $memberCount);
		$stats->add("TOPICS", $topicCount);
		
		// posts - topics because each topic has one post that is the main post for that topic and shouldn't be counted?
		$stats->add("POSTS", $postCount-$topicCount);
		
		$stats->add("NEWESTMEMBER", $newestMember);
		
		
		// We need to get the number of guests and all of the members that are online
		$guestsOnline = "SELECT COUNT(*) FROM ".TBL_PREFIX."online WHERE `userid`=0";
		$guestsOnline = $GLOBALS['super']->db->fetch_result($GLOBALS['super']->db->query($guestsOnline));
		
		$stats->add("GUESTSONLINE", $guestsOnline);
		
		$usersOnline = "SELECT userid
			 FROM ".TBL_PREFIX."online
			 WHERE userid !=0 ORDER BY `time_modified` DESC";
		
		//echo $usersOnline;
		
		$usersOnline = $GLOBALS['super']->db->query($usersOnline);
		$numUsersOnline = $GLOBALS['super']->db->getRowCount($usersOnline);
		$stats->add("NUMUSERSONLINE", $numUsersOnline);
		
		$users = array();
		
		while($user = $GLOBALS['super']->db->fetch_assoc($usersOnline))
		{
			//for($i = 0; $i <= 100; $i++) {
				$users[] = $GLOBALS['super']->functions->getUser($user['userid'], true);
			
		}
		
		$stats->add("USERSONLINE", $users);
		
		
		echo $stats->parse();
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}

?>


	