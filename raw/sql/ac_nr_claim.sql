DROP VIEW IF EXISTS ac_nr_claim;

CREATE VIEW ac_nr_claim AS

SELECT company_id, rep_name, email, phone, date_claimed, remote_addr, status, date_admin_updated, is_from_private_link, date_exported_to_csv, is_paid, 'crunchbase' as source FROM ac_nr_cb_nr_claim

UNION 

SELECT company_id, rep_name, email, phone, date_claimed, remote_addr, status, date_admin_updated, is_from_private_link, date_exported_to_csv, is_paid, 'prweb' as source  FROM ac_nr_prweb_nr_claim

UNION

SELECT company_id, rep_name, email, phone, date_claimed, remote_addr, status, date_admin_updated, is_from_private_link, date_exported_to_csv, is_paid, 'marketwired' as source  FROM ac_nr_marketwired_nr_claim

UNION 

SELECT company_id, rep_name, email, phone, date_claimed, remote_addr, status, date_admin_updated, is_from_private_link, date_exported_to_csv, is_paid, 'businesswire' as source  FROM ac_nr_businesswire_nr_claim

UNION

SELECT company_id, rep_name, email, phone, date_claimed, remote_addr, status, date_admin_updated, is_from_private_link, date_exported_to_csv, is_paid, 'owler' as source  FROM ac_nr_owler_nr_claim

UNION 

SELECT company_id, rep_name, email, phone, date_claimed, remote_addr, status, date_admin_updated, is_from_private_link, date_exported_to_csv, is_paid, 'newswire_ca' as source  FROM ac_nr_newswire_ca_nr_claim

UNION

SELECT company_id, rep_name, email, phone, date_claimed, remote_addr, status, date_admin_updated, is_from_private_link, date_exported_to_csv, is_paid, 'mynewsdesk' as source  FROM ac_nr_mynewsdesk_nr_claim

UNION 

SELECT company_id, rep_name, email, phone, date_claimed, remote_addr, status, date_admin_updated, is_from_private_link, date_exported_to_csv, is_paid, 'pr_co' as source  FROM ac_nr_pr_co_nr_claim