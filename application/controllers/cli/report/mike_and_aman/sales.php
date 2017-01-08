<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Sales_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = false;
	
	public function index()
	{
		$datetime = Date::utc('2016-06-30 23:59:59');
		$startsql = escape_and_quote((string) $datetime);
		$datetime = Date::utc('2016-09-01 00:00:00');
		$endsql = escape_and_quote((string) $datetime);

		$sql = "select t.id, t.date_created, t.is_renewal, t.virtual_cart, i.name as product, i.type as item_type, u.id as user_id, til.item_id, 
			i.raw_data as item_raw_data,
			sum(til.quantity) as quantity, til.price as price from co_transaction t inner join nr_user u on 
			t.user_id = u.id inner join co_transaction_item_log til on til.transaction_id = t.id inner join co_item i on til.item_id = i.id
			where t.date_created > {$startsql} and t.date_created < {$endsql}
			group by year(t.date_created), month(t.date_created), til.item_id, u.id, til.price
			order by date_created asc";

		$dbr = $this->db->query($sql);
		$csv = new CSV_Writer('php://stdout');

		foreach ($dbr->result() as $r)
		{
			$ird = Raw_Data::from_blob($r->item_raw_data);
			
			$vc = Virtual_Cart::instance();
			$vc->unserialize($r->virtual_cart);
			$vc->allow_expired_coupon();
			$vc->allow_deleted_coupon();

			$price = 0;
			foreach ($vc->get_id($r->item_id) as $item)
				$price += $vc->item_cost($item->token());

			$csv->write(array(
				Date::utc($r->date_created)->format('M Y'),
				$r->user_id,
				$r->product,
				$ird->is_auto_renew_enabled ? 'Subscription' : 'One-time',
				$r->is_renewal ? 'Renewal' : 'New',
				Cart::instance()->format($price),
				$r->quantity,
			));
		}
	}

}
