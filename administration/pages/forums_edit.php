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
File: /administration/includes/pages/forums_add.php
Author: SaroSoftware
Date Created: 07/26/2007
Last Edited By: Eli White
Last Edited Date: 2/10/2010
Latest Changes:

TODO:
	Change a forum's parent

Comments:
Edit a forum

-----*/
class ForumsEdit extends adminSub
{
	public $fhelper;
	
	public function __construct()
	{
		parent::__construct();
		$this->setName("Edit");

		require_once(ADMIN_PATH."includes/classes/forumHelper.php");
		$this->fhelper = new fHelper();
	}
	
	function getJS()
	{
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/forum_set.js");
		
		return $js;
	}
	
	/**
	 * Gets the content/main bulk of the page
	 *
	 * @return string The content
	 */
	public function display()
	{
		ob_start();
		
		// If we don't have a forum
		if (!isset($_GET['fid']))
		{
			// We need to pick one.
			$edit = new tpl(ROOT_PATH.'administration/display/templates/forums_edit.php');
			$this->fhelper->get_forum_list();
			
			$edit->add("FORUM_LIST", $this->fhelper->forum_list);
	
			echo $edit->parse();
		}
		elseif(isset($_POST['post']) && $_POST['post'] == 'form' && isSecureForm("addEditForum") && $this->fhelper->edit())
		{
			$success = new tpl(ROOT_PATH.'administration/display/templates/success_redir.php');
			$success->add("message","Forum Updated Successfully!");
			$success->add("url","index.php?act=forums&sub=structure");
			echo $success->parse();
		}
		else
		{
			$content = new tpl(ROOT_PATH.'administration/display/templates/forums_add.php'); // include the content tpl file
			
			$forumID = $GLOBALS['super']->db->escape(intval($_GET['fid']));
			
						
			$forumInfo = "SELECT * FROM ".TBL_PREFIX."forums WHERE `id`=".$forumID;
			$forumInfo = $GLOBALS['super']->db->query($forumInfo);
			$forumInfo = $GLOBALS['super']->db->fetch_assoc($forumInfo);
			//echo "<pre>".$super->functions->print_array($forumInfo)."</pre>";
			
			$content->add("title", "Update a Forum:");
			
			// We need to add initial values to our template
			$content->add("name", stripslashes($forumInfo['name']));
			$content->add("description", stripslashes($forumInfo['description']));
			$content->add("active", $forumInfo['active']);
			$content->add("is_cat", $forumInfo['is_cat']);
			$content->add("redirect", $forumInfo['isRedirect']);
			$content->add("url", htmlentities($forumInfo['redirectURL']));
			
			$this->fhelper->get_forum_list();
			$content->add("PARENT_LIST", $this->fhelper->forum_list); // Insert the navigation links
			
			$theme_sql = "SELECT * FROM ".TBL_PREFIX."themes";
			$theme_query = $GLOBALS['super']->db->query($theme_sql);
			while ($row = $GLOBALS['super']->db->fetch_assoc($theme_query))
			{
				$themes[] = array(
					"id" => $row['id'],
					"displayname" => $row['display_name']
				);
			}
			$content->add("THEME_LIST", $themes); // Insert the navigation links
			
			
			// we need to get all the groups
			$permgroups = array();
			$groups = $GLOBALS['super']->db->query("SELECT * FROM ".TBL_PREFIX."groups");
			while($group = $GLOBALS['super']->db->fetch_assoc($groups))
			{
				$permarr = array();
				$perms = $GLOBALS['super']->db->query("SELECT `value` FROM ".TBL_PREFIX."permissions WHERE `name`='Forum".$forumID."' AND `group_id`=".$group['id']);
				
				if ($GLOBALS['super']->db->getRowCount($perms) > 0)
				{
					$perms = $GLOBALS['super']->db->fetch_result($perms);
					$parts = explode(",",$perms);
					foreach($parts as $part)
					{
						if ($part != "")
						{
							$perm = explode(":",$part);
							$permarr[$perm[0]] = ($perm[1] == true);
						}
					}
				}
				
				$permarr["name"] = $group['name'];
				$permarr["id"] = $group['id'];
					
				$permgroups[] = $permarr;
				
			}
			$content->add("groups", $permgroups);
				
			$content->parse(); // Parse out our header file and continue
		}
	
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>