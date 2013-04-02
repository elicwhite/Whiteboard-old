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
 File: /pages/tdisplay.php
 Author: SaroSoftware
 Date Created: 3/27/09
 Last Edited By: Eli White
 Last Edited Date: 4/10/09
 Latest Changes:
 	4/10/09	- Converted to class
	3/27/09 - Started design and creation

TODO:
	- Go to the last page in pagination (first too?)

 Comments:
	Shows the topic layout for a topic

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

class tDisplay extends forumPage
{
	protected $noTopic;
	protected $currentTopic;

	protected $currentForum;

	/**
	 * Constructor for display topics
	 *
	 * @param int $tid 	If this is defined, this is the topic id, not the value from the url
	 */
	public function __construct($tid = null)
	{
		$this->noTopic = false;
		$this->noForum = false;

		parent::__construct();

		if ($tid)
			$id = $tid;
		else
		{
			// Get the current topic ID from the url
			if (!isset($_GET['id']))
			{
				$this->noTopic = true;
				return;
			}
			else
				$id = $_GET['id'];
		}

		// and sanitize it
		$id = intval($id);
		$id = $GLOBALS['super']->db->escape($id);

		$topic_sql = "SELECT * FROM ".TBL_PREFIX."topics WHERE `id`=".$id;
		$topic = $GLOBALS['super']->db->query($topic_sql);

		if ($GLOBALS['super']->db->getRowCount($topic) == 0 || !isset($_GET['id']))
		{
			$this->noTopic = true;
			return;
		}

		// Save a variable with topic info
		$this->currentTopic = $GLOBALS['super']->db->fetch_assoc($topic);

		$forum_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `id`=".$this->currentTopic['forum_id'];
		$forum = $GLOBALS['super']->db->query($forum_sql);

		if ($GLOBALS['super']->db->getRowCount($forum) == 0)
		{
			$this->noForum = true;
			return;
		}

		$this->currentForum = $GLOBALS['super']->db->fetch_assoc($forum);

		$this->setName($this->currentTopic['name']);
	}

	public function onlineName()
	{
		return "Viewing Topic: ".$this->getName();
	}

	/**
	 * Returns the calendar's breadcrumbs
	 *
	 * @return string The breadcrumb string
	 */
	public function breadCrumb()
	{
		$parentCrumb = parent::breadCrumb();

		// If this forum doesn't exist, we need to short circuit the breadcrumbs
		if ($this->noTopic)
			return $parentCrumb;

		$parentArray = explode(",",$this->currentForum['parent_ids']); // an array form of the string of parents

		// We need to remove ourselves from the breadcrumbs
		//array_shift($parentArray);

		// recursively go through all the parents
		$breadcrumbs = "";
		$breadcrumbs .= $this->recurseNames($parentArray);

		// Add the page name
		$breadcrumbs .= " ".$GLOBALS['super']->config->crumbSeperator.' <a href="?act=tdisplay&amp;id='.$this->currentTopic['id'].'">'.$this->currentTopic['name']."</a>";

		return $parentCrumb." ".
			$breadcrumbs;

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
		if (!$GLOBALS['super']->user->can("ViewBoard") || !$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "View"))
		{
			echo $GLOBALS['super']->user->noPerm();
		}
		elseif ($this->noTopic)
		{
			// either their is no forum with this id, or they didn't enter an id. Most likely a URL issue.
			$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
			$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
			echo $error->parse();
		}
		else
		{
			// the forum exists

			// Lets update our view count
			$updateViews = "UPDATE ".TBL_PREFIX."topics SET `view_count`=`view_count`+1 WHERE `id`=".$this->currentTopic['id'];
			$GLOBALS['super']->db->query($updateViews);

			// Re update our topic with our new count
			//$topic = $GLOBALS['super']->db->query($topic_sql);


			// we have now viewed this topic, save it in the database.
			$GLOBALS['super']->user->viewed_topic($this->currentTopic['id']);//, $this->currentForum['id']);

			// We need to parse our top bar that includes the number of replies and views,
			// forum name and, forum description as well as the new topic/poll buttons

			$topBar = new tpl(ROOT_PATH.'themes/Default/templates/forum_infobar.php');
			$topBar->add("forum_name",$this->currentTopic['name']);

			$topBar->add("newTopicLink", '');
			$topBar->add("hasDescription", "true");
			$topBar->add("forum_description", "By: ".$GLOBALS['super']->functions->getUser($this->currentTopic['user_id'], true));
			$topBar->add("topic_count", $this->currentTopic['post_count']." Replies");
			// +1 on the views because we are adding a view and showing the page
			$topBar->add("post_count", $this->currentTopic['view_count']+1 ." Views");

			$forumActions = array();

			if ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'],"Reply"))
			{
				$forumActions[] = array(
						'url' =>	FORUM_ROOT."index.php?act=topicReply&amp;id=".$this->currentTopic['id'],
						'name'	=>	"Post Reply"
					);
			}
			$topBar->add("LINKS", $forumActions);

			//$post_sql = "SELECT * FROM ".TBL_PREFIX."posts WHERE `topicid`=".$id;



			// START PAGINATION STUFF

			// We also need to know how many total pages we have.
			$totalPages = "SELECT count(id) FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$this->currentTopic['id'];
			$totalPages = $GLOBALS['super']->db->query($totalPages);
			$totalPages = $GLOBALS['super']->db->fetch_result($totalPages);

			$totalPages = max(1, $totalPages);

			// a constant that is saved in the user table
			$postsPerPage = $GLOBALS['super']->user->posts_per_page;

			// Put the total number of pages into the template
			$pageCount = ceil($totalPages/$postsPerPage);

			//if ($totalPages == 0) // We don't have any pages, no posts
			//{
				// We need to know what the current page is.
				if (!isset($_GET['page']))
					$curPage = 1;
				else if($_GET['page'] == "last")
				{
					$curPage = $pageCount;
				}
				else
				{
					// we don't want to let them choose negative pages or even page 0. Max it to 1
					$curPage = max(1,intval($_GET['page']));
				}

				// What is the start of all of our links. Just add a number to the end.
				$paginationLinks = 'index.php?act=tdisplay&amp;id='.$this->currentTopic['id'].'&amp;page=';



				//$horizonalDistance = 2; // How many to display touching each other. 1 or 2?


				$topBar->add("page_count", $pageCount);

				// Lets call our class that will return an array of all of our links ready to be put in the template
				list($paginationArray, $startPost) = $GLOBALS['super']->functions->doPagination($totalPages, $curPage, $paginationLinks, $postsPerPage);
				//$GLOBALS['super']->functions->print_array($paginationArray, false);




				// put our array that contains all the topic's information into our template
				$topBar->add("PAGINATION", $paginationArray['PAGES']);
				$topBar->add("curpage", $curPage);
			//}

			// END PAGINATION STUFF


			echo $topBar->parse();

			$bbCode = new bbCode();



			$post_sql = "SELECT * FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$this->currentTopic['id']." ORDER BY `time_added` ASC LIMIT ".$startPost.", ".$postsPerPage;
			$posts = $GLOBALS['super']->db->query($post_sql);

			// we need to check if we actually have any posts.
			// If not, lets show an error, if we do, lets loop through and
			// stick into our
			if ($GLOBALS['super']->db->getRowCount($posts) > 0)
			{
				// Initialize our template that contains the topics header and
				// a loop for each row
				$topicRow = new tpl(ROOT_PATH.'themes/Default/templates/postview.php');

				// loop through each post in this topic
				while($post = $GLOBALS['super']->db->fetch_assoc($posts))
				{

					$poster = $GLOBALS['super']->functions->getUser($post['user_id']);
					$posterName = $poster['formatted'];

					$joinDate = $GLOBALS['super']->functions->formatDate($poster['time_added'], true);
					$postDate = $GLOBALS['super']->functions->formatDate($post['time_added']);
					// create a slot in the array that contains this forum's info

					$editDate = 0;
					if ($post['last_edited'] != 0)
						$editDate = $GLOBALS['super']->functions->formatDate($post['last_edited']);

					$canEdit = false;
					// if this user can edit other people's posts, or they made the post
					// and they can edit their own post
					if (($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "EditOthers") && $GLOBALS['super']->user->id != $post['user_id'])||
						($post['user_id'] == $GLOBALS['super']->user->id && $GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "EditSelf")))
					{
						$canEdit = true;
					}

					$canDelete = false;
					// if this user can edit other people's posts, or they made the post
					// and they can edit their own post
					if ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteOthers") ||
						($post['user_id'] == $GLOBALS['super']->user->id && $GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteSelf")))
					{
						$canDelete = true;
					}

					$structure_array['POST'][] = array(
						// add the information here
						'id'		=>	$post['id'],
						'avatar'	=>	$GLOBALS['super']->functions->getImage($poster['avatar']),
						'signature'	=>	$bbCode->parse($poster['signature']),
						'poster'	=>	$posterName,
						'postdate'	=>	$postDate,
						'group'		=>	$poster['groupname'],
						'groupcolor'	=>	$poster['color'],
						'joindate'		=>	$joinDate,
						'postcount'	=>	$poster['total_posts']+$poster['total_topics'],
						'message'	=>	$bbCode->parse($post['message']),
						'canEdit'	=>	$canEdit,
						'canDelete'	=>	$canDelete,
						'editDate'	=>	$editDate
						);
				}
				// put our array that contains all the topic's information into our template
				$topicRow->add("POSTS", $structure_array['POST']);
				echo $topicRow->parse();
			}
			else
			{
				// we have no topics, lets tell the user by using the error template
				$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
				$error->add("error_message", "An error has occurred.");
				echo $error->parse();
			}

		}

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>