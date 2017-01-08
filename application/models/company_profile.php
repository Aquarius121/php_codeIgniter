<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Model_Company_Profile extends Model {

	use Raw_Data_Trait;
	
	protected static $__table = 'nr_company_profile';
	protected static $__primary = 'company_id';
	
	public function clean_soc()
	{
		if ($this->soc_twitter)
		{
			$parsed = Social_Twitter_Profile::parse_id($this->soc_twitter);
			$this->soc_twitter = $parsed;
		}
		
		if ($this->soc_facebook)
		{
			$parsed = Social_Facebook_Profile::parse_id($this->soc_facebook);
			$this->soc_facebook = $parsed;
		}
		
		if ($this->soc_gplus)
		{
			$parsed = Social_GPlus_Profile::parse_id($this->soc_gplus);
			$this->soc_gplus = $parsed;
		}
		
		if ($this->soc_youtube)
		{
			$parsed = Social_Youtube_Profile::parse_id($this->soc_youtube);
			$this->soc_youtube = $parsed;
		}
		
		if ($this->soc_linkedin)
		{
			$parsed = Social_Linkedin_Profile::parse_id($this->soc_linkedin);
			$this->soc_linkedin = $parsed;
		}
		
		if ($this->soc_pinterest)
		{
			$parsed = Social_Pinterest_Profile::parse_id($this->soc_pinterest);
			$this->soc_pinterest = $parsed;
		}

		if ($this->soc_vimeo)
		{
			$parsed = Social_Vimeo_Profile::parse_id($this->soc_vimeo);
			$this->soc_vimeo = $parsed;
		}

		if ($this->soc_instagram)
		{
			$parsed = Social_Instagram_Profile::parse_id($this->soc_instagram);
			$this->soc_instagram = $parsed;
		}

		if ($this->soc_linkedin)
		{
			$parsed = Social_Linkedin_Profile::parse_id($this->soc_linkedin);
			$this->soc_linkedin = $parsed;
		}
	}

	public function social_accounts_for_widget()
	{
		$this->clean_soc();
		$soc_accounts = array();

		if ($this->is_facebook_feed_valid())
			$soc_accounts['fblike'] = $this->soc_facebook;
		if ($this->is_twitter_feed_valid())
			$soc_accounts['twitter'] = $this->soc_twitter;
		if ($this->is_gplus_feed_valid())
			$soc_accounts['google'] = $this->soc_gplus;	
		
		if ($this->soc_linkedin)
		{
			$pattern = '#(company)/([0-9]+)#is';
			if (preg_match($pattern, $this->soc_linkedin, $match)) 
			     $soc_accounts['linkedin'] = $match[2];
			else $soc_accounts['linkedin'] = $this->soc_linkedin;
		}

		if ($this->is_youtube_feed_valid())
			$soc_accounts['youtube'] = $this->soc_youtube;
		if ($this->is_pinterest_feed_valid())
			$soc_accounts['pinterest'] = $this->soc_pinterest;

		if ($this->is_vimeo_feed_valid() && 
			!preg_match('/^channels\//', $this->soc_vimeo, $matches))
			$soc_accounts['vimeo'] = $this->soc_vimeo;

		if ($this->soc_rss)
			$soc_accounts['rss'] = $this->soc_rss;

		if (count($soc_accounts))
		{
			$keys = array_keys($soc_accounts);
			$widgets = comma_separate($keys);
		}
		
		$m_ids = array();
		foreach ($soc_accounts as $key => $value)
			$m_ids[] = sprintf('%sId: %s', $key, 
				single_quote($value));
		$media_ids = implode(",\n", $m_ids);

		return array(
			'media' => array_keys($soc_accounts),
			'media_ids' => $media_ids, 
			'soc_accounts' => $soc_accounts,
			'widgets' => $widgets,
		);
	}

	public function social_wire_media()
	{
		return $this->get_social_wire_media();
	}

	public function get_social_wire_media()
	{
		$this->clean_soc();
		$soc_media = array();

		if ($this->is_enable_social_wire)
		{
			if ($this->is_inc_facebook_in_soc_wire())
				$soc_media[] = Model_PB_Social::TYPE_FACEBOOK;

			if ($this->is_inc_twitter_in_soc_wire())
				$soc_media[] = Model_PB_Social::TYPE_TWITTER;

			if ($this->is_inc_gplus_in_soc_wire())
				$soc_media[] = Model_PB_Social::TYPE_GPLUS;

			if ($this->is_inc_youtube_in_soc_wire())
				$soc_media[] = Model_PB_Social::TYPE_YOUTUBE;

			if ($this->is_inc_pinterest_in_soc_wire())
				$soc_media[] = Model_PB_Social::TYPE_PINTEREST;

			if ($this->is_inc_vimeo_in_soc_wire())
				$soc_media[] = Model_PB_Social::TYPE_VIMEO;

			if ($this->is_inc_instagram_in_soc_wire())
				$soc_media[] = Model_PB_Social::TYPE_INSTAGRAM;

			if ($this->is_inc_linkedin_in_soc_wire())
				$soc_media[] = Model_PB_Social::TYPE_LINKEDIN;

		}

		return $soc_media;
	}

	public function has_any_valid_social_feed()
	{
		return ($this->is_inc_facebook_in_soc_wire()
			|| $this->is_inc_twitter_in_soc_wire()
			|| $this->is_inc_gplus_in_soc_wire()
			|| $this->is_inc_youtube_in_soc_wire()
			|| $this->is_inc_pinterest_in_soc_wire()
			|| $this->is_inc_vimeo_in_soc_wire()
			|| $this->is_inc_instagram_in_soc_wire()
			|| $this->is_inc_linkedin_in_soc_wire());
	}

	protected function load_social_wire_settings()
	{
		if (empty($this->soc_wire_settings))
			$this->soc_wire_settings = $this->raw_data_object('social_wire_settings');
	}

	public function is_inc_facebook_in_soc_wire()
	{		
		$this->load_social_wire_settings();
		return $this->is_enable_social_wire && $this->soc_facebook && $this->soc_wire_settings->soc_facebook_is_feed_valid
			&& $this->soc_wire_settings->is_inc_facebook_in_soc_wire;
	}

	public function is_inc_twitter_in_soc_wire()
	{
		$this->load_social_wire_settings();
		return $this->is_enable_social_wire && $this->soc_twitter && $this->soc_wire_settings->soc_twitter_is_feed_valid 
			&& $this->soc_wire_settings->is_inc_twitter_in_soc_wire;
	}

	public function is_inc_gplus_in_soc_wire()
	{
		$this->load_social_wire_settings();
		print_r($this->sw_settings);
		return $this->is_enable_social_wire && $this->soc_gplus && $this->soc_wire_settings->soc_gplus_is_feed_valid 
			&& $this->soc_wire_settings->is_inc_gplus_in_soc_wire;
	}

	public function is_inc_pinterest_in_soc_wire()
	{
		$this->load_social_wire_settings();
		return $this->is_enable_social_wire && $this->soc_pinterest && $this->soc_wire_settings->soc_pinterest_is_feed_valid 
			&& $this->soc_wire_settings->is_inc_pinterest_in_soc_wire;
	}

	public function is_inc_youtube_in_soc_wire()
	{
		$this->load_social_wire_settings();
		return $this->is_enable_social_wire && $this->soc_youtube && $this->soc_wire_settings->soc_youtube_is_feed_valid 
			&& $this->soc_wire_settings->is_inc_youtube_in_soc_wire;
	}

	public function is_inc_vimeo_in_soc_wire()
	{
		$this->load_social_wire_settings();
		return $this->is_enable_social_wire && $this->soc_vimeo && $this->soc_wire_settings->soc_vimeo_is_feed_valid 
			&& $this->soc_wire_settings->is_inc_vimeo_in_soc_wire;
	}

	public function is_inc_instagram_in_soc_wire()
	{
		$this->load_social_wire_settings();
		return $this->is_enable_social_wire && $this->soc_instagram && $this->soc_wire_settings->soc_instagram_is_feed_valid 
			&& $this->soc_wire_settings->is_inc_instagram_in_soc_wire;
	}

	public function is_inc_linkedin_in_soc_wire()
	{
		$this->load_social_wire_settings();
		return $this->is_enable_social_wire && $this->is_linkedin_feed_valid() 
			&& $this->soc_wire_settings->is_inc_linkedin_in_soc_wire;
	}

	public function is_facebook_feed_valid()
	{
		$this->load_social_wire_settings();
		return $this->soc_facebook && $this->soc_wire_settings->soc_facebook_is_feed_valid;
	}

	public function is_twitter_feed_valid()
	{
		$this->load_social_wire_settings();
		return $this->soc_twitter && $this->soc_wire_settings->soc_twitter_is_feed_valid;
	}

	public function is_gplus_feed_valid()
	{
		$this->load_social_wire_settings();
		return $this->soc_gplus && $this->soc_wire_settings->soc_gplus_is_feed_valid;
	}

	public function is_pinterest_feed_valid()
	{
		$this->load_social_wire_settings();
		return $this->soc_pinterest && $this->soc_wire_settings->soc_pinterest_is_feed_valid;
	}

	public function is_youtube_feed_valid()
	{
		$this->load_social_wire_settings();
		return $this->soc_youtube && $this->soc_wire_settings->soc_youtube_is_feed_valid;
	}

	public function is_vimeo_feed_valid()
	{
		$this->load_social_wire_settings();
		return $this->soc_vimeo && $this->soc_wire_settings->soc_vimeo_is_feed_valid;
	}

	public function is_instagram_feed_valid()
	{
		$this->load_social_wire_settings();
		return $this->soc_instagram && $this->soc_wire_settings->soc_instagram_is_feed_valid;
	}

	public function is_linkedin_feed_valid()
	{
		$linkedin_auth = Social_Linkedin_Auth::find($this->company_id);
		return $this->soc_linkedin && $linkedin_auth;
	}

}

?>