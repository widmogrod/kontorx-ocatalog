--
-- Struktura tabeli dla  `catalog_search_find`
--

CREATE TABLE IF NOT EXISTS `catalog_search_find` (
  `query_search_id` bigint(20) unsigned NOT NULL,
  `catalog_id` bigint(20) unsigned NOT NULL,
  `idx` bigint(20) NOT NULL,
  PRIMARY KEY (`query_search_id`,`catalog_id`),
  KEY `new_index` (`idx`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
