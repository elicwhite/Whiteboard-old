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
File: /install/includes/install.php
Author: SaroSoftware
Date Created: 03/10/2007
Last Edited By: Eli White
Last Edited Date: 3/8/09

Comments:
Installer helper class

-----*/
define("SEC", true);
define("INCLUDED", 1);
define("ROOT_PATH", dirname( __FILE__ ) ."/");

require_once('../includes/classes/dconnect.php');
require_once('../includes/classes/functions.php');

class Installer
{	
	public $errors = array();
	
	private function generateHash()
	{
		$hash = srand(microtime());
		$hash = substr($hash, -7);
		
		return $hash;
	}
	
	private function checkConnection($host, $user, $pass, $db)
	{
		
		$connection = mysql_connect($host, $user, $pass);
		if (!$connection)
		{
			$this->errors[] = "Couldn't connect to the MYSQL server";
			return false;
		}
				
		if (!mysql_select_db($db))
		{
			$this->errors[] = "Couldn't connect to the MYSQL database";
			return false;
		}
				
		// we are connected, disconnect and close our connection
		mysql_close($connection);
		return true;
	}
	
	public function register($url, $admin_email, $ip)
	{
		$data = http_build_query(
			array(
				"url"	=> $url,
				"admin_email" => $admin_email,
				"ip"	=>	$ip)
		);
	    
		$params = array('http' => array(
		      'method' => 'POST',
		      'header' => "Content-type: application/x-www-form-urlencoded",
		      'content' => $data
		   ));
		
		$url = "http://sarosoftware.com/products/registerWB";
		
		
		$ctx = stream_context_create($params);
		
		$fp = fopen($url, 'rb', false, $ctx);
		fpassthru($fp);
		fclose($fp);
	}
	
	public function install()
	{
		// database install values
		$dbhost = $_POST['dbhost'];
		$dbname = $_POST['dbname'];
		$dbuser = $_POST['dbusername'];
		$dbpass = $_POST['dbpass'];
		$dbprefix = addslashes($_POST['dbprefix']);
		
		// If we can't connect, fail now
		if (!$this->checkConnection($dbhost, $dbuser, $dbpass, $dbname))
			return false;
		
		// admin values
		$username = $_POST['username'];
		$displayname = $_POST['displayname'];
		$password = $_POST['password'];
		$email = $_POST['email'];
		
		
		
		// Usernames can only be alphanumeric chars between 5 and 20 charachters long
		if (!ctype_alnum($username))
			$this->errors[] = "Usernames must contain only alphanumerics.";
			
		if (strlen($username) < 5 || strlen($username) > 20)
			$this->errors[] = "Usernames must be 5-20 characters.";
		
		// Displaynames must make visible output, but can't be chars like tabs or new lines
		if (!ctype_print($displayname))
			$this->errors[] = "Display names may not contain special characters.";
			
		if (strlen($password) <= 5)
			$this->errors[] = "Passwords must be at least 6 characters long.";
			
		// Forum values
		$root = $_POST['root'];
		$forumname = $_POST['forumname'];
		
		// If the user didn't listen and didn't add a / to the end, lets add it
		if (substr($root, -1) != "/")
			$root .= "/";
			
		if (strlen($forumname) < 5)
			$this->errors[] = "Your site name must be greater than or equal to 5 characters.";
		
		// If we have errors, return false
		if (count($this->errors) > 0)
			return false;
			
		// Generate the unique forum hash
		$hash = $this->generateHash();
				
		// current timestamp for date_modified fields()
		$timestamp = time();
		
		
		// write the config file
		$stringData =
'<?php
//No direct access
if( ! defined("SEC"))
{
	echo "<p>You cannot access this page directly</p>";
	exit();
}

// Initialize the INFO array
$INFO = array();

//Set up INFO variables
$INFO[\'database\'] = "'.$dbname.'"; //Database to connect to
$INFO[\'password\'] = "'.$dbpass.'"; //database password
$INFO[\'username\'] = "'.$dbuser.'"; //database user
$INFO[\'host\'] = "'.$dbhost.'"; //database host

//or do like this
define("TBL_PREFIX", "'.$dbprefix.'"); //Table prefix
define("FORUM_ROOT", "'.$root.'");

define("PASS_HASH", "'.$hash.'");

// We need to set the timezone we are in so all the times are correct
define("TIMEZONE", "-7 hours");
define("TIMEZONENUM", "-7");
?>';
		
	file_put_contents("../config.php", $stringData);
			
	// include our db connection
	require_once('../config.php');
	
	// We now have access to super
	$passhash = encrypt_password($password);
	
	// START CREATING THE MYSQL TABLES AND INSERT THE CONTENT	
	$queries[] = 
	'CREATE TABLE IF NOT EXISTS '.TBL_PREFIX.'bans (
	  `id` int(11) NOT NULL auto_increment,
	  `user_id` int(11) NOT NULL DEFAULT 0,
	  `email` varchar(150) NOT NULL DEFAULT "",
	  `ip` varchar(255) NOT NULL DEFAULT "",
	  `length` varchar(200) NOT NULL DEFAULT "",
	  `message` text NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
	
	$queries[] = 
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."config (
	  `id` int(11) NOT NULL auto_increment,
	  `name` varchar(40) NOT NULL DEFAULT '',
	  `value` varchar(40) NOT NULL DEFAULT '',
	  `readonly` enum('0','1') NOT NULL DEFAULT 0,
	  `description` varchar(255) NOT NULL DEFAULT '',
	  `group` varchar(255) NOT NULL DEFAULT '',
	  `time_modified` int(10) NOT NULL DEFAULT 0,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		
	$queries[] = 
	"INSERT INTO ".TBL_PREFIX."config (`name`, `value`, `readonly`, `description`, `group`, `time_modified`) VALUES
	('siteName', '".$forumname."', '0', 'The name of the website to appear in the titlebar, and at the start of breadcrumbs.', 'general', ".$timestamp."),
	('forumVersion', 'V.1 Alpha', '1', 'The version of the forum system', 'general', ".$timestamp."),
	('defaultTheme', '1', '0', 'The default theme to use for the forum', 'general', ".$timestamp."),
	('crumbSeperator', '&raquo;', '0', 'The seperator used in the breadcrumbs', 'general', ".$timestamp."),
	('timeZone', 'America/Los_Angeles', '0', 'The default timezone for the board to use.', 'general', ".$timestamp."),
	('dateFormat', 'n/j/y', '0', 'The date format to use on the board. Format according to PHP\'s date() function', 'general', ".$timestamp."),
	('timeFormat', 'g:ia', '0', 'The time format to use on the board. Format according to PHP\'s date() function.', 'general', ".$timestamp."),
	('postsPerPage', '10', '0', 'The number of posts to display on a single page', 'general', ".$timestamp."),
	('topicsPerPage', '20', '0', 'The number of topics to display on a single page.', 'general', ".$timestamp."),
	('usersOnlineOffset', '15', '0', 'The number of minutes for the Users Online list to display', 'general', ".$timestamp.");
	";
	
	$queries[] =
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."forums (
	  `id` int(11) NOT NULL auto_increment,
	  `is_cat` enum('0','1') NOT NULL DEFAULT 0,
	  `name` varchar(64) NOT NULL DEFAULT '',
	  `description` varchar(200) NOT NULL DEFAULT '',
	  `sort_order` tinyint(4) NOT NULL DEFAULT 0,
	  `parent_ids` varchar(60) NOT NULL DEFAULT '',
	  `last_post_id` int(11) NOT NULL DEFAULT 0,
	  `post_count` int(11) NOT NULL DEFAULT 0,
	  `topic_count` int(11) NOT NULL DEFAULT 0,
	  `active` enum('0','1') NOT NULL DEFAULT 1,
	  `isRedirect` enum('0','1') NOT NULL DEFAULT 0,
	  `redirectURL` varchar(255) NOT NULL DEFAULT '',
	  `redirectHits` int(11) NOT NULL DEFAULT 0,
	  `mod_user_ids` text NOT NULL,
	  `mod_group_ids` text NOT NULL,
	  `time_added` int(10) NOT NULL DEFAULT 0,
	  `parent_id` int(11) NOT NULL DEFAULT 0,
	  `theme_id` int(11) NOT NULL DEFAULT 0,
	  PRIMARY KEY  (`id`),
	  FULLTEXT KEY `name` (`name`,`description`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
	";
	
	$queries[] =
	"INSERT INTO ".TBL_PREFIX."forums (`is_cat`, `name`, `sort_order`, `parent_ids`, `last_post_id`, `post_count`, `topic_count`, `active`, `isRedirect`, `mod_user_ids`, `mod_group_ids`, `time_added`, `parent_id`, `theme_id`) VALUES
	('1', 'First Category', 1, '1,-1', 0, 0, 1, '1', '0', '','', ".$timestamp.", 0, 1),
	('0', 'First Forum', 1, '2,1,-1', 1, 0, 1, '1', '0', '','', ".$timestamp.", 1, 1)";
	
	$queries[] =
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."groups (
	  `id` int(11) NOT NULL auto_increment,
	  `name` varchar(96) NOT NULL DEFAULT '',
	  `user_title` varchar(50) NOT NULL DEFAULT '',
	  `useimage` enum('0','1') NOT NULL DEFAULT 0,
	  `image_id` int(11) NOT NULL DEFAULT 0,
	  `set_title` enum('0','1') NOT NULL,
	  `flood_delay` int(11) NOT NULL DEFAULT 0,
	  `color` varchar(10) NOT NULL DEFAULT '',
	  `parent_group_id` int(11) NOT NULL DEFAULT 0,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
	";
	
	$queries[] = 
	"INSERT INTO ".TBL_PREFIX."groups (`name`, `user_title`, `useimage`, `image_id`, `set_title`, `flood_delay`, `color`, `parent_group_id`) VALUES
	('Administrator', 'Administrator', '0', 0, '0', 0, '#9F1313', 1),
	('Member', 'Member', '0', 0, '0', 30, '#000000', 2),
	('Guest', 'Guest', '0', 0, '1', 30, '#3F3F3F', 3);
	";
	
	$queries[] = 
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."images (
	  `id` int(11) NOT NULL auto_increment,
	  `name` varchar(64) NOT NULL DEFAULT '',
	  `url` varchar(255) NOT NULL DEFAULT '',
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
	$queries[] =
	"INSERT INTO ".TBL_PREFIX."images (`name`, `url`) VALUES
	('Avatar', '".FORUM_ROOT."images/avatar_1.jpg');
	";
	
	
	$queries[] = 
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."online (
	  `userid` int(11) NOT NULL DEFAULT 0,
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `sessionid` varchar(36) NOT NULL DEFAULT '',
	  `time_modified` int(10) NOT NULL DEFAULT 0,
	  `page` varchar(100) NOT NULL DEFAULT ''
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;
	";
	
	$queries[] =
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."permissions (
	  `id` int(11) NOT NULL auto_increment,
	  `group_id` int(11) NOT NULL DEFAULT 0,
	  `name` varchar(255) NOT NULL DEFAULT '',
	  `value` text NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
	$queries[] =
	"INSERT INTO ".TBL_PREFIX."permissions (`group_id`, `name`, `value`) VALUES
	(1, 'ViewAdmin', 'true'),
	(2, 'ViewAdmin', 'false'),
	(1, 'ViewBoard', 'true'),
	(2, 'ViewBoard', 'true'),
	(3, 'ViewBoard', 'true'),
	(1, 'Forum1', 'View:true,NewTopic:true,Reply:true,EditSelf:true,EditOthers:true,DeleteSelf:true,DeleteOthers:true,'),
	(2, 'Forum1', 'View:true,NewTopic:true,Reply:true,EditSelf:true,DeleteSelf:true,'),
	(3, 'Forum1', 'View:true,'),
	(1, 'Forum2', 'View:true,NewTopic:true,Reply:true,EditSelf:true,EditOthers:true,DeleteSelf:true,DeleteOthers:true,'),
	(2, 'Forum2', 'View:true,NewTopic:true,Reply:true,EditSelf:true,DeleteSelf:true,'),
	(3, 'Forum2', 'View:true,')
	;";
	
	$queries[] =
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."posts (
	  `id` int(11) NOT NULL auto_increment,
	  `topic_id` int(11) NOT NULL DEFAULT 0,
	  `user_id` int(11) NOT NULL DEFAULT 0,
	  `time_added` int(10) NOT NULL DEFAULT 0,
	  `last_edited` int(10) NOT NULL DEFAULT 0,
	  `message` text NOT NULL,
	  PRIMARY KEY  (`id`),
	  FULLTEXT KEY `message` (`message`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
	$queries[] =
	"INSERT INTO ".TBL_PREFIX."posts (`topic_id`, `user_id`, `time_added`, `message`) VALUES
	(1, 1, ".$timestamp.", 'Congratulations, WhiteBoard installed successfully.')";
	
	$queries[] =
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."themes (
	  `id` int(11) NOT NULL auto_increment,
	  `name` varchar(46) NOT NULL DEFAULT '',
	  `display_name` varchar(46) NOT NULL DEFAULT '',
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
	$queries[] =
	"INSERT INTO ".TBL_PREFIX."themes (`name`, `display_name`) VALUES
	('Default', 'Default');";
	
	$queries[] =
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."topics (
	  `id` int(11) NOT NULL auto_increment,
	  `name` varchar(64) NOT NULL DEFAULT '',
	  `forum_id` int(11) NOT NULL DEFAULT 0,
	  `user_id` int(11) NOT NULL DEFAULT 0,
	  `post_count` int(11) NOT NULL DEFAULT 0,
	  `view_count` int(11) NOT NULL DEFAULT 0,
	  `time_added` int(10) NOT NULL DEFAULT 0,
	  `time_modified` int(10) NOT NULL DEFAULT 0,
	  `last_user_id` int(11) NOT NULL DEFAULT 0,
	  PRIMARY KEY  (`id`),
	  FULLTEXT KEY `name` (`name`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
	$queries[] =
	"INSERT INTO ".TBL_PREFIX."topics (`name`, `forum_id`, `user_id`, `post_count`, `view_count`, `time_added`, `time_modified`, `last_user_id`) VALUES
	('Welcome to WhiteBoard', 2, 1, 0, 0, ".$timestamp.", ".$timestamp.", 1)";
	
	$queries[] =
	"CREATE TABLE IF NOT EXISTS ".TBL_PREFIX."users (
	  `id` int(11) NOT NULL auto_increment,
	  `username` varchar(36) NOT NULL DEFAULT '',
	  `displayname` varchar(36) NOT NULL DEFAULT '',
	  `password` varchar(135) NOT NULL COMMENT 'Length is based on the whirlpool hashing algorithm',
	  `email` varchar(150) NOT NULL DEFAULT '',
	  `firstname` varchar(36) NOT NULL DEFAULT '',
	  `lastname` varchar(36) NOT NULL DEFAULT '',
	  `group_id` int(11) NOT NULL DEFAULT 0,
	  `theme_id` int(11) NOT NULL DEFAULT 0,
	  `gender` varchar(20) NOT NULL DEFAULT '',
	  `country` varchar(96) NOT NULL DEFAULT '',
	  `birth_day` int(2) NOT NULL DEFAULT 0,
	  `birth_month` int(2) NOT NULL DEFAULT 0,
	  `birth_year` int(4) NOT NULL DEFAULT 0,
	  `time_added` int(10) NOT NULL DEFAULT 0,
	  `last_active` int(10) NOT NULL DEFAULT 0,
	  `last_login` int(10) NOT NULL DEFAULT 0,
	  `last_ip` varchar(20) NOT NULL DEFAULT '',
	  `avatar` int(11) NOT NULL DEFAULT 0,
	  `signature` text NOT NULL,
	  `date_format` varchar(10) NOT NULL DEFAULT '',
	  `time_format` varchar(10) NOT NULL DEFAULT '',
	  `time_zone` varchar(40) NOT NULL DEFAULT '',
	  `verified` enum('0','1') NOT NULL DEFAULT 0,
	  `total_posts` int(11) NOT NULL DEFAULT 0,
	  `total_topics` int(11) NOT NULL DEFAULT 0,
	  `posts_per_page` int(11) NOT NULL DEFAULT 0,
	  `topics_per_page` int(11) NOT NULL DEFAULT 0,
	  `read_topics` text NOT NULL,
	  `read_time` int(11) NOT NULL DEFAULT 0,
	  `session_id` varchar(36) NOT NULL DEFAULT '',
	  `cookie` varchar(135) NOT NULL DEFAULT '',
	  `ip` varchar(20) NOT NULL DEFAULT '',
	  `show_online` enum('0','1') NOT NULL,
	  `send_digests` enum('0','1') NOT NULL,
	  `website` varchar(128) NOT NULL DEFAULT '',
	  PRIMARY KEY  (`id`),
	  FULLTEXT KEY `username` (`username`),
	  FULLTEXT KEY `displayname` (`displayname`),
	  FULLTEXT KEY `signature` (`signature`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	
	$secUsername = mysql_escape_string($username);
	$secDisplayname = mysql_escape_string($displayname);
	$secEmail = mysql_escape_string($email);
		
	$queries[] =
	"INSERT INTO ".TBL_PREFIX."users (`username`, `displayname`, `password`, `email`, `group_id`, `theme_id`, `time_added`, `last_active`, `last_login`, `avatar`, `signature`, `total_posts`, `total_topics`, `posts_per_page`, `topics_per_page`, `read_topics`,`show_online`) VALUES
	('".$secUsername."', '".$secDisplayname."', '".$passhash."', '".$secEmail."', 1, 1, ".$timestamp.", ".$timestamp.", ".$timestamp.", 1, '', 0, 1, 10, 20, '',1)";
	
	
	// include our db connection
	//require_once('../config.php');
	//die("<pre>".file_get_contents('../config.php')."</pre>");
	
		// Get our database instance
		$db = dconnect::getInstance();
		$db->setUserName($dbuser);
		$db->setUserPassword($dbpass);
		$db->setDatabaseHost($dbhost);
		$db->setDatabaseName($dbname);
		$db->connect();
	
		// Go through each query and execute it
		foreach($queries as $key => $query)
		{
			$db->query($query) or die("An error has occured while running query #".$key);
		}
		$this->register($root, $email, $_SERVER['REMOTE_ADDR']);
		return true;
	}
}
?>