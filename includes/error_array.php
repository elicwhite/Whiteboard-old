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
File: /includes/error_array.php
Author: SaroSoftware
Date Created: 03/10/2007
Last Edited By: Eli White
Last Edited Date: 3/8/09
Latest Changes: 
- Changed lang_main to main
- Added test key

Comments:
Error array files

TODO:
Convert into a class
-----*/

// Define the Error array
$error_array = array(
// 'Error number' => 'Description',
	1 	=>	'you cannot connect to the database.',
	2		=>	'an invalid string is called through a template include.',
	3		=>	'a non-existant template function is called.',
	4		=>	'the needed language file doesnt exist.',
	5		=>	'no topic id is entered.',
	6		=>	'no forum id is entered.',
	7		=>	'the given configuration key does not exist.'
);
?>