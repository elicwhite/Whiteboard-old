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
 File: /includes/classes/online.php
 Author: SaroSoftware
 Date Created: 5/1/09
 Last Edited By: Eli White
 Last Edited Date: 5/1/09
 Latest Changes:
 
 	
 Comments:
	Helper class for the Online List


 -----*/

//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

class onlineClass
{
	
	function __construct()
	{

	}
	
	function generate()
	{
		// get all the people that are online
		$users = "SELECT * FROM ".TBL_PREFIX."online ORDER BY `time_modified` DESC";
		
		$users = $GLOBALS['super']->db->query($users);

		// our array that will store all of our users and their info
		$info = array();
		
		// we need to count how many of each we have
		$guestCount = 0;
		$userCount = 0;
		
		// we need to go through each user
		while($user = $GLOBALS['super']->db->fetch_assoc($users))
		{
			$display = array();
			
			// and check if they are a guest or not.
			if ($user["userid"] == "0")
			{
				// guest
				$display["name"] = "Guest";
				$guestCount++;
			}
			else 
			{
				// member
				$user2display = $GLOBALS['super']->functions->getUser($user["userid"], true);
				// and add the formatted colored string with a link to their profile
				$display["name"] = $user2display;
				$userCount++;
			}
			// add the page and the time
			$display["page"] = $user["page"];
			$display["time"] = $GLOBALS['super']->functions->formatTime($user["time_modified"]);
			
			// and add it to our ouput array
			$info[] = $display;
			
		}
		return array($guestCount, $userCount, $info);
	}
}