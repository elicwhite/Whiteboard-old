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
 File: /includes/functions/functions.php
 Author: SaroSoftware
 Date Created: 3/30/09
 Last Edited By: Eli White
 Last Edited Date: 5/3/09
 	
 Comments:
	Global functions for the discussion board system.


 -----*/

/**
 * The forums encryption scheme
 *
 * @param string $username	The username that we are encrypting the password for
 * @param string $password	The password that we are encrypting
 * @return string			An encrypted string using both the username and password
 */
function encrypt_password($password)  
{  
	$md5_pass = md5($password); 
	$hash_crypt_pass = hash("whirlpool",PASS_HASH.$md5_pass); 
	return $hash_crypt_pass;  
}

/**
 * Adds a security hash to forms to protect against
 * CSRF attacks. Validate the form with isSecureForm()
 *
 * @param boolean $return true if you want it to return the input field, false otherwise
 * @return mixed
 */
function secureForm($formName, $return = false)
{
	$hash = encrypt_password(time());
	$hash = substr($hash, 4, 10);
	$input = '<input type="hidden" name="'.$formName.'Hash" value="'.$hash.'" />';
	$_SESSION[$formName.'Hash'] = $hash;
	
	if ($return)
		return $input;
	else 
		echo $input;
	
}

/**
 * Checks the security of a form created with secureForm()
 * against CSRF attacks
 * 
 * @return boolean true if the form was valid, false otherwise
 */
function isSecureForm($formName)
{
	//echo "ph: ".$_POST['formHash']."<br />";
	//echo "sh: ".$_SESSION['formHash'];
	$return = false;
	
	if (!isset($_POST[$formName.'Hash']) || !isset($_SESSION[$formName.'Hash']))
		return false;
	
	if ($_POST[$formName.'Hash'] == $_SESSION[$formName.'Hash'])
		$return = true;
		
	unset ($_SESSION[$formName.'Hash']);
	
	return $return;
		
}

/**
* Truncates a string and adds a set of ellipses (...)
*
* @param string $string 	The string to truncate
* @param integer $length 	The max length you want the string
* @return string			The truncated string with ... if it is greater than the length,
* 								 returns the string if it is less than the maxlength
*/
function dotdotdot($string, $length)
{
	//echo "string: ".$string.", length: ".$length."<br />";
	if (strlen($string) > $length)
		$string = substr($string, 0, $length - 3)."...";
	
	return $string;
}


/**
* Helps generate singular strings/plural strings
*
* @param integer $num the number to evaluate
* @return string "s" if $num is > 1, "" otherwise
*/

function plural($num, $endsInY = false)
{		
	if ($num != 1)
    {
    	if ($endsInY)
    		return "ies";
    	else
    		return "s";
    }
    else
    {
    	if ($endsInY)
    		return "y";
    	else
    		return "";
    }
}
?>