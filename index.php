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
 File: /index.php
 Author: SaroSoftware
 Date Created: 04/24/2007
 Last Edited By: Eli White
 Last Edited Date: 2/12/10
 Latest Changes:
 	2/12/10 - Finished the control panel pages
 	6/15/09 - Escaped the page to check for user's online
 	6/10/09 - Finished the read topics/forums and mark as read button
 	5/18/09 - Added a registration link for guests
 	5/7/09 - Secured all forms and added functions secureForm(); and check isSecureForm()
 	5/3/09 - Made things show up only if you have permissions
 			- All pages now query an array for the user to display
 				If the user is displayed in one portion of the page,
 				Then we save a query to display them in another portion
 	4/24/09 - Added the time to the footer
 	4/11/09 - Converted all the pages to classes, and we're now getting
 				my stylesheets and javascript files from the pages.	
 	3/7/09	- Adding nav highlighting to the navbar, if act matches the page act.

 GLOBAL TODO:
 		 	
 			
 	FUTURE FEATURES:
 	- Forgot Password
	- Important/Sticky/Announcements
	- Pagination also at the bottom (maybe)
	- Minimize/Maximize forum categories and save by user settings, not just cookies?	
	- Jump to forum
 	- Polls
 	- Ban Network, a trusted site bans a user, and updates other forums that this user should be banned.
 	- More Forum Statistics page
	- Most users ever online at once?
	- Private Messages
	- Advanced BBCode and Poster
		Nested quote tags
	- Member List
	- Edit first post in a topic lets you change the title
		
		WYSIWYG?
		Syntax Highlighting
	- Theme Revamp and Guest choices
	- Search (Filter by type and date)
	- Profile contact (Email, PM, etc.)
 
 Comments:
 Main file

 -----*/

require_once('init.php'); // Include the initialization file

//Allow pages to be run through this file only
define("INCLUDED", 1);

//Include the class that forum pages inherit
require_once(ROOT_PATH."includes/classes/pageClass.php");

if (isset($_GET['act']))
	$current_page = $_GET['act']; // What page does the user want?
else
	$current_page = "index"; // something to trigger the default in the switch
	
$pageClass = new forumPage();	
	
// switches the act of the page (what page are we on)
switch($current_page)
{
	case "postTopic":
		// We have to include fdisplay because we inherit it
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/postTopic.php');
		$pageClass = new postTopic();
		break;
	case "topicReply":
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/tdisplay.php');
		require_once(ROOT_PATH.'pages/topicReply.php');
		$pageClass = new topicReply();
		break;
	case "tdisplay":
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/tdisplay.php');
		$pageClass = new tDisplay();
		break;
	// Display a forum's posts and sub forums
	case "fdisplay":
		require_once(ROOT_PATH.'pages/fdisplay.php');
		$pageClass = new fDisplay();
		break;
	// um...the calendar maybe?
	case "calendar":
		require_once(ROOT_PATH.'pages/calendar.php');
		$pageClass = new Calendar();
		break;
	// the main home page and forum directory
	case "member":
		require_once(ROOT_PATH.'pages/profile.php');
		$pageClass = new Profile();
		break;
	case "memberlist":
		require_once(ROOT_PATH.'pages/memberlist.php');
		$pageClass = new memberList();
		break;
	case "login":
		require_once(ROOT_PATH.'pages/login.php');
		$pageClass = new Login();
		break;
	case "register":
		require_once(ROOT_PATH.'pages/register.php');
		$pageClass = new Register();
		break;
	case "search":
		require_once(ROOT_PATH.'pages/search.php');
		$pageClass = new Search();
		break;
	case "online":
		require_once(ROOT_PATH.'pages/online.php');
		require_once(ROOT_PATH."includes/classes/online.php");
		$onlineHelper = new onlineClass();
		$pageClass = new Online();
		break;
	case "markread":
		$GLOBALS['super']->user->read_time = time();
		$GLOBALS['super']->user->read_topics = "";
		header("Location: ".FORUM_ROOT);
		die();
		break;
	case "editPost":
		require_once(ROOT_PATH.'pages/tdisplay.php');
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/editPost.php');
		$pageClass = new editPost(); 
		break;
	case "deletePost":
		require_once(ROOT_PATH.'pages/tdisplay.php');
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/deletePost.php');
		$pageClass = new deletePost(); 
		break;
	case "deleteTopic":
		require_once(ROOT_PATH.'pages/tdisplay.php');
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/deleteTopic.php');
		$pageClass = new deleteTopic(); 
		break;
	case "controlPanel":
		require_once(ROOT_PATH.'pages/controlpanel.php');
		$pageClass = new ControlPanel();
		break;
	default:
		require_once(ROOT_PATH.'pages/main.php');
		$pageClass = new Main();
		break;
}

// Add the user to the online list
$super->user->updateActive($pageClass);
	

// The doctypes we have, we need to switch it out for mobile browsers (iPhones) so that it displays correctly.
$Doctype_normal = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
$Doctype_mobile = '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">';


// START HEADER TEMPLATING
$header = new tpl(ROOT_PATH.'themes/Default/templates/header.php'); // include the header tpl file
$header->add("TITLE", $pageClass->getTitle());

// Add the correct header to the page
if (strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "iPod") !== false)
	$header->add("DOCTYPE", $Doctype_mobile);
else
	$header->add("DOCTYPE", $Doctype_normal);
	
// We want to loop through all of the css files in our current theme and include them as a stylesheet

$styleSheet = "Default";
$styleDir = 'themes/'.$styleSheet;

$css = $pageClass->getCSS();
// Lets include special css if we are browsing on an iphone or ipod
if (strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") !== false ||
	strpos($_SERVER['HTTP_USER_AGENT'], "iPod") !== false
	&& file_exists($styleDir.'/iPhone.css')
	)
{
	$css[] = array('path' => $styleDir.'/iPhone.css');
}



$header->add("STYLESHEETS", $css); // Add our array of stylesheets to our template loop
$header->add("STYLESHEET", $styleSheet);

$js = $pageClass->getJS();
$js[] = array("path" => "includes/js/GoogleAnalytics.js");
$header->add("SCRIPTS", $js); // Put our scripts in our template

echo $header->parse(); // Parse out our header file and continue
// END HEADER TEMPLATING

// START NAV TEMPLATING
$nav = new tpl(ROOT_PATH.'themes/Default/templates/navbar.php'); // include the navigation tpl file


$nav->add("act", $current_page);

// What are the links?
$menu[] = array("act" => "home", "name" => "Home", "url" => "index.php");

if ($super->user->can("ViewAdmin"))
{
	$menu[] = array("act" => "admin", "name" => "Admin", "url" => "administration/index.php");
}


//$menu[] = array("act" => "memberlist", "name" => "Members", "url" => "index.php?act=memberlist");
$menu[] = array("act" => "search", "name" => "Search", "url" => "index.php?act=search");
$menu[] = array("act" => "calendar", "name" => "Calendar", "url" => "index.php?act=calendar");
if (!$super->user->isLogged())
{
	$menu[] = array("act" => "login", "name" => "Login", "url" => "index.php?act=login");
	$menu[] = array("act" => "register", "name" => "Register", "url" => "index.php?act=register");
}
else
	$menu[] = array("act" => "logout", "name" => "Logout", "url" => "index.php?act=login&amp;do=out");

$nav->add("MENU", $menu); // Insert the navigation links
echo $nav->parse(); // Parse out our nav and continue
// END NAV TEMPLATING

// START USERBAR TEMPLATING
$userbar = new tpl(ROOT_PATH.'themes/Default/templates/userbar.php'); // Include teh userbar!


$username = $super->user->username;
$displayname = $super->user->displayname;
	
if (isset($displayname) && $displayname != "")
	$user_display = $username." (".$displayname.")";
else
	$user_display = $username;


$userbar->add("BREADCRUMBS", $pageClass->breadCrumb());
$userbar->add("LOGGED", $super->user->isLogged());


$userbar->add("USERID", $super->user->id);

$userbar->add("USERNAME_DISPLAYNAME", $user_display); // Define current stylesheet
echo $userbar->parse(); // Parse out our userbar and continue
// END USERBAR TEMPLATING

// Main page display
echo $pageClass->display();


// BEGIN FOOTER TEMPLATING
$footer = new tpl(ROOT_PATH.'themes/Default/templates/footer.php'); // include the navigation tpl file
$curTime = date("g:i A");
$footer->add("time", $curTime);


// We need to get the number of users on this page.
$usersOnPage = "SELECT * FROM ".TBL_PREFIX."online WHERE page='".$GLOBALS['super']->db->escape($pageClass->onlineName())."' AND userid != 0";
$usersOnPage = $super->db->query($usersOnPage);

$guestsOnPage = "SELECT count(*) FROM ".TBL_PREFIX."online WHERE `page`='".$GLOBALS['super']->db->escape($pageClass->onlineName())."' AND `userid`=0";
$guestsOnPage = $super->db->query($guestsOnPage);
$guestsOnPage = $super->db->fetch_result($guestsOnPage);
$pageUsers = array();
while($user = $super->db->fetch_assoc($usersOnPage))
{
	//for ($i = 0; $i<10; $i++) {
	$pageUsers[] = $super->functions->getUser($user['userid'], true);
	//}
}

$footer->add("UsersOnPage", $pageUsers);
$footer->add("GuestsOnPage", $guestsOnPage);

$footer->add("PBB_VERSION", $super->config->forumVersion); // What version are we using?
echo $footer->parse(); // Parse out our footer and continue
// END FOOTER TEMPLATING

require_once('debug.php');
?>