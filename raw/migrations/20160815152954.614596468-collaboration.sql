CREATE TABLE `nr_content_collab` (
  `uuid` char(36) NOT NULL,
  `content_id` int NOT NULL,
  `raw_data` longblob NOT NULL
);

ALTER TABLE `nr_content_collab`
ADD PRIMARY KEY `uuid` (`uuid`),
ADD INDEX `content_id` (`content_id`);

ALTER TABLE `nr_content_collab`
ADD `date_created` datetime NOT NULL AFTER `content_id`;

ALTER TABLE `nr_content_collab`
ADD INDEX `date_created` (`date_created`);

ALTER TABLE `nr_content_collab`
CHANGE `uuid` `id` char(36) COLLATE 'utf8_general_ci' NOT NULL FIRST;

ALTER TABLE `nr_content_collab`
ADD `is_shared` tinyint(1) NOT NULL AFTER `date_created`;

ALTER TABLE `nr_content_collab`
ADD `is_deleted` tinyint(1) NOT NULL AFTER `date_created`;

ALTER TABLE `nr_content_collab`
DROP `is_shared`;

ALTER TABLE `nr_content_collab`
ADD `version` smallint NOT NULL AFTER `content_id`;
