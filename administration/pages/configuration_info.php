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
File: /administration/pages/configuration_info.php
Author: SaroSoftware
Date Created: 07/26/2007
Last Edited By: Eli White
Last Edited Date: 2/10/2010
Latest Changes:

Comments:
Main configuration page

-----*/
class ConfigurationInfo extends adminSub
{
	
	public function __construct()
	{
		parent::__construct();
		$this->setName("Info");
	}
	
	/**
	 * Gets the content/main bulk of the page
	 *
	 * @return string The content
	 */
	public function display()
	{
		ob_start();
		
		$infotext = "Welcome to WhiteBoard Settings and Configuration!";
		$message = "This is the section of the admin panel you will use to change all the settings of your forum and plugins.";
		$content = new tpl(ROOT_PATH.'administration/display/templates/forums_info.php'); // include the content tpl file
		$content->add("FORUM_INFO_TEXT", $infotext); // Add info text to the template
		$content->add("FORUM_MESSAGE", $message); // Add message to the template
		echo $content->parse(); // Parse out our template
	
	
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>