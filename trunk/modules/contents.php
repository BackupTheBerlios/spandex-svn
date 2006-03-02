<?php
/**
 * Project Spandex
 * Copyright (c) 2006 Sydney PHP User Group
 *  http://www.sydphp.org/
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation; either version 2.1
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * See licence.txt for more details
 **/
 
require_once('module.php');

//! Display stories list
class contents extends module {
	var $contents_template = 'contents_main.php';
	var $story_template = 'contents_story.php';

	var $table = 'stories';
	
	// Main execution
	function run() {
		parent::run();

		if (isset($_GET['story']) && is_numeric($_GET['story'])) {
			$this->displayStory($_GET['story']);
		} else {
			$this->displayContents();
		}
	}

	function displayContents()
	{
		$data = array();

		$res = $this->db->query('SELECT id,title from '.$this->table.' ORDER BY title');
		// check that result is not an error
		if (PEAR::isError($res)) {
			$this->finish();
			die("Could not query table {$this->table}");
		}
		while ($res->fetchInto($row, DB_FETCHMODE_ASSOC))
		{
			$data[] = $row;
		}

		include($this->config['templatepath'] . $this->contents_template);
	}
	
	function displayStory($id)
	{
		$res =& $this->db->query('SELECT * from '.$this->table.' WHERE id='.$id);
		// check that result is not an error
		if (PEAR::isError($res)) {
			$this->finish();
			die("Error getting story from database");
		}
		$res->fetchInto($data, DB_FETCHMODE_ASSOC);

		include($this->config['templatepath'] . $this->story_template);
	}

	function formatBreakAndPara($text)
	{
		$text = htmlspecialchars($text);
		$text = preg_replace("/\n\n+/", '</p><p>', $text);
		return nl2br($text);
	}
}

?>










