<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class Upcoming_Renewals_Controller extends Admin_Base {

	const GRACE_PERIOD = 7;

	public function index()
	{
		$results = $this->fetch_results();
		$this->vd->results = $results;

		$this->load->view('admin/header');
		$this->load->view('admin/analytics/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/analytics/reports/upcoming_renewals');
		$this->load->view('admin/post-content');
		$this->load->view('admin/footer');
	}

	public function download()
	{
		$results = $this->fetch_results();
		$buffer = File_Util::buffer_file();
		$csv = new CSV_Writer($buffer);

		$csv->write(array(
			'user_email',
			'item_name',
			'date_created',
			'date_renews',
			'cost',
			'status',
			'comment',
		));

		foreach ($results as $renewal)
		{
			$csv->write(array(
				$renewal->user->email,
				$renewal->item->name,
				Date::utc($renewal->date_created)->format(Date::FORMAT_LOG),
				Date::utc($renewal->date_termination)->format(Date::FORMAT_LOG),
				$renewal->total_with_discount,
				$renewal->is_on_hold ? 'failure' : 'success',
				value_if_test($renewal->is_on_hold, sprintf('%d of %d failures', 
					$renewal->is_on_hold, Renewal::AUTO_RENEW_ATTEMPTS)),
			));
		}

		$csv->close();
		$this->force_download('renewals.csv', 'text/csv', 
			filesize($buffer));

		readfile($buffer);
		unlink($buffer);
		return;
	}

	protected function fetch_results($filter = null)
	{
		$sql = "SELECT SQL_CALC_FOUND_ROWS 
			ci.id AS id FROM co_component_item ci
			INNER JOIN co_item i ON ci.item_id = i.id
			INNER JOIN co_component_set cs ON ci.component_set_id = cs.id
			AND ci.is_auto_renew_enabled = 1
			AND ci.date_termination > ?
			AND ci.is_suspended = 0
			ORDER BY ci.date_termination ASC
			LIMIT 500";
		
		$date_cut = Date::days(-static::GRACE_PERIOD);	
		$query = $this->db->query($sql, array($date_cut));
		$id_list = array();
		foreach ($query->result() as $row)
			$id_list[] = $row->id;
		
		// no results found so exit
		if (!$id_list) return array();
				
		$id_str = sql_in_list($id_list);
		$total_results = $this->db
			->query("SELECT FOUND_ROWS() AS count")
			->row()->count;
			
		$sql = "SELECT ci.*, 
			cs.is_legacy,
			o.id AS order_id, 
			{{ i.* AS item }},
			{{ u.* AS user }},
			{{ cou.* AS coupon }}
			FROM co_component_item ci
			INNER JOIN co_component_set cs 
			ON ci.component_set_id = cs.id 
			INNER JOIN co_item i ON 
			ci.item_id = i.id
			LEFT JOIN co_order o 
			ON o.component_set_id = cs.id
			LEFT JOIN nr_user u 
			ON cs.user_id = u.id
			LEFT JOIN co_coupon cou
			ON cou.id = cs.coupon_id
			WHERE ci.id IN ({$id_str})
			ORDER BY date_termination ASC";
			
		$sql = Model::prepare($sql);
		$query = $this->db->query($sql);
		$results = Model_Component_Item::from_db_all($query);

		foreach ($results as $result)
		{
			$v_cart = Virtual_Cart::instance();
			$v_cart->allow_expired_coupon();
			if ($result->coupon)
				$v_cart->set_coupon($result->coupon);
			$v_cart->remove_one_time_coupon();
			$v_cart->allow_disabled_items();
			$v_cart->add($result->item, $result->quantity, $result->price);
			$result->total = $v_cart->total();
			$result->total_with_discount = $v_cart->total_with_discount();
		}
		
		return $results;
	}

}