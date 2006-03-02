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

<h1>Admin</h1>
<ul class="admin-nav">
<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?m=users">Add/Edit Users</a></li>
<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?m=photos">Photo Gallery</a></li>
<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?m=undo">Undo Changes</a></li>
</ul>

</body>
</html>

