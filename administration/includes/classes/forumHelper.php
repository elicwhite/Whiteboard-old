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
File: /administration/includes/classes/forumHelper.php
Author: SaroSoftware
Date Created: 07/26/2007
Last Edited By: Eli White
Last Edited Date: 5/6/2009
Latest Changes:
 -Added a ton of functions with descriptions

 TODO:
 	Implement the ability to change a parent forum through the edit page
 	Delete forums
 	
Comments:
Forum addition and editing.

-----*/

class fHelper
{
	public $forum_list=array();
	public $structure_array=array();
	
	function __construct()
	{
	}

	function get_forum_list()
	{
		$cat_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `is_cat`='1' ORDER BY `sort_order` ASC";
		$cat_query = $GLOBALS['super']->db->query($cat_sql);
		//$this->forum_list[''] = '---None---';
		$this->forum_list = array();
		
		// If we are currently on a forum (edit page) then we need to build the correct list with the parent forum selected
		if (isset($_GET['fid']))
		{
			// lets get our forumid
			$fid = intval($_GET['fid']);
			
			// Get the parent id
			$parentID = "SELECT `parent_id` FROM ".TBL_PREFIX."forums WHERE `id`=".$GLOBALS['super']->db->escape($fid);
			$parentID = $GLOBALS['super']->db->query($parentID);
			$parentID = $GLOBALS['super']->db->fetch_result($parentID, 0);
			//echo $parentID;
		}
		else
		{
			// We don't have any information, these just need to be different values
			$fid = -1;
			$parentID = -2;
		}
		//echo "Fid: ".$fid.", ParentID: ".$parentID."<br />";
		while ($cat = $GLOBALS['super']->db->fetch_assoc($cat_query))
		{
			// Switch our class in our template if we have found our parent
			if ($cat['id'] == $parentID)
			{
				$selected = "true";
				//echo "true<br />";
			}
			else
			{
				$selected = "false";
			}	
				
			$this->forum_list[] = array('id' => $cat['id'], 'name' => $cat['name'], 'selected' => $selected);
			
			$this->get_forums($cat['id'], $parentID);
		}
		//$GLOBALS['super']->functions->print_array($this->forum_list);
		return $this->forum_list;
	}
	
	/**
	 * Helper for get_forum_list() 
	 *
	 * @param integer $parent_id	The id of the forum we want to find all the children of
	 * @param integer $parentID 	The overall parentid of the forum you are trying to set as selected in the dropdown
	 */
	function get_forums($parent_id, $parentID=0)
	{
		static $counter = 0;

		$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE `parent_id`='".$parent_id."' ORDER BY `sort_order` ASC";
		$result = $GLOBALS['super']->db->query($query);

		
		while($row = $GLOBALS['super']->db->fetch_assoc($result))
		{
			$counter++;	
			$dashes = str_repeat("|--",$counter);
			
			//echo "Fid: ".$fid.", ParentID: ".$parentID."<br />";
			
			if ($row['id'] == $parentID)
			{
				$selected = "true";
				//echo "true<br />";
			}
			else
				$selected = "false";
			
			//$this->forum_list[$row['id']] = $dashes.$row['name'];
			$this->forum_list[] = array('id' => $row['id'], 'name' => $dashes.$row['name'], 'selected' => $selected);
			$this->get_forums($row['id'], $parentID);
			$counter--;
		}
	}
	
	function get_structurelist()
	{
		$cat_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `is_cat`='1' ORDER BY `sort_order` ASC";
		$cat_query = $GLOBALS['super']->db->query($cat_sql);
		while ($cat = $GLOBALS['super']->db->fetch_assoc($cat_query))
		{	
			$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE `id`='".$cat['id']."'";
			$result = $GLOBALS['super']->db->query($query);
			$row = $GLOBALS['super']->db->fetch_assoc($result);
			$sortorder = $row['sort_order'];
			$this->structure_array['Structure'][] = array('id' => $row['id'], 'name' => $row['name'], 'sort_order' => $sortorder, 'indent' => "", 'class' => 'admin_class');		
			$this->get_structuredforums($cat['id'], true);
		}
	}
	function get_structuredforums($parent_id)
	{
		static $counter = 1;
		static $class = 'admin_level1';

		$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE `parent_id`='".$parent_id."' ORDER BY `sort_order` ASC";
		$result = $GLOBALS['super']->db->query($query);
		while($row = $GLOBALS['super']->db->fetch_assoc($result))
		{
			if($class == "admin_line1")
			{
				$class = "admin_line2";
			}
			else
			{
				$class = "admin_line1";
			}
			
			$dashes = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$counter*2);
			
			$sortorder = $row['sort_order'];
			$this->structure_array['Structure'][] = array('id' => $row['id'], 'name' => $row['name'], 'sort_order' => $sortorder, 'indent' => $dashes, 'class' => $class);
			$counter++;
			$this->get_structuredforums($row['id']);
			
			$counter--;
			
		}
		
	}









	/***
	Function: add()
	This function adds in a new forum to the database.  It then rebuilds the parent list and child list based off of the previous insertions.
	All forums must be built this way to stay in unison.
	***/
	function add()
	{
		
		$is_cat = $GLOBALS['super']->db->escape($_POST['is_cat'], true);
		
		// If we are a category, it doesn't matter what the idiot user put as a parent
		if ($is_cat == "'1'")
			$parent_id = 0;
		else
			$parent_id = intval($_POST['parent_id']);
			
		$escapedParentId = $GLOBALS['super']->db->escape($parent_id, true);
		
		$forum_name = $GLOBALS['super']->db->escape($_POST['forum_name'], true);
		$forum_description = $GLOBALS['super']->db->escape($_POST['forum_description'], true);
		$active = $GLOBALS['super']->db->escape($_POST['active'], true);
		
		$theme = $GLOBALS['super']->db->escape($_POST['theme'], true);
		$date = $GLOBALS['super']->db->escape(date("Y-m-d H:i:s"), true);
		
		$isRedirect = $GLOBALS['super']->db->escape(intval($_POST['redirect']), true);
		$url = $GLOBALS['super']->db->escape($_POST['redirecturl'], true);

		// We want to get the value sort order should be to put it at the end
		$sortOrder = 'SELECT `sort_order` FROM '.TBL_PREFIX.'forums WHERE `parent_id`='.$escapedParentId.' ORDER BY `sort_order` DESC LIMIT 1';
		$sortOrder = $GLOBALS['super']->db->query($sortOrder);
		
		// if we don't get any rows, it means our sort order is 1
		if ($GLOBALS['super']->db->getRowCount($sortOrder) == 0)
		{
			$sortOrder = 1;
		}
		else
		{
			// otherwise we have to add it to the end of our current forums
			$sortOrder = $GLOBALS['super']->db->fetch_result($sortOrder);
			// we want 1 PLUS whatever the curren't highest sort order is. (add it to the end)
			$sortOrder++;
			// It should already be safe, but this couldn't hurt.
			$sortOrder = $GLOBALS['super']->db->escape($sortOrder);
		}
		
		$GLOBALS['super']->db->query("INSERT INTO ".TBL_PREFIX."forums (name, description, is_cat, sort_order, parent_id, active, theme_id, isRedirect, redirectURL, mod_user_ids, mod_group_ids, time_added)
			VALUES (".$forum_name.",".$forum_description.",".$is_cat.", ".$sortOrder.",".$parent_id.",".$active.",".$theme.",".$isRedirect.",".$url.", '', '', ".time().")");

		$forum_id = $GLOBALS['super']->db->fetch_lastid();
		//build parents
		$parent_list = $forum_id.",".$this->get_parent_list($parent_id);  //using POST, as the value is escaped and adds the dinks (') around the value

		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."forums SET parent_ids = '".$parent_list."' WHERE id = '".$forum_id."'");
		//build childs
		//$this->build_child_lists($_POST['parent_id']);  //using POST as the value is escaped and adds the dinks (') around the value
		
		
		// all of the ids of groups we have
		$groupids = array();
		// get all of the groups ids
		$groups = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."groups");
		
		// go through each group and add it to the allowed ids
		while($group = $GLOBALS['super']->db->fetch_assoc($groups))
		{
			$groupids[] = $group['id'];
		}
		
		// for each valid group id
		foreach($groupids as $id)
		{
			// if that id is a key in our form (they submitted permissions for that group id)
			if (isset($_POST['perm'][$id]))
			{
				// initialize a new string
				$perm = "";
				// and go through each value in our post's permission id
				foreach($_POST['perm'][$id] as $name => $value)
				{
					// if the checkbox is checked, make it be true
					if ($value == "on")
						$value = "true";
					else
					{
						// otherwise escape the value it gives
						$value = $GLOBALS['super']->db->escape($value);
					}
					
					$name = $GLOBALS['super']->db->escape($name);
						
					// generate the table formatted string
					$perm .= $name.":".$value.",";
					
				}
				
				// and insert it into our database.
				$sql = "INSERT INTO ".TBL_PREFIX."permissions
				(`group_id`,`name`,`value`) VALUES
				(".$id.", 'Forum".$forum_id."', '".$perm."')";
				$GLOBALS['super']->db->query($sql);
			}
		}
		
		
		
		return true;
	}

	
	function edit()
	{
		$curForum = $GLOBALS['super']->db->escape(intval($_GET['fid']));
		
		$is_cat = intval($_POST['is_cat']);
		//echo $is_cat."<br /><br />";
		
		// If we are a category, it doesn't matter what the idiot user put as a parent
		if ($is_cat == 1)
			$parent_id = $GLOBALS['super']->db->escape("0");
		else
			$parent_id = $GLOBALS['super']->db->escape($_POST['parent_id']);
		//echo $parent_id."<br /><br />";
			
		$is_cat = $GLOBALS['super']->db->escape($is_cat, true);
		$forum_name = $GLOBALS['super']->db->escape($_POST['forum_name'], true);
		$forum_description = $GLOBALS['super']->db->escape($_POST['forum_description'], true);
		$active = $GLOBALS['super']->db->escape(intval($_POST['active']), true);
		$theme = $GLOBALS['super']->db->escape($_POST['theme'], true);
		$isRedirect = $GLOBALS['super']->db->escape(intval($_POST['redirect']), true);
		$url = $GLOBALS['super']->db->escape($_POST['redirecturl'], true);
		
		
		
		if ($curForum != $parent_id)
		{
			// We want to get the value sort order should be to put it at the end
			$sortOrder = 'SELECT `sort_order` FROM '.TBL_PREFIX.'forums WHERE `parent_id`='.$parent_id.' ORDER BY `sort_order` DESC LIMIT 1';
			$sortOrder = $GLOBALS['super']->db->query($sortOrder);
			
			// if we don't get any rows, it means our sort order is zero
			if ($GLOBALS['super']->db->getRowCount($sortOrder) == 0)
			{
				$sortOrder = 1;
			}
			else
			{
				// otherwise we have to add it to the end of our current forums
				$sortOrder = $GLOBALS['super']->db->fetch_result($sortOrder);
				// we want 1 PLUS whatever the curren't highest sort order is. (add it to the end)
				$sortOrder++;
				// It should already be safe, but this couldn't hurt.
				$sortOrder = $GLOBALS['super']->db->escape($sortOrder);
			}
		}
		// Update the forum with the new information.
		// For now, lets just replace all the values with the form's values, maybe we can eventually just do the ones that changed.
		$updateQuery = "UPDATE ".TBL_PREFIX."forums
						SET `name`=".$forum_name.",
						`is_cat` = ".$is_cat.",
						`description`=".$forum_description.",
						`active`=".$active.",
						`theme_id`=".$theme.",
						`isRedirect`=".$isRedirect.",
						`redirectURL`=".$url.",
						`parent_id` = ".$parent_id."
						WHERE `id` =".$curForum;
		$GLOBALS['super']->db->query($updateQuery);

		$forum_id = $GLOBALS['super']->db->fetch_lastid();
		
		$query = $GLOBALS['super']->db->query("SELECT id FROM ".TBL_PREFIX."forums WHERE FIND_IN_SET('".$forum_id."', parent_ids)");
		while($row = $GLOBALS['super']->db->fetch_assoc($query))
		{
			$parent_list = $row['id'].",".$this->get_parent_list($row['parent_id']);
			$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."forums SET parent_ids = '".$parent_list."' WHERE id = '".$row['id']."'");
		}
		
		// all of the ids of groups we have
		$groupids = array();
		// get all of the groups ids
		$groups = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."groups");
		
		// go through each group and add it to the allowed ids
		while($group = $GLOBALS['super']->db->fetch_assoc($groups))
		{
			$groupids[] = $group['id'];
		}
		
		// for each valid group id
		foreach($groupids as $id)
		{
			// initialize a new string
			$perm = "";
			// if that id is a key in our form (they submitted permissions for that group id)
			if (isset($_POST['perm'][$id]))
			{
				
				// and go through each value in our post's permission id
				foreach($_POST['perm'][$id] as $name => $value)
				{
					// if the checkbox is checked, make it be true
					if ($value == "on")
						$value = "true";
					else
					{
						// otherwise escape the value it gives
						$value = $GLOBALS['super']->db->escape($value);
					}
					
					$name = $GLOBALS['super']->db->escape($name);
						
					// generate the table formatted string
					$perm .= $name.":".$value.",";
					
				}
			}
				
			// We need to see if we already have this permission setting for this group
			// so we know whether to override it, or add a row
			$checkExist = $GLOBALS['super']->db->query("SELECT * FROM ".TBL_PREFIX."permissions WHERE `name`='Forum".$curForum."' AND `group_id`=".$id);
			if ($GLOBALS['super']->db->getRowCount($checkExist) > 0)
			{
			
				// and insert it into our database.
				$sql = "UPDATE ".TBL_PREFIX."permissions SET
				`value` = '".$perm."' WHERE `name`='Forum".$curForum."' AND `group_id`=".$id;
			}
			else 
			{
				$sql = "INSERT INTO ".TBL_PREFIX."permissions
				(`group_id`,`name`,`value`) VALUES
				(".$id.", 'Forum".$curForum."', '".$perm."')";
			}
			$GLOBALS['super']->db->query($sql);
		}
		return true;
	}
	
	/***
	Function: get_parent_list()
	This function retrieves the parent list from the forum id based on the parent forums information
	-1 is used to sybolize the end of the forum list.  Every child and parent lists will have -1 on the end.
	***/
	function get_parent_list($forum_id)
	{
		if($forum_id == '-1' || $forum_id == "") return '-1';

		$forum = $this->get_parent($forum_id);
		$parent_list = $forum['parent_ids'];

		if (substr($parent_list, -3) != ',-1')
		{
			$parent_list .= ',-1';
		}
		return $parent_list;
	}

	/***
	Function: get_child_list()
	This function retrieves the child list by finding all forums with that have the forum_id in their parent_ids field
	-1 is used to sybolize the end of the forum list.  Every child and parent lists will have -1 on the end.
	***/
	/*
	function get_child_list($forum_id)
	{
		if ($forum_id == '-1' || $forum_id == "") return '-1';

		$child_list = $forum_id;
	
		$query = $GLOBALS['super']->db->query("SELECT id FROM ".TBL_PREFIX."forums WHERE parent_ids LIKE '%,".$forum_id.",%'");
		while ($row = $GLOBALS['super']->db->fetch_assoc($query))
		{
			$child_list .= ',' . $row['id'];
		}
		$child_list .= ',-1';
		return $child_list;	
	}*/

	/***
	Function: get_parent()
	This function retrieves the forums parent forum information
	***/
	function get_parent($parent = '-1')
	{
		if($parent == '-1' || $parent == "") return false;

		$parent = $GLOBALS['super']->db->escape($parent);
		
		$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE id = '".$parent."'";
		$result = $GLOBALS['super']->db->query($query);

		if($GLOBALS['super']->db->getRowCount($result) > 0) {
			$row = $GLOBALS['super']->db->fetch_assoc($result);
			return $row;
		}
		return false;
	}

	/***
	Function: build_parent_list()
	This function builds the parent ids based on the forum id.
	***/
	function build_parent_lists($forum_id)
	{
		$query = $GLOBALS['super']->db->query("SELECT id, (CHAR_LENGTH(parent_ids) - CHAR_LENGTH(REPLACE(parent_ids, ',', ''))) AS parents FROM ".TBL_PREFIX."forums WHERE FIND_IN_SET('$forum_id', parent_ids) ORDER BY parents ASC");
		while($row = $GLOBALS['super']->db->fetch_assoc($query))
		{
			$parent_list = $this->get_parent_list($row['id']);
			$GLOBALS['super']->db->query("UPDATE forums SET parent_ids = '".addslashes($parent_list)."' WHERE id = '".$row['id']."'");
		}
	}

	/***
	Function: build_child_list()
	This function builds the child ids based on the forum id.
	***/
	/*function build_child_lists($forum_id)
	{
		$forum_id = $GLOBALS['super']->db->escape(intval($forum_id));
		$query = $GLOBALS['super']->db->query("SELECT id FROM ".TBL_PREFIX."forums WHERE FIND_IN_SET('$forum_id', child_ids)");
		while ($row = $GLOBALS['super']->db->fetch_assoc($query))
		{
			$child_list = $this->get_child_list($row['id']);
			$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."forums SET child_ids = '".$child_list."' WHERE id = '".$row['id']."'");
		}
	}*/	
}
?>