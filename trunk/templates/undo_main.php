<?php echo '<?xml version = "1.0" encoding = "UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<!--
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
 -->
<html>
<head><title><?php $this->displayTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="adminstyle.css" />
</head>
<body>

<div class="userlinks"><a target="help" href="<?php echo $_SERVER['PHP_SELF']; ?>?m=help">help</a> - <a href="<?php echo $_SERVER['PHP_SELF']; ?>?m=logout">logout</a></div>

<?php $this->displayBreadcrumbs(); ?>

<h1>Undo Buffer</h1>

<?php if ($this->undoDetails) { ?>
	<h3>Details</h3>
	<table>
	<tr>
		<td>Action to Undo:</td>
		<td><strong><?php echo $this->undoDetails['action']; ?></strong> on table
		<strong><?php echo $this->undoDetails['table_name']; ?></strong></td>
	</tr><tr>
		<td>Date:</td>
		<td><?php echo date("d-m-y g:i a",$this->undoDetails['date_created']); ?></td>
	</tr>
	<?php if ($this->undoDetails['data']) { ?>
		<tr><th colspan="2">Data:</th></tr>
		<?php foreach (array_keys($this->undoDetails['data']) as $key) { ?>
		<tr>
			<th><?php echo $key; ?></th>
			<td><?php echo $this->undoDetails['data'][$key]; ?></td>
		</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="2">No Relevant Data</td></tr>
	<?php } ?>
	</table>
<?php 
} elseif ($this->form) { 
	$this->form->display();
} else {
?>

	<p><a href="<?php echo $_SERVER['PHP_SELF'] . '?m=' . $this->module_name 	?>&amp;undo=1">Undo Last Action</a></p>
<?php } ?>

<table class="data">
<tr>
	<th>Action</th>
	<th>Date</th>
	<th>User</th>
	<th>Table</th>
	<th>Details</th>
</tr>
<?php foreach ($data as $row) { ?>
<tr>
	<td><?php echo $row['action']; ?></td>
	<td><?php echo date("d-m-y g:i a",$row['date_created']); ?></td>
	<td><?php echo $row['name']; ?></td>
	<td><?php echo $row['table_name']; ?></td>
	<td><a href="<?php echo $_SERVER['PHP_SELF'] . '?m=' . $this->module_name . '&amp;details=' . $row['id']?>">View Details</a></td>
</tr>
<?php } ?>
</table>


</body>
</html>

