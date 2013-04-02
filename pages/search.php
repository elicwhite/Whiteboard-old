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
 File: /pages/search.php
 Author: SaroSoftware
 Date Created: 04/28/09
 Last Edited By: Eli White
 Last Edited Date: 4/28/09
 Latest Changes:
 	
 TODO:
 	Filter results
 
 NOTES:
 We will take &q=SEARCHTERMS&type=TYPE&page=PAGE
 ex: &q=chocolate&type=all&page=2
 
 
 Comments:
	The main search page

 -----*/



//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}
if( ! defined("INCLUDED")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

class Search extends forumPage
{

	public function __construct()
	{
		parent::__construct();
		$this->setName("Search");
	}
	
	/**
	 * Returns the calendar's breadcrumbs
	 *
	 * @return string The breadcrumb string
	 */
	public function breadCrumb()
	{
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->getName();
	}

	/**
	 * Get the display of the page
	 *
	 * @return string The page content
	 */
	public function display()
	{
		ob_start();
		
		// what are we searching for?
		if (!isset($_GET['q']) || $_GET['q'] == "")
		{
			// We don't have a search, display a search form.	
			$searchForm = new tpl(ROOT_PATH.'themes/Default/templates/search_form.php');
			$searchForm->parse();
		}
		else // If we have a search
		{
			$query = $GLOBALS['super']->db->escape($_GET['q']);
		
			$results = array();
			
			function getSearch($query, $startlimit=false, $endlimit=false)
			{	
				$extra = "";
				if ($startlimit!==false && $endlimit!==false)
				{
					$extra = "LIMIT ".$startlimit.", ".$endlimit;
				}
				
				return "SELECT lower('topic') AS type,
				`id`,`name`, '' AS displayname, `user_id`,`post_count`,`time_added`, '' AS message, `time_modified`, `last_user_id`,
				MATCH(name) AGAINST('".$query."') AS score
				FROM ".TBL_PREFIX."topics
				WHERE MATCH(name) AGAINST('".$query."')
				
				UNION
				
				SELECT lower('post') AS type,
					p.id, t.name, '', p.user_id, p.topic_id, p.time_added, p.message AS message, '', '',
				MATCH(message) AGAINST('".$query."') AS score
				FROM ".TBL_PREFIX."posts AS p
				INNER JOIN ".TBL_PREFIX."topics AS t
				ON p.topic_id=t.id
				WHERE MATCH(message) AGAINST('".$query."')
				
				UNION
				
				SELECT lower('user') AS type,
					`id`, `username`, `displayname`, `avatar`, '', `time_added`, '', '', '',
				MATCH(username) AGAINST('".$query."') AS score
				FROM ".TBL_PREFIX."users
				WHERE MATCH(username) AGAINST('".$query."')         
				
				ORDER BY score DESC ".$extra;
			}
			//die($search);
			
			$numRows = $GLOBALS['super']->db->getRowCount($GLOBALS['super']->db->query(getSearch($query)));
			
			
			
			
			
			$totalPages = max(1, $numRows);
			
			// We need to know what the current page is.
			if (!isset($_GET['page']))
				$curPage = 1;
			else
				// we don't want to let them choose negative pages or even page 0. Max it to 1
				$curPage = max(1,intval($_GET['page']));
			
			// What is the start of all of our links. Just add a number to the end.
			$paginationLinks = 'index.php?act=search&amp;q='.urlencode($query).'&amp;page=';
				
			
			// a constant that is saved in the user table
			$topicsPerPage = $GLOBALS['super']->user->topics_per_page;
			//$horizonalDistance = 2; // How many to display touching each other. 1 or 2?
			
			
			
			// Lets call our class that will return an array of all of our links ready to be put in the template
			list($paginationArray, $startPost) = $GLOBALS['super']->functions->doPagination($totalPages, $curPage, $paginationLinks, $topicsPerPage);
			
			$matches = $GLOBALS['super']->db->query(getSearch($query, $startPost, $topicsPerPage));
				
			if ($GLOBALS['super']->db->getRowCount($matches) > 0)
			{
				while($match = $GLOBALS['super']->db->fetch_assoc($matches))
				{
					$results[] = $match;
				}
		
				
				$searchResults = new tpl(ROOT_PATH.'themes/Default/templates/searchresults.php');
				
				$searchResults->add("numResults",$numRows);
				
				$searchResults->add("QUERY", stripslashes($query));
				$searchResults->add("RESULTS", $results);
				// put our array that contains all the topic's information into our template
				$searchResults->add("PAGINATION", $paginationArray['PAGES']);
				$searchResults->add("curpage", $curPage);
				// Put the total number of pages into the template
				$searchResults->add("page_count", ceil($totalPages/$topicsPerPage));
				$searchResults->parse();
			}
			else
			{
				// we have no results, lets tell the user by using the error template
				$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
				$error->add("error_message", "No results found for your search keywords.<br /> Please broaden your search, or if your search is too broad, you may need to be more specific.");
				echo $error->parse();
			}
		}
			
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}

?>


	