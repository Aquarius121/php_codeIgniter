
DROP TABLE IF EXISTS `nr_prn_distribution`;
CREATE TABLE `nr_prn_distribution` (
  `content_id` int(11) NOT NULL,
  `is_submitted` tinyint(1) NOT NULL,
  `is_blocked` tinyint(1) NOT NULL,
  `raw_data` longblob NOT NULL,
  PRIMARY KEY (`content_id`),
  KEY `is_submitted` (`is_submitted`),
  KEY `is_blocked` (`is_blocked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `nr_prn_distribution`
ADD `date_submit` datetime NOT NULL AFTER `is_blocked`;

ALTER TABLE `nr_prn_distribution`
ADD INDEX `date_submit` (`date_submit`);
