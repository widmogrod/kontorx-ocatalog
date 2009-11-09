CREATE TABLE  `catalog_has_catalog_type` (
  `catalog_id` bigint(20) NOT NULL,
  `catalog_type_id` tinyint(4) NOT NULL,
  PRIMARY KEY  (`catalog_id`,`catalog_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
