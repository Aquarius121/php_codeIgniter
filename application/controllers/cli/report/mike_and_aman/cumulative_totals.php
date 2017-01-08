<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Cumulative_Totals_Controller extends CLI_Base {
	
	protected $trace_enabled = true;
	protected $trace_time = false;
	
	public function index()
	{
		$writer = new CSV_Writer('php://stdout');

		$m0 = Date::utc();
		$m0->setTime(0, 0, 0);
		$m0->modify('first day of this month');
		$m24 = Date::months(-24);
		$m24->setTime(0, 0, 0);
		$m24->modify('first day of this month');

		$cum = 0;
		$sql = "SELECT count(*) as count from nr_content c inner join nr_company cm 
			on c.company_id = cm.id where type = 'pr' and cm.user_id > 1 and c.date_publish < ?";
		$count = $this->db->query($sql, array($m24))->row()->count;
		$cum += $count;
		$writer->write(array(
			'-',
			$count,
			$cum,
		));

		for ($mStart = clone $m24; $mStart < $m0; $mStart->modify('next month'))
		{
			$mEnd = clone $mStart;
			$mEnd->setTime(23, 59, 59);
			$mEnd->modify('last day of this month');
			
			$sql = "SELECT count(*) as count from nr_content c inner join nr_company cm 
				on c.company_id = cm.id where type = 'pr' and cm.user_id > 1 
				and c.date_publish >= ?
				and c.date_publish <= ?";
				
			$count = $this->db->query($sql, array($mStart, $mEnd))->row()->count;
			$cum += $count;
			$writer->write(array(
				$mStart->format('Y-m'),
				$count,
				$cum,
			));
		}

		$writer->write(array('-------------------'));

		$cum = 0;
		$sql = "SELECT count(*) as count from nr_user u
				where u.date_created < ?";
		$count = $this->db->query($sql, array($m24))->row()->count;
		$cum += $count;
		$writer->write(array(
			'-',
			$count,
			$cum,
		));

		for ($mStart = clone $m24; $mStart < $m0; $mStart->modify('next month'))
		{
			$mEnd = clone $mStart;
			$mEnd->setTime(23, 59, 59);
			$mEnd->modify('last day of this month');
			
			$sql = "SELECT count(*) as count from nr_user u
				where u.date_created >= ?
				  and u.date_created <= ?
				  and u.is_verified = 1";

			$count = $this->db->query($sql, array($mStart, $mEnd))->row()->count;
			$cum += $count;
			$writer->write(array(
				$mStart->format('Y-m'),
				$count,
				$cum,
			));
		}

		$writer->write(array('-------------------'));

		$cum = 0;
		$sql = "SELECT count(*) as count from nr_company cm
				where cm.date_created < ?";
		$count = $this->db->query($sql, array($m24))->row()->count;
		$cum += $count;
		$writer->write(array(
			'-',
			$count,
			$cum,
		));

		for ($mStart = clone $m24; $mStart < $m0; $mStart->modify('next month'))
		{
			$mEnd = clone $mStart;
			$mEnd->setTime(23, 59, 59);
			$mEnd->modify('last day of this month');
			
			$sql = "SELECT count(*) as count from nr_company cm
				where cm.date_created >= ?
				  and cm.date_created <= ?";

			$count = $this->db->query($sql, array($mStart, $mEnd))->row()->count;
			$cum += $count;
			$writer->write(array(
				$mStart->format('Y-m'),
				$count,
				$cum,
			));
		}
	}

}
