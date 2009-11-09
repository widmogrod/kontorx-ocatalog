CREATE TABLE  `catalog_time` (
  `catalog_id` bigint(20) NOT NULL,
  `monday_start` time NOT NULL,
  `monday_end` time NOT NULL,
  `tuesday_start` time NOT NULL,
  `tuesday_end` time NOT NULL,
  `wednesday_start` time NOT NULL,
  `wednesday_end` time NOT NULL,
  `thursday_start` time NOT NULL,
  `thursday_end` time NOT NULL,
  `friday_start` time NOT NULL,
  `friday_end` time NOT NULL,
  `saturday_start` time NOT NULL,
  `saturday_end` time NOT NULL,
  `sunday_start` time NOT NULL,
  `sunday_end` time NOT NULL,
  PRIMARY KEY  (`catalog_id`),
  KEY `time_index` (`monday_start`,`tuesday_start`,`wednesday_start`,`wednesday_end`,`thursday_start`,`thursday_end`,`friday_start`,`friday_end`,`saturday_start`,`saturday_end`,`sunday_start`,`sunday_end`,`monday_end`,`tuesday_end`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
