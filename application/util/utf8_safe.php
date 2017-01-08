<?php

// remove characters instead of substitute
ini_set('mbstring.substitute_character', 'none');

// detect as utf-8 if valid otherwise assume iso-8859-1
mb_detect_order(array('UTF-8', 'ISO-8859-1'));
mb_internal_encoding('UTF-8');

function to_utf8_fix_chars($content)
{
	$conversions = (include 'utf8_safe_conversions.php');
	foreach ($conversions as $from => $to)
		$content = str_replace($from, $to, $content);
	
	return $content;
}

function to_utf8_remove_4b($content)
{
	// remove 4 byte sequences to store in database
	return preg_replace('#[\xF0-\xF7]...#s', '', $content);
}

function to_utf8_3b($content)
{
	$encoding = mb_detect_encoding($content);
	if (!$encoding) $encoding = 'ISO-8859-1';
	$content = mb_convert_encoding($content, 'UTF-8', $encoding);
	$content = to_utf8_fix_chars($content);
	$content = to_utf8_remove_4b($content);

	return $content;
}

function to_utf8_3b_array(&$array)
{
	foreach ($array as &$v) 
	{
		if (is_array($v))
		{
			to_utf8_3b_array($v);
		}
		else
		{
			$v = to_utf8_3b($v);
		}
	}
}

to_utf8_3b_array($_COOKIE);
to_utf8_3b_array($_POST);
to_utf8_3b_array($_GET);

?>