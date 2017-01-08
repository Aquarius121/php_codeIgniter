ALTER TABLE `nr_company_profile`
ADD `is_scraped_company_data_sanitized` tinyint(1) NULL DEFAULT '0';

ALTER TABLE `nr_company_profile`
ADD INDEX `is_scraped_company_data_sanitized` (`is_scraped_company_data_sanitized`);

UPDATE nr_pb_prweb_pr
SET is_content_sanitized = 0;

ALTER TABLE `nr_pb_marketwired_pr`
ADD `is_content_sanitized` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `nr_pb_marketwired_pr`
ADD INDEX `is_content_sanitized` (`is_content_sanitized`);

ALTER TABLE `nr_pb_businesswire_pr`
ADD `is_content_sanitized` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `nr_pb_businesswire_pr`
ADD INDEX `is_content_sanitized` (`is_content_sanitized`);

ALTER TABLE `nr_pb_newswire_ca_pr`
ADD `is_content_sanitized` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `nr_pb_newswire_ca_pr`
ADD INDEX `is_content_sanitized` (`is_content_sanitized`);

ALTER TABLE `nr_pb_mynewsdesk_content`
ADD `is_content_sanitized` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `nr_pb_mynewsdesk_content`
ADD INDEX `is_content_sanitized` (`is_content_sanitized`);

ALTER TABLE `nr_pb_pr_co_content`
ADD `is_content_sanitized` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `nr_pb_pr_co_content`
ADD INDEX `is_content_sanitized` (`is_content_sanitized`);


