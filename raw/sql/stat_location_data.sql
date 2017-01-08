CREATE TABLE IF NOT EXISTS `location_data` (
  `geo_country` char(2) 
    CHARACTER SET ascii
    COLLATE ascii_bin
    NOT NULL,
  `geo_sub` char(3)
    CHARACTER SET ascii
    COLLATE ascii_bin
    NOT NULL,
  `country_name` varchar(128)
    CHARACTER SET utf8
    COLLATE utf8_general_ci,
  `sub_name` varchar(128)
    CHARACTER SET utf8
    COLLATE utf8_general_ci,
  PRIMARY KEY `location` (`geo_country`, `geo_sub`)
) ENGINE=InnoDB
CHARACTER SET utf8
COLLATE utf8_general_ci;
