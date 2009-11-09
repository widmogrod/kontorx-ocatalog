CREATE TABLE  `catalog_has_catalog_service` (
  `id` bigint(20) NOT NULL auto_increment,
  `catalog_id` bigint(20) NOT NULL,
  `catalog_service_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `new_index` (`catalog_id`,`catalog_service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
