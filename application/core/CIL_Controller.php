<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CIL_Controller extends CI_Controller {
	
	public $controller_uri_parts;
	public $env;
	public $eob = array();
	public $eoh = array();
	public $feedback;
	public $is_admo_host;
	public $is_common_host;
	public $is_default_newsroom_host;
	public $is_detached_host;
	public $is_own_domain;
	public $is_website_host;
	public $local_tz;
	public $newsroom;
	public $params;
	public $method;
	public $rdata;
	public $session;
	public $vd;

	private $m_common = NR_DEFAULT;
	
	protected $cache_duration = 0;
	protected $catch_with_oee = false;
	protected $config_loaded = false;
	protected $__set_redirect = null;
	
	// allows SSL but does not force it
	protected $ssl_optional = true;

	// forces SSL for the request
	protected $ssl_required = true;

	// forces an SSL redirect even when 
	// post data was included	
	protected $ssl_required_post = true;

	// if the CLI is enabled
	protected $cli_enabled = false;

	protected $log_enabled = false;
	protected $log_file = null;
	protected $log_pid = false;

	// enable output compression?
	protected $compression = true;
	protected $compression_mimes = array(
		'application/json',
		'text/html',
		'text/plain',
	);

	const REQUEST_DELETE  = 'delete';
	const REQUEST_GET     = 'get';
	const REQUEST_HEAD    = 'head';
	const REQUEST_OPTIONS = 'options';
	const REQUEST_POST    = 'post';
	const REQUEST_PUT     = 'put';

	protected static $__db = array();

	protected function __ci_setup()
	{
		parent::__ci_setup();

		// environment config
		$this->env =& $GLOBALS['env'];

		// enable logging on dev
		if ($this->is_development())
			$this->log_enabled = true;

		// check if blocked
		$this->check_blocked();

		// load the configuration
		$this->load_config();
		
		if ($this->_is_internal_redirect())
		{
			$location = $this->_internal_redirect_location();
			$this->env['requested_uri'] = $location;
		}

		if ($this->input->is_cli_request())
		{
			if (!$this->cli_enabled)
			{
				$color = new Colors\Color();
				$message = 'error: CLI not enabled for this controller';				
				$message = $color($message)->red()->bold();
				echo $message;
				echo PHP_EOL;
				exit;
			}

			$this->session = new Console_Session(); 
			return;
		}

		$this->session = new Session(array(
			'cookie' => $this->env['session_cookie'],
			'domain' => $this->env['session_domain'],
			'duration' => $this->env['session_duration'],
			'path' => $this->env['session_path'],
		));
	}

	public function __construct()	
	{
		parent::__construct();

		$this->vd = new View_Data();
		$this->vd->assets_base = $this->conf('assets_base');
		$this->vd->version = $this->env['version'];
		$this->vd->server_id = $this->env['server_id'];
		$this->vd->title = array();

		if ($this->input->is_cli_request())
			return;

		// explicit request for no cache? 
		// => disable query cacher
		if ($this->input->get('no_cache') || 
			 $this->input->get('no-cache') ||
			 $this->is_development())
			Query_Cache::disable();

		// attempt to load entire response from cache
		if (!Auth::is_user_online() && !$this->is_development() && 
			($cached = Data_Cache_ST::read($this->__cache_key())) &&
			(!$this->is_no_cache_request()))
		{
			$cached = json_decode($cached);
			while (ob_get_level()) ob_end_clean();
			foreach ($cached->headers as $header)
				header($header);
			echo $cached->content;
			exit();
		}
		
		// detect bots with bad behaviour
		(new \Bad_Bots\Detector(array(
			'data_dir' => $this->conf('bad_bots_directory'), 
			'remote_addr' => $this->env['remote_addr'], 
			'user_agent' => $this->env['user_agent'], 
		)))->detect();

		// load any associated request data
		$this->rdata = Request_Data::load($this->input);

		// cache capture
		ob_start();
		
		if (isset($this->title))
		{
			$this->vd->title[] = $this->title;
			unset($this->title);
		}
		
		// feedback messages to display in template
		$this->feedback = $this->session->get('nr_feedback');
		if (!$this->feedback) $this->feedback = array();
		
		$common_host = $this->conf('common_host');
		$website_host = $this->conf('website_host');

		// determine if we are on website host
		if ($website_host === $this->env['host'])		
			$this->is_website_host = true;

		// determine if we are on common host 
		if ($common_host === $this->env['host'] ||
		    $website_host === $this->env['host'])
		{
			$this->is_common_host = true;
			$this->newsroom = Model_Newsroom::common();
			if (Auth::requires_user())
			     return Auth::check();
			else return;
		}
		
		$pattern = $this->conf('host_pattern');
		$detached_pattern = $this->conf('detached_pattern');
		$admo_pattern = $this->conf('admo_pattern');

		// extract the newsroom name for detached hosts
		if (preg_match($detached_pattern, $this->env['host'], $match))
		{
			$detached_session_id = $match[1];
			$this->newsroom = Detached_Session::load($detached_session_id);
			if (!$this->newsroom) show_404($this->env['requested_uri']);

			$this->is_detached_host = true;
			$this->is_own_domain = false;
			
			// tried to access protected 
			// section of the site from within
			// a detached session 
			if (Auth::requires_user()) 
				$this->redirect(gstring($this->newsroom->url(
					$this->env['requested_uri'])), false);
			
			// load feedback message for the user
			$feedback_view = 'manage/partials/is-detached-feedback';
			$feedback = $this->load->view($feedback_view, null, true);
			$this->use_feedback($feedback);
			
			if ($this->newsroom->is_common)
				$this->is_common_host = true;
		}
		// extract the user id for admin mode hosts
		else if (preg_match($admo_pattern, $this->env['host'], $match))
		{
			if (!Auth::is_admin_controlled())
				$this->denied();

			$admo_user_id = $match[1];
			$this->newsroom = $this->common();
			$this->is_common_host = true;
			$this->is_website_host = true;
			$this->is_admo_host = true;
			Auth::admo($admo_user_id);
		}
		// extract the newsroom name from standard hosts
		else if (preg_match($pattern, $this->env['host'], $match))
		{
			if (($redirect = Model_Newsroom_Redirect::find($match[1])))
			{
				$url = $redirect->mock_newsroom()->url($this->env['requested_uri']);
				$this->redirect_301($url, false);
			}

			$this->newsroom = Model_Newsroom::find_name($match[1]);
			if (!$this->newsroom) show_404($this->env['requested_uri']);
			if (!$this->newsroom->company_id) 
				$this->is_default_newsroom_host = true;

			// not on newsroom domain (unless header says otherwise)
			$this->is_own_domain = false || 
				(isset($this->env['headers']['x-hosted-on-domain'])
				    && $this->env['headers']['x-hosted-on-domain']
				    && $this->newsroom->domain);

			// hosted on domain 
			// with reverse proxy
			if ($this->is_own_domain)
			{
				$this->env['host'] = $this->newsroom->domain;
				$this->env['base_url'] = $this->newsroom->url(null, true);
			}
		}		
		// newsroom domains		
		else
		{
			$this->newsroom = Model_Newsroom::find_domain($this->env['host']);
			if (!$this->newsroom) show_404($this->env['requested_uri']);
			$this->is_own_domain = true;
		}

		Auth::from_secret();
		
		// do we need to switch to newsroom domain or visa-versa
		if ($this->newsroom->domain && !$this->is_detached_host)
		{
			$requires_own_domain = $this->newsroom->requires_own_domain();

			if (!$requires_own_domain && $this->is_own_domain)
			{
				$relative = $this->env['requested_uri'];
				$url = $this->newsroom->url($relative);
				$this->redirect(gstring($url), false);
			}

			if ($requires_own_domain && !$this->is_own_domain)
			{
				$relative = $this->env['requested_uri'];
				$url = $this->newsroom->url($relative, true);
				$this->redirect(gstring($url), false);
			}
		}

		// this is hack to make sure that 
		// comparison with NULL works as
		// it wouldn't if this were a string
		if (!$this->newsroom->company_id)
			$this->newsroom->company_id = 0;
		
		Auth::check();
	}

	public function is_development()
	{
		return $this->env['environment'] === 'development';
	}

	public function is_production()
	{
		return $this->env['environment'] === 'production';
	}
	
	protected function _check_ssl_status()
	{
		if ($this->input->is_cli_request())
			return;
		
		if ($this->ssl_required && 
		    $this->env['ssl_support'] &&
		   !$this->env['ssl_enabled'] && 
		   !$this->is_own_domain &&
		  (!$this->input->post() || $this->ssl_required_post))
		{
			// redirect to same url over ssl (works for subdomain too)
			$url = "https://{$this->env['host']}{$this->env['requested_uri']}";
			$this->redirect(gstring($url), false);	
		}
		
		if ($this->env['ssl_enabled'] &&
		   !$this->ssl_optional &&
		   !$this->ssl_required && 
		   !$this->input->post())
		{
			// redirect to same url without ssl (works for subdomain too)
			$url = "http://{$this->env['host']}{$this->env['requested_uri']}";
			$this->redirect(gstring($url), false);	
		}
	}

	// make an internal redirect (limited to 1!)
	public function _make_internal_redirect($internal_url)
	{
		if ($internal_url[0] === '/')
			$internal_url = substr($internal_url, 1);
		header("HTTP/1.0 350 INTERNAL REDIRECT");
		header("X-350-Location: /{$internal_url}");
		// used on development environment
		// where nGinX is not setup to handle #350
		echo "INTERNAL REDIRECT<br>/{$internal_url}";
		die();
	}
	
	// if the request was loaded internally
	// using the internal redirect mechanism
	public function _is_internal_redirect()
	{
		return !empty($this->env['headers']['x-internal-redirect']);
	}
	
	// if the request was loaded internally
	// using the internal redirect mechanism
	public function _internal_redirect_location()
	{
		return $this->env['headers']['x-internal-redirect-location'];
	}

	// obtain the current external url
	// in relative form. this will work 
	// with internal redirects as well 
	// as requests that are rewritten
	public function external_url()
	{
		if ($this->_is_internal_redirect())
			return substr($this->_internal_redirect_location(), 1);
		return $this->env['requested_uri'];
	}

	// full request uri including protocol
	// hostname, request path and query string
	// [*] does not include any user:pass string
	public function full_request_uri()
	{
		return gstring(sprintf('%s://%s%s', 
			$this->env['protocol'],
			$this->env['host'], 
			$this->env['requested_uri']));
	}
	
	public function _remap($method, $params = array())
	{
		$this->_check_ssl_status();

		$oee_method = '__on_execution_end';
		$oes_method = '__on_execution_start';
		$method_base = $method;
		$exception = null;
		$params_slice = 0;
		$success = false;
		$rv = null;
		
		if ($method === null) 
		{
			if (!$params) $params = array('index');
			$method_base = $method = $params[0];
			$params = array_slice($params, 1);
		}
		
		for ($i = 0; $i < count($params); $i++)
		{
			$method_base = "{$method_base}_{$params[$i]}";
			if ($this->__is_controllable_method($method_base))
				$params_slice = max($params_slice, $i + 1);
		}
		
		if ($params_slice > 0)
		{
			$method_params = implode('_', array_slice($params, 0, $params_slice));
			$params = array_slice($params, $params_slice);
			$method = "{$method}_{$method_params}";
			$this->params = $params;
			$this->method = $method;
			$this->controller_uri_parts[] = $method;
			call_user_func(array($this, $oes_method));
			try { $rv = call_user_func_array(array($this, $method), $params); }
			catch (Exception $exception) { $rv = null; }
			if ($exception && !$this->catch_with_oee) throw $exception;
			call_user_func(array($this, $oee_method), $exception);
			$this->output->_display();
			$this->__cache_request();
			$success = true;
		}		
		else if ($this->__is_controllable_method($method))
		{
			$this->params = $params;
			$this->method = $method;
			$this->controller_uri_parts[] = $method;
			call_user_func(array($this, $oes_method));
			try { $rv = call_user_func_array(array($this, $method), $params); }
			catch (Exception $exception) { $rv = null; }
			if ($exception && !$this->catch_with_oee) throw $exception;
			call_user_func(array($this, $oee_method), $exception);
			$this->output->_display();
			$this->__cache_request();
			$success = true;
		}		
		else if ($this->__is_controllable_method('index'))
		{
			$this->params = $params = array_merge(array($method), $params);
			$this->method = 'index';
			$this->controller_uri_parts[] = 'index';
			call_user_func(array($this, $oes_method));
			try { $rv = call_user_func_array(array($this, 'index'), $params); }
			catch (Exception $exception) { $rv = null; }
			if ($exception && !$this->catch_with_oee) throw $exception;
			call_user_func(array($this, $oee_method), $exception);
			$this->output->_display();
			$this->__cache_request();
			$success = true;
		}
		
		if ($success)
		{
			// process scheduled feedback for feedback
			// objects that have not required output
			// within the context of this request
			$this->process_scheduled_feedback(false);
			session_write_close();
			return $rv;
		}

		show_404();
	}
	
	public function log()
	{
		if (!$this->log_enabled)
			return;

		$args = func_get_args();
		$object = count($args) === 1 ? 
			$args[0] : $args;

		if ($this->log_file)
		     $log = $this->log_file;
		else $log = strtolower(implode('_', $this->controller_uri_parts));
		if (!$log) $log = 'system';

		$pid = getmypid();
		$date = Date::utc()->format(Date::FORMAT_LOG);
		$file = sprintf('application/logs/%s.log', $log);

		if (is_object($object) || is_array($object))
			$object = var_dump_capture($object);
		
		$object = trim($object);
		if (preg_match('#(\r?\n)#s', $object))
			$object = "\r\n{$object}";
		$object = preg_replace('#(\r?\n)#s', '$1  >>  ', $object);
		$string = "[PID:{$pid}] [{$date}] {$object} \r\n";

		$handle = fopen($file, 'a+');
		fwrite($handle, $string);
		fclose($handle);
	}

	public function current_method_uri()
	{
		return build_url($this->controller_uri_parts);
	}

	protected function __cache_request()
	{
		if ($this->cache_duration <= 0) return;
		if (Auth::is_user_online()) return;
		if ($this->input->post()) return;
		
		$saved = new stdClass();
		$saved->headers = array();
		$saved->content = ob_get_contents();
		while (ob_get_level())
			ob_end_flush();
	
		foreach (headers_list() as $header)
		{
			if (preg_match('#^location:#i', $header))
				$saved->headers[] = $header;
			if (preg_match('#^content-type:#i', $header))
				$saved->headers[] = $header;
			if (preg_match('#^expires:#i', $header))
				$saved->headers[] = $header;
			if (preg_match('#^date:(.*)$#i', $header))
				$saved->headers[] = $header;
			if (preg_match('#^last-modified:(.*)$#i', $header))
				$saved->headers[] = $header;
		}

		Data_Cache_ST::write($this->__cache_key(), 
			json_encode($saved), $this->cache_duration);
	}

	protected function __cache_key()
	{
		$data = new Data_Hash();
		$data->ssl = (int) $this->env['ssl_enabled'];
		$data->host = $this->env['host'];
		$data->path = $this->external_url();
		$data->query = gstring();

		return sprintf('cil_cache_%s', $data->hash());
	}

	protected function is_no_cache_request()
	{
		if ($this->input->post()) return true;
		if ($this->input->get('no_cache')) return true;
		if ($this->input->get('no-cache')) return true;

		$cache_control = $this->env['headers']['cache-control'];
		$pragma        = $this->env['headers']['pragma'];

		if (preg_match('#no-cache#is', $cache_control)) return true;
		if (preg_match('#no-cache#is', $pragma)) return true;

		return false;
	}
	
	protected function __is_controllable_method($method)
	{
		return method_exists($this, $method) && 
			(new ReflectionMethod($this, $method))->isPublic() && 
			!method_exists(get_class(), $method);
	}

	public function load_db($class)
	{
		if ($class === 'default')
			return $this->db;
		
		if (!isset(static::$__db[$class]))
		{
			$db = $this->load->database($class, TRUE);
			static::$__db[$class] = $db;
		}
		
		return static::$__db[$class];
	}
	
	public function common()
	{
		if ($this->m_common === NR_DEFAULT)
			$this->m_common = Model_Newsroom::common();
		return $this->m_common;
	}
	
	public function local_tz()
	{
		// already set? use that
		if ($this->local_tz) return $this->local_tz;

		// use the timezone for the current newsroom
		if ($this->newsroom && $this->newsroom->timezone) return 
			$this->local_tz = new DateTimeZone($this->newsroom->timezone);

		// use the timezone for the default newsroom for this user
		if (($user = Auth::user()) && ($default_newsroom = $user->default_newsroom()))
			if ($default_newsroom->timezone) return
				$this->local_tz = new DateTimeZone($default_newsroom->timezone);

		// use the default UTC timezone
		return $this->local_tz = new DateTimeZone($this->conf('timezone'));
	}

	protected function load_config()
	{
		if ($this->config_loaded) return;
		$this->config->load('newsroom', true);
		$this->config->load('search_bar', true);
		$this->config_loaded = true;
	}
	
	public function conf($name, $index = null)
	{
		// load the configuration
		$this->load_config();

		// load from the database first
		$value = Model_Setting::value($name);
		if ($value !== null) return $value;
		
		// load from the config files
		$value = $this->config->item($name, 'newsroom');
		if ($index !== null) $value = $value[$index];
		return $value;
	}
	
	public function json($data)
	{
		ob_clean();
		$data = json_encode($data);
		$this->output->set_content_type('application/json');
		$this->output->set_output($data);
	}
	
	public function redirect($url, $use_base = true, $terminate = true) 
	{
		// detect passing of absolute urls
		if ($use_base && is_absolute_url($url))
			return $this->redirect($url, false, $terminate);

		if ($use_base)
		{
			// prefix the base for an absolute url
			$base_url = $this->env['base_url'];
			$url = "{$base_url}{$url}";
		}
		
		if ($url === null) $this->redirect(null);
		$header = sprintf('location: %s', $url);
		header($header);

		if ($terminate)
		{
			// process scheduled feedback for feedback
			// objects that have not required 
			// output within the context of this request
			$this->process_scheduled_feedback(false);
			exit();
		}
	}

	public function redirect_301($url, $use_base = true, $terminate = true) 
	{
		header('HTTP/1.1 301 Moved Permanently');
		$this->redirect($url, $use_base, $terminate);
	}
	
	public function set_redirect($url, $use_base = true) 
	{
		$this->__set_redirect = new stdClass();
		$this->__set_redirect->url = $url;
		$this->__set_redirect->use_base = $use_base;
	}
	
	public function clear_redirect() 
	{
		header('location:');
		header_remove('location');
		$this->__set_redirect = null;
	}
	
	public function website_url($relative_url = null, $use_ssl = NR_DEFAULT) 
	{
		// refuse to convert an absolute url
		if (is_absolute_url($relative_url))
			return $relative_url;

		// make sure we don't get double slashes
		if (str_starts_with($relative_url, '/'))
			$relative_url = substr($relative_url, 1);

		if ($use_ssl === NR_DEFAULT)
			$use_ssl = $this->use_ssl();

		if ($use_ssl) 
		{
			return sprintf('https://%s/%s', 
				$this->conf('website_host'), 
				$relative_url);
		}
		else
		{
			return sprintf('http://%s/%s', 
				$this->conf('website_host'), 
				$relative_url);
		}
	}

	public function use_ssl($url = null)
	{
		// when running from cli we would never have
		// ssl enabled so we should try and guess
		// this based on the ssl_required flag
		$use_ssl = !$this->is_own_domain && 
			($this->input->is_cli_request()
				? $this->ssl_required
				: $this->env['ssl_enabled']);

		if ($url)
		     return $this->ssl_url($url);
		else return $use_ssl;
	}

	public function ssl_url($url = null) 
	{
		// convert an absolute url from HTTP to HTTPS
		return preg_replace('#^http://#is', 'https://', $url);
	}
	
	public function add_feedback($feedback)
	{
		$feedback = (string) $feedback;
		$session =& $this->session->reference();
		if (!isset($session['nr_feedback']) || 
		    !is_array($session['nr_feedback']))
			$session['nr_feedback'] = array();
		$session['nr_feedback'][UUID::create()] = $feedback;
	}
	
	public function use_feedback($feedback)
	{
		$feedback = (string) $feedback;
		if (!$this->feedback) $this->feedback = array();
		$this->feedback[UUID::create()] = $feedback;
	}										

	public function schedule_add_feedback($feedback, $pattern, 
		$allow_on_redirects = false)
	{
		$this->schedule_feedback($feedback, $pattern, 
			'add', $allow_on_redirects);
	}
	
	public function schedule_use_feedback($feedback, $pattern, 
		$allow_on_redirects = false)
	{
		$this->schedule_feedback($feedback, $pattern, 
			'use', $allow_on_redirects);
	}

	protected function schedule_feedback($feedback, $pattern, 
		$verb, $allow_on_redirects = false)
	{
		$feedback = (string) $feedback;
		$session =& $this->session->reference();
		if (!isset($session['nr_scheduled_feedback']) || 
		    !is_array($session['nr_scheduled_feedback']))
			$session['nr_scheduled_feedback'] = array();

		$_fb_ob = new stdClass();
		$_fb_ob->feedback = $feedback;
		$_fb_ob->pattern = $pattern;
		$_fb_ob->allow_on_redirects = $allow_on_redirects;
		$_fb_ob->verb = $verb;

		$session['nr_scheduled_feedback'][] = $_fb_ob;
	}

	protected function process_scheduled_feedback($is_cleared)
	{
		$session =& $this->session->reference();
		$scheduled =& $session['nr_scheduled_feedback'];
		if (!$scheduled) return;

		foreach ($scheduled as $k => $_fb_ob)
		{
			if (!$_fb_ob->allow_on_redirects && !$is_cleared) continue;
			if (!$_fb_ob->pattern || preg_match($_fb_ob->pattern, $this->uri->uri_string))
			{
				if ($_fb_ob->verb === 'use')
				     $this->use_feedback($_fb_ob->feedback);
				else $this->add_feedback($_fb_ob->feedback);
				array_splice($scheduled, $k, 1);
			}
		}
	}

	public function clear_feedback()
	{
		$session =& $this->session->reference();
		foreach ($this->feedback as $k => $v)
			unset($session['nr_feedback'][$k]);
	}

	public function process_feedback()
	{
		$this->clear_feedback();
		$this->process_scheduled_feedback(true);
	}
	
	public function add_eob($eob)
	{
		if (!$this->eob) $this->eob = array();
		$this->eob[] = $eob;
	}
	
	public function add_eoh($eoh)
	{
		if (!$this->eoh) $this->eoh = array();
		$this->eoh[] = $eoh;
	}

	public function is_ajax_request()
	{
		// attempt to detect AJAX request from headers (not guaranteed)
		return isset($this->env['headers']['x-requested-with']) &&
			 $this->env['headers']['x-requested-with'] === 'XMLHttpRequest';
	}
	
	public function check_blocked()
	{
		$address = $this->env['remote_addr'];
		$cookies = $this->env['cookies'];
		$ckename = $this->conf('blocked_cookie');
		
		if (isset($cookies[$ckename]))
		{
			$blocked = new Model_Blocked();
			$blocked->uuid_source = $cookies[$ckename];
			$blocked->addr = $address;
			$blocked->save();
			Auth::$is_blocked = true;
			return;
		}
		
		if (($mBlocked = Model_Blocked::find($address)))
		{
			setcookie($ckename, $mBlocked->uuid, 604800 + time(), 
				$this->env['session_path'],
				$this->env['session_domain']);
			Auth::$is_blocked = true;
			return;
		}
	}
	
	public function denied()
	{		
		if (!Auth::is_user_online())
		{
			// redirect to login and preserve intent
			$hash = md5(microtime(true));
			$uri = $this->full_request_uri();
			Data_Cache_LT::write($hash, $uri);
			$url = $this->website_url("login?intent={$hash}", true);
			$this->redirect($url, false);
		}
			
		// if its not manage then must be fail
		if ($this->uri->segment(1) !== 'manage' &&
		    $this->uri->segment(1) !== 'reseller')
			show_404();
		
		// are there some segments we can reverse?
		if (count($segments = $this->uri->segment_array()) <= 1)
			show_404();
		
		// reverse segments to try again
		$segments = array_slice($segments, 0, -1);
		$url = implode('/', $segments);
		$this->redirect($url);
	}

	public function _output($output)
	{
		if ($this->__set_redirect)
		{
			$redirect = $this->__set_redirect;
			$this->redirect($redirect->url, 
				$redirect->use_base, false);
		}

		if (headers_sent() || $this->input->is_cli_request())
		{
			echo $output;
			return;
		}

		$headers = headers_list();
		$has_content_type_header = false;
		$content_type = null;
		
		foreach ($headers as $header)
		{
			if (preg_match('#^content-type:\s*(.*)$#i', $header, $match)) 
			{
				$content_type = $match[1];
				$has_content_type_header = true;
				break;
			}
		}

		if ($this->compression && in_array($content_type, $this->compression_mimes))
		{
			if (isset($this->env['headers']['accept-encoding']) && 
				strpos($this->env['headers']['accept-encoding'], 'gzip') !== false)
			{
				header_remove('content-length');
				ob_start('ob_gzhandler');
			}
		}

		if (!$has_content_type_header)
		{
			$content_type = $this->conf('content_type');
			header("content-type: {$content_type}");
		}

		echo $output;
	}

	public function is_fallback_server()
	{
		return (bool) file_exists('/etc/is_fallback_server');
	}

	public function allow_cors()
	{
		if (isset($this->env['headers']['origin']))
		{
			$origin = $this->env['headers']['origin'];
			header(sprintf('access-control-allow-origin: %s', $origin));
			header('access-control-allow-credentials: true');
		}
	}
	
	public function expires($expires_secs)
	{
		// allows cache for $expires_secs
		$expires_time = time() + $expires_secs;
		$expires_date = gmdate(DateTime::RFC1123, $expires_time);
		header("cache-control: public, max-age={$expires_secs}");
		header("expires: {$expires_date}");
		header("pragma: public");
	}
	
	public function force_download($name, $type, $size = false)
	{
		// force the user to download the file
		$expires_date = gmdate(DateTime::RFC1123, 0);
		header("pragma: public");
		header("expires: {$expires_date}");
		header("cache-control: must-revalidate, post-check=0, pre-check=0");
		header("cache-control: private", false);
		header("content-disposition: attachment; filename=\"{$name}\"");
		header("content-transfer-encoding: binary");
		if ($size !== false)
			header("content-length: {$size}");
		header("content-type: {$type}");
		header("connection: close");
	}

	protected function __on_execution_start()
	{
		// set the profiler uri to the current uri without parameters
		$uri = build_url($this->controller_uri_parts);
		CIL_Profiler::instance()->set_uri($uri);
	}

	protected function __on_execution_end()
	{
		// --------------------------------
	}

}
