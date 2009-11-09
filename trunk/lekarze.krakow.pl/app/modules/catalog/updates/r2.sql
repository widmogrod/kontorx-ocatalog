CREATE TABLE  `catalog_district` (
  `id` int(11) NOT NULL auto_increment,
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Nazwa',
  `url` varchar(255) NOT NULL COMMENT 'Url',
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `visible` tinyint(4) NOT NULL COMMENT 'Widoczność',
  `meta_title` varchar(255) default NULL,
  `meta_description` varchar(255) default NULL,
  `meta_keywords` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `new_index` (`url`),
  KEY `index_path` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8
