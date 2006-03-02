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

error_reporting(E_ALL);

require_once('../etc/config.php');

global $config;
$config['defaultmodule'] = 'admin';

set_magic_quotes_runtime(0);

// Setup include paths
set_include_path(get_include_path() . PATH_SEPARATOR . $config['libpath']);

$module = $config['defaultmodule'];

if (isset($_REQUEST['m'])) {
  // Check if module is valid
  if (preg_match('/^[a-z0-9_]+$/', $_REQUEST['m']) && file_exists($config['modulepath'] . $_REQUEST['m'] . '.php')) {
    $module = $_REQUEST['m'];
  }
}

require_once($config['modulepath'] . $module . '.php');

$moduleObject = new $module;

$moduleObject->module_name = $module;

$moduleObject->init($config);

$moduleObject->run();

$moduleObject->finish();

?>