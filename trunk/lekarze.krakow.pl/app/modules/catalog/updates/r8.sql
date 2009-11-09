CREATE TABLE  `catalog_image` (
  `id` bigint(20) NOT NULL auto_increment,
  `catalog_id` bigint(20) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `new_index` (`catalog_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8
