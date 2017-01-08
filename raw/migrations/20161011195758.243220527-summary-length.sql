UPDATE `nr_setting` SET
`name` = 'summary_max_length',
`description` = 'The maximum length (in characters) of the summary text on content.',
`type` = 1,
`value` = '400'
WHERE `name` = 'summary_max_length' AND `name` = 'summary_max_length' COLLATE utf8mb4_bin;

INSERT INTO `nr_setting` (`name`, `description`, `type`, `value`) VALUES
('prn_subheadline_max_length',	'The maximum length (in characters) of the sub-headline for PR Newswire.',	'INTEGER',	'140');
