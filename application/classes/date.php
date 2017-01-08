<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Date {
	
	// format used for mysql datetime
	const FORMAT_MYSQL = 'Y-m-d H:i:s';
	
	// format used for log output
	const FORMAT_LOG = 'Y-m-d H:i:s';
	
	// format used for salesforce
	const FORMAT_SF = 'c';

	// other standard formats
	const FORMAT_ATOM    = 'Y-m-d\TH:i:sP';
	const FORMAT_COOKIE  = 'l, d-M-Y H:i:s T';
	const FORMAT_ISO8601 = 'Y-m-d\TH:i:sP';
	const FORMAT_RFC1036 = 'D, d M y H:i:s O';
	const FORMAT_RFC1123 = 'D, d M Y H:i:s O';
	const FORMAT_RFC2822 = 'D, d M Y H:i:s O';
	const FORMAT_RFC3339 = 'Y-m-d\TH:i:sP';
	const FORMAT_RFC7231 = 'D, d M Y H:i:s \\G\\M\\T';
	const FORMAT_RFC822  = 'D, d M y H:i:s O';
	const FORMAT_RFC850  = 'l, d-M-y H:i:s T';
	const FORMAT_RSS     = 'D, d M Y H:i:s O';
	const FORMAT_W3C     = 'Y-m-d\TH:i:sP';
	
	public static $now;
	public static $utc;
	
	protected static $__periods = array(
		// the values for year and month are for 365 and 30 days respectively
		array('name' => 'year', 'name_plural' => 'years', 'divisor' => 31536000),
		array('name' => 'month', 'name_plural' => 'months', 'divisor' => 2592000),
		array('name' => 'day', 'name_plural' => 'days', 'divisor' => 86400),
		array('name' => 'hour', 'name_plural' => 'hours', 'divisor' => 3600),
		array('name' => 'minute', 'name_plural' => 'minutes', 'divisor' => 60),
		array('name' => 'second', 'name_plural' => 'seconds', 'divisor' => 1),
	);
	
	public static function difference_in_words($to, $from = null, $short = false)
	{
		if ($from === null) $from = Date::$now;
		$difference = $from->getTimestamp() - $to->getTimestamp();
		if ($difference === 0) return 'now';
		$absolute = abs($difference);
		
		for ($i = 0, $c = count(static::$__periods); $i < $c; $i++)
		{
			$divisor = static::$__periods[$i]['divisor'];
			
			if ($absolute >= $divisor)
			{
				$rounded = (int) round($absolute / $divisor);
				$name = ($rounded === 1 ? static::$__periods[$i]['name'] :
					static::$__periods[$i]['name_plural']);

				if ($short)
					return sprintf('%s %s', 
						$rounded, $name);
				
				return ($difference > 0 ?
					sprintf('%s %s ago', $rounded, $name) :
					sprintf('%s %s from now', $rounded, $name));
			}
		}
	}
	
	public static function in($str = 'now', $timezone = null)
	{
		if ($timezone === null)
			$timezone = static::local_tz();
		if (!($timezone instanceof DateTimeZone))
			$timezone = new DateTimeZone($timezone);
		$datetime = DateTimeExtended::__new((string) $str, $timezone);
		if (!$datetime) return null;
		$datetime->setTimezone(Date::$utc);
		return $datetime;
	}
	
	public static function out($str = 'now', $timezone = null)
	{
		if ($timezone === null)
			$timezone = static::local_tz();
		if (!($timezone instanceof DateTimeZone))
			$timezone = new DateTimeZone($timezone);
		$datetime = DateTimeExtended::__new((string) $str, Date::$utc);
		if (!$datetime) return null;
		$datetime->setTimezone($timezone);
		return $datetime;
	}

	public static function local($str = 'now', $timezone = null)
	{
		if ($timezone === null)
			$timezone = static::local_tz();
		if (!($timezone instanceof DateTimeZone))
			$timezone = new DateTimeZone($timezone);
		$datetime = DateTimeExtended::__new((string) $str, $timezone);
		if (!$datetime) return null;
		return $datetime;
	}

	public static function micro_utc()
	{
		$microtime = microtime(true);
		$time = floor($microtime);
		$micro = substr(($microtime-$time), 2, 6);
		$date = date('Y-m-d H:i:s', $time);
		$date = sprintf('%s.%d', $date, $micro);
		return DateTimeExtended::__new($date);
	}

	public static function ts($ts)
	{
		$datetime = DateTimeExtended::__new(null);
		$datetime->setTimestamp($ts);
		return $datetime;
	}
	
	public static function utc($str = 'now')
	{
		$datetime = DateTimeExtended::__new((string) $str);
		if (!$datetime) return null;
		$datetime->setTimezone(Date::$utc);
		return $datetime;
	}

	public static function gmt($str = 'now')
	{
		$datetime = DateTimeExtended::__new((string) $str);
		if (!$datetime) return null;
		$datetime->setTimezone(Date::$gmt);
		return $datetime;
	}

	public static function format($format)
	{
		return Date::utc()->format($format);
	}
	
	public static function zero()
	{
		$dt = DateTimeExtended::__new();
		if (!$dt) return null;
		$dt->setTimestamp(0);
		return $dt;
	}
	
	public static function local_tz()
	{
		$ci =& get_instance();
		return $ci->local_tz();
	}
	
	public static function relative($amount, $unit, $from = null)
	{
		if ($from === null) $from = Date::utc();
		if ($from && is_string($from)) $from = Date::utc($from);
		$from = clone $from;
		$from->modify(sprintf('%+d %s', $amount, $unit));
		return $from;
	}

	public static function first()
	{
		$dt = Date::utc();
		$dt->setTimestamp(0);
		return $dt;
	}
	
	public static function days($amount, $from = null)
	{
		return static::relative($amount, 'days', $from);
	}
	
	public static function hours($amount, $from = null)
	{
		return static::relative($amount, 'hours', $from);
	}
	
	public static function minutes($amount, $from = null)
	{
		return static::relative($amount, 'minutes', $from);
	}
	
	public static function months($amount, $from = null)
	{
		return static::relative($amount, 'months', $from);
	}
	
	public static function seconds($amount, $from = null)
	{
		return static::relative($amount, 'seconds', $from);
	}
	
	public static function years($amount, $from = null)
	{
		return static::relative($amount, 'years', $from);
	}
	
	public static function interval($y = 0, $m = 0, $d = 0, $h = 0, $i = 0, $s = 0)
	{
		$dt_interval = new DateInterval('PT0S');
		$dt_interval->y = $y;
		$dt_interval->m = $m;
		$dt_interval->d = $d;
		$dt_interval->h = $h;
		$dt_interval->i = $i;
		$dt_interval->s = $s;
		
		return $dt_interval;
	}
	
}

Date::$utc = new DateTimeZone('UTC');
Date::$now = DateTimeExtended::__new();
