# 1000000 per partition (per loop), 10 partitions
CREATE TABLE `sx_hits_referer` (
  `context` bigint(20) NOT NULL,
  `referer_hash` binary(16) NOT NULL COMMENT 'md5 blob',
  `referer_url` varchar(512) NOT NULL,
  PRIMARY KEY (`context`, `referer_hash`)
) ENGINE=InnoDB
CHARACTER SET ascii
COLLATE ascii_bin
PARTITION BY HASH((context DIV 1000000) MOD 10)
PARTITIONS 10;