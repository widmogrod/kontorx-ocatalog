CREATE TABLE  `catalog_service_cost` (
  `id` bigint(20) NOT NULL auto_increment,
  `catalog_id` bigint(20) NOT NULL,
  `catalog_service_id` int(11) NOT NULL,
  `cost_min` float(6,2) NOT NULL,
  `cost_max` float(6,2) NOT NULL,
  `desc` mediumtext character set utf8 NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `new_index` (`catalog_id`,`catalog_service_id`),
  KEY `cost_index` (`cost_min`,`cost_max`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=latin1
