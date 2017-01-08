<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cart_Item extends Model_Base implements JsonSerializable {
	
	public $attached = array();
	public $callback;
	public $item_id;
	public $order_event;
	public $quantity;
	public $track;

	// if the item is hidden from cart
	public $hidden = false;

	// use to keep track of a cart item
	// when it's hash changes
	protected $token;

	// these are made public using
	// __get and __set so need to 
	// be added when serialized
	protected $is_quantity_unlocked = null;
	protected $price = 0;
	protected $name;

	protected $item;
	protected $attached_discount = null;

	// these can be set with __set 
	// but are protected for read
	protected $allowed_setters = array(
		'is_quantity_unlocked',
		'price',
		'name',
	);

	public function jsonSerialize()
	{
		return $this->to_object();
	}

	public function to_object()
	{
		$object = new stdClass();
		$object->token = $this->token;
		$object->attached = array();
		foreach ($this->attached as $atd)
			$object->attached[$atd->token] = $atd->to_object();
		$object->callback = $this->callback;
		$object->item_id = $this->item_id;
		$object->quantity = $this->quantity;
		$object->is_quantity_unlocked = $this->is_quantity_unlocked;
		$object->track = $this->track;
		$object->price = $this->price;
		$object->order_event = $this->order_event;
		$object->hidden = $this->hidden;
		$object->name = $this->name;
		return $object;
	}

	public static function from_object($source, $prefixes = array())
	{
		$instance = parent::from_object_sub($source, $prefixes);		
		$instance->track = Raw_Data::from_object($instance->track);
		$instance->attached = (array) $instance->attached;
		foreach ($instance->attached as $token => $atd)
			$instance->attached[$token] = static::from_object($atd);
		return $instance;
	}

	public function __set($name, $value)
	{
		$method = sprintf('__set_%s', $name);
		if (method_exists($this, $method)) $this->$method($value);
		if (in_array($name, $this->allowed_setters))
			$this->{$name} = $value;
	}

	public function __get($name)
	{
		$method = sprintf('__get_%s', $name);
		if (method_exists($this, $method))
			return $this->$method();
		return null;
	}

	public function flatten()
	{
		$flat = array();
		$flat[$this->token] = $this;
		foreach ($this->attached as $atd)
			$flat = array_merge($flat, $atd->flatten());
		return $flat;
	}
	
	public function hash()
	{
		$hashable = new stdClass();
		$hashable->item_id = $this->item_id;
		$hashable->price = $this->price;
		$hashable->quantity = $this->quantity;
		$hashable->attached = array();
		foreach ($this->attached as $atd)
			$hashable->attached[] = $atd->hash();

		return md5(json_encode($hashable));
	}

	public function has_visible_attached()
	{
		foreach ($this->attached as $atd)
			if (!$atd->hidden) return true;
		return false;
	}
	
	public static function create($item, $quantity = 1, $price = null)
	{
		$instance = new static();
		$instance->token = md5(UUID::create());
		$instance->track = new Raw_Data();
		$instance->quantity = $quantity;
		if (!($item instanceof Model_Item))
			$item = Model_Item::find($item);
		$instance->order_event = $item->order_event;
		$instance->item_id = $item->id;
		$instance->price = $item->price;
		if ($price !== null)
			$instance->price = $price;
		$instance->item = $item;
		return $instance;
	}

	// return the quantity unlocked value (if set)
	// or fallback to the quantity unlocked data in item
	public function __get_is_quantity_unlocked()
	{
		if ($this->is_quantity_unlocked !== null)
			return $this->is_quantity_unlocked;
		$item = $this->item();
		if (!$item) return false;
		$data = $item->raw_data();
		if (isset($data->is_quantity_unlocked))
			return $data->is_quantity_unlocked;
		return false;
	}

	// compute the price recursively
	public function __get_price()
	{
		$price = $this->price;
		foreach ($this->attached as $atd)
			$price += $atd->price_total();
		$price -= $this->attached_discount();
		return $price;
	}

	public function __get_name()
	{
		if ($this->name) return $this->name;
		return $this->item()->name;
	}
	
	public function item()
	{
		if (!$this->item)
			$this->item = Model_Item::find($this->item_id);
		return $this->item;
	}
	
	public function price_total()
	{
		return $this->__get_price() * $this->quantity;
	}

	public function base_price_total()
	{
		return $this->price * $this->quantity;
	}

	public function base_price()
	{
		return $this->price;
	}

	public function attach($cart_item)
	{
		if ($cart_item instanceof Model_Item)
			$cart_item = static::create($cart_item);
		$this->attached[$cart_item->token] = $cart_item;
		return $cart_item;
	}

	public function attached_discount()
	{
		if ($this->attached_discount !== null)
			return $this->attached_discount;

		$this->attached_discount = 0;
		$iad = Model_Item_Attachment_Discount::find_attached($this, $this->attached);
		if ($iad) $this->attached_discount = (float) $iad->discount;
		return $this->attached_discount;
	}

	public function token()
	{
		return $this->token;
	}

	// used to capture the item name
	// so that it can be used in debugging
	public function _cache_item_name()
	{
		$this->name = $this->item()->name;
		foreach ($this->attached as $atd)
			$atd->_cache_item_name();
	}
	
}