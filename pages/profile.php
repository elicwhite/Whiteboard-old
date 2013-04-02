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
 File: /pages/profile.php
 Author: SaroSoftware
 Date Created: 4/6/09
 Last Edited By: Eli White
 Last Edited Date: 6/15/09
 Latest Changes:
 6/15/09 - Converted to templates
 4/11/09 - Added some statistics queries and info
 4/9/09 - Converted to a class
	
 TODO:
 	Link to search
 	Show the birthdate
 	Link the contact information
 
 Comments:
	Profile page for users

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
// require the bbcode class, we will need it to display the topics
require_once(ROOT_PATH."includes/classes/bbCode.php");


class Profile extends forumPage
{
	private $escapedId;
	private $noUser = false;
	private $currentUser;
	private $userName2Display;
	
	public function __construct()
	{
		parent::__construct();
		
		$id = $_GET['id'];
		// and sanitize it
		$id = intval($id);
		$id = $GLOBALS['super']->db->escape($id);
		$this->escapedId = $id;
		
		$userSelect = "SELECT u.*, g.color, g.name FROM ".TBL_PREFIX."users AS u
				INNER JOIN ".TBL_PREFIX."groups AS g
				ON u.group_id =g.id
				WHERE u.id=".$id;
		$userSelect = $GLOBALS['super']->db->query($userSelect);
		
		if ($GLOBALS['super']->db->getRowCount($userSelect) == 0)
		{
			$this->noUser = true;
			return;
		}
		
		$this->currentUser = $GLOBALS['super']->db->fetch_assoc($userSelect);
		// Set the title to be the current userName
		$this->userName2Display = $GLOBALS['super']->functions->user2Display($this->currentUser['username'], $this->currentUser['displayname']);
		$this->setName($this->userName2Display);
		
	}
	
	public function getName()
	{
		return "Viewing ".$this->pageName."'s Profile";
	}

	/**
	 * Gets the Javascript that this page requests
	 *
	 * @return array of the urls of the javascript documents
	 */
	public function getJS()
	{
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/profile.js");
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
		
		$bbCode = new bbCode();
		
		
		// We need to get the number of topics
		$topics = "SELECT count(`id`) FROM ".TBL_PREFIX."topics WHERE `user_id`=".$this->currentUser['id'];
		$topics = $GLOBALS['super']->db->query($topics);
		$topics = $GLOBALS['super']->db->fetch_result($topics);
		
		$totalTopics = $GLOBALS['super']->db->fetch_result($GLOBALS['super']->db->query("SELECT count(`id`) FROM ".TBL_PREFIX."topics"));
		$topicPercent = round(($topics/$totalTopics)*100,2);
		
		// We need to get the number of posts
		$posts = "SELECT count(`id`) FROM ".TBL_PREFIX."posts WHERE `user_id`=".$this->currentUser['id'];
		$posts = $GLOBALS['super']->db->query($posts);
		$posts = $GLOBALS['super']->db->fetch_result($posts) - $topics;
		
		$totalPosts = $GLOBALS['super']->db->fetch_result($GLOBALS['super']->db->query("SELECT count(`id`) FROM ".TBL_PREFIX."posts")) - $totalTopics;
		
		// If we have no posts at all on the forum, pretend like we have 1
		$totalPosts = max(1, $totalPosts);
		
		$postPercent = round(($posts/$totalPosts)*100,2);
		
		$daysSinceRegister = time()-$this->currentUser['time_added'];
		$daysSinceRegister = $daysSinceRegister / (60 * 60 * 24); // seconds * minutes * hours = 1 day
		// set to whole days
		$daysSinceRegister = ceil($daysSinceRegister);
		
		$postsPerDay = round($posts/$daysSinceRegister,1);
		$topicsPerDay = round($topics/$daysSinceRegister,1);
		
		// We want to find the topic that we replied to the most, it is
		// designated as our "favorite" because we are most active in it
		
		// we subtract 1 from the count so we don't count the original topic
		// BUT IN THE CASE WHERE WE DIDNT MAKE THE TOPIC, THIS IS WRONG!
		$favoriteTopic = 'SELECT count(p.id) as Count, p.topic_id, t.name, t.user_id FROM '.TBL_PREFIX.'posts AS p INNER JOIN '.TBL_PREFIX.'topics AS t ON p.topic_id=t.id WHERE p.user_id='.$this->currentUser['id'].' GROUP BY p.topic_id ORDER BY Count DESC LIMIT 1';
		$favoriteTopic = $GLOBALS['super']->db->query($favoriteTopic);
		$favoriteTopic = $GLOBALS['super']->db->fetch_assoc($favoriteTopic);
		
		$biggestTopic = 'SELECT * FROM '.TBL_PREFIX.'topics WHERE `user_id`='.$this->currentUser['id'].' ORDER BY `post_count` DESC LIMIT 1';
		$biggestTopic = $GLOBALS['super']->db->query($biggestTopic);
		$biggestTopic = $GLOBALS['super']->db->fetch_assoc($biggestTopic);
		// add one to count the original topic's message
		$biggestCount = $biggestTopic['post_count']+1;
		
		$lastPost = 'SELECT p.*, t.name AS topicName FROM '.TBL_PREFIX.'posts AS p INNER JOIN '.TBL_PREFIX.'topics AS t ON p.topic_id=t.id WHERE p.user_id='.$this->currentUser['id'].' ORDER BY p.time_added DESC LIMIT 1';
		$lastPost = $GLOBALS['super']->db->query($lastPost);
		$lastPost = $GLOBALS['super']->db->fetch_assoc($lastPost);
		
		$lastPostDate = $GLOBALS['super']->functions->formatDate($lastPost['time_added']);
		
		$avatar = $GLOBALS['super']->functions->getImage($this->currentUser['avatar']);
		$signature = $bbCode->parse($this->currentUser['signature']);
		// We want to know what page this user is currently on. Check if they
		// are on the online list, if not they are "Offline"
		
		$online = "SELECT `page` FROM ".TBL_PREFIX."online WHERE `userid`=".$this->currentUser['id'];
		$online = $GLOBALS['super']->db->query($online);
		$onlinecount = $GLOBALS['super']->db->getRowCount($online);
				
		$fname = $this->currentUser['firstname'];
		$lname = $this->currentUser['lastname'];
		
		$gender = $this->currentUser['gender'];
		$country = $this->currentUser['country'];
		$bday = $this->currentUser['birth_day'];
		$bmonth = $this->currentUser['birth_month'];
		$byear = $this->currentUser['birth_year'];
		
		$website = $this->currentUser['website'];
		
		$name = "";
		if ($fname)
		{
			$name .= $fname;
			if ($lname)
				$name .= " ".$lname;
		}
		
		//echo "N:".$name;
		
		$personal = array();
		if ($name)
			$personal[] = array("Real Name", $name);
		
		if ($gender == "m")
			$gender = "Male";
		elseif ($gender == "f")
			$gender = "Female";
		
		if ($gender)	
			$personal[] = array("Gender", $gender);
			
			
		if ($country)
			$personal[] = array("Country", $country);
		if ($website)
			$personal[] = array("Website", '<a href="'.$website.'">'.$website.'</a>');
		
		
		if ($bday && $bmonth && $byear)
		{
			$timestamp = mktime(null, null, null, $bmonth, $bday, $byear);
			$personal[] = array("Birth Day", date("F jS, Y", $timestamp));
		}
			
		
		if($onlinecount > 0)
			// if we are on the online list
			$page = $GLOBALS['super']->db->fetch_result($online);	
		else 
			$page = "Offline";
		
		$tpl = new tpl(ROOT_PATH.'themes/Default/templates/profile.php');
		$tpl->add("UserName", $this->userName2Display);
		$tpl->add("Avatar", $avatar);
		$tpl->add("currentUser", $this->currentUser);
		$tpl->add("Page", $page);
		$tpl->add("TopicCount", $topics);
		$tpl->add("TopicPercent", $topicPercent);
		$tpl->add("PostCount", $posts);
		$tpl->add("PostPercent", $postPercent);
		$tpl->add("TopicsPerDay", $topicsPerDay);
		$tpl->add("PostsPerDay", $postsPerDay);
		$tpl->add("FavoriteTopic", $favoriteTopic);
		$tpl->add("BiggestCount", $biggestCount);
		$tpl->add("BiggestTopic", $biggestTopic);
		$tpl->add("LastPost", $lastPost);
		$tpl->add("LastPostDate", $lastPostDate);
		$tpl->add("Personal", $personal);
		$tpl->add("Signature", $signature);
		$tpl->parse();
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}

?>


	