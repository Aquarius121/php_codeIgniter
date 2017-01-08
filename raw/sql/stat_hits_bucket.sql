# 1000 per partition, 1000000 (1m) per bucket
# * new buckets must be added manually
#   if they are required at a later time
#   and any context that should have been
#   in the larger bucket MUST be moved
CREATE TABLE IF NOT EXISTS `sx_hits_bucket_0000` (
  `context` BIGINT UNSIGNED NOT NULL ,
  `date_request` datetime NOT NULL ,
  `remote_addr` char(15)
   	CHARACTER SET ascii
		COLLATE ascii_bin
		NOT NULL,
  `geo_country` char(2)
   	CHARACTER SET ascii
		COLLATE ascii_bin
		NOT NULL,
  `geo_sub` char(3)
   	CHARACTER SET ascii
		COLLATE ascii_bin
		NOT NULL,
  KEY `geo_country` (`geo_country`, `geo_sub`) ,
  KEY `date_request` (`date_request`) ,
  KEY `context` (`context`)
) ENGINE=InnoDB 
CHARACTER SET ascii
COLLATE ascii_bin
PARTITION BY HASH((context DIV 1000) MOD 1000)
PARTITIONS 1000;