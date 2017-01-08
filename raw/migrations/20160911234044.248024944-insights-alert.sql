CREATE TABLE `nr_insights_alert` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `secret` char(32) NOT NULL,
  `user_id` int NULL,
  `email` varchar(254) NULL,
  `date_created` datetime NOT NULL,
  `date_sent` datetime NOT NULL,
  `params` blob NOT NULL
);

ALTER TABLE `nr_insights_alert`
ADD INDEX `email` (`email`),
ADD INDEX `user_id` (`user_id`),
ADD INDEX `date_sent` (`date_sent`);

ALTER TABLE `nr_insights_alert`
ADD `is_enabled` tinyint(1) NOT NULL AFTER `date_sent`;

ALTER TABLE `nr_insights_alert`
ADD INDEX `is_enabled` (`is_enabled`);
