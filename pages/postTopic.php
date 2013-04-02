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
 File: /pages/postTopic.php
 Author: SaroSoftware
 Date Created: 3/10/09
 Last Edited By: Eli White
 Last Edited Date: 5/3/09
 Latest Changes:
 	5/3/09 - Topics now post as the person who posted them
	4/8/09 - Converted to a class that inherits from fDisplay to get
			the proper breadcrumb behavior and title behavior.
			No sense recopying code.
 	3/29/09 - Added topbar links
 
 Comments:
	The new topic page.

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
class postTopic extends fDisplay
{
	public function __construct()
	{
		parent::__construct();
		$this->setName("New Topic");
	}
	
	public function getName()
	{
		return $this->currentForum['name']." / ".$this->pageName;
	}

	public function onlineName()
	{
		return $this->pageName.": ".$this->currentForum['name'];
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
		// to be allowed to post, you must be able to view the board, view the forum, and be able to make new topics in the forum
		if (!$GLOBALS['super']->user->can("ViewBoard") ||
			!$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "View") ||
			!$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "NewTopic")
		)
		{
			echo $GLOBALS['super']->user->noPerm();
		}
		elseif (isSecureForm() && isset($_POST['formsent']) && $_POST['formsent'] == "1")
		{
			// for now, lets ignore preview
			
			// An array that contains all the validation errors, if no errors then we can
			// go ahead and submit, otherwise we need to print them all to the user
			$errorArray = array();
			
			$subject = $_POST['subject'];
			$message = $_POST['message'];
			
			
			if (strlen($subject) <= 4 || strlen($subject) > 35)
			{
				$errorArray[] = "The subject must be greater than 4 characters and less than 35";
			}
			if (strlen($message) <= 10)
			{
				$errorArray[] = "The message must be greater than 10 characters";
			}
			
			
			if (!count($errorArray))
			{
				$id = $GLOBALS['super']->db->escape(intval($_GET['id']));
				$subject = $GLOBALS['super']->db->escape($subject);
				$message = $GLOBALS['super']->db->escape($message);
				
				// for testing purposes, we need to make this dynamic
				// when we add the user system
				$posterid = $GLOBALS['super']->user->id; 
				
				
				$addTopicSql = "INSERT INTO ".TBL_PREFIX."topics (
							`id` ,
							`name` ,
							`forum_id` ,
							`user_id` ,
							`time_added` ,
							`time_modified`,
							`last_user_id`
							)
							VALUES (
							NULL , '".$subject."', '".$id."','".$posterid."', ".time().", ".time().", '".$posterid."'
							);
							";
				$GLOBALS['super']->db->query($addTopicSql);
				
				// we need to get the id of the topic we just added, lucky us, a function to do
				// just that.
				$topicId = $GLOBALS['super']->db->escape($GLOBALS['super']->db->fetch_lastid());
				
				$addPostSql = "INSERT INTO ".TBL_PREFIX."posts (
							`id` ,
							`topic_id` ,
							`user_id` ,
							`time_added` ,
							`message`
							)
							VALUES (
							NULL , '".$topicId."', '".$posterid."', ".time().", '".$message."')";
				
				$GLOBALS['super']->db->query($addPostSql);
				
				$postid = $GLOBALS['super']->db->fetch_lastid();
				
				// We need to update the count on the forum this is a child of. Why?
				//	so we don't have tons of nested queries on the main page. Maybe it would be better
				// than 3 queries for adding a topic.
				$updateForumCountSQL = "UPDATE ".TBL_PREFIX."forums SET `topic_count` = `topic_count`+1, `last_post_id` = '".$postid."' WHERE `id` ='".$id."' LIMIT 1";
				$GLOBALS['super']->db->query($updateForumCountSQL);
				
				// We also need to increment a counter on the user table for the number of posts. Are we sure we should be making this many queries here?
				$updateUsersSQL = "UPDATE ".TBL_PREFIX."users SET `total_topics` = `total_topics`+1 WHERE `id`='".$posterid."'";
				$GLOBALS['super']->db->query($updateUsersSQL);
				
				
				// we successfully made the topic
				$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
				$success->add("message","Topic made successfully!");
				$success->add("url","index.php?act=fdisplay&id=".$id);
				echo $success->parse();
				
			}
		}
		
		if (!isset($_POST['formsent']) || count($errorArray))
		{
			?>
			<div class="catrow">
				Add Topic
			</div>
			<div class="contentbox">
			
				<div class="postBox">
					<?php
					if (isset($errorArray) && count($errorArray))
					{
							// we have errors, we need to print them. List format is probably good
					?>
					<div class="error">
						<strong>There Were Errors in Your Topic</strong>
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
					<form action="?act=postTopic&amp;id=<?php echo $_GET['id']?>" method="post" >
						<p>
							<?php secureForm(); ?>
							<input type="hidden" name="formsent" value="1" />
							Subject:<br />
							<input class="text" type="text" name="subject" value="<?php if (isset($subject)) echo $subject ?>" size="40" /><br /><br />
							Message:<br />
							<div class="postArea">
								<!--<ul class="buttons">
									<li><img src="<?php echo FORUM_ROOT?>themes/Default/images/editor/bold.png" alt="" /></li>
									<li><img src="<?php echo FORUM_ROOT?>/themes/Default/images/editor/italic.png" alt="" /></li>
									<li><img src="<?php echo FORUM_ROOT?>/themes/Default/images/editor/underline.png" alt="" /></li>
								</ul>-->
								<textarea name="message" class="post" rows="40" cols="20"><?php if (isset($message)) echo $message ?></textarea>
								<br />
								<div class="buttonwrapper">
									<input type="submit" class="button" name="submit" value="Create Thread" />
								</div>
							</div>
						</p>
					</form>
				</div>
			</div>
			<?php
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}

?>


	