CREATE TABLE  `catalog_site` (
  `catalog_id` bigint(20) NOT NULL COMMENT 'Wizytówka',
  `url` varchar(20) NOT NULL COMMENT 'Adres',
  PRIMARY KEY  USING BTREE (`catalog_id`,`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Strony'
