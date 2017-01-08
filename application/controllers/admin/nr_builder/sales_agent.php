<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Sales_Agent_Controller extends Admin_Base {

	const LISTING_CHUNK_SIZE = 20;
	public $title = 'Sales Agent';

	public function index($status = null, $chunk = 1)
	{
		$chunkination = new Chunkination($chunk);
		$chunkination->set_chunk_size(static::LISTING_CHUNK_SIZE);
		$url_format = gstring("admin/nr_builder/sales_agent/-chunk-");
		$chunkination->set_url_format($url_format);
		$results = $this->fetch_results($chunkination);
		
		if ($chunkination->is_out_of_bounds()) 
		{		
			$url = "admin/nr_builder/sales_agent/";
			$this->redirect(gstring($url));
		}
		
		$this->render_list($chunkination, $results);
	}
	
	protected function fetch_results($chunkination)
	{
		$limit_str = $chunkination->limit_str();

		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* 
				FROM nr_sales_agent a 
				WHERE is_deleted = 0
				ORDER BY 
				a.id DESC {$limit_str}";
			
		$query = $this->db->query($sql);	
		
		$id_list = array();
		foreach ($query->result() as $row)
			$id_list[] = (int) $row->id;
		
		// no results found so exit
		if (!$id_list) return array();

		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;

		$chunkination->set_total($total_results);
		if ($chunkination->is_out_of_bounds())
			return array();

		$id_str = sql_in_list($id_list);
		
		$status_claimed = Model_Newsroom_Claim::STATUS_CLAIMED;
		$sql = "SELECT sa.*,
				v_export_count.export_count AS export_count,
				v_claim_count.claim_count AS claim_count,
				v_verified_count.verified_count AS verified_count,
				v_transaction_count.transaction_count AS transaction_count
				FROM nr_sales_agent sa 
				LEFT JOIN 
					(SELECT sales_agent_id, COUNT(company_id) AS export_count
						FROM ac_nr_auto_built_nr_export ne
						INNER JOIN ac_nr_auto_built_nr_export_x_company xc
						ON xc.auto_built_nr_export_id = ne.id
						GROUP BY ne.sales_agent_id) AS v_export_count 
				ON v_export_count.sales_agent_id = sa.id

				LEFT JOIN 
					(SELECT sales_agent_id, COUNT(xc.company_id) AS claim_count
						FROM ac_nr_auto_built_nr_export ne
						INNER JOIN ac_nr_auto_built_nr_export_x_company xc
						ON xc.auto_built_nr_export_id = ne.id
						INNER JOIN ac_nr_newsroom_claim cl
						ON cl.company_id = xc.company_id
						GROUP BY ne.sales_agent_id) AS v_claim_count
				ON v_claim_count.sales_agent_id = sa.id

				LEFT JOIN 
					(SELECT sales_agent_id, COUNT(xc.company_id) AS verified_count
						FROM ac_nr_auto_built_nr_export ne
						INNER JOIN ac_nr_auto_built_nr_export_x_company xc
						ON xc.auto_built_nr_export_id = ne.id
						INNER JOIN ac_nr_newsroom_claim cl
						ON cl.company_id = xc.company_id
						AND cl.status = ?
						GROUP BY ne.sales_agent_id) AS v_verified_count
				ON v_verified_count.sales_agent_id = sa.id

				LEFT JOIN
					(SELECT ne.sales_agent_id, COUNT(t.id) AS transaction_count
						FROM co_transaction t
						INNER JOIN nr_newsroom nr
						ON t.user_id = nr.user_id
						INNER JOIN ac_nr_auto_built_nr_export_x_company xc
						ON xc.company_id = nr.company_id
						INNER JOIN ac_nr_auto_built_nr_export ne
						ON xc.auto_built_nr_export_id = ne.id
						GROUP BY ne.sales_agent_id) AS v_transaction_count
				ON v_transaction_count.sales_agent_id = sa.id
				WHERE is_deleted = 0
				AND sa.id IN ({$id_str})
				ORDER BY 
				sa.id DESC";
			
		$query = $this->db->query($sql, array(Model_Newsroom_Claim::STATUS_CONFIRMED));
		$results = Model_Sales_Agent::from_db_all($query);		
		return $results;
	}
	
	protected function render_list($chunkination, $results)
	{
		$this->vd->chunkination = $chunkination;
		$this->vd->results = $results;

		$transactions_modal = new Modal();
		$transactions_modal->set_title("Sales");
		$this->add_eob($transactions_modal->render(500, 450));
		$this->vd->transactions_modal_id = $transactions_modal->id;
		
		$this->load->view('admin/header');
		$this->load->view('admin/companies/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/sales_agent/list');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function transactions($sales_agent_id)
	{
		if (!$sales_agent_id)
			return false;

		if (!$sales_agent = Model_Sales_Agent::find($sales_agent_id))
			return false;

		$sql = "SELECT DATE_FORMAT(t.date_created,'%m/%d') AS date_created, 
				DATE_FORMAT(ne.date_exported,'%m/%d') AS date_exported,
				ROUND(t.price, 2) AS price, 
				t.id, t.virtual_cart, t.is_renewal,
				nr.company_id, nr.company_name, nr.name,
				u.first_name, u.last_name, t.user_id
				
				FROM co_transaction t
				INNER JOIN nr_newsroom nr
				ON t.user_id = nr.user_id
				INNER JOIN nr_user u
				ON nr.user_id = u.id
				INNER JOIN ac_nr_auto_built_nr_export_x_company xc
				ON xc.company_id = nr.company_id
				INNER JOIN ac_nr_auto_built_nr_export ne
				ON xc.auto_built_nr_export_id = ne.id
				WHERE ne.sales_agent_id = ?
				ORDER BY t.date_created";

		$query = $this->db->query($sql, array($sales_agent_id));

		$results = Model_Newsroom::from_db_all($query);

		foreach ($results as $result)
		{
			$virtual_cart = json_decode($result->virtual_cart);
			$items = array();

			foreach ($virtual_cart->items as $item)
			{
				$m_item = Model_Item::find($item->item_id);
				$i = new stdClass();
				$i->name = $m_item->name;
				$i->quantity = $item->quantity;
				$items[] = $i;
			}

			$date_created = $result->date_created;
			
			$result->items = $items;
		}

		$this->vd->results = $results;
		$this->vd->sales_agent = $sales_agent;
		$this->load->view('admin/nr_builder/sales_agent/partials/sales_modal');
	}

	public function edit($sales_agent_id = null)
	{
		if ($this->input->post('save'))
			$this->save($sales_agent_id);

		if (!($sales_agent = Model_Sales_Agent::find($sales_agent_id)))
			$sales_agent = new Model_Sales_Agent();

		$this->vd->sales_agent = $sales_agent;

		$this->load->view('admin/header');
		$this->load->view('admin/companies/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/nr_builder/sales_agent/edit');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}
	
	public function save($sales_agent_id)
	{
		if (!$this->input->post('save'))
			return false;

		$post = $this->input->post();

		if (!$sales_agent = Model_Sales_Agent::find($sales_agent_id))
		{
			$sales_agent = new Model_Sales_Agent();
			$sales_agent->date_created = Date::$now->format(Date::FORMAT_MYSQL);
		}

		$sales_agent->first_name = $post['first_name'];
		$sales_agent->last_name = $post['last_name'];
		$sales_agent->email = $post['email'];
		
		$sales_agent->is_active = 0;

		if (!empty($post['is_active']))
			$sales_agent->is_active = $post['is_active'];

		$sales_agent->save();

		$feedback = new Feedback('success', 'Saved!', 'Successfully saved');
		$this->add_feedback($feedback);
		$this->redirect('admin/nr_builder/sales_agent');
	}

	public function delete($sales_agent_id)
	{
		if (!$sales_agent_id) return;
		if (!$sales_agent = Model_Sales_Agent::find($sales_agent_id))
			return;

		if ($this->input->post('confirm'))
		{
			$sales_agent->is_deleted = 1;
			$sales_agent->save();	

			// load feedback message 
			$feedback = new Feedback('success');
			$feedback->set_title('Deleted!');
			$feedback->set_text('Sales agent record deleted.');
			$this->add_feedback($feedback);

			$this->redirect('admin/nr_builder/sales_agent');
		}
		else
		{
			// load confirmation feedback 
			$this->vd->sales_agent_id = $sales_agent_id;
			$feedback_view = 'admin/nr_builder/sales_agent/partials/delete_before_feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			$this->edit($sales_agent_id);
		}
	}
}

?>	