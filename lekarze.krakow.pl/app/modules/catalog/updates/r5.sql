CREATE TABLE  `catalog_has_catalog_staff` (
  `catalog_id` bigint(20) NOT NULL,
  `catalog_staff_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`catalog_id`,`catalog_staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
