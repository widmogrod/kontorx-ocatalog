CREATE TABLE  `user_personal` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `birthday` date NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `index` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1
