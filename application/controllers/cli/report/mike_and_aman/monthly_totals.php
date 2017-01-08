<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Monthly_Totals_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = false;
	
	public function index()
	{
		$datetime = Date::utc('2014-09-01 00:00:00');
		$startsql = escape_and_quote((string) $datetime);

		$sql = "select count(*) as count, year(x.date_created) as y, month(x.date_created) as m
			from (select u.id, u.date_created from nr_user u where date_created >= {$startsql} group by u.id) x 
			group by year(x.date_created),month(x.date_created) order by year(x.date_created) asc, month(x.date_created) asc";
		$this->trace('total new users');
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $r) {
			if ($r->y == '2016' && $r->m == '9') break;
			$this->trace(sprintf('%d-%02d, %d', $r->y, $r->m, $r->count));
		}

		$sql = "select count(*) as count, year(x.date_created) as y, month(x.date_created) as m
			 from (select u.id, u.date_created from nr_user u inner join co_order o on o.user_id = u.id where u.date_created >= {$startsql} group by u.id) x
			group by year(x.date_created),month(x.date_created)  order by year(x.date_created) asc, month(x.date_created) asc";
		$this->trace('total new paid users (based on registration date)');
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $r) {
			if ($r->y == '2016' && $r->m == '9') break;
			$this->trace(sprintf('%d-%02d, %d', $r->y, $r->m, $r->count));
		}

		$sql = "select count(*) as count, year(x.date_created) as y, month(x.date_created) as m
			 from (select u.id, min(o.date_created) as date_created from nr_user u inner join co_order o on o.user_id = u.id where u.date_created > date_sub({$startsql}, interval 1 month) group by u.id) x
			where x.date_created >= {$startsql}
			group by year(x.date_created),month(x.date_created) order by year(x.date_created) asc, month(x.date_created) asc";
		$this->trace('total new paid users (based on first order date)');
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $r) {
			if ($r->y == '2016' && $r->m == '9') break;
			$this->trace(sprintf('%d-%02d, %d', $r->y, $r->m, $r->count));
		}

		$sql = "select count(*) as count, year(x.date_created) as y, month(x.date_created) as m
			 from (select u.id, u.date_created from nr_user u left join co_order o on o.user_id = u.id where u.date_created >= {$startsql} and o.id is null group by u.id) x
			group by year(x.date_created),month(x.date_created)  order by year(x.date_created) asc, month(x.date_created) asc";
		$this->trace('total new free users');
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $r) {
			if ($r->y == '2016' && $r->m == '9') break;
			$this->trace(sprintf('%d-%02d, %d', $r->y, $r->m, $r->count));
		}

// 		$ctypes = sql_in_list(Model_Content::internal_types());
// 		$sql = "select count(*) as count, y, m from (
//   select id, y, m
//   from (select u.id, year(c.date_created) as y, month(c.date_created) as m 
//     from nr_user u inner join nr_company cm on cm.user_id = u.id
//                    inner join nr_content c on cm.id = c.company_id and c.date_created >= {$startsql} and c.type in ({$ctypes})       
//        where u.date_active >= date_sub({$startsql}, interval 1 month)
//  union all
//     select u.id, year(t.date_created) as y, month(t.date_created) as m 
//     from nr_user u inner join co_transaction t on u.id = t.user_id 
//     where u.date_active >= date_sub({$startsql}, interval 1 month)
//     and t.date_created >= {$startsql}   
// ) x
//   group by x.y,x.m,x.id ) x2
// group by y, m
// order by y asc, m asc
// ";

// 		$this->trace('total active users (content/transaction created)');
// 		$dbr = $this->db->query($sql);
// 		foreach ($dbr->result() as $r) {
// 			if ($r->y == '2016' && $r->m == '9') break;
// 			$this->trace(sprintf('%d-%02d, %d', $r->y, $r->m, $r->count));
// 		}


		$cnr = Model_Item::find_slug('claim-nr');
		$sql = "select count(*) as count, year(t.date_created) as y, month(t.date_created) as m  
			from co_transaction t where (virtual_cart like '%\"item_id\":\"{$cnr->id}\"%'
			or virtual_cart like '%\"item_id\":\"20\"%'
			or virtual_cart like '%\"item_id\":\"21\"%'
			or virtual_cart like '%\"item_id\":\"22\"%'
			or virtual_cart like '%\"item_id\":\"23\"%'
			or virtual_cart like '%\"item_id\":\"122\"%'
			or virtual_cart like '%\"item_id\":\"207\"%'
			or virtual_cart like '%\"item_id\":\"469\"%'
			or virtual_cart like '%\"item_id\":\"481\"%'
			or virtual_cart like '%\"item_id\":\"922\"%'
			or virtual_cart like '%\"item_id\":\"929\"%'
			or virtual_cart like '%\"item_id\":\"89\"%'
			or virtual_cart like '%\"item_id\":\"90\"%'
			or virtual_cart like '%\"item_id\":\"87\"%')
			and t.date_created >= {$startsql} 
			group by year(t.date_created),month(t.date_created)  order by year(t.date_created) asc, month(t.date_created) asc";
		$this->trace('total paid newsrooms');
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $r) {
			if ($r->y == '2016' && $r->m == '9') break;
			$this->trace(sprintf('%d-%02d, %d', $r->y, $r->m, $r->count));
		}

		$sql = "select count(*) as count, year(x.date_publish) as y, month(x.date_publish) as m
			from nr_content x inner join nr_company cm on cm.id = x.company_id and cm.user_id > 1
			where x.type = 'pr' and x.is_premium = 1 and x.date_publish >= {$startsql}
			and x.is_scraped_content = 0
			group by year(x.date_publish),month(x.date_publish) order by year(x.date_publish) asc, month(x.date_publish) asc";
		$this->trace('total new premium PR');
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $r) {
			if ($r->y == '2016' && $r->m == '9') break;
			$this->trace(sprintf('%d-%02d, %d', $r->y, $r->m, $r->count));
		}

		$sql = "select count(*) as count, year(x.date_publish) as y, month(x.date_publish) as m
			from nr_content x inner join nr_company cm on cm.id = x.company_id and cm.user_id > 1
			where x.type = 'pr' and x.is_premium = 1 and x.date_publish >= {$startsql}
			and x.is_scraped_content = 0 and x.is_published = 1
			group by year(x.date_publish),month(x.date_publish) order by year(x.date_publish) asc, month(x.date_publish) asc";
		$this->trace('total new premium PR (published)');
		$dbr = $this->db->query($sql);
		foreach ($dbr->result() as $r) {
			if ($r->y == '2016' && $r->m == '9') break;
			$this->trace(sprintf('%d-%02d, %d', $r->y, $r->m, $r->count));
		}


	}

}
