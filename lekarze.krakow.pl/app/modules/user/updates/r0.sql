CREATE TABLE  `user` (
  `id` bigint(20) NOT NULL auto_increment,
  `username` varchar(40) NOT NULL COMMENT 'Użytkownik',
  `password` varchar(255) NOT NULL COMMENT 'Hasło',
  `email` varchar(255) NOT NULL COMMENT 'Adres e-mail',
  `key` varchar(255) default NULL COMMENT 'Klucz walidujący',
  `registered` tinyint(1) default NULL COMMENT 'Zarejestrowany',
  `role` varchar(30) character set latin1 NOT NULL default 'guest' COMMENT 'Rola',
  `last_visite` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Ostatnia wizyta',
  `image` varchar(255) default NULL COMMENT 'Avatar',
  PRIMARY KEY  (`id`),
  KEY `index` USING BTREE (`username`,`email`),
  KEY `options` (`registered`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC
