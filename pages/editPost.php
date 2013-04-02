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
 File: /pages/editPost.php
 Author: SaroSoftware
 Date Created: 6/3/09
 Last Edited By: Eli White
 Last Edited Date: 7/17/09
 Latest Changes:
	7/17/09 - Can now edit post message body
 
 Comments:
	The edit a post page.

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

class editPost extends tDisplay
{
	private $currentPost;
	
	public function __construct()
	{
		$pid = $_GET['id'];
		$pid = intval($pid);
		$pid = $GLOBALS['super']->db->escape($pid);
		
		$post_sql = "SELECT * FROM ".TBL_PREFIX."posts WHERE `id`=".$pid;
		$post = $GLOBALS['super']->db->query($post_sql);
		
		if ($GLOBALS['super']->db->getRowCount($post) >= 0)
		{
			$this->currentPost = $GLOBALS['super']->db->fetch_assoc($post);
			parent::__construct($this->currentPost['topic_id']);
		}
		
		
		
		$this->setName("Edit Post");
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
		
		$bbCode = new bbCode();
		
		// An array that contains all the validation errors, if no errors then we can
		// go ahead and submit, otherwise we need to print them all to the user
		$errorArray = array();
		
		/*
		you can edit if you are allowed to view the board
		or if you can edit other's and you didn't post it
		or if you can edit your own posts and you posted it
		*/
		if (!$GLOBALS['super']->user->can("ViewBoard")
			&&
			(
				!($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "EditOthers") && $this->currentPost['user_id'] != $GLOBALS['super']->user->id)
				|| !($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "EditSelf") && $this->currentPost['user_id'] == $GLOBALS['super']->user->id)
			)
		)
		{
			echo $GLOBALS['super']->user->noPerm();
			return;
			
		}
		elseif ($this->noTopic)
		{
			// either their is no forum with this id, or they didn't enter an id. Most likely a URL issue.
			$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
			$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
			echo $error->parse();
		}
		if(isset($_POST['formsent']) && $_POST['formsent'] == "1")
		{
			if (isSecureForm("editPost"))
			{
			
			
				
				$message = $_POST['message'];
				
			
				if (strlen($message) <= 10)
				{
					$errorArray[] = "The message must be greater than 10 characters";
				}
				
				if (!count($errorArray))
				{
					
					$postId = $GLOBALS['super']->db->escape(intval($this->currentPost['id']));
					$message = $GLOBALS['super']->db->escape($message);
					
					$posterid = $GLOBALS['super']->user->id; 
					
					// Edit the post
					$updatePost = "UPDATE ".TBL_PREFIX."posts SET `message`='".$message."', `last_edited`='".time()."' WHERE `id`=".$postId;
					$GLOBALS['super']->db->query($updatePost);
					
					// we successfully made the post
					$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
					$success->add("message","Post Edited Successfully!");
					$success->add("url","index.php?act=tdisplay&id=".$this->currentPost['topic_id']);
					echo $success->parse();
				}	
			}
			else 
			{
				$errorArray[] = "You cannot attempt to make a different post after this form has been opened.";
			}
		}
		
		
		if (!isset($_POST['formsent']) || count($errorArray))
		{
			
			$edit = new tpl(ROOT_PATH.'themes/Default/templates/editpost.php');
			$edit->add("topicName", $this->currentTopic['name']);
			$edit->add("message", $this->currentPost['message']);
			
			echo $edit->parse();

		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}	
}
?>