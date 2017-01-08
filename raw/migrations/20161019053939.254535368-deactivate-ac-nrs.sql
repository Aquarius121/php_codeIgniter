UPDATE nr_newsroom
SET is_active = 0
WHERE source IN ('newswire_ca', 'prweb')
AND user_id = 1;