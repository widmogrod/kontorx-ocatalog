CREATE TABLE  `language` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(4) character set latin1 NOT NULL COMMENT 'Skrót w formacie ISO',
  `name` varchar(20) character set latin1 NOT NULL COMMENT 'Nazwa',
  PRIMARY KEY  (`id`),
  KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC
