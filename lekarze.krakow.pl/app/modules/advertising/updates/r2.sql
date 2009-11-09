CREATE TABLE  `advertising_client` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Nr. identyfikacyjny',
  `name` varchar(30) NOT NULL COMMENT 'Nazwa klienta',
  `email` varchar(50) NOT NULL COMMENT 'Adres E-mail',
  `password` varchar(50) NOT NULL COMMENT 'Hasło',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Baza reklamodawców'
