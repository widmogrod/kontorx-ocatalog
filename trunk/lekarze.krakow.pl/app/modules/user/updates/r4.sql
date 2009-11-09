CREATE TABLE  `role_has_role_resource` (
  `id` int(11) NOT NULL auto_increment,
  `role_id` int(11) NOT NULL COMMENT 'Rola',
  `role_resource_id` int(11) NOT NULL COMMENT 'Resource',
  `role_access_id` int(11) default NULL,
  `deny` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `role_id` (`role_id`),
  KEY `role_resource_id` (`role_resource_id`),
  KEY `role_access_id` (`role_access_id`),
  KEY `deny` (`deny`)
) ENGINE=InnoDB AUTO_INCREMENT=10942 DEFAULT CHARSET=latin1
