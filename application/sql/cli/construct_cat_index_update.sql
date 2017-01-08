INSERT IGNORE INTO nr_cat_x_content
	SELECT cat_1_id AS cat_id, content_id FROM nr_pb_pr
	WHERE cat_1_id > 0;

INSERT IGNORE INTO nr_cat_x_content
	SELECT cat_2_id AS cat_id, content_id FROM nr_pb_pr 
	WHERE cat_2_id > 0;

INSERT IGNORE INTO nr_cat_x_content
	SELECT cat_3_id AS cat_id, content_id FROM nr_pb_pr
	WHERE cat_3_id > 0;

INSERT IGNORE INTO nr_cat_x_content
	SELECT cat_1_id AS cat_id, content_id FROM nr_pb_news
	WHERE cat_1_id > 0;

INSERT IGNORE INTO nr_cat_x_content
	SELECT cat_2_id AS cat_id, content_id FROM nr_pb_news
	WHERE cat_2_id > 0;

INSERT IGNORE INTO nr_cat_x_content
	SELECT cat_3_id AS cat_id, content_id FROM nr_pb_news
	WHERE cat_3_id > 0;