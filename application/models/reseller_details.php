<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_Reseller_Details extends Model {	
	
	private $website;
	
	protected static $__table        = 'rw_reseller_details';
	protected static $__primary      = 'user_id';
	
	const PRIV_ADMIN_EDITOR          = 'admin_editor';
	const PRIV_DIRECTLY_QUEUE_DRAFT  = 'directly_queue_draft';
	const PRIV_RESELLER_EDITOR       = 'reseller_editor';
	
	public function __get($name)
	{
		if ($name === 'website')
			return $this->website_url();
	}
	
	public function __set($name, $value)
	{
		$this->{$name} = $value;
	}
	
	public function website_url($relative = null)
	{
		if (!$this->website) return null;
		if (substr($this->website, -1) === '/')
			  return "{$this->website}{$relative}";
		else return "{$this->website}/{$relative}";
	}
	
	public function preview_url($order_id, $order_code)
	{
		$url = "PreviewNewPR.php?id={$order_id}";
		$url = "{$url}&view=customer&tcode={$order_code}";
		return $this->website_url($url);		
	}
	
}

?>