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
 
require_once('dataeditor.php');

//! Edit table records
class datacreator extends dataeditor {
	var $lister_template = 'datacreator_main.php';
	
	var $adding_content_msg = "Adding Content";
	var $add_content_msg = "Add Content";

	// Main execution
	function run() {
		if (isset($_REQUEST['new'])) {
			// Create a new element
			$this->newRecord();
		}

		parent::run();
	}
	
	function newRecord() {
		$this->form =& new HTML_QuickForm("content_form",'POST');

		$this->form->setDefaults($this->getNewDefaults());
		
		$this->form->addElement('header',null,$this->adding_content_msg);
		$this->form->addElement('hidden','m');
		$this->form->addElement('hidden','new');
		
		$this->createForm($this->form);

		$this->form->addElement('submit',"submit",$this->add_content_msg);
		
		if (isset($_POST['submit']))
		{
			if ($this->form->validate()) {
				if ($this->addRecordToDatabase($this->form)) {
					$this->form = false; // clear form
				}
			}
		}
	}

	function getNewDefaults() {
		return array('m' => $this->module_name, 'new' => "2");
	}

	function addRecordToDatabase(&$form) {
		$insertValues = array();
		$keys = $form->exportValues();
		foreach (array_keys($this->_tableStructure) as $key) {
			if ($form->elementExists($key)) {
				$insertValues[$key] = $this->exportValue($form, $key);
				if ($insertValues[$key] === null) {
					unset($insertValues[$key]);
				}
			}
		}
		$insertValues['date_created'] = time();
		$insertValues['date_updated'] = time();
		$insertValues['user'] = $this->getUserId();

		if ($this->doInsert($insertValues, $this->table) === false) {
			return false;
		}
		return true;
	}
	
	function doInsert($insertValues, $tableName) {
		$sql = 'INSERT INTO '.$tableName.' (';
		foreach (array_keys($insertValues) as $key) {
			$sql .= $key . ', ';
		}
		$sql .= ') VALUES (';
		foreach (array_keys($insertValues) as $key) {
			$sql .= "'" . $insertValues[$key] .  "', ";
		}
		$sql .= ')';

		$sql = str_replace(', )', ')', $sql);

		$this->querySafe($sql);

		// Get our insert id
		$id = mysql_insert_id();
		
		$this->writeUndo($tableName, $id, 'new');
		
		return $id;
	}
}

?>

