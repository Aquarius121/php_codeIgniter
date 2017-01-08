
DROP TABLE IF EXISTS `nr_captured_landing_page_customers`;
CREATE TABLE `nr_captured_landing_page_customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(254) NOT NULL,
  `customer_email` varchar(254) NOT NULL,
  `date_created` datetime NOT NULL,
  `is_lead_created_in_salesforce` tinyint(1) NOT NULL DEFAULT '0',
  `is_account_found` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
