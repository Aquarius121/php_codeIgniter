DROP TABLE IF EXISTS nr_cat_x_content_builder;
CREATE TABLE nr_cat_x_content_builder (
  cat_id int(11) NOT NULL,
  content_id int(11) NOT NULL,
  UNIQUE cat_content (cat_id, content_id),
  INDEX content_cat (content_id, cat_id)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

INSERT IGNORE INTO nr_cat_x_content_builder
	SELECT cat_1_id AS cat_id, content_id FROM nr_pb_pr
	WHERE cat_1_id > 0;

INSERT IGNORE INTO nr_cat_x_content_builder
	SELECT cat_2_id AS cat_id, content_id FROM nr_pb_pr 
	WHERE cat_2_id > 0;

INSERT IGNORE INTO nr_cat_x_content_builder
	SELECT cat_3_id AS cat_id, content_id FROM nr_pb_pr
	WHERE cat_3_id > 0;

INSERT IGNORE INTO nr_cat_x_content_builder
	SELECT cat_1_id AS cat_id, content_id FROM nr_pb_news
	WHERE cat_1_id > 0;

INSERT IGNORE INTO nr_cat_x_content_builder
	SELECT cat_2_id AS cat_id, content_id FROM nr_pb_news
	WHERE cat_2_id > 0;

INSERT IGNORE INTO nr_cat_x_content_builder
	SELECT cat_3_id AS cat_id, content_id FROM nr_pb_news
	WHERE cat_3_id > 0;

-- This is the slow bit. Would tmpfs help here?
ALTER TABLE nr_cat_x_content_builder ENGINE=InnoDB;

RENAME TABLE nr_cat_x_content TO nr_cat_x_content_discard;
RENAME TABLE nr_cat_x_content_builder TO nr_cat_x_content;
DROP TABLE nr_cat_x_content_discard;