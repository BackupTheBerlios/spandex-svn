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
 
require_once 'DB.php';

//! Interface class for all modules
class module {
	var $config = array();
	
	var $db;

	var $module_name;

	var $breadcrumbs = array();
	
	//! Class constructor
	function module() {
		
	}
	
	//! Initialisation. Can do some output or header stuff
	function init($_config) {
		$this->config = $_config;
		
		// open a DB connection
		$this->db =& DB::connect($this->config['dns']);
		if (PEAR::isError($this->db)) {
			die($this->db->getMessage());
		}
	}
	
	//! Main execution
	function run() {
	}
	
	//! Deconstructor
	function finish() {
		$this->db->disconnect();
	}

	//! Generate breadcrumbs
	function addBreadcrumb($url, $text) {
		$this->breadcrumbs[$text] = $url;
	}

	//! Output breadcrumbs
	function displayBreadcrumbs() {
		if ($this->breadcrumbs) {
			include $this->config['templatepath'] . 'module_breadcrumbs.php';
		}
	}

	function displayTitle() {
		if ($this->breadcrumbs) {
			include $this->config['templatepath'] . 'module_pagetitle.php';
		} else {
			return 'CAPA Admin';
		}
	}

	function fetchDataWithQuery($sql) {
		$result =& $this->querySafe($sql);
		
		$data = array();
		
		while ($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
				$data[] = $row;
		}

		$result->free();
		return $data;
	}

	function fetchDataIntoAssociative($sql) {
		$result =& $this->querySafe($sql);
		
		$data = array();
		
		while ($result->fetchInto($row)) {
				$data[$row[0]] = $row[1];
		}
		$result->free();
		return $data;
	}

	//! Runs a query, Checks for errors, and dies printing an error message if any is found
	function querySafe($sql) {
		$result =& $this->db->query($sql);
		if (PEAR::isError($result)) {
			$this->finish();
			echo "<pre>\nSQL Error\n";
			echo $result->getMessage();
			echo "\n$sql\n";
			echo "</pre>";
			die;
		}
		return $result;
	}

	/* formatting utils */
	function formatMoney($cents) {
		setlocale(LC_MONETARY, 'en_US');
		$dollars = $cents / 100;
		return money_format('%.2n', $dollars);
	}
	
	function formatPhone($number) {
		$result = '';
		if (is_numeric($number)) {
			// Run through raw formatting process
			if ($number >= 100000000) {
				// 10 digits
				// Double check it's not a mobile
				if ($number >= 400000000 && $number < 500000000) {
					return $this->formatMobilePhone($number);
				}
				$prefix = intval($number/100000000);
				$number = $number - ($prefix * 100000000); 
				$result = sprintf("(%02d) ", $prefix);
			}
			if ($number < 1000000) {
				// Six digits
				$firstGroup = intval($number/1000);
				$secondGroup = $number - ($firstGroup * 1000);
				$result .= sprintf("%03d %03d", $firstGroup, $secondGroup);
			} else {
				// 8 digitis
				$firstGroup = intval($number/10000);
				$secondGroup = $number - ($firstGroup * 10000);
				$result .= sprintf("%04d %04d", $firstGroup, $secondGroup);
			}
		} else {
			if (preg_match('/(\d+)\+(\d+)/', $number, $matches)) {
				$number = $this->formatPhone($matches[1]);
				$number .= ' +' . $matches[2];
			}
			// Double check format is okay
			$result = htmlspecialchars($number);
		}
		return $result;
	}

	function formatMobilePhone($number) {
		$result = '';
		if (is_numeric($number)) {
			$prefix = intval($number/1000000);
			$number = $number - ($prefix * 1000000); 
			$firstGroup = intval($number/1000);
			$secondGroup = $number - ($firstGroup * 1000);
			$result = sprintf("%04d %03d %03d", $prefix, $firstGroup, $secondGroup);
		} else {
			// Double check format is okay
			$result = htmlspecialchars($number);
		}
		return $result;
	}
}

?>
