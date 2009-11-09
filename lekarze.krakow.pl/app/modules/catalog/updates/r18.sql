CREATE TABLE  `catalog_time2` (
  `id` bigint(20) NOT NULL auto_increment,
  `catalog_id` bigint(20) NOT NULL,
  `time` time NOT NULL,
  `day` tinyint(4) unsigned NOT NULL,
  `start_end` enum('END','START') character set utf8 collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `catalog_index` (`catalog_id`),
  KEY `time_index` (`time`),
  KEY `day_index` (`day`)
) ENGINE=MyISAM AUTO_INCREMENT=885 DEFAULT CHARSET=utf8
