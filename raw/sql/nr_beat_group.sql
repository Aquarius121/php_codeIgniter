DROP VIEW IF EXISTS nr_beat_group;
DROP TABLE IF EXISTS nr_beat_group;

CREATE VIEW nr_beat_group AS 

SELECT
	b.id AS id,
	b.name AS name,
	b.slug AS slug,
	b.description AS description
FROM nr_beat b
WHERE b.id = b.beat_group_id