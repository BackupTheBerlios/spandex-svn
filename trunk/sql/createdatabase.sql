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

-- 
-- Table structure for table `stories`
-- 

CREATE TABLE `stories` (
  `id` int(11) NOT NULL auto_increment,
  `user` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `content` text,
  `date_created` int(11) NOT NULL default '0',
  `date_updated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM COMMENT='Stories published on site';

-- --------------------------------------------------------

-- 
-- Table structure for table `undobuffer`
-- 

CREATE TABLE `undobuffer` (
  `id` int(11) NOT NULL auto_increment,
  `user` int(11) default NULL,
  `date_created` int(11) NOT NULL default '0',
  `action` enum('new','edit','delete') NOT NULL default 'new',
  `table_name` varchar(64) NOT NULL default '',
  `data` blob NOT NULL,
  `record` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Data to allow undo actions. Typically up to 500';

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `pass` varchar(255) NOT NULL default '',
  `email` varchar(255) default NULL,
  `last_login_date` int(11) default NULL,
  `date_created` int(11) NOT NULL default '0',
  `date_updated` int(11) NOT NULL default '0',
  `user` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM COMMENT='Users authentication information';

-- 
-- Data for table `users`
-- 

INSERT INTO `users` VALUES (1, 'admin', MD5('welcome'), 'admin@localhost', 0, 0, 0, 1);
        