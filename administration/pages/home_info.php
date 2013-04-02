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
 File: /administration/pages/home_info.php
 Author: SaroSoftware
Date Created: 07/05/2007
 Last Edited By: Eli White
 Last Edited Date: 5/6/09
 
 Comments:
	Main forum page

 -----*/
class HomeInfo extends adminSub
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
		
		$version_info[] = "WhiteBoard ".$this->super->config->forumVersion;
		$version_info[] = "&copy; Copyright 2005 - ".date("Y");
		$version_info[] = "Created by SaroSoftware";
		
		$server_info[] = "Operating System: ".php_uname("s");
		$server_info[] = "HTTP Server: ".$_SERVER["SERVER_SOFTWARE"];
		$server_info[] = "PHP Version ".phpversion();
		
		$sql="SELECT VERSION()";
		$result = $this->super->db->query($sql, true);
		$result = $this->super->db->fetch_result($result);
							
		$database_info[] = "MYSQL Version: ".$result;
		
		$dbsize = 0;
		$numrows = 0;
		
		// We want to get the number of rows, and the size of the database
		$rows = $this->super->db->query("SHOW table STATUS");
		while ($row = $this->super->db->fetch_assoc($rows))
		{
			$dbsize += $row['Data_length'] + $row['Index_length'];
			$numrows += $row['Rows'];
		}
	
		$dbsize = $this->super->functions->fileSizeFormat($dbsize);
		
		$database_info[] = "Rows: ".$numrows;
		$database_info[] = "Size: ".$dbsize['size']." ".$dbsize['type'];
		
		$infotext = "Welcome to WhiteBoard Adminstration!";
		$message = "The pages in this section will give you information about your server setup, license status, forum statistics as well as version update information.";
		
		$content = new tpl(ROOT_PATH.'administration/display/templates/home_info.php'); // include the content tpl file
		$content->add("HOME_INFO_TEXT", $infotext); // Add info text to the template
		$content->add("HOME_MESSAGE", $message); // Add message to the template
		$content->add("VERSION", $version_info); // Insert the version info
		$content->add("SERVER", $server_info); // Insert the server info
		$content->add("DATABASE", $database_info); // Insert the server info
		echo $content->parse(); // Parse out our header file and continue
	
	
	
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>