<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Content_Collab extends Model {

	use Raw_Data_Trait;
	
	protected static $__table = 'nr_content_collab';
	protected static $__compressed = array('raw_data');

	// the values are defined after class
	public static $colors = array();
	
	public static function create(Model_Content $m_content)
	{
		$m_content->load_content_data();
		$m_content->load_local_data();
		
		$rd = new Raw_Data();
		$rd->detached_content = Model_Detached_Content::from_model_content($m_content);
		$rd->timezone = $m_content->newsroom()->timezone;
		$rd->annotations = array();
		$rd->approvals = array();
		$rd->users = array();
		$rd->color = 0;

		$table = static::$__table;
		$content_id = (int) $m_content->id;
		$sql = "SELECT MAX(version) AS max FROM {$table}
			WHERE content_id = {$content_id}";
		$row = static::__db()->query($sql)->row();
		if ($row) $version = $row->max + 1;
		else $version = 1;

		$instance = new static();
		$instance->date_created = Date::utc();
		$instance->content_id = $m_content->id;
		$instance->version = $version;
		$instance->id = UUID::create();
		$instance->raw_data($rd);
		$instance->save();

		return $instance;
	}

	public static function find_latest($content_id)
	{
		$latest = static::find_all(array(
			array('is_deleted', 0),
			array('content_id', (int) $content_id),
		), array('date_created', 'desc'), 1);
		if ($latest) return $latest[0];
		return null;
	}

	public function url()
	{
		return build_url('view', 'collab', $this->id);
	}

	public function nice_id()
	{
		$short = substr($this->id, 0, 8);
		$short = strtoupper($short);
		return $short;
	}

	public function owner()
	{
		$sql = "SELECT u.* FROM nr_user u 
			INNER JOIN nr_company cm ON u.id = cm.user_id
			INNER JOIN nr_content c ON cm.id = c.company_id
			WHERE c.id = ?";
		return Model_User::from_sql($sql,
			array($this->content_id));
	}

	public static function suid(Model_User $user = null)
	{
		if ($user === null)
		     return uniqid();
		else return substr(md5($user->id), 0, 16);
	}
	
}

Model_Content_Collab::$colors = array(

	// light yellow with black text
	Raw_Data::from_array(array(
		'background' => array(255, 255, 10, 0.3),
		'text' => array(75, 86, 100, 1.0),
	)),

	// light green with black text
	Raw_Data::from_array(array(
		'background' => array(5, 255, 5, 0.3),
		'text' => array(75, 86, 100, 1.0),
	)),

	// light red with black text
	Raw_Data::from_array(array(
		'background' => array(255, 5, 5, 0.3),
		'text' => array(75, 86, 100, 1.0),
	)),

	// light purple with black text
	Raw_Data::from_array(array(
		'background' => array(5, 5, 255, 0.3),
		'text' => array(75, 86, 100, 1.0),
	)),

	// light pink with black text
	Raw_Data::from_array(array(
		'background' => array(255, 10, 255, 0.3),
		'text' => array(75, 86, 100, 1.0),
	)),

	// light blue with black text
	Raw_Data::from_array(array(
		'background' => array(5, 196, 255, 0.3),
		'text' => array(75, 86, 100, 1.0),
	)),

	// light orange with black text
	Raw_Data::from_array(array(
		'background' => array(255, 123, 0, 0.3),
		'text' => array(75, 86, 100, 1.0),
	)),

	// light grey with black text
	Raw_Data::from_array(array(
		'background' => array(127, 127, 127, 0.3),
		'text' => array(75, 86, 100, 1.0),
	)),

);