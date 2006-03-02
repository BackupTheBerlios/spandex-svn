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

//! Main admin page
class admin extends securemodule {
	// Constructor
	function admin() {
		$this->addBreadcrumb($_SERVER['PHP_SELF'], 'Admin');
	}

	// Main execution
	function run() {
		parent::run();
		include($this->config['templatepath'] . 'admin_main.php');
	}
}

?>
