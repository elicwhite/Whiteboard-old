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
 File: /includes/classes/pageClass.php
 Author: SaroSoftware
 Date Created: 4/7/2009
 Last Edited By: Eli White
 Last Edited Date: 4/7/2009
 Latest Changes:

 3/30/2009: Added securities to disallow direct access
 7/05/2007: added mysql_result
 
 Comments:
 Database Wrapper

 -----*/

//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

require_once(ADMIN_PATH."includes/classes/subPage.php");

/**
 * Forum Page class that is inherited to make different pages on the forums;
 *
 */
abstract class adminPage
{
	protected $pageName;
	protected $super;
	
	public $sub;
	
	public $isRedirect;
	public $active;
	public $link;
	public $default;
	
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
	 * Page name to show on the Online List
	 *
	 * @return string The displayable name of this forum
	 */
	public function onlineName()
	{
		return "Admin Panel";
	}
	
	/**
	 * Function to get the title of the page.
	 *
	 * @return string	The title of the page 
	 */
	public function getTitle()
	{
		return "Admin: ".$this->getName()." - ".$this->sub->getName()." ".$this->getAddon();
	}
	
	protected function getAddon()
	{
		
		return "(".$this->super->config->siteName.")";
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
		$css[] = array("path" => "display/admin.css");
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
		$js[] = array("path" => "../includes/js/jquery-1.3.2.js");
		// and the admin's functions
		$js[] = array("path" => "includes/js/functions.js");
		return $js;
	}
	
	
	// Define your array of sub pages/ children in this method.
	abstract function children();
}

?>