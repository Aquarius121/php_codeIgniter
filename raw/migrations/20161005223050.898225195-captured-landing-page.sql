ALTER TABLE `nr_captured_landing_page_customers`
CHANGE `customer_name` `customer_name` varchar(100) COLLATE 'utf8_general_ci' NULL AFTER `id`,
ADD `customer_company` varchar(100) COLLATE 'utf8_general_ci' NOT NULL AFTER `customer_email`,
ADD `customer_phone` varchar(50) COLLATE 'utf8_general_ci' NOT NULL AFTER `customer_company`;
