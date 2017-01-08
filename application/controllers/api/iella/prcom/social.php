<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('api/iella/prcom/base');

class Social_Controller extends PRCom_API_Base {
	
	public function parse()
	{
		$this->iella_out = $this->iella_in;
		
		if ($this->iella_in->soc_twitter)
		{
			$parsed = Social_Twitter_Profile::parse_id($this->iella_in->soc_twitter);
			$this->iella_out->soc_twitter = $parsed;
		}
		
		if ($this->iella_in->soc_facebook)
		{
			$parsed = Social_Facebook_Profile::parse_id($this->iella_in->soc_facebook);
			$this->iella_out->soc_facebook = $parsed;
		}
		
		if ($this->iella_in->soc_gplus)
		{
			$parsed = Social_GPlus_Profile::parse_id($this->iella_in->soc_gplus);
			$this->iella_out->soc_gplus = $parsed;
		}
		
		if ($this->iella_in->soc_youtube)
		{
			$parsed = Social_Youtube_Profile::parse_id($this->iella_in->soc_youtube);
			$this->iella_out->soc_youtube = $parsed;
		}
		
		if ($this->iella_in->soc_linkedin)
		{
			$parsed = Social_Linkedin_Profile::parse_id($this->iella_in->soc_linkedin);
			$this->iella_out->soc_linkedin = $parsed;
		}
		
		if ($this->iella_in->soc_pinterest)
		{
			$parsed = Social_Pinterest_Profile::parse_id($this->iella_in->soc_pinterest);
			$this->iella_out->soc_pinterest = $parsed;
		}
	}

	public function urls()
	{
		$this->iella_out = $this->iella_in;
		
		if ($this->iella_in->soc_twitter)
		{
			$parsed = Social_Twitter_Profile::url($this->iella_in->soc_twitter);
			$this->iella_out->soc_twitter = $parsed;
		}
		
		if ($this->iella_in->soc_facebook)
		{
			$parsed = Social_Facebook_Profile::url($this->iella_in->soc_facebook);
			$this->iella_out->soc_facebook = $parsed;
		}
		
		if ($this->iella_in->soc_gplus)
		{
			$parsed = Social_GPlus_Profile::url($this->iella_in->soc_gplus);
			$this->iella_out->soc_gplus = $parsed;
		}
		
		if ($this->iella_in->soc_youtube)
		{
			$parsed = Social_Youtube_Profile::url($this->iella_in->soc_youtube);
			$this->iella_out->soc_youtube = $parsed;
		}
		
		if ($this->iella_in->soc_linkedin)
		{
			$parsed = Social_Linkedin_Profile::url($this->iella_in->soc_linkedin);
			$this->iella_out->soc_linkedin = $parsed;
		}
		
		if ($this->iella_in->soc_pinterest)
		{
			$parsed = Social_Pinterest_Profile::url($this->iella_in->soc_pinterest);
			$this->iella_out->soc_pinterest = $parsed;
		}
	}
	
}

?>