CREATE TABLE IF NOT EXISTS `addr_location` (
  `IP_from` INT unsigned NOT NULL,
  `IP_to` INT unsigned NOT NULL,
  `geo_country` char(2) 
  		CHARACTER SET ascii
		COLLATE ascii_bin
		NOT NULL,
  `geo_sub` char(3)
  		CHARACTER SET ascii
		COLLATE ascii_bin
		NOT NULL,
  KEY `IP_from` (`IP_from`)
) ENGINE=InnoDB
CHARACTER SET ascii
COLLATE ascii_bin;
