UPDATE `co_item` SET
`id` = '1',
`name` = 'Silver Plan (2015)',
`tracking` = 'silver_plan',
`slug` = null,
`comment` = '',
`descriptor` = 'SilverPLN',
`help_text` = '',
`type` = 1,
`price` = '79',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '1';

UPDATE `co_item` SET
`id` = '2',
`name` = 'Gold Plan (2015)',
`tracking` = 'gold_plan',
`slug` = null,
`comment` = '',
`descriptor` = 'GoldPLN',
`help_text` = '',
`type` = 1,
`price` = '149',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '2';

UPDATE `co_item` SET
`id` = '3',
`name` = 'Platinum Plan (2015)',
`tracking` = 'platinum_plan',
`slug` = null,
`comment` = '',
`descriptor` = 'PlatPLN',
`help_text` = '',
`type` = 1,
`price` = '499',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '3';

UPDATE `co_item` SET
`id` = '6',
`name` = 'Silver Plan (12 Months) (2015)',
`tracking` = 'silver_plan_12_months',
`slug` = null,
`comment` = '',
`descriptor` = 'Silver12M',
`help_text` = '',
`type` = 1,
`price` = '853',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '6';

UPDATE `co_item` SET
`id` = '7',
`name` = 'Gold Plan (12 Months) (2015)',
`tracking` = 'gold_plan_12_months',
`slug` = null,
`comment` = '',
`descriptor` = 'Gold12M',
`help_text` = '',
`type` = 1,
`price` = '1609',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '7';

UPDATE `co_item` SET
`id` = '8',
`name` = 'Platinum Plan (12 Months) (2015)',
`tracking` = 'platinum_plan_12_months',
`slug` = null,
`comment` = '',
`descriptor` = 'Plat12M',
`help_text` = '',
`type` = 1,
`price` = '5389',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '8';

UPDATE `co_item` SET
`id` = '9',
`name` = 'Silver Plan (24 Months) (2015)',
`tracking` = 'silver_plan_24_months',
`slug` = null,
`comment` = '',
`descriptor` = 'Silver24M',
`help_text` = '',
`type` = 1,
`price` = '1422',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '9';

UPDATE `co_item` SET
`id` = '10',
`name` = 'Gold Plan (24 Months) (2015)',
`tracking` = 'gold_plan_24_months',
`slug` = null,
`comment` = '',
`descriptor` = 'Gold24M',
`help_text` = '',
`type` = 1,
`price` = '2682',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '10';

UPDATE `co_item` SET
`id` = '11',
`name` = 'Platinum Plan (24 Months) (2015)',
`tracking` = 'platinum_plan_24_months',
`slug` = null,
`comment` = '',
`descriptor` = 'Plat24M',
`help_text` = '',
`type` = 1,
`price` = '8982',
`secret` = '',
`activate_event` = 'item_activate_plan',
`order_event` = 'item_order_plan',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '11';



UPDATE `co_plan` SET
`id` = '1',
`name` = 'Silver (2015)',
`package` = '1',
`is_protected` = '1',
`is_legacy` = '0',
`period` = '30',
`connected_item_id` = NULL
WHERE `id` = '1';

UPDATE `co_plan` SET
`id` = '2',
`name` = 'Gold (2015)',
`package` = '2',
`is_protected` = '1',
`is_legacy` = '0',
`period` = '30',
`connected_item_id` = NULL
WHERE `id` = '2';

UPDATE `co_plan` SET
`id` = '3',
`name` = 'Platinum (2015)',
`package` = '3',
`is_protected` = '1',
`is_legacy` = '0',
`period` = '30',
`connected_item_id` = NULL
WHERE `id` = '3';


UPDATE `co_item` SET
`id` = '20',
`name` = 'Newsroom Credit (2015)',
`tracking` = 'newsroom_credit',
`slug` = 'newsroom-credit-platinum',
`comment` = 'Platinum Users',
`descriptor` = NULL,
`help_text` = '',
`type` = 2,
`price` = '52',
`secret` = 'd27ce447aa0a365e',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '1',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '20';

UPDATE `co_item` SET
`id` = '21',
`name` = 'Newsroom Credit (2015)',
`tracking` = 'newsroom_credit',
`slug` = 'newsroom-credit-gold',
`comment` = 'Gold Users',
`descriptor` = NULL,
`help_text` = '',
`type` = 2,
`price` = '59',
`secret` = '56706454d34775d4',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '1',
`is_listed` = '0',
`is_custom` = '0'
WHERE `id` = '21';

UPDATE `co_item` SET
`id` = '22',
`name` = 'Newsroom Credit (2015)',
`tracking` = 'newsroom_credit',
`slug` = 'newsroom-credit-silver',
`comment` = 'Silver Users',
`descriptor` = NULL,
`help_text` = '',
`type` = 2,
`price` = '63',
`secret` = 'de44e62874f00b89',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '1',
`is_listed` = '0',
`is_custom` = '0'
WHERE `id` = '22';

UPDATE `co_item` SET
`id` = '23',
`name` = 'Newsroom Credit (2015)',
`tracking` = 'newsroom_credit',
`slug` = 'newsroom-credit',
`comment` = '',
`descriptor` = NULL,
`help_text` = '',
`type` = 2,
`price` = '70',
`secret` = '24430277027f8499',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '1',
`is_listed` = '0',
`is_custom` = '0'
WHERE `id` = '23';

-- ---------------------------------------------------------
-- ---------------------------------------------------------
-- ---------------------------------------------------------
-- ---------------------------------------------------------
-- ---------------------------------------------------------

INSERT INTO `co_plan` (`id`, `name`, `package`, `is_protected`, `is_legacy`, `period`, `connected_item_id`) VALUES
(null,	'Enterprise',	3,	1,	0,	30,	NULL),
(null,	'Small Business',	2,	1,	0,	30,	NULL),
(null,	'Professional',	1,	1,	0,	30,	NULL);

INSERT INTO `co_plan_credit` (`id`, `plan_id`, `type`, `available`, `period`, `is_rollover_to_held_enabled`) VALUES
(null,	/* from above */,	'NEWSROOM',	5,	NULL,	0),
(null,	/* from above */,	'EMAIL',	3000,	NULL,	1),
(null,	/* from above */,	'BASIC_PR',	0,	NULL,	0),
(null,	/* from above */,	'PREMIUM_PR',	15,	NULL,	1),
(null,	/* from above */,	'NEWSROOM',	2,	NULL,	0),
(null,	/* from above */,	'EMAIL',	800,	NULL,	1),
(null,	/* from above */,	'BASIC_PR',	0,	NULL,	0),
(null,	/* from above */,	'PREMIUM_PR',	4,	NULL,	1),
(null,	/* from above */,	'NEWSROOM',	1,	NULL,	0),
(null,	/* from above */,	'EMAIL',	400,	NULL,	1),
(null,	/* from above */,	'BASIC_PR',	0,	NULL,	0),
(null,	/* from above */,	'PREMIUM_PR',	2,	NULL,	1);

-- ---------------------------------------------------------
-- ---------------------------------------------------------
-- ---------------------------------------------------------
-- ---------------------------------------------------------
-- ---------------------------------------------------------

UPDATE `co_item` SET
`id` = '5',
`name` = 'Premium PR',
`tracking` = 'premium_pr_credit',
`slug` = 'premium-pr-credit',
`comment` = 'Premium 2016',
`descriptor` = 'PremiumPR',
`help_text` = 'Credit for Premium Press Release.',
`type` = 2,
`price` = '99',
`secret` = '',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '5';

UPDATE `co_item` SET
`id` = '12',
`name` = 'Premium PR',
`tracking` = 'premium_pr_credit',
`slug` = 'premium-pr-credit-platinum',
`comment` = 'Platinum Users, Premium 2016',
`descriptor` = 'PremiumPR',
`help_text` = '',
`type` = 2,
`price` = '46.60',
`secret` = 'ddd132943e08b4ce',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '12';


UPDATE `co_item` SET
`id` = '13',
`name` = 'Premium PR',
`tracking` = 'premium_pr_credit',
`slug` = 'premium-pr-credit-gold',
`comment` = 'Gold Users, Premium 2016',
`descriptor` = 'PremiumPR',
`help_text` = '',
`type` = 2,
`price` = '69.75',
`secret` = '65111deb0d8637c2',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '13';


UPDATE `co_item` SET
`id` = '14',
`name` = 'Premium PR',
`tracking` = 'premium_pr_credit',
`slug` = 'premium-pr-credit-silver',
`comment` = 'Silver Users, Premium 2016',
`descriptor` = 'PremiumPR',
`help_text` = '',
`type` = 2,
`price` = '79.50',
`secret` = '4d1c9be2c8fd20a2',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '14';

UPDATE `co_item` SET
`id` = '19',
`name` = 'Media Outreach Credit',
`tracking` = 'email_credit',
`slug` = 'email-credit',
`comment` = '',
`descriptor` = 'EmailCred',
`help_text` = '',
`type` = 2,
`price` = '0.2',
`secret` = '',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '19';

UPDATE `co_item` SET
`id` = '18',
`name` = 'Media Outreach Credit',
`tracking` = 'email_credit',
`slug` = 'email-credit-silver',
`comment` = 'Silver Users',
`descriptor` = 'EmailCred',
`help_text` = '',
`type` = 2,
`price` = '0.17',
`secret` = '70270ca63a3de2d8',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '18';

UPDATE `co_item` SET
`id` = '16',
`name` = 'Media Outreach Credit',
`tracking` = 'email_credit',
`slug` = 'email-credit-gold',
`comment` = 'Gold Users',
`descriptor` = 'EmailCred',
`help_text` = '',
`type` = 2,
`price` = '0.14',
`secret` = '0fe534fe4755c013',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '16';

UPDATE `co_item` SET
`id` = '15',
`name` = 'Media Outreach Credit',
`tracking` = 'email_credit',
`slug` = 'email-credit-platinum',
`comment` = 'Platinum Users',
`descriptor` = 'EmailCred',
`help_text` = '',
`type` = 2,
`price` = '0.1',
`secret` = 'b62d28106808ba51',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '15';


INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Enterprise Plan',	'platinum_plan',	'platinum-plan',	'',	'EnterPLN',	'',	'PLAN',	699,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":100,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Small Business Plan',	'gold_plan',	'gold-plan',	'',	'SmallBPLN',	'',	'PLAN',	279,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":101,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Professional Plan',	'silver_plan',	'silver-plan',	'',	'ProfesPLN',	'',	'PLAN',	159,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":102,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0);



INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Professional Plan (12 Months)',	'silver_plan_12_months',	'silver-plan-12-months',	'',	'Profes12M',	'',	'PLAN',	1717,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":102,\"period_repeat_count\":12,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Small Business Plan (12 Months)',	'gold_plan_12_months',	'gold-plan-12-months',	'',	'SmallB12M',	'',	'PLAN',	3013,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":101,\"period_repeat_count\":12,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Enterprise Plan (12 Months)',	'platinum_plan_12_months',	'platinum-plan-12-months',	'',	'Enter12M',	'',	'PLAN',	7549,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":100,\"period_repeat_count\":12,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Professional Plan (24 Months)',	'silver_plan_24_months',	'silver-plan-24-months',	'',	'Profes24M',	'',	'PLAN',	3434,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":102,\"period_repeat_count\":24,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Small Business Plan (24 Months)',	'gold_plan_24_months',	'gold-plan-24-months',	'',	'SmallB24M',	'',	'PLAN',	6026,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":101,\"period_repeat_count\":24,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Enterprise Plan (24 Months)',	'platinum_plan_24_months',	'platinum-plan-24-months',	'',	'Enter24M',	'',	'PLAN',	15098,	'',	'item_activate_plan',	'item_order_plan',	'{\"plan_id\":100,\"period_repeat_count\":24,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0);





UPDATE `co_item` SET
`id` = '28',
`name` = 'PR Writing',
`tracking` = 'writing_credit',
`slug` = 'writing-credit',
`comment` = '',
`descriptor` = NULL,
`help_text` = '',
`type` = 2,
`price` = '199',
`secret` = '',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '28';

UPDATE `co_item` SET
`id` = '125',
`name` = 'Pitch Writing',
`tracking` = 'pitch_writing_credit',
`slug` = 'pitch-writing-credit',
`comment` = '',
`descriptor` = NULL,
`help_text` = 'Have us write a quality email campaign for you. ',
`type` = 2,
`price` = '99',
`secret` = '',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '125';



INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Pitch Writing',	'pitch_writing_credit',	'pitch-writing-credit-platinum',	'Platinum Users',	NULL,	'Have us write a quality email campaign for you. ',	'CREDIT',	74,	'68351ceabb77f11e',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"PITCH_WRITING\",\"is_quantity_unlocked\":true}',	0,	1,	0),
(null,	'Pitch Writing',	'pitch_writing_credit',	'pitch-writing-credit-gold',	'Gold Users',	NULL,	'Have us write a quality email campaign for you. ',	'CREDIT',	84,	'dc3aaae3bb62331f',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"PITCH_WRITING\",\"is_quantity_unlocked\":true}',	0,	1,	0),
(null,	'Pitch Writing',	'pitch_writing_credit',	'pitch-writing-credit-silver',	'Silver Users',	NULL,	'Have us write a quality email campaign for you. ',	'CREDIT',	89,	'e061e862b0ed878c',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"PITCH_WRITING\",\"is_quantity_unlocked\":true}',	0,	1,	0),
(null,	'PR Writing',	'writing_credit',	'writing-credit-silver',	'Silver Users',	NULL,	'',	'CREDIT',	179.1,	'3ec38e927896abf9',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"WRITING\",\"is_quantity_unlocked\":true}',	0,	1,	0),
(null,	'PR Writing',	'writing_credit',	'writing-credit-gold',	'Gold Users',	NULL,	'',	'CREDIT',	169,	'y eg sdfgsdfg',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"WRITING\",\"is_quantity_unlocked\":true}',	0,	1,	0),
(null,	'PR Writing',	'writing_credit',	'writing-credit-platinum',	'Platinum Users',	NULL,	'',	'CREDIT',	149,	'083601ec9d18b20f',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"WRITING\",\"is_quantity_unlocked\":true}',	0,	1,	0);


INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Premium Plus',	'premium_plus',	'premium-plus-credit',	'',	NULL,	'Credit for a Premium Plus Press Release.',	NULL,	299,	md5('9a7a2asdfc0010a9ee2d'),	NULL,	'item_order_premium_plus',	'{\"bundled_email_credits\":100,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus',	'premium_plus',	'premium-plus-credit-platinum',	'Platinum Users',	NULL,	'Credit for a Premium Plus Press Release.',	NULL,	179,	md5('9a7a2c0asfgh011a9ee2d'),	NULL,	'item_order_premium_plus',	'{\"bundled_email_credits\":100,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus',	'premium_plus',	'premium-plus-credit-gold',	'Gold Users',	NULL,	'Credit for a Premium Plus Press Release.',	NULL,	199,	md5('9a7a2c0dafh012a9ee2d'),	NULL,	'item_order_premium_plus',	'{\"bundled_email_credits\":100,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus',	'premium_plus',	'premium-plus-credit-silver',	'Silver Users',	NULL,	'Credit for a Premium Plus Press Release.',	NULL,	229,	md5('9a7a2c0ae4af013a9ee2d'),	NULL,	'item_order_premium_plus',	'{\"bundled_email_credits\":100,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0);



UPDATE `co_item` SET
`name` = 'Newsroom'
WHERE `name` LIKE '%Newsroom%' AND `is_custom` = '0' AND ((`id` = '23') OR (`id` = '22') OR (`id` = '21') OR (`id` = '20'));



UPDATE `co_item` SET
`id` = '74',
`name` = 'Targeted Media Campaign',
`tracking` = 'media_outreach_credit',
`slug` = 'media-outreach-credit',
`comment` = '',
`descriptor` = NULL,
`help_text` = 'Increase the reach of your press release by sending your press release directly to targeted news and media. ',
`type` = 2,
`price` = '199',
`secret` = '',
`activate_event` = 'item_activate_credit',
`order_event` = 'item_order_credit',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '74';



INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Targeted Media Campaign',	'media_outreach_credit',	'media-outreach-credit-platinum',	'Platinum Users',	NULL,	'Increase the reach of your press release by sending your press release directly to targeted news and media. ',	'CREDIT',	149,	'db2cda6319cebb8c',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"MEDIA_OUTREACH\",\"is_quantity_unlocked\":true}',	0,	1,	0),
(null,	'Targeted Media Campaign',	'media_outreach_credit',	'media-outreach-credit-gold',	'Gold Users',	NULL,	'Increase the reach of your press release by sending your press release directly to targeted news and media. ',	'CREDIT',	169,	'66d52e5bef97351a',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"MEDIA_OUTREACH\",\"is_quantity_unlocked\":true}',	0,	1,	0),
(null,	'Targeted Media Campaign',	'media_outreach_credit',	'media-outreach-credit-silver',	'Silver Users',	NULL,	'Increase the reach of your press release by sending your press release directly to targeted news and media. ',	'CREDIT',	179,	'4f64b482e3c88472',	'item_activate_credit',	'item_order_credit',	'{\"type\":\"MEDIA_OUTREACH\",\"is_quantity_unlocked\":true}',	0,	1,	0);






INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Premium Plus State Newsline',	'premium_plus_state',	'premium-plus-state-credit',	null,	NULL,	'Credit for a Premium Plus State Newsline Press Release.',	NULL,	579,	md5('9a7a2asasddfc0010a9ee2d'),	NULL,	'item_order_premium_plus_state',	'{\"bundled_email_credits\":150,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus State Newsline',	'premium_plus_state',	'premium-plus-state-credit-platinum',	'Platinum Users',	NULL,	'Credit for a Premium Plus State Newsline Press Release.',	NULL,	375,	md5('9a7a2c0a12213124sfgh011a9ee2d'),	NULL,	'item_order_premium_plus_state',	'{\"bundled_email_credits\":150,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus State Newsline',	'premium_plus_state',	'premium-plus-state-credit-gold',	'Gold Users',	NULL,	'Credit for a Premium Plus State Newsline Press Release.',	NULL,	425,	md5('9a7a2c0da1451515fh012a9ee2d'),	NULL,	'item_order_premium_plus_state',	'{\"bundled_email_credits\":150,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus State Newsline',	'premium_plus_state',	'premium-plus-state-credit-silver',	'Silver Users',	NULL,	'Credit for a Premium Plus State Newsline Press Release.',	NULL,	510,	md5('9a7a2c0ae4af1616161616013a9ee2d'),	NULL,	'item_order_premium_plus_state',	'{\"bundled_email_credits\":150,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0);




INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Premium Financial',	'premium_financial',	'premium-financial-credit',	null,	NULL,	'Credit for a Premium Financial Press Release.',	NULL,	649,	md5('9aasddfc0010a9ee2d'),	NULL,	'item_order_premium_financial',	'{\"bundled_email_credits\":150,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Financial',	'premium_financial',	'premium-financial-credit-platinum',	'Platinum Users',	NULL,	'Credit for a Premium Financial Press Release.',	NULL,	486.75,	md5('9a7a2c0a12213124sfghd'),	NULL,	'item_order_premium_financial',	'{\"bundled_email_credits\":150,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Financial',	'premium_financial',	'premium-financial-credit-gold',	'Gold Users',	NULL,	'Credit for a Premium Financial Press Release.',	NULL,	552,	md5('9a71515fh012ae2d'),	NULL,	'item_order_premium_financial',	'{\"bundled_email_credits\":150,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Financial',	'premium_financial',	'premium-financial-credit-silver',	'Silver Users',	NULL,	'Credit for a Premium Financial Press Release.',	NULL,	584.10,	md5('9a7ee2d'),	NULL,	'item_order_premium_financial',	'{\"bundled_email_credits\":150,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0);




INSERT INTO `nr_iella_event` (`name`, `method`)
VALUES ('item_order_premium_plus', 'api/iella/item_event/order_premium_plus');

INSERT INTO `nr_iella_event` (`name`, `method`)
VALUES ('item_order_premium_plus_state', 'api/iella/item_event/order_premium_plus_state');

INSERT INTO `nr_iella_event` (`name`, `method`)
VALUES ('item_order_premium_financial', 'api/iella/item_event/order_premium_financial');





DELETE FROM `nr_iella_event`
WHERE ((`name` = 'item_order_release_plus' AND `name` = 'item_order_release_plus' COLLATE utf8mb4_bin AND `method` = 'api/iella/item_event/order_release_plus' AND `method` = 'api/iella/item_event/order_release_plus' COLLATE utf8mb4_bin));




UPDATE `co_item` SET
`order_event` = 'item_order_premium_plus'
WHERE `name` LIKE '%PR Newswire%' AND `is_custom` = '0' AND ((`id` = '36') OR (`id` = '37') OR (`id` = '101') OR (`id` = '102') OR (`id` = '103') OR (`id` = '171'));



update nr_limit_common_held set type = 'PREMIUM_PLUS' where type = 'RP_PRNEWSWIRE';


DROP TABLE IF EXISTS `nr_content_distribution_bundle`;
CREATE TABLE `nr_content_distribution_bundle` (
  `content_id` int(11) NOT NULL,
  `bundle` varchar(32) NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL,
  `raw_data` blob,
  PRIMARY KEY (`content_id`),
  KEY `bundle` (`bundle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DELETE FROM `nr_setting`
WHERE ((`name` = 'publish_max_attempts' AND `name` = 'publish_max_attempts' COLLATE utf8mb4_bin));


ALTER TABLE `nr_content`
DROP `date_attempt`,
DROP `is_on_credit_hold`;





INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Media Database Full Access',	'media_database',	'media-database-access-gold',	'Gold Users',	NULL,	'Full unobfuscated access to our media database and all our press contacts.',	null,	254,	md5('56706asdf454d34775d4'),	'item_activate_mdb_plus',	'item_order_component',	'{\"period\":30,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Media Database Full Access',	'media_database',	'media-database-access-silver',	'Silver Users',	NULL,	'Full unobfuscated access to our media database and all our press contacts.',	null,	269,	md5('de44easdf62874f00b89'),	'item_activate_mdb_plus',	'item_order_component',	'{\"period\":30,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0),
(null,	'Media Database Full Access',	'media_database',	'media-database-access',	'',	NULL,	'Full unobfuscated access to our media database and all our press contacts.',	null,	299,	md5('24430275yab7027f8499'),	'item_activate_mdb_plus',	'item_order_component',	'{\"period\":30,\"is_renewable\":1,\"is_auto_renew_enabled\":1}',	0,	1,	0);


INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Extra 100 Words',	'extra_100_words',	'extra-100-words',	'Used with PR Newswire',	NULL,	'',	null,	125,	md5('asdasdasdaserabv'),	null,	null,	null,	0,	0,	0);


UPDATE `co_item` SET
`id` = '36',
`name` = 'Premium with PR Newswire',
`tracking` = 'release_plus_prnewswire',
`slug` = 'release-plus-prnewswire-subscriber',
`comment` = '',
`descriptor` = NULL,
`help_text` = 'Credit for Premium Press Release with PR Newswire distribution.',
`type` = NULL,
`price` = '159',
`secret` = '0d2547528691d252',
`activate_event` = NULL,
`order_event` = 'item_order_premium_plus',
`is_disabled` = '0',
`is_listed` = '1',
`is_custom` = '0'
WHERE `id` = '36';


update `co_plan_extra_credit` set type = 'PREMIUM_PLUS' where type = 'RP_PRNEWSWIRE';
update `co_plan_credit` set type = 'PREMIUM_PLUS' where type = 'RP_PRNEWSWIRE';


UPDATE `co_plan_credit` SET
`available` = '0'
WHERE ((`id` = '2') OR (`id` = '6') OR (`id` = '10'));


INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'PR Revision Writing',	'pr_revision_writing',	'pr-revision-writing-platinum',	'Platinum Users',	NULL,	'Have us professionally revise your press release.',	null,	90,	md5('5670asd6asdf454d34775d4'),	'item_activate_custom',	'item_order_custom',	null,	0,	1,	0),
(null,	'PR Revision Writing',	'pr_revision_writing',	'pr-revision-writing-gold',	'Gold Users',	NULL,	'Have us professionally revise your press release.',	null,	102,	md5('5670asd6asddfgf454d34775d4'),	'item_activate_custom',	'item_order_custom',	null,	0,	1,	0),
(null,	'PR Revision Writing',	'pr_revision_writing',	'pr-revision-writing-silver',	'Silver Users',	NULL,	'Have us professionally revise your press release.',	null,	108,	md5('de444easdf62874f00b89'),	'item_activate_custom',	'item_order_custom',	null,	0,	1,	0),
(null,	'PR Revision Writing',	'pr_revision_writing',	'pr-revision-writing',	'',	NULL,	'Have us professionally revise your press release.',	null,	120,	md5('24430275yasvfb7027f8499'),	'item_activate_custom',	'item_order_custom',	null,	0,	1,	0);





INSERT INTO `nr_iella_event` (`name`, `method`)
SELECT 'item_order_premium', 'api/iella/item_event/order_premium'
FROM `nr_iella_event`
WHERE ((`name` = 'item_order_premium_financial' AND `name` = 'item_order_premium_financial' COLLATE utf8mb4_bin AND `method` = 'api/iella/item_event/order_premium_financial' AND `method` = 'api/iella/item_event/order_premium_financial' COLLATE utf8mb4_bin));





--  ---------------------------
--  ---------------------------



INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Premium Plus National',	'asi_premium_plus_national',	'premium-plus-national-credit',	null,	NULL,	'Credit for a Premium Plus National Press Release.',	'CREDIT',	999,	md5('9a0000c0010a9ee2d'),	NULL,	'item_order_premium_plus_national',	'{\"bundled_email_credits\":350,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus National',	'asi_premium_plus_national',	'premium-plus-national-credit-platinum',	'Platinum Users',	NULL,	'Credit for a Premium Plus National Press Release.',	'CREDIT',	900,	md5('9a00003124sfgh011a9ee2d'),	NULL,	'item_order_premium_plus_national',	'{\"bundled_email_credits\":350,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus National',	'asi_premium_plus_national',	'premium-plus-national-credit-gold',	'Gold Users',	NULL,	'Credit for a Premium Plus National Press Release.',	'CREDIT',	925,	md5('9a00001515fh012a9ee2d'),	NULL,	'item_order_premium_plus_national',	'{\"bundled_email_credits\":350,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0),
(null,	'Premium Plus National',	'asi_premium_plus_national',	'premium-plus-national-credit-silver',	'Silver Users',	NULL,	'Credit for a Premium Plus State Newsline Press Release.',	'CREDIT',	959,	md5('9a00001616161616013a9ee2d'),	NULL,	'item_order_premium_plus_national',	'{\"bundled_email_credits\":350,\"is_quantity_unlocked\":true,\"terms_html\":\"You also confirm that you have read and agree to PR Newswire\'s <a target=\\\"_blank\\\" href=\\\"pr-newswire-terms-and-conditions\\\">terms and conditions</a>.\"}',	0,	1,	0);





INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'Extra 100 Words',	'asi_extra_100_words',	'ppn-extra-100-words',	'Premium Plus National',	NULL,	'',	null,	225,	md5('asdasda333sdaserabv'),	null,	null,	null,	0,	0,	0);


UPDATE `co_item` SET
`id` = '465',
`name` = 'Extra 100 Words',
`tracking` = 'asi_extra_100_words',
`slug` = 'pps-extra-100-words',
`comment` = 'Premium Plus State',
`descriptor` = NULL,
`help_text` = '',
`type` = NULL,
`price` = '125',
`secret` = '5aac1640be6bceae',
`activate_event` = NULL,
`order_event` = NULL,
`raw_data` = NULL,
`is_disabled` = '0',
`is_listed` = '0',
`is_custom` = '0'
WHERE `slug` = 'extra-100-words';


INSERT INTO `nr_iella_event` (`name`, `method`)
SELECT 'item_order_premium_plus_national', 'api/iella/item_event/order_premium_plus_national'
FROM `nr_iella_event`
WHERE ((`name` = 'item_order_premium_plus_state' AND `name` = 'item_order_premium_plus_state' COLLATE utf8mb4_bin AND `method` = 'api/iella/item_event/order_premium_plus_state' AND `method` = 'api/iella/item_event/order_premium_plus_state' COLLATE utf8mb4_bin));

INSERT INTO `nr_iella_event` (`name`, `method`)
VALUES ('item_order_microlist', 'api/iella/item_event/order_microlist');

DROP TABLE IF EXISTS `nr_content_distribution_extras`;
CREATE TABLE `nr_content_distribution_extras` (
  `content_id` int(11) NOT NULL,
  `raw_data` longblob NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


UPDATE `co_item` SET
`is_disabled` = '0'
WHERE ((`id` = '20') OR (`id` = '21') OR (`id` = '22') OR (`id` = '23'));


INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'PRN Image Distribution',	'asi_prn_image_distribution',	'prn-image-distribution',	'',	NULL,	'',	null,	200,	md5('assssvvvvvvvvve4'),	null,	'item_order_prn_image',	null,	0,	0,	0);

INSERT INTO `nr_iella_event` (`name`, `method`)
VALUES ('item_order_prn_image', 'api/iella/item_event/order_prn_image');

ALTER TABLE `nr_pb_pr`
ADD `outreach_email_country` int NOT NULL;

-- -------------------------------------
-- --------------------------------------
--    28 July 2016
-- ---------------------------

INSERT INTO `co_item` (`id`, `name`, `tracking`, `slug`, `comment`, `descriptor`, `help_text`, `type`, `price`, `secret`, `activate_event`, `order_event`, `raw_data`, `is_disabled`, `is_listed`, `is_custom`) VALUES
(null,	'PRN Video Distribution',	'asi_prn_video_distribution',	'prn-video-distribution',	'',	NULL,	'',	NULL,	200,	md5('axxxxxxxxxxc5dfeaa3f2dd38e'),	NULL,	'item_order_prn_video',	NULL,	0,	0,	0);


INSERT INTO `nr_iella_event` (`name`, `method`)
SELECT 'item_order_prn_video', 'api/iella/item_event/order_prn_video'
FROM `nr_iella_event`
WHERE ((`name` = 'item_order_prn_image' AND `name` = 'item_order_prn_image' COLLATE utf8mb4_bin AND `method` = 'api/iella/item_event/order_prn_image' AND `method` = 'api/iella/item_event/order_prn_image' COLLATE utf8mb4_bin));