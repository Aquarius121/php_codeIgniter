# 1000000 per partition (per loop), 100 partitions
CREATE TABLE IF NOT EXISTS `sx_hits_summation` (
  `context` BIGINT UNSIGNED NOT NULL,
  `sum` INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`context`),
  KEY `sum` (`sum`)
) ENGINE=InnoDB 
CHARACTER SET ascii
COLLATE ascii_bin
PARTITION BY HASH((context DIV 1000000) MOD 100)
PARTITIONS 100;