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

//! Allow undo of database operations
class undo extends securemodule {
	var $undo_template = 'undo_main.php';

	var $undoDetails = false;

	var $form = false;

	// Constructor
	function undo() {
		$this->addBreadcrumb($_SERVER['PHP_SELF'], 'Admin');
		$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=undo', 'Undo Buffer');
		if (isset($_GET['undo'])) {
			$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=undo&amp;undo=1', 'Undo Last Action');
		} elseif (isset($_GET['details'])) {
			$this->addBreadcrumb($_SERVER['PHP_SELF'] . '?m=undo&amp;details=' . $_GET['details'], 'Undo Details');
		}
	}
	
	function run() {
		parent::run();
		
		if (isset($_REQUEST['details']) && is_numeric($_REQUEST['details'])) {
			// Edit the selected element
			$this->generateDetails($_REQUEST['details']);
		} elseif (isset($_REQUEST['undo'])) {
			// Delete the selected element
			$this->performUndo();
		}

		$data = $this->fetchDataWithQuery('SELECT un.id, un.date_created, un.table_name, un.action, us.name  FROM undobuffer un, users us WHERE us.id = un.user ORDER BY un.date_created DESC, un.id DESC LIMIT 100');

		include($this->config['templatepath'] . $this->undo_template);
	}

	function generateDetails($undoRecord) {
		// Pull the details for the undo action

		$result =& $this->querySafe('SELECT * FROM undobuffer WHERE id='.$undoRecord);

		$result->fetchInto($row, DB_FETCHMODE_ASSOC);

		if ($row['data']) {
			$row['data'] = unserialize($row['data']);
		}

		$this->undoDetails = $row;
	}

	// Find the last undo record, perform an undo op, and delete the record
	function performUndo() {
		$this->form =& new HTML_QuickForm("undo_form",'POST');

		$this->form->setDefaults(array('m' => $this->module_name, 'undo' => 1));

		$this->form->addElement('header',null,"Undo Last Action");
		$this->form->addElement('hidden',"m");
		$this->form->addElement('hidden',"undo");
		
		$this->form->addElement('submit',"submit","Confirm Undo");

		if (isset($_POST['submit']))
		{
			if ($this->undoDatabaseAction($this->form)) {
				$this->form = false; // clear form
			}
		}
	}

	function undoDatabaseAction($form) {
		$result =& $this->querySafe('SELECT * FROM undobuffer ORDER BY date_created DESC, id DESC LIMIT 1');

		$result->fetchInto($row, DB_FETCHMODE_ASSOC);

		$id = $row['id'];

		if ($row['data']) {
			$row['data'] = unserialize($row['data']);
		}

		switch ($row['action']) {
		case 'new':
			// Delete the record
			$sql = 'DELETE FROM ' . $row['table_name'] . ' WHERE id='.$row['record'];
			break;
		case 'delete':
			// Re-create the record
			$sql = 'INSERT INTO ' . $row['table_name'] . '(';
			foreach (array_keys($row['data']) as $key) {
				$sql .= $key . ', ';
			}
			$sql .= ') VALUES (';
			foreach (array_keys($row['data']) as $key) {
				$sql .= "'" . $row['data'][$key] .  "', ";
			}
			$sql .= ')';
	
			$sql = str_replace(', )', ')', $sql);
			break;
		case 'edit':
			$sql = 'UPDATE ' . $row['table_name'] . ' SET ';
			foreach (array_keys($row['data']) as $key) {
				$sql .= $key."='".$row['data'][$key]."', ";
			}
			$sql .= 'WHERE';
			$sql = str_replace(', WHERE', ' WHERE', $sql);
			$sql .= ' id='.$row['record'];
			break;
		}
		
		$this->querySafe($sql);

		$this->querySafe('DELETE FROM undobuffer WHERE id='.$id);

		return true;
	}
}

?>