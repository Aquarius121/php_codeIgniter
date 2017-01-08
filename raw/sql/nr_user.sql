DROP VIEW IF EXISTS nr_user;

CREATE VIEW nr_user AS 
SELECT ub.*, p.package, 1 AS is_migrated FROM 
nr_user_base ub
LEFT JOIN co_user_plan up ON ub.id = up.user_id
	AND up.date_expires > UTC_TIMESTAMP()
	AND up.is_active = 1
LEFT JOIN co_plan p ON up.plan_id = p.id