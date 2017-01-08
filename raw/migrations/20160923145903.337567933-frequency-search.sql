DROP TABLE `nr_search_term_frequency`;

ALTER TABLE `nr_search_terms`
ADD `count` int NOT NULL;

ALTER TABLE `nr_search_terms`
ADD INDEX `count` (`count`);
