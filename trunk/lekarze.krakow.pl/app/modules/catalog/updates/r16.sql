CREATE TABLE  `catalog_staff` (
  `id` bigint(20) NOT NULL auto_increment,
  `fullname` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `image` varchar(100) default NULL,
  `catalog_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fullname_index` (`fullname`),
  KEY `catalog_index` (`catalog_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Personel'
