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
 File: /pages/fdisplay.php
 Author: SaroSoftware
 Date Created: 3/8/09
 Last Edited By: Eli White
 Last Edited Date: 4/8/09
 Latest Changes:
 	4/8/09 - Converted to a class
	3/11/09 - Working on pagination

 Comments:
	Shows the forum view for a forum.

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

class fDisplay extends forumPage
{
	protected $currentForum;
	protected $noForum;
	protected $escapedId;

	public function __construct()
	{
		$this->noForum = false;

		parent::__construct();


		// We need to check if we have already set our forum (in the case where we are the parent of a topic page)
		if (empty($this->currentForum))
		{
			// Get the current ID from the url
			$id = $_GET['id'];
		}
		else
		{
			$id = $this->currentForum['id'];
		}

		// and sanitize it
		$id = intval($id);
		$id = $GLOBALS['super']->db->escape($id);
		$this->escapedId = $id;

		$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE `id`=".$id." AND `active`='1'";

		$query = $GLOBALS['super']->db->query($query);

		if ($GLOBALS['super']->db->getRowCount($query) == 0)
		{
			$this->noForum = true;
			return;
		}

		$this->currentForum = $GLOBALS['super']->db->fetch_assoc($query);
		// Set the title to be the current forum name
		$this->setName($this->currentForum['name']);

		// If we are a redirect forum...lets increment our counter and redirect
		if ($this->currentForum['isRedirect'] == "1")
		{

			$hits = "UPDATE ".TBL_PREFIX."forums SET `redirectHits`=`redirectHits`+1 WHERE `id` = ".$this->currentForum['id'];
			$GLOBALS['super']->db->query($hits);

			header('Location: '.$this->currentForum['redirectURL']);
		}
	}

	public function getJS()
	{
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/hoverIntent.js");
		$js[] = array("path" => "includes/js/jquery.tools.min.js");
		$js[] = array("path" => "includes/js/viewforum.js");
		return $js;
	}

	public function onlineName()
	{
		return "Viewing Forum: ".$this->getName();
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
		if ($this->noForum)
			return $parentCrumb;

		$parentArray = explode(",",$this->currentForum['parent_ids']); // an array form of the string of parents

		// We need to remove ourselves from the breadcrumbs
		//array_shift($parentArray);

		// recursively go through all the parents
		$breadcrumbs = "";
		$breadcrumbs .= $this->recurseNames($parentArray);

		return $parentCrumb." ".
			$breadcrumbs;
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
			return;
		}
		else
		{
			if ($this->currentForum['is_cat'] == 1)
			{
				// this forum is a cat, we can't post to it
				$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
				$error->add("error_message", "This forum is marked as a category and does not accept posts.");
				echo $error->parse();
			}
			elseif ($this->noForum)
			{
				// this forum is a cat, we can't post to it
				$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
				$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
				echo $error->parse();
			}

			// Run our class that displays all of the forums by using the ShowForums class and pass it our "Category"
			require_once(ROOT_PATH.'includes/classes/forum_display.php');
			$displayForum = new showForums($this->escapedId, "Sub-Forums");
			// we want to echo it, not return it
			$displayForum->display(false);

			// We need to parse our top bar that includes the number of posts and topics,
			// forum name and, forum description as well as the new topic/poll buttons

			$topBar = new tpl(ROOT_PATH.'themes/Default/templates/forum_infobar.php');
			$topBar->add("forum_name",$this->currentForum['name']);

			// Store a variable we can check in the template to see if the forum has a description
			if ($this->currentForum['description'] != "")
				$hasDescription = "true";
			else
				$hasDescription = "false";

			$topBar->add("hasDescription", $hasDescription);
			$topBar->add("forum_description", $this->currentForum['description']);
			$topBar->add("topic_count", $this->currentForum['topic_count']." Topics");
			$topBar->add("post_count", $this->currentForum['post_count']." Replies");

			$forumActions = array();

			// if we can post topics, add the link
			if ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'],"NewTopic"))
			{
				$forumActions[] = array(
					'url' =>	FORUM_ROOT."index.php?act=postTopic&amp;id=".$this->currentForum['id'],
					'name'	=>	"New Topic"
				);
			}
			$topBar->add("LINKS", $forumActions);

			// START PAGINATION STUFF

			// We also need to know how many total pages we have.
			$totalPages = "SELECT count(id) FROM ".TBL_PREFIX."topics WHERE `forum_id`=".$this->currentForum['id'];
			$totalPages = $GLOBALS['super']->db->query($totalPages);
			$totalPages = $GLOBALS['super']->db->fetch_result($totalPages);

			$totalPages = max(1, $totalPages);

			// We need to know what the current page is.
			if (!isset($_GET['page']))
				$curPage = 1;
			else
				// we don't want to let them choose negative pages or even page 0. Max it to 1
				$curPage = max(1,intval($_GET['page']));

			// What is the start of all of our links. Just add a number to the end.
			$paginationLinks = 'index.php?act=fdisplay&amp;id='.$this->currentForum['id'].'&amp;page=';


			// a constant that is saved in the user table
			$topicsPerPage = $GLOBALS['super']->user->topics_per_page;
			//$horizonalDistance = 2; // How many to display touching each other. 1 or 2?

			// Put the total number of pages into the template
			$topBar->add("page_count", ceil($totalPages/$topicsPerPage));

			// Lets call our class that will return an array of all of our links ready to be put in the template
			list($paginationArray, $startPost) = $GLOBALS['super']->functions->doPagination($totalPages, $curPage, $paginationLinks, $topicsPerPage);


			// put our array that contains all the topic's information into our template
			$topBar->add("PAGINATION", $paginationArray['PAGES']);
			$topBar->add("curpage", $curPage);

			// END PAGINATION STUFF


			echo $topBar->parse();


			$topic_sql = "SELECT * FROM ".TBL_PREFIX."topics WHERE `forum_id`=".$this->currentForum['id']." ORDER BY `time_modified` DESC LIMIT ".$startPost.", ".$topicsPerPage;
			$topics = $GLOBALS['super']->db->query($topic_sql);

			// we need to check if we actually have any topics.
			// If not, lets show an error, if we do, lets loop through and
			// stick into our
			if ($GLOBALS['super']->db->getRowCount($topics) > 0)
			{
				// Initialize our template that contains the topics header and
				// a loop for each row
				$topicRow = new tpl(ROOT_PATH.'themes/Default/templates/forum_topicrow.php');


				// Display the moderation column
				$canMod = false;

				// loop through each topic in this forum
				while($topic = $GLOBALS['super']->db->fetch_assoc($topics))
				{



					$poster = $GLOBALS['super']->functions->getUser($topic['user_id'], true);

					$lastPostTime = $topic['time_modified'];
					$lastPostDate = $GLOBALS['super']->functions->formatDate($lastPostTime);
					$lastposter = $GLOBALS['super']->functions->getUser($topic['last_user_id'], true);

					$preview = $GLOBALS['super']->db->query("SELECT `message` FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$topic['id']." ORDER BY `time_added` ASC LIMIT 1");
					$preview = $GLOBALS['super']->db->fetch_result($preview);

					$bbCode = new bbCode();

					// Remove all the new lines from $preview
					$preview = str_replace("\n", " ", $preview);
					$preview = $bbCode->stripBBCode($preview);
					$preview = dotdotdot($preview, 115);

					$image = FORUM_ROOT."themes/Default/images/icon_old.gif";
					if ($lastPostTime > $GLOBALS['super']->user->read_time && !$GLOBALS['super']->user->has_viewed_topic($topic['id']))
						$image = FORUM_ROOT."themes/Default/images/icon_new.gif";

					$canDelete = ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteOthers") && $topic['user_id'] != $GLOBALS['super']->user->id)
								|| ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteSelf") && $topic['user_id'] == $GLOBALS['super']->user->id);

					// If we can delete, but haven't set the canMod flag yet, set it.
					if ($canDelete && !$canMod)
						$canMod = true;

					// create a slot in the array that contains this forum's info
					$structure_array['TOPICS'][] = array(
						'id'	=>	$topic['id'],
						'image'	=> $image,
						// add the information here
						'link'	=>	'index.php?act=tdisplay&amp;id='.$topic['id'],
						'name'	=>	$topic['name'],
						'starter'	=>	$poster,
						'replies'	=>	$topic['post_count'],
						'views'		=>	$topic['view_count'],
						'lastpostdate'	=>	$lastPostDate,
						'lastposter'	=> $lastposter,
						'preview'		=> $preview,
						'canDelete'		=> $canDelete
						);
				}
				// put our array that contains all the topic's information into our template
				$topicRow->add("TOPICS", $structure_array['TOPICS']);

				$topicRow->add("canMod", $canMod);

				echo $topicRow->parse();
			}
			else
			{
				// we have no topics, lets tell the user by using the error template
				$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
				$error->add("error_message", "This forum currently has no topics.");
				echo $error->parse();
			}
		}

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>