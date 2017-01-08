<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Package {

	const PACKAGE_SILVER = 1;
	const PACKAGE_GOLD = 2;
	const PACKAGE_PLATINUM = 3;
	const PACKAGE_BASIC = 4;

	public static function name($package)
	{
		if ($package == static::PACKAGE_PLATINUM)
			  return 'Platinum';
		else if ($package == static::PACKAGE_GOLD)
			  return 'Gold';
		else if ($package == static::PACKAGE_SILVER)
			  return 'Silver';
		else if ($package == static::PACKAGE_BASIC)
			  return 'Basic';
		else return 'None';
	}

}
