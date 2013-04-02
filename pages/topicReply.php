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
 File: /pages/topicReply.php
 Author: SaroSoftware
 Date Created: 3/29/09
 Last Edited By: Eli White
 Last Edited Date: 4/18/10
 Latest Changes:
 	4/18/10 - Fixed a permissions issue
 	4/10/09 - Converted to class
	3/29/09 - Initial Creation
 
 Comments:
	The reply to topic page.

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

class topicReply extends tDisplay
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setName("Post Reply");
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
	function getJS()
	{
		//Syntax Highlighter package root
		$highlighterRoot = "includes/packages/Syntax Highlighter/";

		$js = parent::getJS();
		$js[] = array("path" => $highlighterRoot."scripts/shCore.js");

		$syntaxScripts = scandir($highlighterRoot."scripts");
		foreach($syntaxScripts as $syntax)
		{
			// is_dir returns true for '.' and '..'
			if (!is_dir($syntax))
			{
				$js[] = array("path" => $highlighterRoot."scripts/".$syntax);
			}
		}
		
		$js[] = array("path" => "includes/js/syntaxHighlighter.js");
		
		return $js;
	}
	
	function getCSS()
	{
		$highlighterRoot = "includes/packages/Syntax Highlighter/";

		$css = parent::getCSS();
		$css[] = array("path" => $highlighterRoot."styles/shCore.css");
		$css[] = array("path" => $highlighterRoot."styles/shThemeDefault.css");
		return $css;
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
		
		// to be allowed to reply, you must be able to view the board, view the forum, and be able to make replies in the forum
		if (!$GLOBALS['super']->user->can("ViewBoard") ||
			!$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "View") ||
			!$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "Reply")
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
		
		if (isset($_POST['formsent']) && $_POST['formsent'] == "1")
		{
			if (isSecureForm("topicReply"))
			{
				// for now, lets ignore preview
	
				$message = $_POST['message'];
				
			
				if (strlen($message) <= 10)
				{
					$errorArray[] = "The message must be greater than 10 characters";
				}
				
				if (!count($errorArray))
				{
					$topicId = $GLOBALS['super']->db->escape(intval($_GET['id']));
					$message = $GLOBALS['super']->db->escape(strip_tags($message));
					
					// for testing purposes, we need to make this dynamic
					// when we add the user system
					$posterid = $GLOBALS['super']->user->id; 
					
					// Add the post
					$addPostSql = "INSERT INTO ".TBL_PREFIX."posts (
								`topic_id`,
								`user_id`,
								`time_added`,
								`message`
								)
								VALUES (
								'".$topicId."', '".$posterid."',".time().", '".$message."'
								);
								";
					$GLOBALS['super']->db->query($addPostSql);
					$postid = $GLOBALS['super']->db->fetch_lastid();
					
					// Update user post count
					$updateUsersSQL = "UPDATE ".TBL_PREFIX."users SET `total_posts` = `total_posts`+1 WHERE `id`='".$posterid."'";
					$GLOBALS['super']->db->query($updateUsersSQL);
					
					
					// Update topic post count
					$updateTopicSQL = "UPDATE ".TBL_PREFIX."topics SET `post_count` = `post_count`+1, `time_modified`=".time().", `last_user_id`=".$posterid." WHERE `id`='".$topicId."'";
					//echo $updateTopicSQL;
					$GLOBALS['super']->db->query($updateTopicSQL);
					
					
					// Update forum post count
					$forumId = "SELECT `forum_id` FROM ".TBL_PREFIX."topics WHERE `id`=".$topicId;
					$forumId = $GLOBALS['super']->db->query($forumId);
					$forumId = $GLOBALS['super']->db->fetch_result($forumId);
					
					$updateTopicSQL = "UPDATE ".TBL_PREFIX."forums SET `post_count` = `post_count`+1, `last_post_id`=".$postid." WHERE `id`='".$forumId."'";
					$GLOBALS['super']->db->query($updateTopicSQL);
					
					
					// we successfully made the post
					$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
					$success->add("message","Reply made successfully!");
					$success->add("url","index.php?act=tdisplay&id=".$topicId.'&page=last');
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

			if (isset($errorArray) && count($errorArray) > 0)
			{
					// we have errors, we need to print them. List format is probably good
			?>
			<div class="error">
				<strong>There Were Errors in Your Post</strong>
				<ul>
				<?php
					
				foreach($errorArray as $error)
				{
					echo '<li>'.$error.'<li>';
				}
				?>
				</ul>
			</div>
			<?php
				// end the error printing
			}
			?>
			<div class="catrow">
				Post Reply
			</div>
			<div class="contentbox">
			
				<div class="postBox">
					
					<form action="?act=topicReply&amp;id=<?php echo $_GET['id']?>" method="post" >
						<p>
							<?php secureForm("topicReply"); ?>
							<input type="hidden" name="formsent" value="1" />
							Message:<br />
							<div class="postArea">
								<!--<ul class="buttons">
									<li><img src="<?php echo FORUM_ROOT?>themes/Default/images/editor/bold.png" alt="" /></li>
									<li><img src="<?php echo FORUM_ROOT?>themes/Default/images/editor/italic.png" alt="" /></li>
									<li><img src="<?php echo FORUM_ROOT?>themes/Default/images/editor/underline.png" alt="" /></li>
								</ul>-->
								<textarea name="message" class="post" rows="40" cols="20"><?php if (isset($message)) echo $message ?></textarea>
								<br />
								<div class="buttonwrapper">
									<input type="submit" class="button" name="submit" value="Post Reply" />
								</div>
							</div>
						</p>
					</form>
				</div>
			</div>
			
			<?php
			// We want to display the last 10 posts that have been posted in this topic.
			$review = new tpl(ROOT_PATH.'themes/Default/templates/postreply_review.php');
			
			// What topic are we in?
			$topicId = $GLOBALS['super']->db->escape(intval($_GET['id']));
			
			// Query to get the last 10 posts in this topic in reverse order
			$last10 = "SELECT * FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$topicId." ORDER BY `time_added` DESC LIMIT 10";
			$last10 = $GLOBALS['super']->db->query($last10);
			
			// Go through each one
			while($postMess = $GLOBALS['super']->db->fetch_assoc($last10))
			{
				$user = $GLOBALS['super']->functions->getUser($postMess['user_id'], true);
				// And add it to an array
				$postMessages[] = array(
					'date' => $GLOBALS['super']->functions->formatDate($postMess['time_added']),
					'user' => $user,
					'usercolor' => $user[1],
					'message' => $bbCode->parse($postMess['message']),
				);
			}
			$review->add("topicurl", '?act=tdisplay&id='.$topicId);
			// Put the array in our template loop
			$review->add("POSTS", $postMessages);
			echo $review->parse();

		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}	
}
?>