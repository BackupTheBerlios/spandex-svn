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
 
require_once('datacreator.php');

//! Select a table from the database and build a list display
class users extends datacreator {
	var $table = 'users';

	var $adding_content_msg = "Adding New User";
	var $add_content_msg = "Add User";
	var $editing_content_msg = "Editing User Information";
	var $edit_content_msg = "Edit User";
	

	function users() {
		$this->addBreadcrumb($_SERVER['PHP_SELF'], 'Admin');
		$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=users', 'Users');
		if (isset($_GET['edit'])) {
			$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=users&amp;edit=' . $_GET['edit'], 'Edit User');
		} elseif (isset($_GET['new'])) {
			$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=users&amp;new=1', 'New User');
		}
	}

	function createForm(&$form) {
		parent::createForm($form);

		$form->removeElement('last_login_datetime');
	}

	function formatColumn($column) {
		if ($column == 'pass') {
			return 'Password';
		} elseif ($column == 'name') {
			return 'User Name';
		} elseif ($column == 'last_login_datetime') {
			return 'Last Login Date+Time';
		} else {
			return parent::formatColumn($column);
		}
	}
}

?>
