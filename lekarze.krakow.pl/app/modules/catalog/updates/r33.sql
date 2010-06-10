CREATE TABLE `catalog_has_district` (
  `catalog_id` bigint UNSIGNED NOT NULL,
  `district_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`catalog_id`, `district_id`)
)
ENGINE = MyISAM;

