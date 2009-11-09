CREATE TABLE  `catalog_site_frame` (
  `alias` varchar(55) character set utf8 collate utf8_bin NOT NULL,
  `uri` varchar(255) NOT NULL,
  `frame` enum('YES','NO') NOT NULL default 'YES',
  `meta_title` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  PRIMARY KEY  (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
