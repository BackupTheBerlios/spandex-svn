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
 
require_once('securemodule.php');

class datafeed extends securemodule {
	
	var $feedType = null;
	
	function setData($feedType)
	{
		$this->feedType = $feedType;
	}

	// Main execution
	function run() {
		parent::run();
		$data = array();
		// TODO
		// Build an array to feed back to the AJAX component
		echo "new Array(";
		foreach ($data as $row) {
			echo '"' . $row[$this->feedType] . '"';
			if ($row != end($data)) {
				echo ", ";
			}
		}
		echo ")\n";
	}
}

?>
