ALTER TABLE `nr_pb_pr`
ADD `is_publish_date_selected` tinyint(1) NOT NULL;

update nr_pb_pr set is_publish_date_selected = 1;
