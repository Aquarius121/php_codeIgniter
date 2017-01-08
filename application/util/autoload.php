<?php 

function class_autoload($class)
{
	// php does not check if the class exists
	if (class_exists($class, false)) return;

	// load from the core folder
	if (preg_match('#^CIL_#', $class)) 
	{
		// load a core class if exists
		$file = "application/core/{$class}.php";
		if (is_file($file)) return require_once $file;
	}

	// filenames are all lowercase
	$class = strtolower($class);

	// load from the models folder
	if (preg_match('#^model_#', $class)) 
	{
		$model = substr($class, 6);
		$file = "application/models/{$model}.php";
		if (is_file($file)) return require_once $file;
		
		// fallback to pattern matching with glob
		$pattern = str_replace('_', '{_,/}', $class);
		$file = "application/models/{$pattern}.php";
		$file_list = glob($file, GLOB_BRACE);
		if (isset($file_list[0]))
			return require_once $file_list[0];
	}

	// convert namespaces to folders
	$class = str_replace('\\', '/', $class);
		
	// load from the classes folder
	$file = "application/classes/{$class}.php";
	if (is_file($file)) return require_once $file;
	
	// fallback to pattern matching with glob
	$pattern = str_replace('_', '{_,/}', $class);
	$file = "application/classes/{$pattern}.php";
	$file_list = glob($file, GLOB_BRACE);
	if (isset($file_list[0]))
		return require_once $file_list[0];
}

function lib_autoload($name, $context = null)
{
	$file = "application/libloader/{$name}/loader.php";
	if (is_file($file)) require_once $file;
}

function load_controller($name)
{
	$file = "application/controllers/{$name}.php";
	if (is_file($file)) require_once $file;
}

function load_parent_controller($name)
{
	load_controller($name);
}

function load_shared_fnc($name)
{
	load_controller($name);
}

// enable auto loading of classes
spl_autoload_register('class_autoload');