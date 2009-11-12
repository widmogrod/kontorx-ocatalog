--
-- Struktura tabeli dla  `catalog_search_query`
--

CREATE TABLE IF NOT EXISTS `catalog_search_query` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(500) CHARACTER SET utf8 NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `query_index` (`query`(333))
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
