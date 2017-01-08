<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CIL_Loader extends CI_Loader {
	
	protected $within_view = 0;
	
	/**
	 * Load View
	 *
	 * This function is used to load a "view" file.  It has three parameters:
	 *
	 * 1. The name of the "view" file to be included.
	 * 2. An associative array of data to be extracted for use in the view.
	 * 3. TRUE/FALSE - whether to return the data or load it.  In
	 * some cases it's advantageous to be able to return data so that
	 * a developer can process it in some way.
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	public function view($view, $vars = array(), $return = false)
	{
		// force return on multiple levels
		if ($this->within_view++ > 0)
			$return = true;

		// convert vars to array
		if ($vars && is_object($vars))
			$vars = (array) $vars;
		
		// make ci accessible 
		$vars['controller'] =& get_instance();
		$vars['ci'] =& get_instance();
		$vars['vd'] = $vars['ci']->vd;
		$vars['vars'] = Raw_Data::from_array($vars);
		$return = parent::view($view, $vars, $return);
		$this->within_view--;
		return $return;
	}

	// view() with return set to true
	public function view_return($view, $vars = array())
	{
		return $this->view($view, $vars, true);
	}
	
	// similar to load view but html
	// * ensures PHP is not executed
	public function view_html($view)
	{
		$view = "application/views/{$view}.html";
		if (is_file($view))
		     return file_get_contents($view);
		else throw new Exception('missing view');
	}

	// similar to load view but for assets
	// * ensures PHP is not executed
	public function view_raw($view)
	{
		$view = "application/views/{$view}";
		if (is_file($view))
		     return file_get_contents($view);
		else throw new Exception('missing view');
	}

	// test if a view is valid
	public function view_test($view)
	{
		return $this->_ci_load(array('_ci_view' => $view), true);
	}

	/**
	 * Database Loader
	 *
	 * @param	string	the DB credentials
	 * @param	bool	whether to return the DB object
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	object
	 */
	public function database($params = '', $return = FALSE, $active_record = NULL)
	{
		if ($return)
		{
			$db = parent::database($params, $return, $active_record);
			if (!$db) return $db;
			$db->cached = new Query_Cache($db);
			return $db;
		}
		else
		{
			$res = parent::database($params, $return, $active_record);
			$db = get_instance()->db;
			$db->cached = new Query_Cache($db);
			return $res;
		}
	}
	
}
