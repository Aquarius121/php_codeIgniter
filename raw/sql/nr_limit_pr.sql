DROP VIEW IF EXISTS nr_limit_pr;

CREATE VIEW nr_limit_pr AS

SELECT 
	up.user_id AS user_id,
	up.id AS limit_id,
	p.package AS package,
	upc.total AS amount_total,
	upc.used AS amount_used,
	upc.period AS period,  
	if (pc.type = 'PREMIUM_PR', 'PREMIUM', 'BASIC') AS type,
	if (upc.period IN ('DAILY', 'WEEKLY', 'MONTHLY'), 1, 0) 
		AS uses_calculated,
	up.date_expires AS date_expires
FROM
	co_plan p 
	INNER JOIN co_plan_credit pc ON p.id = pc.plan_id
		AND pc.type IN ('PREMIUM_PR', 'BASIC_PR')
	INNER JOIN co_user_plan up ON p.id = up.plan_id
		AND up.is_active = 1
	INNER JOIN co_user_plan_credit upc ON up.id = upc.user_plan_id
		AND pc.id = upc.plan_credit_id 