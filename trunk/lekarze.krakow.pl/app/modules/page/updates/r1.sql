CREATE TABLE  `page_block` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL,
  `content` text NOT NULL COMMENT 'Treść',
  `block_id` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `page_id` USING BTREE (`page_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8
