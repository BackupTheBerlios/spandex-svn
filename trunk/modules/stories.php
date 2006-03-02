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
 
require_once('datadeleter.php');

//! Select a table from the database and build a list display
class stories extends datadeleter {
	var $table = 'stories';

	// Constructor
	function stories() {
		$this->addBreadcrumb($_SERVER['PHP_SELF'], 'Admin');
		$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=stories', 'Stories');
		if (isset($_GET['edit'])) {
			$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=stories&amp;edit=' . $_GET['edit'], 'Edit Story');
		} elseif (isset($_GET['delete'])) {
			$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=stories&amp;delete=' . $_GET['delete'], 'Delete Story');
		} elseif (isset($_GET['new'])) {
			$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=stories&amp;new=1', 'New Story');
		}
	}
}

?>
