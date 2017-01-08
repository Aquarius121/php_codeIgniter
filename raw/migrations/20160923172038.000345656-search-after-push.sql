DROP TABLE IF EXISTS `nr_search_terms`;

TRUNCATE TABLE `nr_search_builder_company`;
TRUNCATE TABLE `nr_search_builder_content`;
TRUNCATE TABLE `nr_search_index_company`;
TRUNCATE TABLE `nr_search_index_content`;
TRUNCATE TABLE `nr_search_term`;

ALTER TABLE `nr_search_index_company`
ADD `quality` mediumint NOT NULL;

ALTER TABLE `nr_search_index_content`
ADD `quality` mediumint NOT NULL;
