<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class File_Util {

	const BUFFER_FILE_PREFIX = 'NR_BF_';
	
	public static function detect_mime($filename)
	{
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		return $finfo->file($filename);
	}
	
	public static function buffer_file()
	{
		return tempnam(SYSTEM_TEMP_DIR, static::BUFFER_FILE_PREFIX);
	}

	public static function download($url)
	{
		$buffer = static::buffer_file();
		$result = @copy($url, $buffer);
		if (!$result) return false;
		return $buffer;
	}
	
}