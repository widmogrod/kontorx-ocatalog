CREATE TABLE  `role_access` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL COMMENT 'Nazwa',
  `role_resource_id` int(11) NOT NULL COMMENT 'Resource',
  PRIMARY KEY  (`id`),
  KEY `role_resource_id` (`role_resource_id`)
) ENGINE=MyISAM AUTO_INCREMENT=403 DEFAULT CHARSET=utf8