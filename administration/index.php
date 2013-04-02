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
File: /administration/index.php
Author: SaroSoftware
Date Created: 07/06/2007
Last Edited By: Eli White
Last Edited Date: 07/06/2007
Latest Changes:


Comments:
Main Admin Page

TODO: 
	Home
		- License Page
	Users
		- Add / Edit / Delete
	Forum
		- Delete	

-----*/

require_once('../init.php');
define("INCLUDED", 1);

// only display the page if we can view the admin panel
if (!$super->user->can("ViewAdmin"))
{
	// if we can't, redirect to the forum root
	header("Location: ".FORUM_ROOT);
	// keep things from executing before we successfully redirect.
	die();
}

//Define administration root
define("ADMIN_PATH", dirname( __FILE__ ) ."/");

//Include the class that forum pages inherit
require_once(ADMIN_PATH."includes/classes/adminPage.php");


// if we have set an action 
if (isset($_GET['act']))
	$curr_act = $_GET['act']; // Fetch the page
else
{
	// If there is no action or an invalid action
	//  we want to be on the home admin page
	$curr_act = "home";
}

switch ($curr_act)
{
	
	case "forums":
	    require_once(ADMIN_PATH.'pages/forums.php');
	    $pageClass = new Forums();
	    break;
	case "config":
	    require_once(ADMIN_PATH.'pages/configuration.php');
	    $pageClass = new Configuration();
	    break;
	default:
	    require_once(ADMIN_PATH.'pages/home.php');
	    $pageClass = new Home();
	    break;
}

// Add the user to the online list
// Don't worry, it only says "Admin Panel"
$super->user->updateActive($pageClass);

$curr_sub = "";
if (isset($_GET['sub']))
	$curr_sub = $_GET['sub'];

// we need to lower case this so that it matches the act
$curr_sub = ucwords($curr_sub);
$curr_sub = $pageClass->setSub($curr_sub);

//setup main menus
$mainitems[] = array('text' => 'Home', 'act' => 'home', 'redirect' => 'No');
$mainitems[] = array('text' => 'Forums', 'act' => 'forums', 'redirect' => 'No');
//$mainitems[] = array('text' => 'Users', 'act' => 'users', 'redirect' => 'No');
$mainitems[] = array('text' => 'Configuration', 'act' => 'config', 'redirect' => 'No');


$subitems = $pageClass->children();

$header = new tpl(ADMIN_PATH.'display/templates/header.php'); // include the header tpl file
$header->add("title", $pageClass->getTitle());
$header->add("stylesheets", $pageClass->getCSS());
$header->add("scripts", $pageClass->getJS()); // Put our scripts in our template

$header->add("curact", $curr_act);
$header->add("cursub", $curr_sub);
$header->add("mainmenu", $mainitems); // Insert the navigation links
$header->add("submenu", $subitems); // Insert the sub navigation links

echo $header->parse(); // Parse out our header file and continue



echo $pageClass->display();

$footer = new tpl(ADMIN_PATH.'display/templates/footer.php'); // include the footer tpl file
echo $footer->parse();
?>