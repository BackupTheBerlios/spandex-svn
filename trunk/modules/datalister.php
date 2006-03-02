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

//! Select a table from the database and build a list display
class datalister extends securemodule {
	
	var $table = '';
	
	var $lister_template = 'datalister_main.php';
	
	var $row_template = 'datalister_row.php';
	
	var $_passwordDisplay = '********';

	var $max_records_per_page = 100;
	
	// Main execution
	function run() {
		parent::run();
		
		$this->display_list();
	}
	
	function display_list() {
		$columns = array();
		
		$data = array();

		// First check if we've got too much data
		$count = $this->countRecords($this->table);
		$min = 0;
		$max = $this->max_records_per_page - 1;
		if ($count > $this->max_records_per_page) {
			if (isset($_GET['min']) && is_numeric($_GET['min']) && $_GET['min'] <= $count) {
				$min = $_GET['min'];
				$max = $_GET['min'] + $this->max_records_per_page - 1;
			}
		}

		$res = $this->selectTable($this->table, $min, $max);

		// Get each row
		while ($res->fetchInto($row, DB_FETCHMODE_ASSOC)) {
			if (!$columns) {
				$columns = $this->fetchColumnNames(array_keys($row));
			}
			
			$data[] = $row;
		}
		
		include($this->config['templatepath'] . $this->lister_template);
	}

	function formatColumn($column) {
		$column = str_replace('_', ' ', $column);
		return ucfirst($column);
	}

	function formatTableName() {
		$name = str_replace('_', ' ', $this->table);
		return ucfirst($name);
	}

	// Get a number of records in the table
	function countRecords($tableName) {
		$result =& $this->querySafe('SELECT COUNT(id) FROM ' . $tableName);

		$result->fetchInto($row);

		return $row[0];
	}
	
	// Select database table
	function selectTable ($tableName, $min, $max) {
		$result =& $this->querySafe("SELECT * FROM $tableName ORDER BY date_created DESC LIMIT $min,$max");

		return $result;
	}

	function fetchColumnNames ($tableRow) {
		// Strip out id, user, date updated, date created
		return array_diff($tableRow, array('id', 'user', 'date_created', 'date_updated'));
	}
}

?>

