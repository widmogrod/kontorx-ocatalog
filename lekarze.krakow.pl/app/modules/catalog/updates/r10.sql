CREATE TABLE  `catalog_promo_time` (
  `id` int(11) NOT NULL auto_increment,
  `catalog_promo_type_id` tinyint(4) NOT NULL,
  `t_start` date NOT NULL,
  `t_end` date NOT NULL,
  `catalog_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `time` (`t_start`,`t_end`),
  KEY `type` USING BTREE (`catalog_promo_type_id`,`catalog_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1
