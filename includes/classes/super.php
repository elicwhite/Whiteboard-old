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
File: /includes/classes/super.php
Author: SaroSoftware
Date Created: 04/17/2007
Last Edited By: Dennis Blake
Last Edited Date: 04/17/2007
Latest Changes:
- Initial Creation

Comments:
Main global class handler
-----*/

//No direct access
if(!defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

require_once(ROOT_PATH."config.php");
require_once(ROOT_PATH."includes/classes/err.php");
require_once(ROOT_PATH."includes/classes/dconnect.php");
require_once(ROOT_PATH."includes/classes/languages.php");
require_once(ROOT_PATH."includes/classes/functions.php");
require_once(ROOT_PATH."includes/classes/user.php");
require_once(ROOT_PATH."includes/classes/config.php");

//Ohh yes...super-class time.
class super
{
	public $db;					//Superclass database holder
	public $err;				//Superclass error holder
	public $lang;				//Superclass Language holder
	public $config;				//Superclass Forum config holder
	public $user;				//Superclass User Info holder
	public $current_page;		//Superclass Current page holder
	public $functions;			//Superclass Generic functions holder

	//constructor -> changed to super function to prevent duplicate call 
	//moved all includes out of this area and into the top.  Your including them anyway and always might as well add them all in one spot.
	function __construct()
	{

		// Include Error Function		
		$this->err = new err(); //Set error class
		
		//Add the DB class to the super
		$this->db = dconnect::getInstance(); // Initialize the DB Class
		
		$this->config = new config($this);

		$this->functions = new functions($this);
		
		$this->user = new user($this);
		
	}	
	
	/**
	 * Gets a string representation of the 
	 *
	 * @return string
	 */
	function __toString()
	{
		return "The WhiteBoard Super Class. Fear it's power!";
	}
}


?>
