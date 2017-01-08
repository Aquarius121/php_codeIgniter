<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

Trait Status_Update_Report_Trait {
	
	protected $date_end;	
	protected $date_end_local;

	protected function default_date_options()
	{
		$timezone = new DateTimeZone('America/New_York');
		$this->date_end = Date::hours(-24);
		$this->date_end->setTimezone($timezone);
		$this->date_end->setTime(23, 59, 59);
		$this->date_end->setTimezone(Date::$utc);

		$date_start_1d = Date::days(-1, $this->date_end);
		$date_start_30d = Date::days(-30, $this->date_end);

		$date_month_start = Date::hours(-24);
		$date_month_start->setTimezone($timezone);
		$month = (int) $date_month_start->format('n');
		$year = (int) $date_month_start->format('Y');
		$date_month_start->setDate($year, $month, 1);
		$date_month_start->setTime(0, 0, 0);
		$date_month_start->setTimezone(Date::$utc);

		$date_month = clone $date_month_start;
		$date_month->setTimezone($timezone);
		$this->vd->month = $date_month->format('M Y');

		$date_day = clone $this->date_end;
		$date_day->setTimezone($timezone);
		$this->vd->day = $date_day->format('jS M');

		$date_end_local = clone $this->date_end;
		$date_end_local->setTimezone($timezone);
		$this->date_end_local = $date_end_local;

		return array(
			'date_end' => $this->date_end,
			'date_end_local' => $date_end_local,
			'date_month_start' => $date_month_start,
			'date_start_1d' => $date_start_1d,
			'date_start_30d' => $date_start_30d,
		);
	}
	
	protected function generate_report_data($extract)
	{
		extract($extract);

		$order_stats_1d = $this->order_stats($date_start_1d);
		$order_stats_30d = $this->order_stats($date_start_30d);
		$order_stats_month = $this->order_stats($date_month_start);
		
		$this->vd->order_stats = new stdClass();
		$this->vd->order_stats->items = array();
		$this->vd->order_stats->total_1d = 0;
		$this->vd->order_stats->total_30d = 0;
		$this->vd->order_stats->total_month = 0;
		
		foreach ($order_stats_1d as $item_id => $stat)
		{
			if (!isset($this->vd->order_stats->items[$item_id]))
			{
				$this->vd->order_stats->items[$item_id] = new stdClass();
				$this->vd->order_stats->items[$item_id]->name = $stat->name;
				$this->vd->order_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->order_stats->items[$item_id]->tracking = $stat->tracking;
			}

			$this->vd->order_stats->items[$item_id]->count_1d = $stat->count;
			$this->vd->order_stats->items[$item_id]->billed_1d = $stat->billed;
			$this->vd->order_stats->total_1d += $stat->billed;
		}

		foreach ($order_stats_30d as $item_id => $stat)
		{
			if (!isset($this->vd->order_stats->items[$item_id]))
			{
				$this->vd->order_stats->items[$item_id] = new stdClass();
				$this->vd->order_stats->items[$item_id]->name = $stat->name;
				$this->vd->order_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->order_stats->items[$item_id]->tracking = $stat->tracking;
			}

			$this->vd->order_stats->items[$item_id]->count_30d = $stat->count;
			$this->vd->order_stats->items[$item_id]->billed_30d = $stat->billed;
			$this->vd->order_stats->total_30d += $stat->billed;
		}

		foreach ($order_stats_month as $item_id => $stat)
		{
			if (!isset($this->vd->order_stats->items[$item_id]))
			{
				$this->vd->order_stats->items[$item_id] = new stdClass();
				$this->vd->order_stats->items[$item_id]->name = $stat->name;
				$this->vd->order_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->order_stats->items[$item_id]->tracking = $stat->tracking;
			}

			$this->vd->order_stats->items[$item_id]->count_month = $stat->count;
			$this->vd->order_stats->items[$item_id]->billed_month = $stat->billed;
			$this->vd->order_stats->total_month += $stat->billed;
		}
		
		$renew_stats_1d = $this->renew_stats($date_start_1d);
		$renew_stats_30d = $this->renew_stats($date_start_30d);
		$renew_stats_month = $this->renew_stats($date_month_start);
		$this->vd->renew_stats_1d = $renew_stats_1d;
		$this->vd->renew_stats_30d = $renew_stats_30d;
		$this->vd->renew_stats_month = $renew_stats_month;
		
		$this->vd->renew_stats = new stdClass();
		$this->vd->renew_stats->items = array();
		$this->vd->renew_stats->total_1d = 0;
		$this->vd->renew_stats->total_30d = 0;
		$this->vd->renew_stats->total_month = 0;

		foreach ($renew_stats_1d as $item_id => $stat)
		{
			if (!isset($this->vd->renew_stats->items[$item_id]))
			{
				$this->vd->renew_stats->items[$item_id] = new stdClass();
				$this->vd->renew_stats->items[$item_id]->name = $stat->name;
				$this->vd->renew_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->renew_stats->items[$item_id]->tracking = $stat->tracking;
			}
			
			$this->vd->renew_stats->items[$item_id]->count_1d = $stat->count;
			$this->vd->renew_stats->items[$item_id]->billed_1d = $stat->billed;
			$this->vd->renew_stats->total_1d += $stat->billed;
		}

		foreach ($renew_stats_30d as $item_id => $stat)
		{
			if (!isset($this->vd->renew_stats->items[$item_id]))
			{
				$this->vd->renew_stats->items[$item_id] = new stdClass();
				$this->vd->renew_stats->items[$item_id]->name = $stat->name;
				$this->vd->renew_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->renew_stats->items[$item_id]->tracking = $stat->tracking;
			}
			
			$this->vd->renew_stats->items[$item_id]->count_30d = $stat->count;
			$this->vd->renew_stats->items[$item_id]->billed_30d = $stat->billed;
			$this->vd->renew_stats->total_30d += $stat->billed;
		}
		
		foreach ($renew_stats_month as $item_id => $stat)
		{
			if (!isset($this->vd->renew_stats->items[$item_id]))
			{
				$this->vd->renew_stats->items[$item_id] = new stdClass();
				$this->vd->renew_stats->items[$item_id]->name = $stat->name;
				$this->vd->renew_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->renew_stats->items[$item_id]->tracking = $stat->tracking;
			}
			
			$this->vd->renew_stats->items[$item_id]->count_month = $stat->count;
			$this->vd->renew_stats->items[$item_id]->billed_month = $stat->billed;
			$this->vd->renew_stats->total_month += $stat->billed;
		}
		
		$cancel_stats_1d = $this->cancel_stats($date_start_1d);
		$cancel_stats_30d = $this->cancel_stats($date_start_30d);
		$cancel_stats_month = $this->cancel_stats($date_month_start);
		$this->vd->cancel_stats_1d = $cancel_stats_1d;
		$this->vd->cancel_stats_30d = $cancel_stats_30d;
		$this->vd->cancel_stats_month = $cancel_stats_month;
		
		$this->vd->cancel_stats = new stdClass();
		$this->vd->cancel_stats->items = array();
		
		foreach ($cancel_stats_1d as $item_id => $stat)
		{
			if (!isset($this->vd->cancel_stats->items[$item_id]))
			{
				$this->vd->cancel_stats->items[$item_id] = new stdClass();
				$this->vd->cancel_stats->items[$item_id]->name = $stat->name;
				$this->vd->cancel_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->cancel_stats->items[$item_id]->tracking = $stat->tracking;
			}
			
			$this->vd->cancel_stats->items[$item_id]->count_1d = $stat->count;
		}

		foreach ($cancel_stats_30d as $item_id => $stat)
		{
			if (!isset($this->vd->cancel_stats->items[$item_id]))
			{
				$this->vd->cancel_stats->items[$item_id] = new stdClass();
				$this->vd->cancel_stats->items[$item_id]->name = $stat->name;
				$this->vd->cancel_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->cancel_stats->items[$item_id]->tracking = $stat->tracking;
			}
			
			$this->vd->cancel_stats->items[$item_id]->count_30d = $stat->count;
		}

		foreach ($cancel_stats_month as $item_id => $stat)
		{
			if (!isset($this->vd->cancel_stats->items[$item_id]))
			{
				$this->vd->cancel_stats->items[$item_id] = new stdClass();
				$this->vd->cancel_stats->items[$item_id]->name = $stat->name;
				$this->vd->cancel_stats->items[$item_id]->comment = $stat->comment;
				$this->vd->cancel_stats->items[$item_id]->tracking = $stat->tracking;
			}
			
			$this->vd->cancel_stats->items[$item_id]->count_month = $stat->count;
		}
		
		usort($this->vd->order_stats->items, array($this, 'usort_items'));
		usort($this->vd->renew_stats->items, array($this, 'usort_items'));
		usort($this->vd->cancel_stats->items, array($this, 'usort_items'));

		$legacy_stats_1d = $this->legacy_stats($date_start_1d);
		$legacy_stats_30d = $this->legacy_stats($date_start_30d);
		$legacy_stats_month = $this->legacy_stats($date_month_start);
		$this->vd->legacy_stats_1d = $legacy_stats_1d;
		$this->vd->legacy_stats_30d = $legacy_stats_30d;
		$this->vd->legacy_stats_month = $legacy_stats_month;
		
		$this->vd->overall_total_1d = 
			$this->vd->order_stats->total_1d + 
			$this->vd->renew_stats->total_1d + 
			$this->vd->legacy_stats_1d;
			
		$this->vd->overall_total_30d = 
			$this->vd->order_stats->total_30d + 
			$this->vd->renew_stats->total_30d + 
			$this->vd->legacy_stats_30d;

		$this->vd->overall_total_month = 
			$this->vd->order_stats->total_month + 
			$this->vd->renew_stats->total_month + 
			$this->vd->legacy_stats_month;
		
		$pr_stats_1d = $this->pr_stats($date_start_1d);
		$pr_stats_30d = $this->pr_stats($date_start_30d);
		$pr_stats_month = $this->pr_stats($date_month_start);
		$this->vd->pr_stats_1d = $pr_stats_1d;
		$this->vd->pr_stats_30d = $pr_stats_30d;
		$this->vd->pr_stats_month = $pr_stats_month;
		
		$active_stats_1d = $this->active_stats($date_start_1d);
		$active_stats_30d = $this->active_stats($date_start_30d);
		$active_stats_month = $this->active_stats($date_month_start);
		$this->vd->active_stats_1d = $active_stats_1d;
		$this->vd->active_stats_30d = $active_stats_30d;
		$this->vd->active_stats_month = $active_stats_month;
		
		$register_stats_1d = $this->register_stats($date_start_1d);
		$register_stats_30d = $this->register_stats($date_start_30d);
		$register_stats_month = $this->register_stats($date_month_start);
		$this->vd->register_stats_1d = $register_stats_1d;
		$this->vd->register_stats_30d = $register_stats_30d;
		$this->vd->register_stats_month = $register_stats_month;
		
		$newsroom_stats = $this->newsroom_stats();
		$this->vd->newsroom_stats = $newsroom_stats;
	}

	protected function order_stats($date_start)
	{
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $this->date_end->format(Date::FORMAT_MYSQL);
		
		$sql = "SELECT o.*, t.order_id, t.virtual_cart
			FROM co_order o
			INNER JOIN co_transaction t 
			ON t.order_id = o.id
			WHERE t.is_renewal = 0
			AND o.date_created >= '{$date_start_str}' 
			AND o.date_created < '{$date_end_str}'";
		
		$db_result = $this->db->query($sql);
		$orders = Model_Order::from_db_all($db_result);
		$item_totals = array();
		
		Model_Item::enable_cache();
		
		foreach ($orders as $order)
		{
			$cart = Virtual_Cart::instance();
			$cart->unserialize($order->virtual_cart);
			$cart->allow_expired_coupon();
			$cart->allow_deleted_coupon();
			
			foreach ($cart->items() as $item)
			{
				if (!isset($item_totals[$item->item_id]))
				{
					$item_totals[$item->item_id] = new stdClass();
					$item_totals[$item->item_id]->name = $item->item()->name;
					$item_totals[$item->item_id]->comment = $item->item()->comment;
					$item_totals[$item->item_id]->tracking = $item->item()->tracking;
					$item_totals[$item->item_id]->count = 0;
					$item_totals[$item->item_id]->billed = 0;
				}
				
				$item_totals[$item->item_id]->count += $item->quantity;
				$item_totals[$item->item_id]->billed += $item->quantity 
					* $cart->item_cost($item->token());
			}
		}
		
		Model_Item::disable_cache();
		
		return $item_totals;
	}
	
	protected function renew_stats($date_start)
	{
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $this->date_end->format(Date::FORMAT_MYSQL);
		
		$sql = "SELECT t.*
			FROM co_transaction t 
			INNER JOIN co_order o
			ON t.order_id = o.id
			WHERE t.is_renewal = 1
			AND t.date_created >= '{$date_start_str}' 
			AND t.date_created < '{$date_end_str}'";

		$db_result = $this->db->query($sql);
		$transactions = Model_Transaction::from_db_all($db_result);
		$item_totals = array();
		
		Model_Item::enable_cache();
		
		foreach ($transactions as $transaction)
		{
			$cart = Virtual_Cart::instance();
			$cart->unserialize($transaction->virtual_cart);
			$cart->allow_expired_coupon();
			$cart->allow_deleted_coupon();
			
			foreach ($cart->items() as $item)
			{
				if (!isset($item_totals[$item->item_id]))
				{
					$item_totals[$item->item_id] = new stdClass();
					$item_totals[$item->item_id]->name = $item->item()->name;
					$item_totals[$item->item_id]->comment = $item->item()->comment;
					$item_totals[$item->item_id]->tracking = $item->item()->tracking;
					$item_totals[$item->item_id]->count = 0;
					$item_totals[$item->item_id]->billed = 0;
				}
				
				$item_totals[$item->item_id]->count += $item->quantity;
				$item_totals[$item->item_id]->billed += $item->quantity 
					* $cart->item_cost($item->token());
			}
		}
		
		Model_Item::disable_cache();
		
		return $item_totals;
	}
	
	protected function cancel_stats($date_start)
	{
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $this->date_end->format(Date::FORMAT_MYSQL);
		
		$sql = "SELECT c.*, i.id AS item_id,
			i.name AS item_name, 
			i.comment AS item_comment,
			i.tracking AS item_tracking,
			ci.quantity AS item_quantity
			FROM co_cancellation c
			INNER JOIN co_component_item ci 
			ON ci.id = c.component_item_id 
			INNER JOIN co_item i
			ON ci.item_id = i.id
			WHERE c.date_cancel >= '{$date_start_str}' 
			AND c.date_cancel < '{$date_end_str}'";
			
		$db_result = $this->db->query($sql);
		$cancellations = Model_Cancellation::from_db_all($db_result);
		$item_totals = array();
		
		foreach ($cancellations as $cancellation)
		{
			if (!isset($item_totals[$cancellation->item_id]))
			{
				$item_totals[$cancellation->item_id] = new stdClass();
				$item_totals[$cancellation->item_id]->name = $cancellation->item_name;
				$item_totals[$cancellation->item_id]->comment = $cancellation->item_comment;
				$item_totals[$cancellation->item_id]->tracking = $cancellation->item_tracking;
				$item_totals[$cancellation->item_id]->count = 0;
			}
				
			$item_totals[$cancellation->item_id]->count 
				+= $cancellation->item_quantity;
		}
		
		return $item_totals;
	}
	
	protected function legacy_stats($date_start)
	{
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $this->date_end->format(Date::FORMAT_MYSQL);
		$total_amount = 0;
		
		$sql = "SELECT r.* FROM raw_payments r
			INNER JOIN (
				SELECT r.orderid,
					MIN(r.id) AS id
				FROM raw_payments r
				WHERE r.from = 'ULTRA'
				AND r.status = 'Processed'
				GROUP BY r.orderid
			) r_min
			/* narrow down to first processed */
			ON r_min.orderid = r.orderid
			AND r_min.id = r.id
			/* ------------------------------- */
			WHERE datetime >= '{$date_start_str}' 
			AND datetime < '{$date_end_str}'";
			
		$ldb = LEGACY::database();
		$db_result = $ldb->query($sql);
		
		foreach ($db_result->result() as $record)
		{
			$xml = simplexml_load_string($record->raw_data);
			$amount = (float) ($xml->xpath('order/total')[0]->__toString());
			$total_amount += $amount;
		}
		
		return $total_amount;
	}
	
	protected function pr_stats($date_start)
	{
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $this->date_end->format(Date::FORMAT_MYSQL);
		$totals = new stdClass();

		$content_types = sql_in_list(array(
			Model_Content::TYPE_PR,
		));

		$sql = "SELECT count(c.id) as count from nr_content c 
			  inner join nr_company cm on cm.id = c.company_id and cm.user_id > 1
			  where c.type IN ({$content_types}) and c.is_premium = ? 
			  and c.date_publish >= '{$date_start_str}'
			  and c.date_publish <= '{$date_end_str}'
			  and c.is_published = 1";

		$totals->premium = Model::from_sql($sql, array(1))->count;
		$totals->basic = Model::from_sql($sql, array(0))->count;
		
		return $totals;
	}
	
	protected function active_stats($date_start)
	{
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $this->date_end->format(Date::FORMAT_MYSQL);
		
		$criteria = array();
		$criteria[] = array('date_active', '>=', $date_start_str);
		$criteria[] = array('date_active', '<', $date_end_str);
		
		return Model_User::count_all($criteria);
	}
	
	protected function newsroom_stats()
	{
		return Model_Newsroom::count_all(array(
			array('user_id', Model::CMP_GREATER_THAN, 1),
			array('is_active', 1),			
		));
	}

	protected function register_stats($date_start)
	{
		$date_start_str = $date_start->format(Date::FORMAT_MYSQL);
		$date_end_str = $this->date_end->format(Date::FORMAT_MYSQL);
		$totals = new stdClass();
		
		$criteria = array();
		$criteria[] = array('date_created', '>=', $date_start_str);
		$criteria[] = array('date_created', '<', $date_end_str);
		
		$totals->all = Model_User::count_all($criteria);
		
		$criteria = array();
		$criteria[] = array('date_created', '>=', $date_start_str);
		$criteria[] = array('date_created', '<', $date_end_str);
		$criteria[] = array('is_verified', 1);
		
		$totals->verified = Model_User::count_all($criteria);
		
		$criteria = array();
		$criteria[] = array('date_created', '>=', $date_start_str);
		$criteria[] = array('date_created', '<', $date_end_str);
		$criteria[] = array('date_active', '>=', $date_start_str);
		
		$totals->active = Model_User::count_all($criteria);
		
		return $totals;
	}

	protected function usort_items($a, $b)
	{
		if ($a->tracking < $b->tracking) return -1;
		if ($a->tracking > $b->tracking) return +1;
		if ($a->name < $b->name) return -1;
		if ($a->name > $b->name) return +1;
		if ($a->comment < $b->comment) return -1;
		if ($a->comment > $b->comment) return +1;
		return 0;
	}
	
}

?>