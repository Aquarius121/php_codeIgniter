INSERT ignore into nr_beat_x_content (beat_id, content_id)
select ctb.beat_id, p.content_id from nr_pb_pr p inner join nr_cat_to_beat ctb 
on (ctb.cat_id = p.cat_1_id or ctb.cat_id = p.cat_2_id or ctb.cat_id = p.cat_3_id);