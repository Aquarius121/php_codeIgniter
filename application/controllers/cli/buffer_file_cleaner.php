<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_controller('cli/base');

class Buffer_File_Cleaner_Controller extends CLI_Base {

	/* find buffer files in the temp 
		directory that are older than HOURS
		and delete them from the system. */
	
	const HOURS = 12;

	public function index()
	{
		$cut_off = time() - (3600 * static::HOURS);
		$base = SYSTEM_TEMP_DIR;
		$files = scandir($base);
		
		foreach ($files as $file)
		{
			if (!str_starts_with($file, File_Util::BUFFER_FILE_PREFIX))
				continue;

			$file = build_path($base, $file);
			if (!file_exists($file)) continue;

			$modt = filemtime($file);
			if ($modt < $cut_off)
				unlink($file);
		}
	}

}
