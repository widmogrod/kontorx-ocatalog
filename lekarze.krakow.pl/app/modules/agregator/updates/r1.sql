CREATE TABLE  `agregator_agregator` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int(10) unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` mediumtext CHARACTER SET utf8 NOT NULL,
  `dateModified` datetime NOT NULL,
  `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `link_index` (`link`),
  KEY `title_index` (`title`),
  KEY `date_index` (`dateModified`),
  UNIQUE(`link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
