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
File: /includes/functions/err.php
Author: SaroSoftware
Date Created: 03/10/2007
Last Edited By: Eli White
Last Edited Date: 04/18/2007
Latest Changes:
- In throwerr function, set err_thrown to TRUE

Comments:
-----*/

//No direct access
if( ! defined("SEC"))
{
	echo "<p>You cannot access this page directly</p>";
	exit();
}

//Main error-handling class
class err
{
	public $error = array(); //The array that contains all possible errors
	
	public $errors = array(); // The array that contains the thrown errors
	
	function __construct()
	{
		require_once(ROOT_PATH."includes/error_array.php"); // Include the error array
		$this->error = $error_array; // Set the error array as a public array
	}
	
	function throwError($errorno)
	{	
		if (key_exists($errorno, $this->error))
		{
			$this->errors[] = array('id' => $errorno, 'message' => $this->error[$errorno]);
		}
	}	
	
	function getNumErrors()
	{
		return count($this->errors);
	}
	
	function showErrors()
	{
		if (count($this->errors) > 0)
		{
			return "There are ".count($this->errors)." errors on this page.";
		}
	}
}
?>