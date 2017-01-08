<?php 

class HTTP_Request {
	
	const METHOD_POST    = 'POST';
	const METHOD_GET     = 'GET';	
	const METHOD_PUT     = 'PUT';
	const METHOD_DELETE  = 'DELETE';
	const METHOD_HEAD    = 'HEAD';
	const METHOD_OPTIONS = 'OPTIONS';
	const DEFAULT_UA     = 'Newswire';

	public $url;
	public $data;
	public $conf;
	public $error;
	
	protected $headers;
	protected $files;
	protected $timeout = 60;
		
	public function __construct($url = null)
	{
		$this->url = $url;
		$this->headers = array();
		$this->data = new Raw_Data();
		$this->conf = new Raw_Data();
		$this->conf->http = new Raw_Data();
		$this->conf->ssl = new Raw_Data();
		$this->files = array();

		$this->set_header('User-Agent',
			static::DEFAULT_UA);
	}

	public function set_http_version($version)
	{
		if (!isset($this->conf->http))
			$this->conf->http = array();
		$this->conf->http->protocol_version = $version;
	}

	public function set_timeout($value = 60)
	{
		$this->timeout = $value;
	}
	
	public function set_header($name, $value)
	{
		$header = "{$name}: {$value}";
		$lower_name = strtolower($name);
		$this->headers[$lower_name] = $header;
	}

	public function add_file($name, $file)
	{
		$_file = new stdClass();
		$_file->name = $name;
		$_file->path = $file;
		$_file->mime = File_Util::detect_mime($file);
		$this->files[] = $_file;
	}
	
	public function post()
	{
		return $this->exec(static::METHOD_POST);
	}
	
	public function get()
	{
		return $this->exec(static::METHOD_GET);
	}

	public function disable_redirects()
	{
		$this->conf->http->follow_location = 0;
	}

	public function enable_redirects()
	{
		$this->conf->http->follow_location = 1;
	}

	public function disable_ssl_verification()
	{
		$this->conf->ssl->verify_peer = false;
		$this->conf->ssl->verify_peer_name = false;
	}

	public function add_data_to_query_string()
	{
		if ($this->data instanceof Raw_Data || is_object($this->data))
			$this->data = get_object_vars($this->data);

		if (is_array($this->data) && count($this->data))
		{
			$this->url = insert_into_query_string($this->url, $this->data);
			$this->data = new Raw_Data();
		}
	}
	
	public function follow_redirects($limit = 10)
	{
		$response = new HTTP_Response();
		
		while (--$limit > 0)
		{
			// fetch response headers
			$headers = @get_headers($this->url);
			if (!$headers) return false;
			$response->headers = $headers;
			$redirect_url = $response->header('location');
			if (!$redirect_url) return $this->url;
			$this->url = $redirect_url;
		}
	}

	public function encode_as_form_data()
	{
		$multipart = new p3k\Multipart();
		$multipart->addArray($this->data);
		foreach ($this->files as $file)
			$multipart->addFile($file->name, $file->path, $file->mime);
		$this->data = $multipart->data();
		$this->set_header('Content-Type', 
			$multipart->contentType());
	}
	
	public function exec($method)
	{
		$headers =& $this->headers;
		foreach ($headers as $k => $v)
		{
			$headers[$k] = str_replace("\r", '', $headers[$k]);
			$headers[$k] = str_replace("\n", '', $headers[$k]);
		}

		if ($this->data instanceof Raw_Data || is_object($this->data))
			$this->data = get_object_vars($this->data);

		if (is_array($this->data) && count($this->data))
		{			
			if ($method === static::METHOD_GET)
			{
				$this->url = insert_into_query_string($this->url, $this->data);
				$this->data = new Raw_Data();
			}	
			else
			{
				if (count($this->files))
				{
					$this->encode_as_form_data();
				}
				else
				{
					$this->data = http_build_query($this->data);
					$this->set_header('Content-Type', 
						'application/x-www-form-urlencoded');
				}
			}	
		}
		
		if (is_string($this->data) && $len = strlen($this->data))
		{
			if (!isset($headers['content-type']))
				$this->set_header('Content-Type', 'text/plain');
			$this->set_header('Content-Length', $len);
		}

		$context_o = array();
		$context_o['http'] = array();
		$context_o['http']['method'] = $method;
		$context_o['http']['header'] = implode("\r\n", $headers);
		$context_o['http']['content'] = $this->data;

		// apply options given in options property
		foreach ($this->conf as $name => $value)
		{
			if (!isset($context_o[$name]))
				$context_o[$name] = array();
			$context_o[$name] = array_merge($context_o[$name], (array) $value);
		}
		
		$context = stream_context_create($context_o);
		$handle = @fopen($this->url, 'b', false, $context);
		
		if ($handle === false)
		{
			$this->error = error_get_last();
			return false;
		}
		
		stream_set_timeout($handle, 
			$this->timeout);

		$res = new HTTP_Response();
		$meta = stream_get_meta_data($handle);
		$res->headers = $meta['wrapper_data'];
		$res->data = stream_get_contents($handle);
		fclose($handle);

		return $res;
	}
	
}

