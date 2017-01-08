<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/base');
load_controller('writing/common_writing_trait');

class Main_Controller extends Iella_Base {
	
	use Common_Writing_Trait;	
	
	public function get_writer_new_writing_tasks()
	{
		$writer_id = $this->iella_in->writer_id;
		
		$chunk = $this->iella_in->chunk;		
		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();
		
		$pr_wr_list = sql_in_list(array(Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER, 
						Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION, 
						Model_Writing_Order::STATUS_SENT_BACK_TO_WRITER, 
						Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE, 
						Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS,
						Model_Writing_Order::STATUS_REVISED_DETAILS_ACCEPTED));
						
		$pw_wr_list = sql_in_list(array(Model_Pitch_Order::STATUS_ASSIGNED_TO_WRITER, 
						Model_Pitch_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION, 
						Model_Pitch_Order::STATUS_SENT_BACK_TO_WRITER, 
						Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE, 
						Model_Pitch_Order::STATUS_CUSTOMER_REVISE_DETAILS));				
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				t.company_id as company_id, 
				w.content_id,
				w.id as id, 
				c.name as company_name,
				tc.writing_order_code as writing_order_code, 
				w.primary_keyword as primary_keyword,
				w.status as status,
				w.writing_angle as writing_angle,
				w.angle_detail as angle_detail,
				cp.summary as company_summary,
				cat1.name as category_1_name,
				cat2.name as category_2_name,
				'PR' as task_type,
				w.latest_status_date as date_of_last_status

				FROM rw_writing_order_code tc 
				INNER JOIN rw_writing_order w 
				ON tc.id = w.writing_order_code_id 
				INNER JOIN nr_content t
				ON w.content_id = t.id
				INNER JOIN nr_company c 
				ON t.company_id = c.id 
				INNER JOIN nr_company_profile cp 
				ON cp.company_id = c.id
				LEFT JOIN nr_pb_pr pb
				ON pb.content_id = t.id
				LEFT JOIN nr_cat cat1
				ON pb.cat_1_id = cat1.id
				LEFT JOIN nr_cat cat2
				ON pb.cat_2_id = cat2.id
				WHERE w.status IN ({$pr_wr_list}) 
				AND w.writer_id = ?
				
				UNION
				SELECT  ca.company_id, 
				ca.content_id, 
				po.id as pitch_order_id,
				co.name as company_name,
				po.delivery,				
				po.keyword as primary_keyword,
				po.status as status,
				'Media Pitch' as writing_angle,
				po.pitch_highlight as pitch_highlight,
				po.additional_comments,				
				bt1.name as category_name,
				bt2.name as category_2_name,
				'Pitch' as task_type,
				po.date_of_last_status

				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN pw_pitch_content pc
				ON pc.pitch_order_id = po.id
				LEFT JOIN nr_content c 				
				ON ca.content_id = c.id
				LEFT JOIN nr_company co
				ON ca.company_id = co.id
				LEFT JOIN nr_beat bt1
				
				ON po.beat_1_id = bt1.id
				LEFT JOIN nr_beat bt2
				ON po.beat_2_id = bt2.id
				WHERE po.status IN ({$pw_wr_list})
				AND po.writer_id = ?
				ORDER BY date_of_last_status DESC
				{$limit_str}";
				
		$query = $this->db->query($sql, array($writer_id, $writer_id));
		
		$total_results = $this->db
						->query("SELECT FOUND_ROWS() AS count")
						->row()->count;

		$chunkination->set_total($total_results);				
						
		$tasks = Model_Content::from_db_all($query);

		foreach ($tasks as $task)
			if ($task->task_type == "PR")
			{
				$m_content = Model_Content::find($task->content_id);
				$beats = $m_content->get_beats();
				if (count($beats))
				{
					$task->category_1_name = null;
					$task->category_2_name = null;
				

					foreach ($beats as $i => $beat)
						if ($i == 0)
							$task->category_1_name = $beat->name;
						elseif ($i == 1)
							$task->category_2_name = $beat->name;
				}
			}
					

		
			
		
		$this->iella_out->total_results = $total_results;
		$this->iella_out->tasks = $tasks;
		$this->iella_out->success = true;
		$this->send();						
	}
	
	public function get_writer_pending_writing_tasks()
	{
		$writer_id = $this->iella_in->writer_id;		
		
		$chunk = $this->iella_in->chunk;		
		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();
		
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				t.company_id as company_id, 
				w.content_id,
				w.id as id, c.name as company_name,
				tc.writing_order_code as writing_order_code, 
				w.primary_keyword as primary_keyword,
				w.status as status,
				w.writing_angle as writing_angle,
				cp.summary as company_summary,
				cat1.name as category_1_name,
				cat2.name as category_2_name,
				'PR' as task_type,
				w.latest_status_date as date_of_last_status
				
				FROM rw_writing_order_code tc 
				INNER JOIN rw_writing_order w 
				ON tc.id = w.writing_order_code_id  
				INNER JOIN nr_content t
				ON w.content_id = t.id
				INNER JOIN nr_company c 
				ON t.company_id = c.id 
				INNER JOIN nr_company_profile cp 
				ON cp.company_id = c.id
				LEFT JOIN nr_pb_pr pb
				ON pb.content_id = t.id
				LEFT JOIN nr_cat cat1
				ON pb.cat_1_id = cat1.id
				LEFT JOIN nr_cat cat2
				ON pb.cat_2_id = cat2.id
				WHERE w.status = ? 
				AND w.writer_id = ?
				
				UNION
				
				SELECT  ca.company_id, 
				ca.content_id, 
				po.id as pitch_order_id,
				co.name as company_name,
				po.delivery,				
				po.keyword as primary_keyword,
				po.status as status,
				'Media Pitch' as writing_angle,
				po.additional_comments,				
				bt1.name as category_name,
				bt2.name as category_2_name,
				'Pitch' as task_type,
				po.date_of_last_status

				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN pw_pitch_content pc
				ON pc.pitch_order_id = po.id
				LEFT JOIN nr_content c 				
				ON ca.content_id = c.id
				LEFT JOIN nr_company co
				ON ca.company_id = co.id
				LEFT JOIN nr_beat bt1
				
				ON po.beat_1_id = bt1.id
				LEFT JOIN nr_beat bt2
				ON po.beat_2_id = bt2.id
				WHERE po.status = ?
				AND po.writer_id = ?
				
				ORDER BY date_of_last_status DESC
				{$limit_str}
				
				";
				
		$query = $this->db->query($sql, array(Model_Writing_Order::STATUS_RESELLER_REJECTED, $writer_id,
											Model_Pitch_Order::STATUS_ADMIN_REJECTED, $writer_id));
		
		$total_results = $this->db
						->query("SELECT FOUND_ROWS() AS count")
						->row()->count;

		$chunkination->set_total($total_results);
													
		$tasks = Model_Content::from_db_all($query);

		foreach ($tasks as $task)
			if ($task->task_type == "PR")
			{
				$m_content = Model_Content::find($task->content_id);
				$beats = $m_content->get_beats();
				if (count($beats))
				{
					$task->category_1_name = null;
					$task->category_2_name = null;
				

					foreach ($beats as $i => $beat)
						if ($i == 0)
							$task->category_1_name = $beat->name;
						elseif ($i == 1)
							$task->category_2_name = $beat->name;
				}
			}
		
		$this->iella_out->total_results = $total_results;
		$this->iella_out->tasks = $tasks;
		$this->iella_out->success = true;
		$this->send();
	}
	
	public function get_writer_completed_writing_tasks()
	{
		$writer_id = $this->iella_in->writer_id;
		$chunk = $this->iella_in->chunk;
		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();
		
		$pr_wr_list = sql_in_list(array(Model_Writing_Order::STATUS_CUSTOMER_ACCEPTED, 
									Model_Writing_Order::STATUS_APPROVED));						
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				co.company_id as company_id, 
				w.content_id, w.id as id, 
				c.name as company_name,
				tc.writing_order_code as writing_order_code, 
				w.primary_keyword as primary_keyword,
				w.status as status,
				w.writing_angle as writing_angle,
				cp.summary as company_summary,
				cat1.name as category_1_name,
				cat2.name as category_2_name,
				'PR' as task_type,
				w.latest_status_date as date_of_last_status
				
				FROM rw_writing_order_code tc 
				INNER JOIN rw_writing_order w 
				ON tc.id = w.writing_order_code_id 
				INNER JOIN nr_content co
				ON w.content_id = co.id
				INNER JOIN nr_company c 
				ON co.company_id = c.id 
				INNER JOIN nr_company_profile cp 
				ON cp.company_id = c.id				
				LEFT JOIN nr_pb_pr pb
				ON pb.content_id = co.id
				LEFT JOIN nr_cat cat1
				ON pb.cat_1_id = cat1.id
				LEFT JOIN nr_cat cat2
				ON pb.cat_2_id = cat2.id
				WHERE w.status IN ({$pr_wr_list})
				AND w.writer_id = ?			
				
				UNION
				
				SELECT  ca.company_id, 
				ca.content_id, 
				po.id as pitch_order_id,
				co.name as company_name,
				po.delivery,				
				po.keyword as primary_keyword,
				po.status as status,
				'Media Pitch' as writing_angle,
				po.additional_comments,				
				bt1.name as category_name,
				bt2.name as category_2_name,
				'Pitch' as task_type,
				po.date_of_last_status

				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN pw_pitch_content pc
				ON pc.pitch_order_id = po.id
				LEFT JOIN nr_content c 				
				ON ca.content_id = c.id
				LEFT JOIN nr_company co
				ON ca.company_id = co.id
				LEFT JOIN nr_beat bt1
				
				ON po.beat_1_id = bt1.id
				LEFT JOIN nr_beat bt2
				ON po.beat_2_id = bt2.id
				WHERE po.status = ?
				AND po.writer_id = ?
				
				ORDER BY date_of_last_status DESC
				{$limit_str}";
				
		
		$query = $this->db->query($sql, array($writer_id, Model_Pitch_Order::STATUS_CUSTOMER_ACCEPTED, $writer_id));	
				
		$total_results = $this->db
						->query("SELECT FOUND_ROWS() AS count")
						->row()->count;
		
		$chunkination->set_total($total_results);
			
		$tasks = Model_Content::from_db_all($query, array($writer_id));

		foreach ($tasks as $task)
			if ($task->task_type == "PR")
			{
				$m_content = Model_Content::find($task->content_id);
				$beats = $m_content->get_beats();
				if (count($beats))
				{
					$task->category_1_name = null;
					$task->category_2_name = null;
				

					foreach ($beats as $i => $beat)
						if ($i == 0)
							$task->category_1_name = $beat->name;
						elseif ($i == 1)
							$task->category_2_name = $beat->name;
				}
			}
		
		$chunkination->set_url_format('writing_task/completed_tasks/-chunk-');
		
		$this->iella_out->total_results = $total_results;
		
		$this->iella_out->tasks = $tasks;
		$this->iella_out->success = true;
		$this->send();
	}
	
	
	public function get_writer_under_review_writing_tasks()
	{
		$writer_id = $this->iella_in->writer_id;
			
		$chunk = $this->iella_in->chunk;		
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(10);
		$limit_str = $chunkination->limit_str();	
		
		$pr_wr_list = sql_in_list(array(Model_Writing_Order::STATUS_WRITTEN_SENT_TO_RESELLER, 
						Model_Writing_Order::STATUS_SENT_TO_CUSTOMER, 
						Model_Writing_Order::STATUS_CUSTOMER_REJECTED));
						
		$pw_wr_list = sql_in_list(array(Model_Pitch_Order::STATUS_WRITTEN_SENT_TO_ADMIN, 
						Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER, 
						Model_Pitch_Order::STATUS_CUSTOMER_REJECTED));	
						
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				co.company_id as company_id, 
				w.content_id, w.id as id, 
				c.name as company_name,
				tc.writing_order_code as writing_order_code, 
				w.primary_keyword as primary_keyword,
				w.status as status,
				w.writing_angle as writing_angle,
				cp.summary as company_summary,
				cat1.name as category_1_name,
				cat2.name as category_2_name,
				'PR' as task_type,
				w.latest_status_date as date_of_last_status
				
				FROM rw_writing_order_code tc 
				INNER JOIN rw_writing_order w 
				ON tc.id = w.writing_order_code_id 
				INNER JOIN nr_content co
				ON w.content_id = co.id
				INNER JOIN nr_company c 
				ON co.company_id = c.id 
				INNER JOIN nr_company_profile cp 
				ON cp.company_id = c.id				
				LEFT JOIN nr_pb_pr pb
				ON pb.content_id = co.id
				LEFT JOIN nr_cat cat1
				ON pb.cat_1_id = cat1.id
				LEFT JOIN nr_cat cat2
				ON pb.cat_2_id = cat2.id
				WHERE w.status IN ({$pr_wr_list})
				AND w.writer_id = ?			
				
				UNION
				
				SELECT  ca.company_id, 
				ca.content_id, 
				po.id as pitch_order_id,
				co.name as company_name,
				po.delivery,				
				po.keyword as primary_keyword,
				po.status as status,
				'Media Pitch' as writing_angle,
				po.additional_comments,				
				bt1.name as category_name,
				bt2.name as category_2_name,
				'Pitch' as task_type,
				po.date_of_last_status

				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN pw_pitch_content pc
				ON pc.pitch_order_id = po.id
				LEFT JOIN nr_content c 				
				ON ca.content_id = c.id
				LEFT JOIN nr_company co
				ON ca.company_id = co.id
				LEFT JOIN nr_beat bt1
				
				ON po.beat_1_id = bt1.id
				LEFT JOIN nr_beat bt2
				ON po.beat_2_id = bt2.id
				WHERE po.status IN ({$pw_wr_list})
				AND po.writer_id = ?
				
				ORDER BY date_of_last_status DESC
				{$limit_str}
				";
				
		$query = $this->db->query($sql, array($writer_id, $writer_id));
		
		$total_results = $this->db
						->query("SELECT FOUND_ROWS() AS count")
						->row()->count;
						
		$tasks = Model_Content::from_db_all($query);

		foreach ($tasks as $task)
			if ($task->task_type == "PR")
			{
				$m_content = Model_Content::find($task->content_id);
				$beats = $m_content->get_beats();
				if (count($beats))
				{
					$task->category_1_name = null;
					$task->category_2_name = null;
				

					foreach ($beats as $i => $beat)
						if ($i == 0)
							$task->category_1_name = $beat->name;
						elseif ($i == 1)
							$task->category_2_name = $beat->name;
				}
			}
		
		$this->iella_out->total_results = $total_results;
		
		$this->iella_out->tasks = $tasks;
		$this->iella_out->success = true;
		$this->send();
	}
	
	public function get_writer_dashboard_details()
	{
		$writer_id=$this->iella_in->writer_id;		
		
		$pr_wr_list = sql_in_list(array(Model_Writing_Order::STATUS_ASSIGNED_TO_WRITER, 
						Model_Writing_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION, 
						Model_Writing_Order::STATUS_SENT_BACK_TO_WRITER, 
						Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE, 
						Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS,
						Model_Writing_Order::STATUS_REVISED_DETAILS_ACCEPTED));
						
		$pw_wr_list = sql_in_list(array(Model_Pitch_Order::STATUS_ASSIGNED_TO_WRITER, 
						Model_Pitch_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION, 
						Model_Pitch_Order::STATUS_SENT_BACK_TO_WRITER, 
						Model_Pitch_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE, 
						Model_Pitch_Order::STATUS_CUSTOMER_REVISE_DETAILS));				
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				w.id as id,	
				c.name as company_name,			
				tc.writing_order_code as writing_order_code, 				
				w.status as status,
				w.writing_angle as writing_angle,				
				'PR' as task_type,
				w.latest_status_date as date_of_last_status
				FROM rw_writing_order_code tc 
				INNER JOIN rw_writing_order w 
				ON tc.id = w.writing_order_code_id 
				INNER JOIN nr_content t
				ON w.content_id = t.id
				INNER JOIN nr_company c 
				ON t.company_id = c.id 						
				WHERE w.status IN ({$pr_wr_list}) 
				AND w.writer_id = ?
				
				UNION
				
				SELECT  				
				po.id as pitch_order_id,
				co.name as company_name,
				po.delivery,
				po.status as status,
				'Media Pitch' as writing_angle,				
				'Pitch' as task_type,
				po.date_of_last_status

				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN pw_pitch_content pc
				ON pc.pitch_order_id = po.id
				LEFT JOIN nr_content c 				
				ON ca.content_id = c.id
				LEFT JOIN nr_company co
				ON ca.company_id = co.id				
				WHERE po.status IN ({$pw_wr_list})
				AND po.writer_id = ?
				ORDER BY date_of_last_status DESC
				LIMIT 0, 5";
				
		$query = $this->db->query($sql, array($writer_id, $writer_id));		
		$total_results = $this->db
							->query("SELECT FOUND_ROWS() AS count")
							->row()->count;
							
		$this->iella_out->new_tasks_count = $total_results;					
		$tasks = Model_Content::from_db_all($query);	
		$this->iella_out->new_tasks = $tasks;
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
				w.id as id,	
				c.name as company_name,			
				tc.writing_order_code as writing_order_code, 				
				w.status as status,
				w.writing_angle as writing_angle,				
				'PR' as task_type,
				w.latest_status_date as date_of_last_status
				FROM rw_writing_order_code tc 
				INNER JOIN rw_writing_order w 
				ON tc.id = w.writing_order_code_id 
				INNER JOIN nr_content t
				ON w.content_id = t.id
				INNER JOIN nr_company c 
				ON t.company_id = c.id 						
				WHERE w.status = ?
				AND w.writer_id = ?
				
				UNION
				
				SELECT  				
				po.id as pitch_order_id,
				co.name as company_name,
				po.delivery,
				po.status as status,
				'Media Pitch' as writing_angle,				
				'Pitch' as task_type,
				po.date_of_last_status

				FROM nr_campaign ca
				INNER JOIN pw_pitch_order po 
				ON po.campaign_id = ca.id
				LEFT JOIN pw_pitch_content pc
				ON pc.pitch_order_id = po.id
				LEFT JOIN nr_content c 				
				ON ca.content_id = c.id
				LEFT JOIN nr_company co
				ON ca.company_id = co.id				
				WHERE po.status = ?
				AND po.writer_id = ?
				ORDER BY date_of_last_status DESC
				LIMIT 0, 5
				
				";
				
		$query = $this->db->query($sql, array(Model_Writing_Order::STATUS_RESELLER_REJECTED, $writer_id,
											Model_Pitch_Order::STATUS_ADMIN_REJECTED, $writer_id));											
			
		$total_results = $this->db
							->query("SELECT FOUND_ROWS() AS count")
							->row()->count;
		$this->iella_out->pending_tasks_count = $total_results;
		$tasks = array();
		foreach ($query->result() as $result)
		{
			$result->writing_angle_desc = Model_Writing_Order::full_angle_name($result->writing_angle);
			$tasks[] = $result;	
		}
		$this->iella_out->pending_tasks = $tasks;
		
		$sql="select count(id) AS count from rw_writing_order where 
				writer_id = ? AND
				status in ('customer_accepted', 'approved')";
		$query = $this->db->query($sql, array($writer_id));
		$this->iella_out->completed_tasks_count = $query->row()->count;

		$this->iella_out->success = true;
		$this->send();
	}
	
	
	
	
}

?>

