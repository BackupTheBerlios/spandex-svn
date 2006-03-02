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

foreach ($columns as $columnName) { 
	echo '<td>';

	if (strstr($columnName, 'datetime')) {
		echo date('j/M/y g:i a',$row[$columnName]); 
	} elseif (strstr($columnName, 'date')) {
		echo date('j/M/y',$row[$columnName]); 
	} elseif (strstr($columnName, 'pass')) {
		echo $this->_passwordDisplay; 
	} else {
		echo substr(htmlspecialchars($row[$columnName]), 0, 256); 
	}
?>
	</td>
<?php } ?>