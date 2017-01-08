ALTER TABLE `nr_scheduled_iella_request`
ADD `is_active` tinyint(1) NOT NULL;

ALTER TABLE `nr_scheduled_iella_request`
ADD INDEX `is_active` (`is_active`);

ALTER TABLE `nr_scheduled_iella_event`
ADD `is_active` tinyint(1) NOT NULL;

ALTER TABLE `nr_scheduled_iella_event`
ADD INDEX `is_active` (`is_active`);

ALTER TABLE `nr_scheduled_iella_request`
ADD `label` varchar(32) NULL AFTER `date_execute`;

ALTER TABLE `nr_scheduled_iella_event`
ADD `label` varchar(32) NULL AFTER `date_execute`;

ALTER TABLE `nr_scheduled_iella_event`
ADD INDEX `label` (`label`);

ALTER TABLE `nr_scheduled_iella_request`
ADD INDEX `label` (`label`);