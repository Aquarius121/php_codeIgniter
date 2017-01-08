<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Content_Slug_Redirect extends Model {
	
	protected static $__table = 'nr_content_slug_redirect';
	protected static $__primary = 'old_slug';
	
	public static function find_and_redirect($slug)
	{
		$ci =& get_instance();
		$redirect = static::find($slug);
		if (!$redirect) return false;
		$url = gstring($redirect->url());
		$url = $ci->website_url($url);
		$ci->redirect($url, false);
	}

	public static function create($old, $new)
	{
		$m_redirect = new Model_Content_Slug_Redirect();
		$m_redirect->date_added = Date::utc();
		$m_redirect->old_slug = $old;
		$m_redirect->new_slug = $new;
		$m_redirect->save();
		return $m_redirect;
	}

	public static function find_all_within_chain($slug, $limit = false)
	{
		return Model_Content_Slug_Redirect::find_all(array('new_slug', $slug),
			array('date_added', Model::ORDER_DESC), $limit);
	}

	public static function update_all_in_chain($old, $new)
	{
		$chain = Model_Content_Slug_Redirect::find_all(array('new_slug', $old));

		foreach ($chain as $redirect)
		{
			$redirect->new_slug = $new;
			$redirect->save();
		}
	}
	
	public function url()
	{
		return "view/{$this->new_slug}";
	}
	
}

?>