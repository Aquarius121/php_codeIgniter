ALTER TABLE `nr_sales_planner`
ADD `source` varchar(16) NULL AFTER `step_max`;

ALTER TABLE `nr_sales_planner`
ADD INDEX `source` (`source`);
