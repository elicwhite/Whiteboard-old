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
 File: /administration/includes/classes/subPage.php
 Author: SaroSoftware
 Date Created: 5/6/2009
 Last Edited By: Eli White
 Last Edited Date: 5/6/2009
 Latest Changes:

 
 Comments:
	Class for children pages

 -----*/

//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

/**
 * Forum Page class that is inherited to make different pages on the forums;
 *
 */
abstract class adminSub
{
	protected $pageName;
	protected $super;
	
	public function __construct()
	{
		global $super;
		$this->super = $super;
		
		$this->setName("");
	}

	/**
	 * Set the page name
	 *
	 * @param string $name	Set the name of the page (used for the title)
	 */
	protected function setName($name)
	{
		$this->pageName = $name;
	}
	
	/**
	 * Get the page name
	 *
	 * @return string The name of the current page.
	 */
	public function getName()
	{
		return $this->pageName;
	}
	
	
	/**
	 * Gets the content/main bulk of the page
	 *
	 * @return string The content
	 */
	public function display()
	{
		ob_start();
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	/**
	 * Gets the CSS that this page requests
	 *
	 * @return array of the urls of the css documents
	 */
	public function getCSS()
	{
		$css = array();
		return $css;
	}
	
	/**
	 * Gets the Javascript that this page requests
	 *
	 * @return array of the urls of the javascript documents
	 */
	public function getJS()
	{
		// include the forum's jquery
		$js = array();
		return $js;
	}
}

?>