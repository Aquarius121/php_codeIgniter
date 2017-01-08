<?php

class Copyscape_API_Factory {

	public static function create()
	{
		$config = get_instance()->conf('copyscape');
		return new Copyscape_API($config);
	}

}