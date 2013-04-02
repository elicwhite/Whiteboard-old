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
 File: /pages/calendar.php
 Author: SaroSoftware
 Date Created: 3/9/09
 Last Edited By: Eli White
 Last Edited Date: 4/24/2009
 
 Comments:
	Main calendar page

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
class Calendar extends forumPage
{

	public function __construct()
	{
		parent::__construct();
		$this->setName("Calendar");
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
		
		// Get the current information
		$actualMonth = date('m');
		$actualYear = date('Y');
		$actualDay = date('d'); // 09
		
		// Current day month and year we are viewing
		if (isset($_GET['m']))
			$curMonth = intval($_GET['m']);
		else
			$curMonth = $actualMonth; // 03 
		
		if (isset($_GET['y']))
			$curYear = intval($_GET['y']);
		else
			$curYear = $actualYear; // 2009
			
		
		// We need to get the info from the first day of the month by using maketime with the current info
		//mktime(hour, minute, second, monnth, day, year);
		$firstDayOfMonth = mktime(0,0,0,$curMonth, 1, $curYear); 
		
		
		// Get the full month name of the current month (ex: March)
		$monthName = date('F', $firstDayOfMonth); 
		
		// We need to know what day the first day is so that we know how many empty spots we need and when we wil
		// start the calendar
		$firstDayName = date('D', $firstDayOfMonth);
		
		// We need to increment a day counter so we can use it in the mysql search
		$curDay = mktime(0,0,0,$curMonth, 1, $curYear); 
		
		// We need to get a count of how many blank days we have. Assuming the calendar starts on sunday.
		switch($firstDayName)
		{
			case "Sun": $blank = 0; break;
			case "Mon": $blank = 1; break;
			case "Tue": $blank = 2; break;
			case "Wed": $blank = 3; break;
			case "Thu": $blank = 4; break;
			case "Fri": $blank = 5; break;
			case "Sat": $blank = 6; break;
		}
		
		// how many days are in the current month?
		$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $curMonth, $curYear);
		
		$lastDayOfMonth = mktime(0,0,0,$curMonth, $daysInMonth, $curYear); 
		
		// We need to get the information about the previous month (to display the days at the end of the last month)
		// make sure it doesn't roll over the year.
		if ($curMonth == 1)
		{
			$prevMonth = 12;
			$prevYear = $curYear - 1;
		}
		else
		{
			$prevMonth = $curMonth-1;
			$prevYear = $curYear;
		}
			
		
		$daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
		
		// This is our counter for how many days we are at so far. When we hit 7, we need to make a new row and start over
		//		It keeps our table square, instead of all in one row.
		$dayCounter = 1;
		
		// Counter we will increment through the days while we make the calendar
		$day_num = 1;

		// We need to generate the urls for the prev and next links
		
		if ($curMonth == 1)
		{
			$linkPrevM = 12;
			$linkPrevY = $curYear - 1;
		}
		else
		{
			$linkPrevM = $curMonth - 1;
			$linkPrevY = $curYear;
		}
		
		if ($curMonth == 12)
		{
			$linkNextM = 1;
			$linkNextY = $curYear + 1;
		}
		else
		{
			$linkNextM = $curMonth + 1;
			$linkNextY = $curYear;
		}
		?>
		<table class="calendar" border="0">
			<caption class="catrow"><a href="index.php?act=calendar&amp;m=<?php echo $linkPrevM?>&amp;y=<?php echo $linkPrevY?>">&laquo;</a> <?php echo $monthName?> <a href="index.php?act=calendar&amp;m=<?php echo $linkNextM?>&amp;y=<?php echo $linkNextY?>">&raquo;</a></caption>
			<tr>
				<th>Sun</th>
				<th>Mon</th>
				<th>Tue</th>
				<th>Wed</th>
				<th>Thu</th>
				<th>Fri</th>
				<th>Sat</th>
			</tr>
		<?php
		echo "<tr>";
		//first we take care of those blank days
		for ($i = $blank-1; $i >= 0; $i--)
		{
			echo '<td class="inactive">
			'.($daysInPrevMonth-$i).'
			</td>';
			$dayCounter++;
		} 
		
		// Set the timezone to match php (This might not work on all installations.
		$GLOBALS['super']->db->query("SET time_zone = '".date_default_timezone_get()."'");
		
		$topicsQuery = "
		SELECT DATE(FROM_UNIXTIME(time_added)) AS date , count(*) AS topicCount
		FROM ".TBL_PREFIX."topics
		WHERE DATE(FROM_UNIXTIME(time_added)) BETWEEN '".$curYear."-".$curMonth."-01' AND '".$curYear."-".$curMonth."-".$daysInPrevMonth."'
		GROUP BY DATE(FROM_UNIXTIME(time_added))
		ORDER BY DATE(FROM_UNIXTIME(time_added))";
		$topicsQuery = $GLOBALS['super']->db->query($topicsQuery);

		$postsQuery = "
		SELECT DATE(FROM_UNIXTIME(time_added)) AS date , count(*) AS postCount
		FROM ".TBL_PREFIX."posts
		WHERE DATE(FROM_UNIXTIME(time_added)) BETWEEN '".$curYear."-".$curMonth."-01' AND '".$curYear."-".$curMonth."-".$daysInPrevMonth."'
		GROUP BY DATE(FROM_UNIXTIME(time_added))
		ORDER BY DATE(FROM_UNIXTIME(time_added))";
		$postsQuery = $GLOBALS['super']->db->query($postsQuery);
		
		$usersQuery = "
		SELECT DATE(FROM_UNIXTIME(time_added)) AS date , count(*) AS userCount
		FROM ".TBL_PREFIX."users
		WHERE DATE(FROM_UNIXTIME(time_added)) BETWEEN '".$curYear."-".$curMonth."-01' AND '".$curYear."-".$curMonth."-".$daysInPrevMonth."'
		GROUP BY DATE(FROM_UNIXTIME(time_added))
		ORDER BY DATE(FROM_UNIXTIME(time_added))";
		$usersQuery = $GLOBALS['super']->db->query($usersQuery);
		
		//echo $GLOBALS['super']->db->fetch_result($GLOBALS['super']->db->query("SELECT NOW()"));
		$perDayArray = array();
		
		while($day = $GLOBALS['super']->db->fetch_assoc($topicsQuery))
		{
			//$formatteddate = date("Y-m-d",$day['date']);
			$perDayArray[$day['date']]["Topics"] = $day['topicCount'];
		}
		while($day = $GLOBALS['super']->db->fetch_assoc($postsQuery))
		{
			//$formatteddate = date("Y-m-d",$day['date']);
			// have to subtract the number of topics today from number of posts today
			if (isset($perDayArray[$day['date']]["Topics"]))
				$daysTopics = $perDayArray[$day['date']]["Topics"];
			else 
				$daysTopics = 0;
				
			$perDayArray[$day['date']]["Reply"] = $day['postCount']-$daysTopics;
		}
		while($day = $GLOBALS['super']->db->fetch_assoc($usersQuery))
		{
			$perDayArray[$day['date']]["Users"] = $day['userCount'];
		}
		
		//$GLOBALS['super']->functions->print_array($perDayArray);
		
		// go through all the days in the month and print them in a <td>
		// when we go through 7, lets reset the counter
		while ($day_num <= $daysInMonth)
		{
			
			$mysqlFormat = date('Y-m-d', $curDay);
			//echo "cur day:".$mysqlFormat."<br />";
			if (key_exists($mysqlFormat, $perDayArray))
			{
				if(isset($perDayArray[$mysqlFormat]["Topics"]) && $perDayArray[$mysqlFormat]["Topics"] > 0)
				{
					$plural = plural($perDayArray[$mysqlFormat]["Topics"]);
					$topicsToday = "<li>".$perDayArray[$mysqlFormat]["Topics"]." Topic".$plural."</li>";
				}
				else
					$topicsToday = "";
					
				if($perDayArray[$mysqlFormat]["Reply"] > 0)
				{
					$plural = plural($perDayArray[$mysqlFormat]["Reply"],true);
					$postsToday = "<li>".$perDayArray[$mysqlFormat]["Reply"]." Repl".$plural."</li>";
				}
				else
					$postsToday = "";
					
				if(key_exists("Users",$perDayArray[$mysqlFormat]) &&  $perDayArray[$mysqlFormat]["Users"] > 0)
				{
					$plural = plural($perDayArray[$mysqlFormat]["Users"]);
					$usersToday = "<li>".$perDayArray[$mysqlFormat]["Users"]." User".$plural."</li>";
				}
				else
					$usersToday = "";
				
			}
			else
			{
					$topicsToday = "";
					$postsToday = "";
					$usersToday = "";
			}
			
			// increment our time value by one day. We need this to use in our mysql statement.
			$curDay = mktime(null, null, null, $curMonth, $day_num+1, $curYear);
			
			//echo $curDay."<br />";
			// We want to make the class be current on the cell that is the current date.
			$class = "";
			if ($actualMonth == $curMonth && $actualYear == $curYear && $day_num == $actualDay)
			{
				$class = ' class="current"';
			}
			
			// we need to format $curDay to match mysql's date format:
			
			// 2007-12-31
			
			/*
			<span>4 Posts</span>
					<span>4 Topics</span>
					<span>1 New Member</span>
			*/
			echo '<td'.$class.'>
				<ul>
				<li class="daynum">'.$day_num.'</li>
				'.$topicsToday.
				$postsToday.
				$usersToday.'
				</ul>
				</td>';
			$day_num++;
			$dayCounter++;
			
			//Make sure we start a new row every week
			if ($dayCounter > 7)
			{
				echo "</tr><tr>";
				$dayCounter = 1;
			}

			
			
		}
		// if we don't have an even square, lets finish out this week
		// We are reseting the counter to make it count for the next month
		$day_num = 1;
		while($dayCounter >1 && $dayCounter <= 7)
		{
			echo '<td class="inactive">'. $day_num.'</td>';
			$day_num++;
			$dayCounter++;
		}
		echo "</tr>";
		echo "</table>";

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}

?>


	