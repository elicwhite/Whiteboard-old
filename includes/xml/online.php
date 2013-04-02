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
 File: /includes/xml/online.php
 Author: SaroSoftware
 Date Created: 05/1/2007
 Last Edited By: Eli White
 Last Edited Date: 5/1/09
 Latest Changes:


 Comments:
	Ajax helper for the Users Online list

 -----*/
require_once('../../init.php'); // Include the initialization file
// allow us to include a page's class
define("INCLUDED", 1);


// Include our online list helper class
require_once(ROOT_PATH."includes/classes/online.php");
// and initialize it
$onlineHelper = new onlineClass();

//Include the class that forum pages inherit
require_once(ROOT_PATH."includes/classes/pageClass.php");
// Include the online page. To be able to set the user online
require_once(ROOT_PATH.'pages/online.php');
$pageClass = new Online();

// Add the user to the online list
$super->user->updateActive($pageClass);

// get the users online info and counts
list($guestCount, $userCount, $info) = $onlineHelper->generate();

// flush our buffer, we want to start clean as an xml doc.
ob_end_clean();
		
// Start generating the output xml
$dom = new DOMDocument("1.0");
// make this page act like an xml file
header("Content-Type: text/xml");

$root = $dom->createElement("onlinelist");
$dom->appendChild($root);

$guests = $dom->createElement("guestCount",$guestCount);
$root->appendChild($guests);

$users = $dom->createElement("userCount", $userCount);
$root->appendChild($users);


foreach($info as $user)
{
	$usernode = $dom->createElement("user");
	$root->appendChild($usernode);
	
	$name = $dom->createElement("name",$user["name"]);
	$usernode->appendChild($name);
	
	$page = $dom->createElement("page", $user["page"]);
	$usernode->appendChild($page);
	
	$time = $dom->createElement("time", $user["time"]);
	$usernode->appendChild($time);
}

echo $dom->saveXML();
?>