<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
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
<head>
<title>Ashryel's Library - <?php echo htmlspecialchars($data['title']); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="sean.css" />
</head>

<body>
<div class="backdrop">
<div class="header">
	<h1>
	<img src="images/title.gif" width="400" height="39" alt="Ashryel's Library" />
	</h1>
<p class="top-title"><?php echo $data['title']; ?></p>
</div>

<div class="main">
<p><?php echo $this->formatBreakAndPara($data['content']); ?></p>

<div class="home"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Home</a></div>
</div>

</div>

</body>
</html>


