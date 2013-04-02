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
 File: /administration/pages/configuration.php
 Author: SaroSoftware
 Date Created: 07/05/2007
 Last Edited By: Eli White
 Last Edited Date: 5/6/09
 Latest Changes:
 	- Converted to class format
 
 Comments:
	Main page for the Forums category in the admin panel

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
class Configuration extends adminPage
{
	
	public function __construct()
	{
		parent::__construct();
		$this->setName("Configuration");
		
	}
	
	
	public function children()
	{
		$children[] = array('text' => 'Info', 'act' => 'info', 'redirect' => 'No');
		$children[] = array('text' => 'Edit', 'act' => 'edit', 'redirect' => 'No');
		return $children;
	}
	
	public function setSub($sub)
	{
		switch ($sub)
		{
			case "Edit":
				require_once(ADMIN_PATH.'pages/configuration_edit.php');
	    		$this->sub = new ConfigurationEdit();
				break;
			default:
				require_once(ADMIN_PATH.'pages/configuration_info.php');
	    		$this->sub = new ConfigurationInfo();
				break;
		}
		return $this->sub->getName();
	}
	
	/**
	 * Gets the content/main bulk of the page
	 *
	 * @return string The content
	 */
	public function display()
	{
		ob_start();
		
		echo $this->sub->display();
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	
	/*
	else
	{
		$group = $super->db->escape($admin->curr_sub);
		$configresult = $super->db->query("SELECT * FROM ".TBL_PREFIX."config WHERE `group`='".$group."' AND `readonly`='0'");
		if ($super->db->getRowCount($configresult) > 0)
		{
			while($config = $super->db->fetch_assoc($configresult))
			{
				$configArray[] = array('name' => ucwords($config['name']), 'description' => $config['description'], 'value' => stripslashes($config['value']), 'inputfield' => 'text');
			}
			
			
			$content = new tpl(ROOT_PATH.'administration/display/templates/config_edit.php');
			$content->add("page", $admin->curr_sub);
			$content->add("CONFIG", $configArray);
			echo $content->parse();
		}
		else
		{
			// We have no config values with the given group
			$content = new tpl(ROOT_PATH.'administration/display/templates/config_error.php');
			$content->add("message", "There are no config values with this group name"); // Add info text to the template
			echo $content->parse(); // Parse out our template
		}
		// Get information about each page, get the valid settings and display the edit form
	}
	
	*/
	
}

?>