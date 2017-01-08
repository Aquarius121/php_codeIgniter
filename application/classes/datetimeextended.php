<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DateTimeExtended extends DateTime {

	public function __toString()
	{
		// we use the mysql format by default
		// because it's suitable generally 
		// and would work perfectly in mysql code
		return $this->format(Date::FORMAT_MYSQL);
	}

	public static function __new($time = null, $timezone = null)
	{
		try { return new static($time, $timezone); }
		catch (Exception $e) { return null; }
	}

	// returns a string like +01:00 for +1 hour
	public function getTimezoneOffsetString()
	{
		return $this->format('P');
	}

	public function setSeconds($seconds)
	{
		$hours = $this->format('H');
		$minutes = $this->format('i');

		$this->setTime($hours, $minutes, $seconds);
		return $this;
	}

	public function setMinutes($minutes)
	{
		$hours = $this->format('H');
		$seconds = $this->format('s');

		$this->setTime($hours, $minutes, $seconds);
		return $this;
	}

	public function setHours($hours)
	{
		$minutes = $this->format('i');
		$seconds = $this->format('s');

		$this->setTime($hours, $minutes, $seconds);
		return $this;
	}

}