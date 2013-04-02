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
 File: /administration/pages/configuration_edit.php
Author: SaroSoftware
Date Created: 07/26/2007
Last Edited By: Eli White
Last Edited Date: 3/15/2010
Latest Changes:

TODO:
 	-Automated Modification?
 	
Comments:
Edit configuration settings

-----*/
class ConfigurationEdit extends adminSub
{
	public function __construct()
	{
		parent::__construct();
		$this->setName("Edit");
	}
	
	/**
	 * Gets the content/main bulk of the page
	 *
	 * @return string The content
	 */
	public function display()
	{
		ob_start();
		
		if(isset($_POST['post']) && $_POST['post'] == 'update')
		{
			if(isSecureForm("editForum"))
			{
				$errors = $this->validate();
				if (!is_array($errors))
				{
					$success = new tpl(ROOT_PATH.'administration/display/templates/success_redir.php');
					$success->add("message","Config Updated Successfully!");
					
					$success->add("url","index.php?act=config&sub=info");
					echo $success->parse();
					die();
				}
				else 
					print_r($errors);
			}
		}
		$content = new tpl(ADMIN_PATH.'display/templates/config_edit.php'); // include the content tpl file
		
		//$this->fhelper->get_forum_list();
		//$content->add("PARENT_LIST", $this->fhelper->forum_list); // Insert the navigation links
		$content->add("title", "Edit Configuration Items:");
		
		// Set the defaults.
		$content->add("SiteName", $GLOBALS['super']->config->siteName);
		$content->add("Seperator", htmlentities($GLOBALS['super']->config->crumbSeperator));
		$content->add("DateFormat", $GLOBALS['super']->config->dateFormat);
		$content->add("TimeFormat", $GLOBALS['super']->config->timeFormat);
		$content->add("PostsPerPage", $GLOBALS['super']->config->postsPerPage);
		$content->add("TopicsPerPage", $GLOBALS['super']->config->topicsPerPage);
		$content->add("OnlineOffset", $GLOBALS['super']->config->usersOnlineOffset);
		
		// Set up our timezone array
		$content->add("TimeZone", $GLOBALS['super']->config->timeZone);
		$content->add("Zones", DateTimeZone::listIdentifiers());
		
		echo $content->parse(); // Parse out our form
	
	
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	/**
	 * This function validates and updates our config values
	 *
	 */
	private function validate()
	{
		// Get and sanitize our variables
		$siteName = $GLOBALS['super']->db->escape($_POST['site_name'], true);
		$crumb = $GLOBALS['super']->db->escape($_POST['crumb_seperator'], true);
		$timeZone = intval($_POST['time_zone']);
		$dateFormat = $GLOBALS['super']->db->escape($_POST['date_format'], true);
		$timeFormat = $GLOBALS['super']->db->escape($_POST['time_format'], true);
		$postsPerPage = intval($_POST['posts_per_page']);
		$topicsPerPage = intval($_POST['topics_per_page']);
		$onlineOffset = intval($_POST['online_offset']);
		
		// Our error array
		$errors = array();
		
		$zones = DateTimeZone::listIdentifiers();
		
		// Validation
		if (strlen($siteName) < 5)
			$errors[] = "Your site name must be greater than or equal to 5 characters.";
		
		// $timeZone gives us an integer key
		if (!key_exists($timeZone, DateTimeZone::listIdentifiers()))
			$errors[] = "You must choose a valid timezone.";
		else 
		{
			// It's possible a default timezone has a bad character. Escape the timezone
			$timeZone = $GLOBALS['super']->db->escape($zones[$timeZone], true);
		}
			
		// Use negation instead of < 0 beacause of falsey values	
		if (!($postsPerPage > 0))
			$errors[] = "You must show at least one post per page.";
			
		if (!($topicsPerPage >= 0))
			$errors[] = "You must show at least one topic per page.";
		
		if (!($onlineOffset > 0))
			$errors[] = "Online list offset must be greater than 0.";
		
		// If we have errors, return them	
		if (count($errors) != 0)
			return $errors;
			
		// Update our config values
		//$sql = "UPDATE ".TBL_PREFIX."config SET `value` = '".$siteName."' WHERE `name`='siteName'";
		//die($sql);
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."config SET `value` = ".$siteName." WHERE `name`='siteName'");
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."config SET `value` = ".$crumb." WHERE `name`='crumbSeperator'");
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."config SET `value` = ".$timeZone." WHERE `name`='timeZone'");
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."config SET `value` = ".$dateFormat." WHERE `name`='dateFormat'");
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."config SET `value` = ".$timeFormat." WHERE `name`='timeFormat'");
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."config SET `value` = ".$postsPerPage." WHERE `name`='postsPerPage'");
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."config SET `value` = ".$topicsPerPage." WHERE `name`='topicsPerPage'");
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."config SET `value` = ".$onlineOffset." WHERE `name`='usersOnlineOffset'");
		
		return true;
	}
}
?>