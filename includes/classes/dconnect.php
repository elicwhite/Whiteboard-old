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
 File: /includes/classes/dconnect.php
 Author: SaroSoftware
 Date Created: 07/05/2007
 Last Edited By: Eli White
 Last Edited Date: 3/30/2009
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

class dconnect{
	private $database = null;
	private  $dieOnError = false;
	private $dbHostName = null;
	private $dbName = null;
	private $dbOptions = null;
	private $userName=null;
	private $userPassword=null;


	
	static function &getInstance()
	{
			static $dbm;
			static $count;
			static $old_count;
			if(!isset($dbm))
			{
				$count++;
				$dbm = new dconnect();
				$dbm->resetSettings();
				$dbm->count_id = $count;
				$dbm->references = 0;
				
			}
			else
			{
				$old_count++;
				$dbm->references = $old_count;
			}
			
			return $dbm;
	}
	
	function setDieOnError($value)
	{
		$this->dieOnError = $value;
	}

	function setUserName($name)
	{
		$this->userName = $name;
	}

	function setOption($name, $value)
	{
		if(isset($this->dbOptions))
		{
			$this->dbOptions[$name] = $value;
		}
		if(isset($this->database))
		{
			$this->database->setOption($name, $value);
		}
	}

	function setUserPassword($pass)
	{
		$this->userPassword = $pass;
	}

	function setDatabaseName($db)
	{
		$this->dbName = $db;
	}

	function setDatabaseHost($host)
	{
		$this->dbHostName = $host;
	}

	//checks error and outputs as needed
	function checkError($msg='', $dieOnError=false)
	{
		if (mysql_errno())
		{
			$error = $msg."MySQL error ".mysql_errno().": ".mysql_error();
    		if($this->dieOnError || $dieOnError)
    		{	
    			die ($error);		
    			//die("An SQL error has occured.");
       		}
    		else
    		{
    			$this->last_error = $error;
    		}
        }
        return false;
	}

	//checks current connection
	function checkConnection()
	{
			$this->last_error = '';
			if(!isset($this->database)) $this->connect();
	}

	//added support for debug information.  Gets current microtime, runs the query and then gets end micro time to 
	//establish a total query execution time and saved into the global array $debug.
	function query($sql, $dieOnError=true, $msg='Error: ')
	{
		global $debug;
		$query_start = microtime();

		$this->checkConnection();
		$result = mysql_query($sql);

		$this->lastmysqlrow = -1; 
		$this->checkError($msg.' Query Failed:' . $sql . ' :: ', $dieOnError);

		$_start = explode(' ', $query_start);
		$_end = explode(' ', microtime());
		$_time = number_format(($_end[1] + $_end[0] - ($_start[1] + $_start[0])), 6);
		
		$debug['QUERIES'][] = trim($sql);
		$debug['TIME'][] = $_time;

		return $result;
	}

	//gets the row count of the query executed
	function getRowCount(&$result){
		if(isset($result) && !empty($result))
				return mysql_num_rows($result);

		return 0;

	}

	// gets the id of the last row added
	function fetch_lastid()
	{
		return mysql_insert_id();
	}
	
	//returns result set into an array for output, you will loop this one with while.  Or return to an array
	//$row[] = $this->fetchByAssoc...
    function fetch_assoc(&$result){
		$row = mysql_fetch_assoc($result);
		return $row;
	}
	function fetch_result($result, $row=0)
	{
		$result = mysql_result($result, $row);
		return $result;
	}
	
	//gets last insert id
	function getLastId() {
		return mysql_insert_id();
	}

	//connects to the database
	function connect()
	{
		//echo "Host: ".$this->dbHostName.", User: ".$this->userName.", Pass: ".$this->userPassword.", DB: ".$this->dbName."<br />";
			$this->database = @mysql_connect($this->dbHostName,$this->userName,$this->userPassword) or die("Can't connect: ".mysql_error());
			@mysql_select_db($this->dbName) or die( "Unable to select database: " . mysql_error());
	}
	function resetSettings(){
		global $INFO;

		
		$this->disconnect();
		$this->setUserName($INFO['username']);
		$this->setUserPassword($INFO['password']);
		$this->setDatabaseHost($INFO['host']);
		$this->setDatabaseName($INFO['database']);
	}

	//quotes the text string before inserting into the database.  USE IT!
	/**
	 * Escapes a string to be inserted into the database
	 *
	 * @param string $string		The string to make safe
	 * @param boolean $encapsulate	true if you want it to return the string with ' ' false to return the plain string
	 * @return string				the database safe string
	 */
    function escape($string, $encapsulate=false)
	{
		
		$this->checkConnection();
		if(get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}     
		if(function_exists('mysql_real_escape_string'))
		{
        	$string = mysql_real_escape_string($string);
		}
		else
		{
			$string = mysql_escape_string($string);
		}
        //return "'".$output."'";
        if ($encapsulate)
        {
        	$string = "'".$string."'";
        }
        return $string;
        
    }

	//disconnects the current database connection
    function disconnect() {    
		if(isset($this->database)){
			mysql_close($this->database);
			unset($this->database);
        }
    }   
}
?>