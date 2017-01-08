# 1000000 per partition (per loop), 100 partitions
CREATE TABLE IF NOT EXISTS `context_hash` (
  `context` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash` binary(20) NOT NULL COMMENT 'sha1 blob',
  PRIMARY KEY (`context`)
) ENGINE=InnoDB 
CHARACTER SET ascii
COLLATE ascii_bin
PARTITION BY HASH((context DIV 1000000) MOD 100)
PARTITIONS 100;

# 256 partitions by key (hash)
CREATE TABLE IF NOT EXISTS `hash_context` (
  `hash` binary(20) NOT NULL COMMENT 'sha1 blob',
  `context` bigint(20) UNSIGNED NOT NULL ,
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB 
CHARACTER SET ascii
COLLATE ascii_bin
PARTITION BY KEY()
PARTITIONS 256;