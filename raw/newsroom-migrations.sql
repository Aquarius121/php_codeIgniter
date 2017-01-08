-- removed old migrations 30th september 2016

DROP TABLE IF EXISTS `nr_content_change`;
CREATE TABLE `nr_content_change` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `date_saved` datetime DEFAULT NULL,
  `raw_data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='record changes after content has been submitted';
