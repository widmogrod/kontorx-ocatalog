CREATE TABLE  `role` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) NOT NULL COMMENT 'Nazwa',
  `role_id` int(11) default NULL COMMENT 'Rozszerz o rolę',
  `name_sanitized` varchar(20) NOT NULL COMMENT 'Święta nazwa',
  PRIMARY KEY  (`id`),
  KEY `role_id` (`role_id`),
  KEY `name_sanitized` (`name_sanitized`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1
