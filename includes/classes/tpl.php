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
 File: /includes/classes/tpl.php
 Author: SaroSoftware
 Date Created: 04/19/2009
 Last Edited By: Eli White
 Last Edited Date: 4/22/2009
 Latest Changes:
 
 4/22/09 - Added the ability to access super's functions class.
 4/19/09 - Started PHP Templates
 
 Comments:
 Template class file

 TODO:
	Clean up loop function establish attributes (Done)
	Create foreach function
	Create if / else function (Started)
	Add support for (&& || != == > < >= <=) (Started)
	Add syntax error checking for templating, especially when using functions (Started)
	Streamline all code and make sure its as fast as possible (Started)
	Allow inline comments and maybe block comments (// and /* * /)

 -----*/

class tpl
{
	// location of the template file
	private $template_location = null;
	// string that contains the template

	// array that contains all the vars accessible by the template template
	private $variables = array();
	
	// pointer to our functions class.
	private $functions = null;
	
	/**
	 * Creates a template class from a template file
	 *
	 * @param String $templateFile	Path to a template file
	 */
	function __construct($templateFile)
	{
		$this->functions =& $GLOBALS['super']->functions;
		
		
		$this->template_location = $templateFile;
		if (!is_file($this->template_location))
		{
			echo $this->template_location." does not exist";
		}
	}
	
	function __get($var)
	{
		if (key_exists($var, $this->variables))
			return ($this->variables[$var]);
	}
	
	function parse($return = false)
	{
		if ($this->template_location == null)
			return;
		
			
		ob_start();
		
		require($this->template_location);
		
		$content = ob_get_contents();
		ob_end_clean();
		if ($return)
			return $content;
		else
			echo $content;
	}
	
	function add($var, $value = null)
	{
		$this->variables[$var] = $value;
	}
}
		
?>