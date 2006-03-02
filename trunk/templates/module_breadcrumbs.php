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
<ul class="breadcrumbs">
	<?php foreach (array_keys($this->breadcrumbs) as $name) { ?>
		<?php if ($name == current(array_keys($this->breadcrumbs))) { ?>
			<li class="first"><a href="<?php echo $this->breadcrumbs[$name]; ?>"><?php echo $name; ?></a></li>
		<?php } elseif ($name == end(array_keys($this->breadcrumbs))) { ?>
			<li><?php echo $name; ?></li>
		<?php } else { ?>
			<li><a href="<?php echo $this->breadcrumbs[$name]; ?>"><?php echo $name; ?></a></li>
		<? } ?>
	<? } ?>
</ul>