<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Send_Reseller_Report_Emails_Controller extends CLI_Base {
		
	public function index()
	{
		$now_str = Date::$now->format(Date::FORMAT_MYSQL);
		$sql = "SELECT c.company_id, c.id, c.title, 
			woc.customer_name, woc.customer_email, 
			rd.company_name AS reseller_name,
			u.email AS reseller_email,
			c.date_publish
			FROM rw_writing_order wo INNER JOIN	rw_writing_order_code woc
			ON wo.writing_order_code_id = woc.id
			INNER JOIN nr_content c ON wo.content_id = c.id
			AND c.type = ? AND c.is_legacy = 0 AND c.is_published = 1
			AND c.date_publish <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 DAY)
			INNER JOIN nr_company cm ON c.company_id = cm.id
			INNER JOIN rw_reseller_details rd
			ON woc.reseller_id = rd.user_id
			INNER JOIN nr_user u ON rd.user_id = u.id
			LEFT JOIN rw_report_sent rs ON c.id = rs.content_id
			WHERE rs.date_sent IS NULL";
				
		$query = $this->db->query($sql, array(Model_Content::TYPE_PR));
		if (!$query->num_rows()) return;
		
		$results = array();
		$sent_ids = array();
		
		foreach ($query->result() as $result)
		{
			$data = array();
			$data['date_sent'] = $now_str;
			$data['content_id'] = $result->id;
			$this->db->insert('rw_report_sent', $data);
			$results[] = $result;
		}
		
		foreach ($results as $result)
		{
			set_time_limit(300);			
			if (!$result->customer_email)
				continue;
			
			// generate report from Analytics directly
			$newsroom = Model_Newsroom::find_company_id($result->company_id);
			$url = "manage/analyze/content/dist_index/{$result->id}";
			$url = $newsroom->url($url);
			$dist = new PDF_Generator($url);
			$dist_file = $dist->generate();
			
			// send from reseller or
			// fallback to Newswire
			if ($result->reseller_email)
			     $from_email = $result->reseller_email;
			else $from_email = $this->conf('email_address');
			
			// generate email based on title, date
			$content = $this->load->view('reseller/report/email', 
				array('result' => $result), true);
			
			$em = new Email();
			$em->set_subject('PR Distribution Report');
			$em->set_to_email($result->customer_email);
			$em->set_from_email($from_email);
			$em->set_message($content);
			$em->enable_html();
			$em->add_attachment($dist_file, 'report.pdf');			
			Mailer::send($em, Mailer::POOL_TRANSACTIONAL);
			
			unlink($dist_file);
		}
	}
	
}

?>