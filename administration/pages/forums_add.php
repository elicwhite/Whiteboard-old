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

Comments:
Add a forum

-----*/
class ForumsAdd extends adminSub
{
	public $fhelper;
	
	public function __construct()
	{
		parent::__construct();
		$this->setName("Add");

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
		
		if(isset($_POST['post']) && $_POST['post'] == 'form')
		{
			if(isSecureForm("addForum") && $this->fhelper->add())
			{
				$success = new tpl(ROOT_PATH.'administration/display/templates/success_redir.php');
				$success->add("message","Forum Added Successfully!");
				
				$success->add("url","index.php?act=forums&sub=structure");
				//$success->add("timeout","5000");
				echo $success->parse();
			}
		}
		$content = new tpl(ADMIN_PATH.'display/templates/forums_add.php'); // include the content tpl file
		
		$this->fhelper->get_forum_list();
		$content->add("PARENT_LIST", $this->fhelper->forum_list); // Insert the navigation links
		$content->add("title", "Add New Forum:");
		
		// Set the defaults.
		$content->add("name", "");
		$content->add("description", "");
		$content->add("active", "1");
		$content->add("is_cat", "0");
		$content->add("redirect", "0");
		$content->add("url", "");
		
		$theme_sql = "SELECT * FROM ".TBL_PREFIX."themes";
		$theme_query = $GLOBALS['super']->db->query($theme_sql);
		while ($row = $GLOBALS['super']->db->fetch_assoc($theme_query))
		{
			$themes[] = array(
				"id"	=>	$row['id'],
				"displayname" => $row['display_name']
			);
		}
		$content->add("THEME_LIST", $themes); // Insert the navigation links
		
		
		// we need to get all the groups
		$permgroups = array();
		$groups = $GLOBALS['super']->db->query("SELECT * FROM ".TBL_PREFIX."groups");
		while($group = $GLOBALS['super']->db->fetch_assoc($groups))
		{
			$permgroups[] = array("name" => $group['name'], "id" => $group['id']);
		}
		$content->add("groups", $permgroups);
		echo $content->parse(); // Parse out our header file and continue
	
	
	
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>