CREATE TABLE  `page` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT 'Nazwa',
  `url` varchar(255) NOT NULL COMMENT 'Url',
  `content` text NOT NULL COMMENT 'Treść',
  `meta_title` varchar(255) default NULL COMMENT 'Meta title',
  `meta_keywords` varchar(255) default NULL COMMENT 'Meta keywords',
  `meta_description` varchar(255) default NULL COMMENT 'Meta description',
  `publicated` tinyint(1) NOT NULL default '0' COMMENT 'Opublikować',
  `language_url` varchar(4) NOT NULL COMMENT 'Wersja językowa',
  `path` varchar(255) character set latin1 NOT NULL COMMENT 'Zagnieżdżenie',
  `t_create` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Data utworzenia',
  `t_start` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT 'Start publikacji',
  `t_end` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT 'Koniec publikacji',
  `user_id` int(11) NOT NULL,
  `redirect` varchar(255) default NULL COMMENT 'Przekierowanie',
  `visible` tinyint(4) NOT NULL default '0' COMMENT 'Widoczność',
  PRIMARY KEY  (`id`),
  KEY `url` (`url`),
  KEY `language_url` (`language_url`),
  KEY `path` (`path`),
  KEY `time_create` (`t_create`),
  KEY `time_start` (`t_start`),
  KEY `time_end` USING BTREE (`t_end`),
  KEY `user_id` (`user_id`),
  KEY `publicated_visible` USING BTREE (`publicated`,`visible`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8
