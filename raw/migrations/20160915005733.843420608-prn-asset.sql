DROP TABLE IF EXISTS `nr_prn_distribution_asset`;
CREATE TABLE `nr_prn_distribution_asset` (
  `hash` char(32) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

