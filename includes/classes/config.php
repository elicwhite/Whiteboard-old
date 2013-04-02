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
 File: /includes/classes/config.php
 Author: SaroSoftware
 Date Created: 4/01/2009
 Last Edited By: Eli White
 Last Edited Date: 4/01/2009
 Latest Changes:

TODO:
	Configuration Items (Read (array/object), set, add?)
 		$super->config->val['siteName'];
 		$super->config->set("siteName", "My Site");
 		
 		$super->config->siteName;
 		$super->config->siteName = "My Site";
 		
 		$super->config->add("newKey", "New Value");
 		exists?
 
 Comments:
	Configuration class to read, set, and add configuration values to the database.

 -----*/

//No direct access
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}

class config 
{
	
	private $configarray = array();
	private $super;
	
	function __construct($super)
	{
		$this->super = $super;
		
		//print_r($GLOBALS);
		// We need to get all the config rows
		$configresult = $this->super->db->query("SELECT * FROM ".TBL_PREFIX."config");
		// and loop through it
		while($configvalue = $this->super->db->fetch_assoc($configresult))
		{
			// and add it to our configuration array
			// accessed by $super->config['siteName']
			$this->configarray[$configvalue['name']] = array(
				'id' => $configvalue['id'],
				'value' => $configvalue['value'],
				'description' => $configvalue['time_modified'],
				'readonly' => $configvalue['readonly']
				);
		}
		//$GLOBALS['super']->functions->print_array($this->configarray);
	}
	public function __set($key, $val)
	{
		if (key_exists($key, $this->configarray))
		{
			// We have to check if the key is readonly, some things we don't want to change.
			if ($this->configarray[$key]['readonly'] == 0)
			{
				$this->configarray[$key]['value'] = $val;
				// update the database.
			}
		}
		else
		{
			$this->super->err->throwError(7);
		}
	}
	
	public function __get($key)
	{
		if (key_exists($key, $this->configarray))
		{
			return $this->configarray[$key]['value'];
		}
		else
		{
			$this->super->err->throwError(7);
		}
	}
}

?>