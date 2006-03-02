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
 
require_once('module.php');

require_once 'Auth/Auth.php';
require_once ("HTML/QuickForm.php");

// global template pointer
static $_mytemplate = '';

function secure_loginpage() {
	global $_mytemplate;
	securemodule::loginpage($_mytemplate);
}

//! Will run a session and use PEAR::Auth to check the user
class securemodule extends module {
	var $authorisation;
	
	// Initialisation. Can do some output or header stuff
	function init($_config) {
		global $_mytemplate;
		
		parent::init($_config);
		
		$params = array(
			'dsn' => $this->config['dns'],
            		'table' => 'users',
            		'usernamecol' => 'name',
            		'passwordcol' => 'pass'
            	);
		
		$_mytemplate = $this->config['templatepath'] . 'securemodule_login.php';
	
		$this->authorisation = new Auth("DB", $params, "secure_loginpage");
		$this->authorisation->setSessionname('CAPAADMIN');
		$this->authorisation->start();
		
		// Do authorisation BEFORE run
		if (!$this->authorisation->getAuth()) {
			// Display login form
			$this->finish();
			die; // abort rest of execution
		} elseif (isset($_POST['username']) && isset($_POST['password'])) {
			// Update the 'last login' field 
			$this->querySafe("UPDATE users SET last_login_datetime=" . time() . " WHERE name='" . $this->authorisation->getUsername() . "'");
		}
	}

	// Static function
	function loginpage($template) {
		$message = '';
		
		$form =& new HTML_QuickForm("login_form",'POST');
		$form->addElement('header',null,"Please Login");
		$form->addElement('text',"username","Name:",array('size' => 53, 'maxlength' => 128));
		$form->addElement('password',"password","Password:",array('size' => 53, 'maxlength' => 128));
		$form->addElement('submit',"submit","Login");
		$form->applyFilter('username',"trim");
		$form->applyFilter('password',"trim");
		$form->addRule('username',"Name required","required",null,"client");
		$form->addRule('password',"A password is required","required",null,"client");
	
		if (isset($_POST['submit']))
		{
			$form->validate(); // Just so we get errors
			$message = 'Incorrect login';
		}
	
		include($template);
	}
	
	function finish() {
		// Perform cleanup of undo buffer
		if ((time() % 60) == 0) {
			// Delete records older than 7 days
			$this->querySafe('DELETE FROM undobuffer WHERE date_created < '.(time()-604800));
		}

		parent::finish();

	}

	// Find the id for our current user
	function getUserId() {
		$res =& $this->querySafe("SELECT id FROM users WHERE name='" . $this->authorisation->getUsername() . "'");

		$res->fetchInto($user, DB_FETCHMODE_ASSOC);
		return $user['id'];
	}

	// Write to our Undo log
	function writeUndo($table, $id, $action, $data=null) {
		if ($action != 'new' && $action != 'edit' && $action != 'delete') {
			$this->finish();
			die("$action is not a valid Undo action! Operation successful but cannot be undone!");
		}		
		$sql = "INSERT INTO undobuffer (date_created, user, table_name, action, record, data) VALUES (";
		$sql .= time() . ', '. $this->getUserId(). ", '";
		$sql .= $table . "', '" . $action . "', " . $id. ",";
		if ($data === null) {
			$sql .= " ''";
		} else {
			$sql .= " '" . serialize($data) . "'";
		}
		$sql .= ')';
		$this->querySafe($sql);
	}

}

?>
