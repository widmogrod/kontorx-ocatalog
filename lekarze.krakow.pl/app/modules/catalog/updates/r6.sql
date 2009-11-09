CREATE TABLE  `catalog_has_catalog_tag` (
  `id` bigint(20) NOT NULL auto_increment,
  `catalog_id` bigint(20) NOT NULL,
  `catalog_tag_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `new_index` (`catalog_id`,`catalog_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
