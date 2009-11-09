CREATE TABLE  `catalog_options` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  `alias` varchar(40) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `alias_index` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8
