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
 File: /pages/deleteTopic.php
 Author: SaroSoftware
 Date Created: 2/13/10
 Last Edited By: Eli White
 Last Edited Date: 2/13/10
 Latest Changes:
	2/13/10 - Initial creation
 
 Comments:
	Delete a topic page.

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

class deleteTopic extends tDisplay
{
	//private $currentTopic;
	
	public function __construct()
	{
		parent::__construct();
		$this->setName("Delete Topic");
	}
	
	public function getName()
	{
		return $this->currentTopic['name']." / ".$this->pageName;
	}
	
	public function onlineName()
	{
		return $this->pageName.": ".$this->currentTopic['name'];
	}
	
	/**
	 * Returns the calendar's breadcrumbs
	 *
	 * @return string The breadcrumb string
	 */
	public function breadCrumb()
	{
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->pageName;
	}
	
	
	/**
	 * Get the display of the page
	 *
	 * @return string The page content
	 */
	public function display()
	{
		ob_start();

		/*
		you can delete if you are allowed to view the board
		or if you can delete other's and you didn't post it
		or if you can delete your own posts and you posted it
		*/
		if (!$GLOBALS['super']->user->can("ViewBoard")
			&&
			(
				!($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteOthers") && $this->currentPost['user_id'] != $GLOBALS['super']->user->id)
				|| !($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteSelf") && $this->currentPost['user_id'] == $GLOBALS['super']->user->id)
			)
		)
		{
			echo $GLOBALS['super']->user->noPerm();
			return;
			
		}
		else if (isSecureForm("deleteTopic") && isset($_POST['formsent']) && $_POST['formsent'] == "1")
		{

			// Delete the topic
			$query = $GLOBALS['super']->db->query("DELETE FROM ".TBL_PREFIX."topics WHERE `id`=".$this->currentTopic['id']);
			
			// We need to get the last post in the forum. We may have deleted it.
			$lastPost = $GLOBALS['super']->db->query("SELECT p.id FROM ".TBL_PREFIX."posts AS p INNER JOIN ".TBL_PREFIX."topics AS t ON p.topic_id=t.id INNER JOIN ".TBL_PREFIX."forums AS f ON t.forum_id = f.id WHERE f.id=".$this->currentForum['id']." ORDER BY p.time_added DESC LIMIT 1");
			
			// If we have more posts
			if ($GLOBALS['super']->db->getRowCount($lastPost) > 0)
				$lastPost = $GLOBALS['super']->db->fetch_result($lastPost);
			else 
			{
				// otherwise there are no more posts in this forum
				$lastPost = 0;
			}
			
			
			// Update the last post field in forums
			$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."forums SET `last_post_id`=".$lastPost.", `topic_count` = ".($this->currentForum['topic_count']-1) .", `post_count`=".($this->currentForum['post_count']-$this->currentTopic['post_count'])."  WHERE `id`=".$this->currentForum['id']);
			
			
			
			// we successfully made the post
			$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
			$success->add("message","Topic Deleted Successfully!");

			$url = "index.php?act=fdisplay&id=".$this->currentTopic['forum_id'];

			$success->add("url",$url);
			echo $success->parse();

		}
		elseif (!isset($_POST['formsent']) && !$this->noTopic)
		{
			
			$delete = new tpl(ROOT_PATH.'themes/Default/templates/deletetopic.php');
			$delete->add("topicName", $this->currentTopic['name']);			
			echo $delete->parse();

		}
		else
		{
			// either their is no forum with this id, or they didn't enter an id. Most likely a URL issue.
			$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
			$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
			echo $error->parse();
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>