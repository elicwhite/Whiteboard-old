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
File: /includes/functions/languages.php
Author: SaroSoftware
Date Created: 04/17/2007
Last Edited By: Dennis Blake
Last Edited Date: 04/17/2007
Latest Changes:
- Initial Creation

Comments:
-----*/

//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

//Main langauge class
class lang
{
	public $language; // What language to use
	public $main=array();
	public $text=array();

	//continue the main db variable throughout all class files
	function __construct()
	{
	}
	function init()
	{
		$string = ROOT_PATH."languages/".$this->language.".php";
		if (file_exists($string))
		{
   			require_once($string);
			$this->text = $main; // Set the main lang array as public text variable 
		}
		else
		{
			$GLOBALS['super']->err->errno = 4;
			$GLOBALS['super']->err->throwerr();
		}
	}	
}
?>