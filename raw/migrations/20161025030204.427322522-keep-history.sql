SET NAMES utf8;

DROP TABLE IF EXISTS `nr_content_change_deleted`;
CREATE TABLE `nr_content_change_deleted` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `raw_data` blob,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `date_saved` (`date_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
