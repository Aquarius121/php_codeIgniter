<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CIL_Thread extends Thread {

	public function __construct()
	{
		// placeholder
	}

	public function run()
	{
		// enable auto loading of classes
		spl_autoload_register('class_autoload');
	}

}