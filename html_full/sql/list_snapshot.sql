CREATE TABLE `list_snapshot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `vmid` int(11) NOT NULL,
  `datelog` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(4) DEFAULT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;