<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('admin/base');

class PR_Planner_Controller extends Admin_Base {

	public function index()
	{
		$this->vd->counts = $counts = new Raw_Data();
		$counts->direct = new Raw_Data();
		$counts->intro = new Raw_Data();
		$date30 = escape_and_quote((string) Date::days(-30));

		// ------------------------------------------------
		// ------------------------------------------------
		// ------------------------------------------------	

		// reached at start of the planner
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'direct' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 0";
		$counts->direct->zero = $this->db
			->query($sql)->row()->count;

		// reached at least step 1
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'direct' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 1";
		$counts->direct->one = $this->db
			->query($sql)->row()->count;

		// reached at least step 2
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'direct' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 2";
		$counts->direct->two = $this->db
			->query($sql)->row()->count;

		// reached at least step 3
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'direct' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 3";
		$counts->direct->three = $this->db
			->query($sql)->row()->count;

		// reached at least step 4
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'direct' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 4";
		$counts->direct->four = $this->db
			->query($sql)->row()->count;

		// reached at least step 5
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'direct' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 5";
		$counts->direct->five = $this->db
			->query($sql)->row()->count;

		// reached at least step 6
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'direct' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 6";
		$counts->direct->six = $this->db
			->query($sql)->row()->count;

		// reached at least step 7
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'direct' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 7";
		$counts->direct->seven = $this->db
			->query($sql)->row()->count;

		// reached the conclusion of the planner
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.date_created >= {$date30} 
			AND sp.source = 'direct' 
			AND sp.step_max >= 1
			AND sp.is_finished = 1";
		$counts->direct->finished = $this->db
			->query($sql)->row()->count;

		// ------------------------------------------------
		// ------------------------------------------------
		// ------------------------------------------------		

		// reached at start of the planner
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'intro' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 0";
		$counts->intro->zero = $this->db
			->query($sql)->row()->count;

		// reached at least step 1
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'intro' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 1";
		$counts->intro->one = $this->db
			->query($sql)->row()->count;

		// reached at least step 2
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'intro' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 2";
		$counts->intro->two = $this->db
			->query($sql)->row()->count;

		// reached at least step 3
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'intro' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 3";
		$counts->intro->three = $this->db
			->query($sql)->row()->count;

		// reached at least step 4
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'intro' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 4";
		$counts->intro->four = $this->db
			->query($sql)->row()->count;

		// reached at least step 5
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'intro' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 5";
		$counts->intro->five = $this->db
			->query($sql)->row()->count;

		// reached at least step 6
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'intro' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 6";
		$counts->intro->six = $this->db
			->query($sql)->row()->count;

		// reached at least step 7
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.source = 'intro' 
			AND sp.date_created >= {$date30}
			AND sp.step_max >= 7";
		$counts->intro->seven = $this->db
			->query($sql)->row()->count;

		// reached the conclusion of the planner
		$sql = "SELECT COUNT(*) AS count FROM nr_sales_planner sp
			WHERE sp.date_created >= {$date30} 
			AND sp.source = 'intro' 
			AND sp.step_max >= 1
			AND sp.is_finished = 1";
		$counts->intro->finished = $this->db
			->query($sql)->row()->count;

		$this->load->view('admin/header');
		$this->load->view('admin/analytics/menu');
		$this->load->view('admin/pre-content');
		$this->load->view('admin/analytics/reports/pr_planner');
		$this->load->view('admin/post-content'); 
		$this->load->view('admin/footer');
	}

}
