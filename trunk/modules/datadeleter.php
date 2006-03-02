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

//! Delete table records
class datadeleter extends datacreator {
	var $actions_template = 'datadeleter_actions.php';

	var $deleting_content_msg = "Deleting Content";
	var $delete_content_msg = "Confirm Delet";

	// Main execution
	function run() {
		if (isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])) {
			// Delete the selected element
			$this->deleteRecord($_REQUEST['delete']);
		}

		parent::run();
	}
	
	function deleteRecord($record_id) {
		$this->form =& new HTML_QuickForm("delete_form",'POST');

		$this->form->setDefaults(array('m' => $this->module_name, 'delete' => $record_id));

		$this->form->addElement('header',null,$this->deleting_content_msg);
		$this->form->addElement('hidden',"m");
		$this->form->addElement('hidden',"delete");
		
		$this->form->addElement('submit',"submit",$this->delete_content_msg);

		if (isset($_POST['submit']))
		{
			if ($this->deleteRecordFromDatabase($this->form)) {
				$this->form = false; // clear form
			}
		}
	}

	function deleteRecordFromDatabase(&$form) {
		$id = $form->exportValue('delete');

		// Get the old value first!
		$res =& $this->querySafe('SELECT * FROM '.$this->table.' WHERE id='.$id);
		$res->fetchInto($oldValues, DB_FETCHMODE_ASSOC);

		$this->querySafe('DELETE FROM '.$this->table.' WHERE id='.$id);

		// Store undo
		$this->writeUndo($this->table, $id, 'delete', $oldValues);

		return true;
	}
}

?>

