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

/**
 * Forum Page class that is inherited to make different pages on the forums;
 *
 */
class forumPage
{
	protected $pageName;

	public function __construct()
	{
		$this->setName($GLOBALS['super']->config->siteName);
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
		return $this->getName();
	}
	
	/**
	 * Function to get the title of the page.
	 *
	 * @return string	The title of the page 
	 */
	public function getTitle()
	{
		return $this->getName()." ".$this->getAddon();
	}
	
	protected function getAddon()
	{
		
		return " - A WhiteBoard System";
	}
	
	/**
	 *  Returns the siteName as a link to the homepage
	 */
	public function breadCrumb()
	{
		
		return '<a href="index.php">'.$GLOBALS['super']->config->siteName.'</a>';
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
		$css[] = array("path" => "themes/Default/main.css");
		return $css;
	}
	
	/**
	 * Gets the Javascript that this page requests
	 *
	 * @return array of the urls of the javascript documents
	 */
	public function getJS()
	{
		$js[] = array("path" => "includes/js/jquery-1.3.2.js");
		$js[] = array("path" => "includes/js/ui.core.js");
		$js[] = array("path" => "includes/js/global_functions.js");
		return $js;
	}
	
	/**
	 * Recursive function that will go through the array of parent ids and
	 * create links back to each forum and return a string of links and forum names
	 *
	 * @param array $parentArray 	the array of parentids (not including yourself)
	 * @return string 				the string of links and forum titles
	 */
	protected function recurseNames($parentArray)
	{
		//$GLOBALS['super']->functions->print_array($parentArray, true);
		// base case, the first item in the array is a -1 (flag for no more parents)
		if ($parentArray[0] == "-1"  )
		{
			return "";
		}
		else
		{
			// we have more forums
			$sql = "SELECT `is_cat`, `name`, `id` FROM ".TBL_PREFIX."forums WHERE `id` = ".$parentArray[0];
			$result = $GLOBALS['super']->db->query($sql);
			$result = $GLOBALS['super']->db->fetch_assoc($result);
			
			//$GLOBALS['super']->functions->print_array($parentArray, false);
			// lets remove the first forum and keep recursing
			$currentid = array_shift($parentArray);
			
			
			// dont show cats
			if ($result['is_cat'] != 1)
			{
				$link = " ".$GLOBALS['super']->config->crumbSeperator.' <a href="index.php?act=fdisplay&amp;id='.$result['id'].'">'.$result['name'].'</a>';
				return $this->recurseNames($parentArray).$link;
			}
			else
			{
				return $this->recurseNames($parentArray);
			}
		}
	}
}

?>