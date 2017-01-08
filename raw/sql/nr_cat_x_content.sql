CREATE TABLE IF NOT EXISTS nr_cat_x_content (
  cat_id int(11) NOT NULL,
  content_id int(11) NOT NULL,
  UNIQUE cat_content (cat_id, content_id),
  INDEX content_cat (content_id, cat_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
