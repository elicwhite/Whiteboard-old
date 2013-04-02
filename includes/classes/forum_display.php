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
 File: /includes/showForums.php
 Author: SaroSoftware
 Date Created: 3/8/09
 Last Edited By: Eli White
 Last Edited Date: 3/8/09
 Latest Changes:
	- Initial Creation
 
 Comments:
	Included by /pages/main.php and /page/fdisplay.php to show all the children
	forums of a category

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

class showForums
{
	// The forum id we are using as a category to find all the children of
	private $catid;
	private $db;
	
	private $catName;
	
	private $functions;
	
	/**
	 * Constructs a class to display all of the children
	 * of the passed parameter.
	 *
	 * @param string $catid the category you want to display all the forums of. Must already be sanitized by db->escape() so that it contains the ' around it.
	 */	
	function __construct($catid, $catname = "")
	{
		
		$this->functions = $GLOBALS['super']->functions;
		$this->db = $GLOBALS['super']->db;
		
		$this->catid = $catid;
		$this->catName = $catname;
		
	}
	
	/**
	 * Displays all the forums that are children of the $catid field
	 *
	 * @param boolean $return true if you want it to return the info, false if you want it to echo.
	 */
	function display($return = true)
	{
		// Grab all the forums that are direct sub forums of this category
		$forum_sql = '
SELECT f.*,
(
	SELECT count(t.id)
	FROM '.TBL_PREFIX.'topics AS t
	INNER JOIN '.TBL_PREFIX.'forums AS f ON t.forum_id=f.id
	WHERE f.parent_id="'.$this->catid.'" AND
	NOT FIND_IN_SET(t.id, "'.$GLOBALS['super']->user->read_topics.'") AND
	t.time_modified > "'.$GLOBALS['super']->user->read_time.'"
) AS num_unread_count
FROM '.TBL_PREFIX.'forums AS f
WHERE f.parent_id="'.$this->catid.'"
ORDER BY f.sort_order ASC';
		
		
		//$forum_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `parent_id`=".$this->catid." AND `active`='1' ORDER BY `sort_order` ASC";
		//echo $forum_sql;
		$forum_query = $this->db->query($forum_sql);
		if (
			$this->db->getRowCount($forum_query) == 0 ||
			!$GLOBALS['super']->user->can("Forum".$this->catid,"View")
			)
		{
			// we have no children forums, or we aren't allowed to view this
			// category. lets stop.
			return;
		}
		
		$content = new tpl(ROOT_PATH.'themes/Default/templates/main_forum_display.php'); // include the content tpl file
		$content->add("NAME", $this->catName);
		// Initialize and run through each forum
		
		
		
		$structure_array = array();
		while ($forum = $this->db->fetch_assoc($forum_query))
		{
			if ($GLOBALS['super']->user->can("Forum".$forum['id'],"View"))
			{
				$temparray = array();
				
				$temparray['name'] = $forum['name'];
				$temparray['link'] = "index.php?act=fdisplay&amp;id=".$forum['id'];
				$temparray['description'] = $forum['description'];
				$temparray['post_activity'] = $forum['post_count'];
				$temparray['topic_activity'] = $forum['topic_count'];
				$temparray['redirectHits'] = $forum['redirectHits'];
				$temparray['hasChildren'] = false;
				
				// store the forum into an array with information
				$children_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `parent_id`='".$forum['id']."' AND `active`='1' ORDER BY `sort_order` ASC";
				$children_query = $this->db->query($children_sql);
				
				$childrenStr = "";		
				
				if ($this->db->getRowCount($children_query) > 0)
				{
					
					while($children = $this->db->fetch_assoc($children_query))
					{
						/*if ($children['isRedirect'] == 1)
							$link = $children['redirectURL'];
						else
						*/
						if ($GLOBALS['super']->user->can("Forum".$children['id'],"View"))
						{
							$temparray['hasChildren'] = true;
							$link = "index.php?act=fdisplay&amp;id=".$children['id'];
		
							$childrenStr .= '<a href="'.$link.'">'.$children['name']."</a>, ";
						}
					}
					// Fence post issue, remove the last string and space from the string
					$childrenStr = substr($childrenStr, 0, -2);
					$temparray['children'] = $childrenStr;
				}
					
				
				if ($forum['isRedirect'] == 1)
				{
					$temparray['icon'] = FORUM_ROOT."themes/Default/images/icon_redir.gif";
					//$link = $forum['redirectURL'];
					$postinfo = "Redirect";
					$temparray['forumType'] = "redirect";
				}
				else
				{
					//$temparray['icon'] = "themes/Default/images/icon_old.gif";
					if ($forum['num_unread_count'] == 0)
						$temparray['icon'] = FORUM_ROOT."themes/Default/images/icon_old.gif";
					else
						$temparray['icon'] = FORUM_ROOT."themes/Default/images/icon_new.gif";
					//$link = "index.php?act=fdisplay&amp;id=".$forum['id'];
					
					$temparray['forumType'] = "stdForum";
					
				
					// We need to make the info about the last post
					if ($forum['last_post_id'] == 0)
					{
						$temparray['forumType'] = "noPosts";
						// we have no posts, we need to define these vars
						$lastPost['topic_id'] = $lastPost['name'] = $lastPost['user_id'] = $lastPostName = $lastPostDate = "";
					}
					else
					{
						// information about the last post
						$lastPostSQL = "
							SELECT 	t.name, t.id AS topicid,
									p.*
									
									FROM ".TBL_PREFIX."posts AS p
									INNER JOIN ".TBL_PREFIX."topics AS t
									ON p.topic_id=t.id
									WHERE p.id='".$forum['last_post_id']."'";
						$lastPost = $this->db->query($lastPostSQL);
						$lastPost = $this->db->fetch_assoc($lastPost);
						
						$lastPostTime = $lastPost['time_added'];
						$temparray['lastPostDate'] = $this->functions->formatDate($lastPostTime);
						//echo "Poster: ".$lastPost['user_id']."<br />";
						$temparray['lastPoster'] = $this->functions->getUser($lastPost['user_id'], true);
						
						//if ($lastPostTime > $GLOBALS['super']->user->read_time && !$GLOBALS['super']->user->has_viewed_forum($forum['id']))
						//	$temparray['icon'] = FORUM_ROOT."themes/Default/images/icon_new.gif";
						
						$temparray['name'] = $temparray['name'];
						$temparray['topic'] = dotdotdot($lastPost['name'], 20);
						$temparray['topiclink'] = 'index.php?act=tdisplay&amp;id='.$lastPost['topicid'].'&amp;page=last';
					}
				}
				$structure_array[] = $temparray;
			}
		}
		// insert our array of forums into our template loop
		$content->add("FORUMS", $structure_array);
		if ($return)
			return $content->parse();
		else
			echo $content->parse();
	}
}