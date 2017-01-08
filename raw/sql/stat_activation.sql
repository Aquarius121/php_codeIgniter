# 1000000 per partition (per loop), 100 partitions
CREATE TABLE IF NOT EXISTS `sx_activation` (
  `context` BIGINT UNSIGNED NOT NULL ,
  `context_set` BIGINT UNSIGNED NOT NULL ,
  `date_request` datetime NOT NULL ,
  INDEX `date_request` (`date_request`) ,
  INDEX `context_set` (`context_set`) ,
  PRIMARY KEY `context` (`context`)
) ENGINE=InnoDB 
CHARACTER SET ascii
COLLATE ascii_bin
PARTITION BY HASH((context DIV 1000000) MOD 100)
PARTITIONS 100;