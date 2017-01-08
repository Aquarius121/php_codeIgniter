<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Cancels_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = false;
	
	public function index()
	{
		$datetime = Date::utc('2016-06-30 23:59:59');
		$startsql = escape_and_quote((string) $datetime);
		$datetime = Date::utc('2016-09-01 00:00:00');
		$endsql = escape_and_quote((string) $datetime);

		$csv = new CSV_Writer('php://stdout');

		// CANCELLATIONS WITH REASON

		$sql = "SELECT c.date_cancel, ci.price as price_before_coupon, cs.coupon_id, cs.user_id, ci.quantity, i.name as product, i.id as item_id
			from co_cancellation c
			inner join co_component_item ci
			on c.component_item_id = ci.id
			inner join co_item i on i.id = ci.item_id
			inner join co_component_set cs on cs.id = ci.component_set_id
			where c.date_cancel > {$startsql} and c.date_cancel < {$endsql}";

		$dbr = $this->db->query($sql);

		foreach ($dbr->result() as $r)
		{
			$i = Model_Item::find($r->item_id);
			$vc = new Virtual_Cart();
			$vc->allow_expired_coupon();
			$vc->allow_deleted_coupon();
			$vc->allow_disabled_items();
			$ci = $vc->add($i, $r->quantity, $r->price_before_coupon);
			if ($r->coupon_id) $vc->set_coupon(Model_Coupon::find($r->coupon_id));
			$price = $vc->item_cost($ci->token());
			
			$csv->write(array(
				Date::utc($r->date_cancel)->format('M Y'),
				$r->user_id,
				$r->product,
				$vc->format($price), 
				$r->quantity,
			));
		}


		// CANCELLATIONS THAT HAVE NO RECORDED REASON

		$sql = "SELECT ci.date_termination as date_cancel, i.name as product, i.id as item_id, ci.price as price_before_coupon, cs.coupon_id, ci.quantity, cs.user_id
			FROM  co_component_item ci left join `co_cancellation` c on ci.id = c.component_item_id inner join co_component_set cs on cs.id = ci.component_set_id 
			inner join co_item i on ci.item_id = i.id 
			where ci.date_termination > {$startsql} and ci.date_termination < {$endsql}
			and ci.period >= 0 and ci.period < 31
			and ci.period_repeat_count = 1
			and c.component_item_id is null
			and i.raw_data like '%is_auto_renew_enabled\":1%'
			order by date_termination asc";

		$dbr = $this->db->query($sql);

		foreach ($dbr->result() as $r)
		{
			$i = Model_Item::find($r->item_id);
			$vc = new Virtual_Cart();
			$vc->allow_expired_coupon();
			$vc->allow_deleted_coupon();
			$vc->allow_disabled_items();
			$ci = $vc->add($i, $r->quantity, $r->price_before_coupon);
			if ($r->coupon_id) $vc->set_coupon(Model_Coupon::find($r->coupon_id));
			$price = $vc->item_cost($ci->token());
			
			$csv->write(array(
				Date::utc($r->date_cancel)->format('M Y'),
				$r->user_id,
				$r->product,
				$vc->format($price), 
				$r->quantity,
			));
		}
	}

}
