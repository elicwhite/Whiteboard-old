<?php

/*-----
 File: /administration/pages/forums.php
 Author: SaroSoftware
 Date Created: 07/05/2007
 Last Edited By: Eli White
 Last Edited Date: 5/6/09
 Latest Changes:
 	- Converted to class format
 
 Comments:
	Main page for the Forums category in the admin panel

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
class Forums extends adminPage
{
	
	public function __construct()
	{
		parent::__construct();
		$this->setName("Forums");
		
	}
	
	
	public function children()
	{
		$children[] = array('text' => 'Info', 'act' => 'info', 'redirect' => 'No');
		$children[] = array('text' => 'Structure', 'act' => 'structure','redirect' => 'No');
		$children[] = array('text' => 'Add', 'act' => 'add', 'redirect' => 'No');
		$children[] = array('text' => 'Edit', 'act' => 'edit', 'redirect' => 'No');
		//$children[] = array('text' => 'Delete', 'act' => 'delete', 'redirect' => 'No');

		return $children;
	}
	
	public function setSub($sub)
	{
		switch ($sub)
		{
			case "Structure":
				require_once(ADMIN_PATH.'pages/forums_structure.php');
	    		$this->sub = new ForumsStruct();
				break;
			case "Add":
				require_once(ADMIN_PATH.'pages/forums_add.php');
	    		$this->sub = new ForumsAdd();
				break;
			case "Edit":
				require_once(ADMIN_PATH.'pages/forums_edit.php');
	    		$this->sub = new ForumsEdit();
				break;
			//case "Delete":
				//break;
			// info and everything else
			default:
				require_once(ADMIN_PATH.'pages/forums_info.php');
	    		$this->sub = new ForumsInfo();
				break;
		}
		return $this->sub->getName();
	}
	
	/**
	 * Gets the content/main bulk of the page
	 *
	 * @return string The content
	 */
	public function display()
	{
		ob_start();
		
		echo $this->sub->display();
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	public function getJS()
	{
		$js = parent::getJS();
		return array_merge($js, $this->sub->getJS());
	}
	
}

?>