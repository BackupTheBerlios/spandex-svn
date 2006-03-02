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
 	if ($count > $this->max_records_per_page) {
		echo '<ul class="pages">';
		for ($i=0; $i<$count; $i += $this->max_records_per_page) {
			echo '<li>';
			if (isset($_GET['min']) && $_GET['min'] == $i) {
				echo '<strong>';
			}
			echo '<a href="' . $_SERVER['PHP_SELF'] . '?m=' . $this->module_name . '&amp;min=' . $i . '">';
			echo 'Records ' . ($i+1) . ' to ' . ($i + $this->max_records_per_page);
			echo '</a>';
			if (isset($_GET['min']) && $_GET['min'] == $i) {
				echo '</strong>';
			}
			echo '</li>';
		}
		echo '</ul>';
	}
?>