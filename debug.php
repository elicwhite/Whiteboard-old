<?php
if (DEBUG == 'on')
{
	echo "<div>";
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	echo '<div align="center"><span class="smallText">Current Parse Time: <b>' . $parse_time . ' s</b> with <b>' . sizeof($debug['QUERIES']) . ' queries</b></span></div>';
	echo '<b>QUERY DEBUG:</b> ';
	$super->functions->print_array($debug['QUERIES']);
	$super->functions->print_array($debug['TIME']);
	echo '<hr>';
	echo '<b>SESSION:</b> ';
	$super->functions->print_array($_SESSION);
	echo '<hr>';
	echo '<b>COOKIE:</b> ';
	$super->functions->print_array($_COOKIE);
	echo '<b>POST:</b> ';
	$super->functions->print_array($_POST);
	echo '<hr>';
	echo '<b>GET:</b> ';
	$super->functions->print_array($_GET);
	echo '<hr>';
	echo '<b>MEMORY:</b> ';
	$super->functions->print_array($debug['memory']);
	echo "</div>";
}
unset($debug);
?>
