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
 File: /includes/classes/user.php
 Author: SaroSoftware
 Date Created: 3/30/09
 Last Edited By: Eli White
 Last Edited Date: 5/3/09
 Latest Changes:
 	5/3/09 - Finished Forum Permissions and coded it in.
  	4/17/09 - Made a prototype of the functions that need to be written
 	
 Comments:
	Does all the user actions, log in, rename, check permissions, etc.


 -----*/

//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

class user
{
	private $super;
	
	private $cookiename = "sarologin";
	
	// the user that is currently logged in
	private $currentUser;
	
	// settings for guests
	private $guestUser;
	
	/**
	 * permissions array
	 * $super->user->can->viewBoard
	 * $super->user->can->("forum1","view")
	 * 
	 */
	private $perm;
	// oh, har har ^^

	private $viewedTopics = array();
	private $viewedForums = array();

	
	function __construct(&$super)
	{
		$this->super = $super;
		
		//if we are logged in
	
		
		if (isset($_SESSION['logged']) && $_SESSION['logged'])
		{

			// grab the user_id and session id.
			$user_id = $this->super->db->escape($_SESSION['user_id']);
			$sessionid = $this->super->db->escape(session_id());
			
			// check if the user with that id has that sessionid
			$user = "SELECT * FROM ".TBL_PREFIX."users WHERE (`id` = ".$user_id.") AND (`session_id` = '".$sessionid."')";
			$user = $this->super->db->query($user);
			
			if($this->super->db->getRowCount($user) > 0)
			{
				// if they do, set the $currentUser array
				$result = $this->super->db->fetch_assoc($user);
				$this->setUser($result);
			}
			else 
			{
				// otherwise clear everything out
				$this->logout();
			}
		}
		elseif(isset($_COOKIE[$this->cookiename]))
		{
			//echo "hello";
			// for some reason, if magic_quotes is on, then it automatically adds
			// slashes when you serialize() an array. Weird
			if (get_magic_quotes_gpc())
			{
				list($user_id, $cookie) = unserialize(stripslashes($_COOKIE[$this->cookiename]));
			}
			else
			{
				list($user_id, $cookie) = unserialize(stripslashes($_COOKIE[$this->cookiename]));
			}
			
			// if either the username or cookie is blank, return
			if (!$user_id || !$cookie)
				return;
				
			// echo "username: ".$username.", cookie: ".$cookie."<br />";
				
			$user_id = $this->super->db->escape($user_id);
			$cookie = $this->super->db->escape($cookie);
			$user = "SELECT * FROM ".TBL_PREFIX."users WHERE (`id` = '".$user_id."') AND (`cookie` = '".$cookie."')";
			$user = $this->super->db->query($user);
			
			if($this->super->db->getRowCount($user) > 0)
			{
				//echo "yesm";
				$user = $this->super->db->fetch_assoc($user);
				$this->setUser($user);
			}
			else
				$this->logout();
		}
		else
		{
			//echo "maybe so";
			$this->logout();
		}
		
		$this->guestUser['username'] = "Guest";
		$this->guestUser['group_id'] = "3";
		$this->guestUser['theme_id'] = $this->super->config->defaultTheme;
		$this->guestUser['date_format'] = $this->super->config->dateFormat;
		$this->guestUser['time_format'] = $this->super->config->timeFormat;
		$this->guestUser['time_zone'] = $this->super->config->timeZone;
		//avatar?
		$this->guestUser['posts_per_page'] = $this->super->config->postsPerPage;
		$this->guestUser['topics_per_page'] = $this->super->config->topicsPerPage;
		$this->guestUser['read_timt'] = "0";
		$this->guestUser['read_topics'] = "";
		
		// lets set up permissions.
		// get group_id with our __get $this-group_id
		// we are splitting up the values and nested values in the
		// permissions table into a nicely formatted
		// array that can be queried using the can($key, $subkey) function
		$perm = "SELECT * FROM ".TBL_PREFIX."permissions WHERE `group_id`=".$this->group_id;
		$perm = $this->super->db->query($perm);
		while($perms = $this->super->db->fetch_assoc($perm))
		{
			if (strpos($perms["value"], ",") !== false)
			{
				$subarray = array();
				// we have a listed permission set
				// View:true,NewTopic:true,Reply:true,EditSelf:true
				$parts = explode(",", $perms["value"]);
				foreach($parts as $part)
				{
					// we get errors when evaluating the last element
					// maybe something to fix?
					if ($part != "")
					{
						$permparts = explode(":",$part);
						
						// if the value is true, we want to convert to boolean true
						$val = false;
						if ($permparts[1] == "true")
						{
							$val = true;
						}
						
						$subarray[$permparts[0]] = $val;
					}
				}
				$this->perm[$perms["name"]] = $subarray;	
			}
			else
			{
				$val = false;
				if ($perms["value"] == "true")
				{
					$val = true;
				}
				$this->perm[$perms["name"]] = $val;
			}
		}
		
		// split a string like '4:7,2:8,5:7,9:12,'
		$topics = explode(",",$this->read_topics);
		// since we have a trailing comma, we need to pop the last element
		array_pop($topics);

		foreach($topics as $topic)
		{
			// get the topicid and forumid
			//$parts = explode(":", $topic);
			if (!in_array($topic, $this->viewedTopics))
				$this->viewedTopics[] = $topic;
			//if (!in_array($parts[0], $this->viewedForums))
			//	$this->viewedForums[] = $parts[0];
		}
		
		$forums = explode(",",$this->read_forums);
		// since we have a trailing comma, we need to pop the last element
		array_pop($forums);
		
		foreach($forums as $forum)
		{
			if (!in_array($forum, $this->viewedForums))
				$this->viewedForums[] = $forum;
		}
	}
	
	// need a setUser(user array, boolean setcookie)
	function setUser($user)
	{
		$this->currentUser = $user;
		$_SESSION['logged'] = true;
		$_SESSION['user_id'] = $this->currentUser['id'];
		
		$sessionid = $this->super->db->escape(session_id());
		// if our session id, but we are the right user, we should update it.
		if ($this->currentUser['session_id'] != $sessionid)
		{
			$sql = "UPDATE ".TBL_PREFIX."users SET `session_id` = '".$sessionid."' WHERE `id` = ".$this->currentUser['id'];
			$this->super->db->query($sql);
		}
	}
	
	function logout()
	{
		// change the session id. Tricky little trick.
		$_SESSION['logged'] = false;
		$_SESSION['user_id'] = 0;
		// expire the cookie
		setcookie($this->cookiename, serialize(""), time()-3600);
		
		$this->currentUser = null;
	}
	
	function checkLogin($name, $pass, $remember)
	{
		// checks the cookies and sessions to make sure the user is who
		// they say they are. Make sure to check against the database
		// Duh.
		$name = $this->super->db->escape($name);
		
		$pass = encrypt_password($pass);
		$pass = $this->super->db->escape($pass);
		
		$existsSql = "SELECT * FROM ".TBL_PREFIX."users WHERE `username` = '".$name."' AND `password` = '".$pass."'";
		$existsSql = $this->super->db->query($existsSql);
		if ($this->super->db->getRowCount($existsSql) > 0)
		{
			
			$user = $this->super->db->fetch_assoc($existsSql);
			// passed information is valid for some user.
			$this->setUser($user);
			
			$time = encrypt_password(time());
			
			if ($remember)
			{
				$sql = "UPDATE ".TBL_PREFIX."users SET `cookie` = '".$time."' WHERE `id` = ".$this->currentUser['id'];
				$this->super->db->query($sql);
				
				$cookie = serialize( array($this->currentUser['id'], $time) );
				setcookie($this->cookiename, $cookie, time() + 60*60*24*30);
			}
			
			return true;
			
		}
		else 
		{
			// passed information is invalid.
			// set as guest?
			$this->logout();
			return false;
		}
	}	
	
	
	// need a logout function
	// that removes the cookie and destroies the session
	
	function isLogged()
	{
		// return a boolean if a user is logged in or not
		return is_array($this->currentUser) && count($this->currentUser) > 0;
	}
	
	// Gets values from $currentUser
	// called like $super->user->displayname
	public function __get($key)
	{
		if (is_array($this->currentUser) && key_exists($key, $this->currentUser) && $this->currentUser[$key] != "")
		{
			return $this->currentUser[$key];
		}
		elseif (key_exists($key, $this->guestUser))
		{
			return $this->guestUser[$key];
		}
		else
		{
			return false;
		}
	}
	
	/// set values on the current user. I think this should be a global set like
	// this instead of a seperate set for each value so that modules
	// can add columns to the user table and modify the values without
	// editing this file
	public function __set($key, $val)
	{
		// You can't edit a user's id number
		if ($key == "id")
			return false;
			
		if ($this->isLogged() && key_exists($key, $this->currentUser))
		{
			$this->currentUser[$key] = $val;
			$sql = "UPDATE ".TBL_PREFIX."users SET `".$GLOBALS['super']->db->escape($key)."`= '".$GLOBALS['super']->db->escape($val)."' WHERE `id`=".$this->currentUser['id'];
			$GLOBALS['super']->db->query($sql);
		}
		else
		{
			return false;
		}
	}
	
	function updateActive($pageClass)
	{
		
		//echo "yes, i'm here";
		
		// If they don't want to show up online
		if ($this->isLogged() && $this->currentUser["show_online"] == 0)
			return;

			
			
		// forces the correct format of ips
		$ip = long2ip(ip2long($_SERVER['REMOTE_ADDR']));
		$ip = $this->super->db->escape($ip);
		
		
		$id = $this->id;
		
		// if we aren't logged in, set it to be 0 (guest)
		if ($id == false)
			$id = 0;

		$time = time();
		$page = $pageClass->onlineName();
		$page = $this->super->db->escape($page);
		$latestTime = time() - 60*$this->super->config->usersOnlineOffset;
		
		$sessionid = $this->super->db->escape(session_id());
		
		// THIS CODE NEEDS TO BE REFACTORED. 
		$userInsert = "INSERT INTO ".TBL_PREFIX."online (`userid`, `ip`, `sessionid`, `time_modified`, `page`) VALUES
					(".$id.", '".$ip."', '".$sessionid."', ".$time.", '".$page."');";
		//$guestInsert = "INSERT INTO ".TBL_PREFIX."online (`userid`, `ip`, `sessionid`, `time_modified`, `page`) VALUES
					//(0, '".$ip."', '".$sessionid."', ".$time.", '".$page."');";
		
		
		$removal = "DELETE FROM ".TBL_PREFIX."online WHERE `userid` = ".$id." OR `sessionid` = '".$sessionid."' OR `sessionid`=' ' OR `time_modified` < ".$latestTime;
		$this->super->db->query($removal);
		
		$this->last_active = time();
		
		//if ($id >0)
		//{
			$this->super->db->query($userInsert);
		//}
		//else
		//{
			//$this->super->db->query($guestInsert);
		//}
		
		// the latest time a user could stay "active"
		
		//echo "curr time: ".time().", 15 minutes ago: ".$latestTime;
	}
	
	/**
	 * Gets a permission value
	 *
	 * @param string $key	The permission to check
	 * @param string $subkey	The sub permission to check
	 * @return mixed	The permission value if it exists, false otherwise
	 */
	function can($key, $subkey = null)
	{
		// if we are in the array check the value
		if (key_exists($key, $this->perm))
		{
			// if this key is an array of its own
			if (is_array($this->perm[$key]))
			{
				// we need to check the subkey
				if (key_exists($subkey, $this->perm[$key]) && $subkey != null)
				{	
					return $this->perm[$key][$subkey];
				}
				else
				{
					return false;
				}
			}
			else
				return $this->perm[$key];
		}
		return false;
	}
	
	function noPerm()
	{
		$tpl = new tpl(ROOT_PATH."themes/Default/templates/error.php");
		$tpl->add("error_message", "You don't have permission to view this page.");
		return $tpl->parse(true);
	}
	
	function viewed_topic($topicid)//, $forumid)
	{
		// if we aren't logged in, then we have no place to save this, everything is unread to us.
		if (!$this->isLogged())
			return;
			
		/*$string = intval($topicid).":".intval($forumid);
		$query = 'UPDATE '.TBL_PREFIX.'users SET `read_topics` = CONCAT(`read_topics`,"'.$string.',") WHERE `id`="'.$this->currentUser['id'].'" AND NOT FIND_IN_SET("'.$string.'", `read_topics`)';
		$this->super->db->query($query);
		*/
		
		$t = intval($topicid);
		//$f = intval($forumid);
		$query = 'UPDATE '.TBL_PREFIX.'users
					SET `read_topics` = CONCAT(`read_topics`,"'.$t.',")
					WHERE `id`="'.$this->currentUser['id'].'"
					AND NOT FIND_IN_SET("'.$t.'", `read_topics`)
				';
		$this->super->db->query($query);
		/*$query = 'UPDATE '.TBL_PREFIX.'users
					SET `read_forums` = CONCAT(`read_forums`,"'.$f.',")
					WHERE `id`="'.$this->currentUser['id'].'"
					AND NOT FIND_IN_SET("'.$f.'", `read_forums`)
				';
		$this->super->db->query($query);
		*/
	}
	function has_viewed_topic($topicid)
	{
		return in_array($topicid, $this->viewedTopics);
	}
	/*function has_viewed_forum($forumid)
	{
		return in_array($forumid, $this->viewedForums);
	}*/
}
?>