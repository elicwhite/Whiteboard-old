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
 File: /includes/classes/generic.php
 Author: SaroSoftware
 Date Created: 3/8/09
 Last Edited By: Eli White
 Last Edited Date: 5/3/09
 Latest Changes:
 
 5/03/09 - Added a user array for all the people we found so far
 4/22/09 - Made plural work with 0
 3/28/09 - Added a username/ displayname function
 3/18/09 - Added FileSizeFormat
 
 TODO:
 	- rewrite FileSizeFormat
 	
 Comments:
	A class that contains a few generic functions that can be used through the super class


 -----*/

//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

class functions
{
	private $super;
	
	function __construct($super)
	{
		$this->super = $super;
	}
	
	/**
	 * Prints a formatted version of an array
	 *
	 * @param array $array		The array to format
	 * @param boolean $exit		true if you want the script to stop after printing the array.
	 */
	function print_array ($array, $exit = false)
	{
		print "<pre>";
		print_r ($array);
		print "</pre>";
		if ($exit) exit();
	}
	
	/**
	 * Pagination function
	 *
	 * @param integer $totalPages		The total number of pages we have
	 * @param integer $curPage			The current page we are on
	 * @param String $link				The url we will be adding a number to the end of
	 * @param integer $topicsPerPage	How many topics to display per paginated page
	 * @param integer $horizonalDistance	How many links on either side of the current to display. For example
	 * 											if horizontalDistance = 2, and the current page is 3. then we would see
	 * 											[1][2][3][4][5] (assuming there are at least 5 pages.
	 * 											if horizontalDistance = 1, and the current page is 3, then we would see
	 * 											[2][3][4]
	 * @return array					Returns an array, first element is the array of paginated links ready for
	 * 										template insertion. Second element is the start post for the page (needed
	 * 										for queries
	 */
	function doPagination($totalPosts, $curPage, $link, $topicsPerPage, $horizonalDistance=2)
	{
		// We have the total number of results, the total number of pages is the ceil of
		// totalPages/topicsPerPage
		$totalPages = ceil($totalPosts/$topicsPerPage);
				
		// page 1: 1 - 20
		// page 2: 21 - 40
		// If we are on page 2, and we show 20 topics per page, the first topic
		// will be topic #21
		
		$startPost = ($curPage-1)*$topicsPerPage;
		
		/*
		<li>&lt;</li>
		<li>1</li>
		<li class="current">2</li>
		<li>3</li>
		<li>...</li>
		<li>7</li>
		<li>&gt;</li>
		<li>&#187;</li>
		*/

		// The first page we are going to display on the pagination
		$firstPaginationPage = max(1,$curPage - $horizonalDistance);
		$lastPaginationPage = min($totalPages,$curPage+$horizonalDistance);
		
		//echo "display: ".$firstPaginationPage." to ".$lastPaginationPage."<br />";
		
		// We need to display the << if we can't see page 1. The jump to first page button
		if ($firstPaginationPage > 1)
		{
			$pagination['PAGES'][] = array('value' => '&laquo;', 'link' => $link."1");
		}
		
		// If we aren't on the first page, we need to display the <. The back one page button
		if ($curPage != 1)
		{
			$pagination['PAGES'][] = array('value' => '&lt;', 'link' => $link.($curPage-1));
		}
		
		// If we are 10 from the first page, lets be able to skip back 10 pages at a time.
		if ($curPage - 10 > 1)
		{
			$pagination['PAGES'][] = array('value' => $curPage - 10, 'link' => $link.($curPage-10));
		}
		
		// Lets loop through from page 1 to how ever many pages there are, or one greater than the current page.
		// and add them to our pagination
		for ($i = $firstPaginationPage; $i <= $lastPaginationPage; $i++)
		{
			$pagination['PAGES'][] = array('value' => $i, 'link' => $link.$i);
		}
		
		// If we are 10 away from the final page, lets be able to skip forward 10 at a time.
		if ($totalPages - 10 > $curPage)
		{
			$pagination['PAGES'][] = array('value' => $curPage + 10, 'link' => $link.($curPage+10));
		}
		
		// if we aren't on the last page, we need to display the >. Forward one page.
		if ($curPage != $totalPages)
		{
			$pagination['PAGES'][] = array('value' => '&gt;', 'link' => $link.($curPage+1));
		}
		// If we can't see the last page, we need to display the >>. Jump to last page.
		if ($lastPaginationPage < $totalPages)
		{
			$pagination['PAGES'][] = array('value' => '&raquo;', 'link' => $link.$totalPages);
		}
		return array($pagination,$startPost);
	}
	
	
	
	/**
	 * Formats the date for a standard forum time. Takes care of timezones
	 * 
	 * @param string $date A date string in any format
	 * @param boolean $short	True if you want the short format
	 * @return string A formatted string depending on how far away $date is from now.
	 */
	function formatDate($date, $short = false)
	{
		$dateformat = $this->super->user->date_format;
		$timeformat = $this->super->user->time_format;
		
		// make an extra variable we will override
		$extra = "";
		
		// Most of this code found on php's date() page by davet15@hotmail.com on 08-Mar-2009 04:13
		$time = time();
		
		// The difference (integer representing seconds)
		$diff = $time - $date;
		
		if($diff < 60)
		{
			return "Less than 1 minute ago";
		}
		else if($diff < 60 * 60) // less than an hour ago
		{ 
			// 2:01am (13 minutes ago)
			// how many minutes ago
			$result = floor($diff/60);
			$plural = plural($result);
			
			// The time ago if we don't want the short version
			if (!$short)
				$extra = " (".$result." minute".$plural." ago)";
			
			
			return date($timeformat,$date).$extra;
		}
		else if($diff < 60 * 60 * 24) // less than 24 hours ago
		{
			//7:28pm (7 hours ago)
			
			$result = floor($diff/(60*60));
			$plural = plural($result);
			if (date($dateformat,$date) != date($dateformat,$time))
			{
				$text = date($dateformat,$date);
			}
			else
			{
				$text = date($timeformat,$date);
			}
		
			if (!$short)
				$extra = " (".$result." hour".$plural." ago)";
			
			return $text.$extra;
		}
		else if($diff < 60 * 60 * 24 * 30)
		{
			$result = floor($diff/(60*60*24));
			$plural = plural($result);
			$extra = "";
			if (!$short)
				$extra = " ($result day$plural ago)";
			
			return date($dateformat,$date).$extra;
		}
		// else
		$result = floor($diff/(60*60*24*30));
		$plural = plural($result);
		
		if (!$short)
		$extra = " ($result month$plural ago)";
		
		return date($dateformat,$date).$extra;
	}
	
	/**
	 * Formats the date for a standard forum time. Takes care of timezones
	 * 
	 * @param string $date A date string in unix timestamp format
	 * @return string A formatted string depending on how far away $date is from now.
	 */
	function formatTime($date)
	{
		$time = time();
		
		// The difference (integer representing seconds)
		$diff = $time - $date;
		
		if ($diff == 0)
		{
			return "An instant ago";
		}
		elseif($diff < 60)
		{
			$plural = plural($diff);
			return $diff." second".$plural." ago";
		}
		else if($diff < 60 * 60) // less than an hour ago
		{ 
			// 2:01am (13 minutes ago)
			// how many minutes ago
			$result = floor($diff/60);
			$plural = plural($result);
			
			// The time ago if we don't want the short version
			return $result." minute".$plural." ago";
		}
		else if($diff < 60 * 60 * 24) // less than 24 hours ago
		{
			//7:28pm (7 hours ago)
			$result = floor($diff/(60*60));
			$plural = plural($result);
			return $result." hour".$plural." ago)";
		}
	}
	
	/**
	 * Displays a file size in a nice format
	 *
	 * @param integer $filesize the number of bytes to format
	 * @return array	"size" index is the size, "type" index is the type of size (kb, mb, gb, tb);
	 */
	function fileSizeFormat($filesize)
	{
		// Function found on http://support.netfirms.com/idx.php/75/623/article/How-large-is-my-MySQL-database.html
		
		$bytes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
		# values are always displayed
		
		if ($filesize < 1024)
			$filesize = 1;
		
		# in at least kilobytes.
		
		for ($i = 0; $filesize > 1024; $i++)
			$filesize /= 1024;
		
		$file_size_info['size'] = ceil($filesize);
		$file_size_info['type'] = $bytes[$i];
		return $file_size_info;
	}
	
	/**
	 * Returns the display name or username of a user with the given Id
	 *
	 * @param integer $id	The user's id
	 * @return string		The displayname if there is one, username otherwise.
	 */
	function userId2Display($id)
	{
		$id = $this->super->db->escape($id);
		$query = "SELECT u.username, u.displayname, g.color, g.name FROM ".TBL_PREFIX."users AS u INNER JOIN ".TBL_PREFIX."groups AS g ON u.group_id=g.id WHERE u.id=".$id;
		
		$result = $this->super->db->query($query);
		$user = $this->super->db->fetch_assoc($result);
		$name = $this->user2display($user['username'], $user['displayname']);
		return array(
			$name,
			$user['color'],
			'<a title="'.$user['name'].'" href="?act=member&amp;id='.$id.'"><span style="color:'.$user["color"].'">'.$name.'</span></a>'
			);
	}
	
	/**
	 * Returns the displayname if there is one, otherwise the username
	 *
	 * @param string $username		The username to check
	 * @param string $displayname	The display name to check
	 * @return string				The display name if there is one, username otherwise.
	 */
	function user2display($username, $displayname)
	{
		if ($displayname != "")
		{
			return $displayname;
		}
		
		return $username;
	}
	
	/**
	 * Gets an array containing image information from an image id
	 *
	 * @param int $id Row number of the image you are trying to get
	 * @return mixed Array with indices name (alt text) and url of the image, or false if it doesn't exist in the database.
	 */
	function getImage($id)
	{
		$id = $this->super->db->escape(intval($id));
		$query = "SELECT * FROM ".TBL_PREFIX."images WHERE `id` = ".$id;
		$query = $this->super->db->query($query);
		
		if ($this->super->db->getRowCount($query) > 0)
		{
			$row = $this->super->db->fetch_assoc($query);
			// weve got an image row
			$image = array(
				"name"	=>	$row['name'],
				"url"	=>	$row['url']
				);
				
			return $image;
		}
		else
		{
			return false;
		}
		
	}
	
	function formatUser($group, $color, $id, $username, $displayname, $both=false)
	{
		if ($both)
		{
			if (isset($displayname) && $displayname != "")
				$display = $username." (".$displayname.")";
			else
				$display = $username;
		}
		else
			$display = $this->user2display($username, $displayname);
		
		return '<a class="username" style="color:'.$color.'" title="'.$group.'" href="?act=member&amp;id='.$id.'">'.$display.'</a>';
	}
	
	/**
	 * The incredible query saving array that
	 * holds all the users that we have displayed thus far.
	 */
	private $usersDisplayed = array();
	
	/**
	 * Gets a user and their group from the user's id
	 *
	 * @param int $id	The id of the user to lookup
	 * @param string $formmated		Whether you want the string formatted display of the user
	 * @param boolean $both		Whether you want to display Username (Displayname)
	 * @return mixed	The array of info or the formatted string
	 */
	function getUser($id, $formatted = false, $both = false)
	{
		// secure the ID
		$id = intval($id);
		// and escape it
		$id = $this->super->db->escape($id);
		// If we already have this user
		if (!key_exists($id,$this->usersDisplayed))
		{
			// we need to get this user's info
			$query = "SELECT u.*, g.name AS groupname, g.color
				FROM ".TBL_PREFIX."users AS u
				INNER JOIN ".TBL_PREFIX."groups AS g
				ON u.group_id=g.id
				WHERE u.id=".$id;
			//echo $query;
			$query = $this->super->db->query($query);
			if ($this->super->db->getRowCount($query))
			{
				$user = $this->super->db->fetch_assoc($query);
				$user['formatted'] = $this->formatUser($user['groupname'], $user["color"], $user['id'], $user['username'],$user['displayname'], $both);
			$this->usersDisplayed[$user['id']] = $user;
			}
			
			
		}
		// if we want the formatted version
		if ($formatted)
			return $this->usersDisplayed[$id]["formatted"];	
		else
			return $this->usersDisplayed[$id];
	}
}
?>