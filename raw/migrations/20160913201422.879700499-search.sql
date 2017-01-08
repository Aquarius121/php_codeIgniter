CREATE TABLE `nr_search_term_frequency` (
  `search_term_id` int NOT NULL,
  `count` int NOT NULL
);

ALTER TABLE `nr_search_term_frequency`
ADD PRIMARY KEY `search_term_id` (`search_term_id`);

ALTER TABLE `nr_search_term_frequency`
ADD INDEX `count` (`count`);


