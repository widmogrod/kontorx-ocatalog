CREATE TABLE  `catalog_service` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT 'Nazwa',
  `description` mediumtext COMMENT 'Opis',
  `ico` varchar(255) default NULL COMMENT 'Grafika',
  `alias` varchar(50) character set utf8 collate utf8_bin NOT NULL COMMENT 'Alias',
  `meta_title` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `alias_index` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='Rodzaje usług'
