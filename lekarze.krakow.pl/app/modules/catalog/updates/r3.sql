CREATE TABLE  `catalog_has_catalog_options` (
  `catalog_id` bigint(20) NOT NULL,
  `catalog_options_id` tinyint(4) NOT NULL,
  PRIMARY KEY  (`catalog_id`,`catalog_options_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
