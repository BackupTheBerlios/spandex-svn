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
 
require_once('datalister.php');
require_once('HTML/QuickForm.php');

//! Edit table records
class dataeditor extends datalister {
	
	var $lister_template = 'dataeditor_main.php';
	
	var $actions_template = 'dataeditor_actions.php';

	var $form = false;

	var $_tableStructure = false;

	var $editing_content_msg = "Editing Content";
	var $edit_content_msg = "Edit Content";
	
	// Main execution
	function run() {
		if (isset($_REQUEST['edit']) && is_numeric($_REQUEST['edit'])) {
			// Edit the selected element
			$this->editRecord($_REQUEST['edit']);
		}

		parent::run();
	}
	
	function display_list() {
		if ($this->form == false) {
			parent::display_list();
		} else {
			include($this->config['templatepath'] . $this->lister_template);
		}
	}

	function editRecord($record_id) {
		$this->form =& new HTML_QuickForm("content_form",'POST');

		$defaults = array('m' => $this->module_name, 'edit' => $record_id);
		$res =& $this->querySafe('SELECT * FROM ' . $this->table . ' WHERE id='.$record_id);
		$res->fetchInto($row, DB_FETCHMODE_ASSOC);
		if (isset($row['pass'])) {
			// Clear any password
			$row['pass'] = $this->_passwordDisplay;
			$row['pass2'] = $this->_passwordDisplay;
		}
		
		$this->form->setDefaults(array_merge($defaults, $row));
		
		$this->form->addElement('header',null,$this->editing_content_msg);
		$this->form->addElement('hidden',"m");
		$this->form->addElement('hidden',"edit");
		
		$this->createForm($this->form);
		
		$this->form->addElement('submit',"submit",$this->edit_content_msg);
		
		if (isset($_POST['submit']))
		{
			if ($this->form->validate()) {
				if ($this->editRecordInDatabase($this->form)) {
					$this->form = false; // clear form
				}
			}
		}
	}
	
	function editRecordInDatabase(&$form) {
		$setValues = array();
		$keys = $form->exportValues();
		$id = $form->exportValue('edit');

		// Get the old value first!
		$data = $this->fetchDataWithQuery('SELECT * from '.$this->table.' WHERE id='.$id);
		$oldValues = $data[0];

		foreach (array_keys($this->_tableStructure) as $key) {
			if ($form->elementExists($key)) {
				$insertValues[$key] = $this->exportValue($form, $key);
				if ($insertValues[$key] === null) {
					unset($insertValues[$key]);
				}
			}
		}
		$insertValues['date_updated'] = time();

		$sql = 'UPDATE '.$this->table.' SET ';
		foreach (array_keys($insertValues) as $key) {
			$sql .= $key."='".$insertValues[$key]."', ";
		}
		$sql .= 'WHERE';
		$sql = str_replace(', WHERE', ' WHERE', $sql);
		$sql .= ' id='.$id;
		

		$this->querySafe($sql);

		// Store undo
		$this->writeUndo($this->table, $id, 'edit', $oldValues);

		return true;
	}
	
	function createForm(&$form) {
		$structure = $this->queryTableStructure($this->table);
		// Store the structure so we can use it later
		$this->_tableStructure = $structure;
		$columns = $this->fetchColumnNames(array_keys($structure));
		
		foreach ($columns as $col) {
			$this->createColumnForm($form, $col, $structure);
		}
	}

	function createColumnForm(&$form, $col, $structure)
	{
		$formattedCol = $this->formatColumn($col);

		$required = false;
		
		if (!(strstr($structure[$col], '*') === false)) {
			$structure[$col] = str_replace('*', '', $structure[$col]);
			$required = true;
		}
		
		if ($structure[$col] == 'int') {
			$this->addInt($form, $col, $formattedCol, $required);
		} elseif ($structure[$col] == 'date') {
			$this->addDate($form, $col, $formattedCol, $required);
		} elseif ($structure[$col] == 'text') {
			$this->addTextArea($form, $col, $formattedCol, $required);
		} elseif (strstr($col, 'pass')) {
			$this->addPassword($form, $col, $formattedCol, $structure[$col], $required);
			$this->addPassword($form, $col . '2', 'Repeat Password', $structure[$col], $required);
			$form->addRule(array($col, $col . '2'), 'The passwords for '.$formattedCol.' do not match', 'compare', null, 'client');
		} elseif (strstr($structure[$col],'enum')) {
			preg_match_all('/[a-zA-Z]+/', $structure[$col], $states);
			$states = $states[0];
			// pop off enum
			array_shift($states);
			$this->addEnum($form, $col, $formattedCol, $states, $required);
		} else {
			$this->addText($form, $col, $formattedCol, $structure[$col], $required);
		}
	}

	function addInt(&$form, $name, $label, $required=false)
	{
		$form->addElement('text',$name,$label . ':',
				array('size' => 53, 'maxlength' => 11));
		$form->applyFilter($name, 'trim');
		$form->addRule($name, $label.' must be a number' ,'numeric', null, 'client');
		if ($required) {
			$form->addRule($name, $label.' requires a value', 'required', null, 'client');
		}
	}
	
	function addDate(&$form, $name, $label, $required=false)
	{
		$form->addElement('date',$name,$label.':',
				array('format' => 'dMY', 'minYear' => 1990, 'addEmptyOption' => !$required, 'emptyOptionValue' => 0));
		if ($required) {
			$form->addRule($name, $label.' requires a value', 'required', null, 'client');
		}
	}
	
	function addTextArea(&$form, $name, $label, $required=false)
	{
		$form->addElement('textarea',$name,$label.':',
			array('rows' => 20, 'cols' => 60));
		$form->applyFilter($name, 'trim');
		if ($required) {
			$form->addRule($name, $label.' requires a value', 'required', null, 'client');
		}
	}

	function addPassword(&$form, $name, $label, $maxlength, $required=false)
	{
		$form->addElement('password',$name,$label.':',
				array('size' => 53, 'maxlength' => $maxlength));
		$form->applyFilter($name, 'trim');
		$form->applyFilter($name, 'md5');
		if ($required) {
			$form->addRule($name, $label.' requires a value', 'required', null, 'client');
		}
	}

	function addText(&$form, $name, $label, $maxlength, $required=false)
	{
		$form->addElement('text',$name,$label.':',
			array('size' => 53, 'maxlength' => $maxlength));
		$form->applyFilter($name, 'trim');
		if (strstr($name, 'email')) {
			$form->addRule($name, $label.' must be a valid email address' ,'email', null, 'client');
		}
		if ($required) {
			$form->addRule($name, $label.' requires a value', 'required', null, 'client');
		}
	}
	
	function addEnum(&$form, $name, $label, $states, $required=false)
	{
		// If there's only two states then handle it as radio buttons!
		if (count($states) < 3) {
			$element1 = $form->createElement('radio', null, null, $states[0], $states[0]);
			$element2 = $form->createElement('radio', null,null, $states[1], $states[1]);
			$form->addElement('group',$name,$label.':', array($element1, $element2));
		} else {
			// Build array of states as an associative array
			$elements = array();
			foreach ($states as $state) {
				$elements[$state] = $state;
			}
			$form->addElement('select',$name,$label.':', $elements);
		}
		if ($required) {
			$form->addRule($name, $label.' requires a value', 'required', null, 'client');
		}
	}
	
	// Query the table structure
	function queryTableStructure ($tableName) {	
		$result = array();
		$dbres =& $this->querySafe('SHOW COLUMNS FROM ' . $tableName);

		while ($dbres->fetchInto($row, DB_FETCHMODE_ASSOC)) {
			// Store some basic info on each field
			$name = $row['Field'];
			
			if (strstr($row['Type'], 'int')) {
				if (strstr($name, 'date')) {
					$result[$name] = 'date';	
				} else {
					$result[$name] = 'int';	
				}
			} else {
				if (strstr($row['Type'], 'text')) {
					$result[$name] = 'text';	
				} else if (strstr($row['Type'], 'enum')) {
					// Extract out states
					$states = preg_replace('/^enum\((.+)\)$/',"$1", $row['Type']);
					$result[$name] = 'enum' . $states;
				} else {
					// Must be varchar. Figure out how big
					preg_match('/\d+/', $row['Type'], $matches);
					$result[$name] = $matches[0];
				}
			}
			if ($row['Null'] != 'YES') {
				$result[$name] .= '*'; // Mark as required
			}
		}
		return $result;
	}

	function exportValue($form, $key) {
		$ret = addslashes($form->exportValue($key));

		if (strstr($key, 'pass') && $ret == md5($this->_passwordDisplay)) {
			return null; // password unchanged
		} elseif (strstr($key, 'datetime')) {
			// Build a timestamp from the form elements dMYHi
			if ($ret['Y'] == 0) {
				$ret = 'NULL';
			} else {
				$dateArray = $ret;
				$ret = mktime($dateArray['H'], $dateArray['i'], 0, $dateArray['M'], $dateArray['d'], $dateArray['Y']); 
			}
		} elseif (strstr($key, 'date')) {
			// Build a timestamp from the form elements dMYHi
			if ($ret['Y'] == 0) {
				$ret = 'NULL';
			} else {
				$dateArray = $ret;
				$ret = mktime(0, 0, 0, $dateArray['M'], $dateArray['d'], $dateArray['Y']); 
			}
		}
		return $ret;
	}
}

?>

