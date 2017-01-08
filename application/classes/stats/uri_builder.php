<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stats_URI_Builder {

	// ACTION_VIEW is default assumed
	// const ACTION_VIEW = null;
	const ACTION_IMPRESSION = 'IMPRESSION';
	const ACTION_VIEW_NETWORK = 'VIEW_NETWORK';

	protected $rec;
	protected $sum;
	protected $ref;

	const MEDIA_IMAGE = 'im';
	const MEDIA_JS    = 'js';

	public function __construct()
	{
		$this->rec = array();
		$this->sum = array();
		$this->ref = array();
	}

	public function add_content_view($m_newsroom, $m_content)
	{
		// user and content type
		$hash = new Stats_Hash();
		$hash->user = $m_newsroom->user_id;
		$hash->type = $m_content->type;
		$context = $hash->context_encoded();
		$this->rec[] = $context;
		$this->sum[] = $context;

		// company and content type
		$hash = new Stats_Hash();
		$hash->company = $m_newsroom->company_id;
		$hash->type = $m_content->type;
		$context = $hash->context_encoded();
		$this->rec[] = $context;
		$this->sum[] = $context;

		// just the content 
		$hash = new Stats_Hash();
		$hash->content = $m_content->id;
		$context = $hash->context_encoded();
		$this->rec[] = $context;
		$this->sum[] = $context;
		$this->ref[] = $context;
	}

	public function add_remote_content_view($m_content, $source)
	{
		// just the content 
		$hash = new Stats_Hash();
		$hash->content = $m_content->id;
		$hash->source = $source;
		$context = $hash->context_encoded();
		$this->rec[] = $context;
		$this->sum[] = $context;
	}

	public function add_network_content_view($m_content)
	{
		// just the content 
		$hash = new Stats_Hash();
		$hash->action = static::ACTION_VIEW_NETWORK;
		$hash->content = $m_content->id;
		$context = $hash->context_encoded();
		$this->rec[] = $context;
		$this->sum[] = $context;
	}

	// !! do not include views as we just add that on
	public function add_content_impression($m_content)
	{
		// just the content 
		$hash = new Stats_Hash();
		$hash->action = static::ACTION_IMPRESSION;
		$hash->content = $m_content->id;
		$context = $hash->context_encoded();
		$this->rec[] = $context;
		$this->sum[] = $context;
	}

	public function add_newsroom_view($m_newsroom)
	{
		// just the company 
		$hash = new Stats_Hash();
		$hash->company = $m_newsroom->company_id;
		$context = $hash->context_encoded();
		$this->rec[] = $context;
		$this->sum[] = $context;

		// just the user 
		$hash = new Stats_Hash();
		$hash->user = $m_newsroom->user_id;
		$context = $hash->context_encoded();
		$this->rec[] = $context;
		$this->sum[] = $context;
	}

	public function build($media = null, $add_scheme = false)
	{
		if ($media === null)
			$media = static::MEDIA_IMAGE;
		
		$ci =& get_instance();
		$rec_enc = Stats_Engine::data_encode($this->rec);
		$sum_enc = Stats_Engine::data_encode($this->sum);
		$ref_enc = Stats_Engine::data_encode($this->ref);
		$format = $add_scheme ? 
			'http://%s/hit/%s?%s' : 
			'//%s/hit/%s?%s';

		$params = array();
		$params['rec'] = $rec_enc;
		$params['sum'] = $sum_enc;
		$params['ref'] = $ref_enc;
		$this->compact_params($params);

		return sprintf($format, 
			$ci->conf('stats_host'),
			$media, 
			http_build_query($params)
		);
	}

	protected function compact_params(&$params)
	{
		// compact the params to 
		// use comma notation for 
		// equal values
		foreach ($params as $k => $v)
		{
			// check for unset() call
			if (!isset($params[$k])) continue;
			$keys = array_keys($params, $v);

			foreach ($keys as $_ => $k2)
			{
				if ($k2 != $k) 
				{
					unset($params[$k]);
					unset($params[$k2]);
					$k = comma_separate(array($k, $k2));
					$params[$k] = $v;
				}
			}
		}
	}

}

?>