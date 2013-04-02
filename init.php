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
File: /init.php
Author: SaroSoftware
Date Created: 04/17/2007
Last Edited By: Eli White
Last Edited Date: 04/22/2009
Latest Changes:

4/22/09 - Added an update so that we can check for users currently online

- Included the template file

Comments:
Main definition page
-----*/

error_reporting(E_ALL);
// buffer the output so the headers aren't sent.
ob_start();
set_magic_quotes_runtime(false);

//setup debugging for queries and page parse time (SERVER parse time / Not actual browser load time
define('DEBUG', 'false');
define('PAGE_PARSE_START_TIME', microtime());
$debug = array();

// Start up the session
@session_start();

//Define website root
define("ROOT_PATH", dirname( __FILE__ ) ."/");

//Security Define
define("SEC", 1);

// If we don't have a config.php file, run the installer
if (!file_exists(ROOT_PATH."config.php"))
{
	header("Location: install/index.php");
}

require_once(ROOT_PATH.'includes/functions/functions.php');
//This file contains the stored database information...username, password, database, and host in the INFO array
//moved to superclass file, thats where its used

//Call up the super-global class
require_once(ROOT_PATH."includes/classes/super.php");

//Start up the super-class, set up database connection, initialize variables ALL IN ONE LINE :)
$super = new super();


// set the forum's default timezone
date_default_timezone_set($super->user->time_zone);

//Include the infamous template class
require_once(ROOT_PATH."includes/classes/tpl.php");

// We need to update the user's last action so we
// can show the users currently online
if ($GLOBALS['super']->user->isLogged())
{
	$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."users SET `last_active` = ".time()." WHERE `id` = ".$super->user->id);	
	date_default_timezone_set($super->user->time_zone);
	
}
?>