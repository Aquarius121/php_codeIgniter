<?php

// constants for use with sql_search_terms()
// SQL_SEARCH_TERMS_EQUALS compare with =
// SQL_SEARCH_TERMS_LIKE compare with LIKE %value%
// SQL_SEARCH_TERMS_LIKE_INDEXED compare with LIKE value%
define('SQL_SEARCH_TERMS_EQUALS', 1);
define('SQL_SEARCH_TERMS_LIKE', 2);
define('SQL_SEARCH_TERMS_LIKE_INDEXED', 3);

function value_or_null($value) 
{
	return $value ? $value : null;
}

function value_or($value, $alternative) 
{
	return $value ? $value : $alternative;
}

function value_if_test($test, $value, $else = null)
{
	if ($test) return $value;
	if ($else !== null) return $else;
	return null;
}

function value_if($test, $value, $else = null)
{
	return value_if_test($test, $value, $else);
}

function safer_email_html($email)
{
	$safer = array();
	for ($i = 0; $i < strlen($email); $i++)
		$safer[] = sprintf('<span>&#%d;</span>', ord($email[$i]));
	return implode($safer);
}

function str_ends_with($haystack, $needle)
{
	return substr($haystack, -strlen($needle)) == $needle;
}

function str_starts_with($haystack, $needle)
{
	return strpos($haystack, $needle) === 0;
}

function str_contains($haystack, $needle)
{
	return strpos($haystack, $needle) !== false;
}

function build_path($segments = null)
{
	if (is_array($segments))
	     $path = implode(DIRECTORY_SEPARATOR, $segments);
	else $path = implode(DIRECTORY_SEPARATOR, func_get_args());
	return str_replace(
		concat(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR),
		DIRECTORY_SEPARATOR, 
		$path);
}

function build_url($segments = null)
{
	if (!is_array($segments))
		$segments = func_get_args();
	$segments = array_values($segments);

	// remove slashes from end of segments
	for ($i = 0; $i < count($segments)-1; $i++)
		if (strlen($segments[$i]) && $segments[$i][(strlen($segments[$i])-1)] === '/')
			$segments[$i] = substr($segments[$i], 0, -1);

	// remove slashes from start of segments
	for ($i = 1; $i < count($segments); $i++)
		if (strlen($segments[$i]) && $segments[$i][0] === '/')
			$segments[$i] = substr($segments[$i], 1);

	return implode('/', $segments);
}

function var_swap(&$a, &$b)
{
	$t = $a;
	$a = $b;
	$b = $t;
}

function array_remove_all(&$array, $value, $strict = false)
{
	while (($index = array_search($value, $array, $strict)) !== false)
		array_splice($array, $index, 1);
}

function concat()
{
	return implode(func_get_args());
}

function current_url()
{
	$ci =& get_instance();
	return $ci->external_url();
}

function var_dump_capture($var)
{
	ob_start();
	var_dump($var);
	$capture = ob_get_contents();
	ob_end_clean();
	return $capture;
}

function json_pretty_print($var)
{
	return json_encode($var, JSON_PRETTY_PRINT);
}

// test a html string to determine
// if it is effectively empty 
// (only contains white space)
function test_for_empty_html($html)
{
	return preg_match('#^(&nbsp;|\s|</?br\s*/?>|[\x{200B}-\x{200D}])*$#ui', $html);
}

function object_merge($ob_a, $ob_b)
{
	foreach ($ob_b as $k => $value)
		$ob_a->{$k} = $value;
	return $ob_a;
}

function is_numeric_int($i)
{
	return (bool) preg_match('#^\d+$#', $i);
}

// emulate a boolean full text search
// * [^a-z0-9\-] => used as wildcard
// @match the columns to match against
// @against the search terms as a string
function sql_search_terms($match, $against)
{
	if (!$against) return 1;
	if (!is_array($match)) $match = array($match);
	$raw_terms = explode(' ', $against);
	$sql_conds = array();
	
	// convert each term to sql like
	for ($i = 0, $c = count($raw_terms); $i < $c; $i++)
	{
		$bool = true;
		$term = trim($raw_terms[$i]);
		$cond = array();
		
		if (strlen($term) === 0) continue;
		
		// check for + or - at the start
		if (preg_match('#^[\+\-]#', $term))
		{
			$bool = $term[0] === '+';
			$term = substr($term, 1);
		}
		
		// loop over each column in the match array
		for ($i2 = 0, $c2 = count($match); $i2 < $c2; $i2++)
		{
			$match_i2 = $match[$i2];			
			if (is_array($match_i2))
			{
				$match_col = $match_i2[0];
				$match_method = $match_i2[1];
			}
			else
			{
				$match_method = SQL_SEARCH_TERMS_LIKE;
				$match_col = $match_i2;				
			}
			
			if ($match_method == SQL_SEARCH_TERMS_LIKE)
			{				
				// convert any non-standard character to wildcard
				// * does not match the standard fulltext behaviour
				// * this also prevents sql injection
				$term = preg_replace('#[^a-z0-9\-_]#i', '%', $term);
				
				// generate the sql logic for one column  
				// using like with wildcards each side
				$cond[] = $bool ? " {$match_col} like '%{$term}%' ":
					" {$match_col} not like '%{$term}%' ";
			}
			else if ($match_method == SQL_SEARCH_TERMS_LIKE_INDEXED)
			{
				// convert any non-standard character to wildcard
				// * does not match the standard fulltext behaviour
				// * this also prevents sql injection
				$term = preg_replace('#[^a-z0-9\-_]#i', '%', $term);
				
				// generate the sql logic for one column  
				// using like with wildcards post only
				$cond[] = $bool ? " {$match_col} like '{$term}%' ":
					" {$match_col} not like '%{$term}%' ";
			}
			else if ($match_method == SQL_SEARCH_TERMS_EQUALS)
			{
				// removes any non-standard character 
				// * this also prevents sql injection
				$term = preg_replace('#[^a-z0-9@\#\-_,.:; ]#i', '', $term);
				
				// generate the sql logic for one  
				// column using exact matches only
				$cond[] = $bool ? " {$match_col} = '{$term}' ":
					" {$match_col} != '{$term}' ";
			}
		}
		
		// require that all columns exclude when -term
		$cond = implode(($bool ? 'or' : 'and'), $cond);
		$sql_conds[] = "({$cond})";
	}
	
	if (count($sql_conds) === 0) return 1;
	return implode(' and ', $sql_conds);
}

function & first(&$collection)
{
	foreach ($collection as $c) 
		return $c;
}

function double_quote($text)
{
	return sprintf('"%s"', addslashes($text));
}

function single_quote($text)
{
	return sprintf("'%s'", addslashes($text));
}

function backtick($text)
{
	return sprintf('`%s`', $text);
}

function sql_loose_term($term)
{
	return preg_replace('#[^a-z0-9\-]#i', '%', $term);
}

function comma_separate($list, $spacing = false, $comma = ',')
{
	if (is_string($spacing) && strlen($spacing))
	     $separator = concat($comma, $spacing);
	else if ($spacing) 
	     $separator = concat($comma, ' ');
	else $separator = $comma;

	return implode($separator, $list);
}

function comma_explode($str, $trim = true)
{
	$exploded = explode(',', $str);
	foreach ($exploded as $k => $v)
		$exploded[$k] = trim($v);
	return $exploded;
}

function sql_in_list($list)
{
	$ci =& get_instance();

	// fix for IN ()
	if (!count($list))
		return 'null';

	foreach ($list as &$item)
	{
		if (is_integer($item)) continue;
		if (is_float($item)) continue;
		$item = $ci->db->escape($item);
	}
	
	return comma_separate($list);
}

function escape_and_quote($data)
{
	$ci =& get_instance();
	if (is_integer($data)) return $data;
	if (is_float($data)) return $data;
	return $ci->db->escape($data);
}

function sql_insert_line($insert)
{
	return sprintf('(%s)', sql_in_list($insert));
}

function nl2p($content)
{
	$content = "<p>{$content}</p>";
	// convert double lines (allowing spaces) to paragraphs
	$content = preg_replace('#(\r?\n[\t ]+){2}#s', '</p><p>', $content);
	// convert any remaining single lines to line break
	$content = preg_replace('#(\r?\n){1}#s', '<br />', $content);
	return $content;
}

function gstring($url = null)
{
	$gstring = $_SERVER['QUERY_STRING'];
	if (empty($gstring)) return $url;
	if (strpos($url, $gstring) !== false) return $url;
	if (strpos($url, '?') === false) $url = "{$url}?";
	$url = str_replace('&&', '&', "{$url}&{$gstring}");
	$url = str_replace('?&', '?', $url);
	return $url;
}

function insert_into_query_string($url, $params)
{
	$qs = http_build_query($params);
	if (str_contains($url, '?'))
	     return preg_replace('/(\?[^#]*)/im', 
			sprintf('$1&%s', $qs), $url);
	else return preg_replace('/^([^#]*)/im', 
			sprintf('$1?%s', $qs), $url);
}

function is_absolute_url($url)
{
	// based on this regex for full url matching
	// http://tools.ietf.org/html/rfc3986#appendix-B
	return (bool) preg_match('%^[^:/?#]+://[^/?#]+%s', $url);
}

function set_memory_limit($value)
{
	ini_set('memory_limit', $value);
}

function get_memory_limit()
{
	return ini_get('memory_limit');
}

function get_http_response_code($url) 
{
	$headers = get_headers($url);
	return substr($headers[0], 9, 3);
}

function capture_include($file)
{
	ob_start();
	include $file;
	$r = ob_get_contents();
	ob_end_clean();
	return $r;
}

function sxml_to_raw_data($sxml)
{
	if (count($sxml->children()) > 0)
	{
		$object = new Raw_Data();

		foreach ($sxml->children() as $child)
		{
			$child_object = sxml_to_raw_data($child);
			$child_name = $child->getName();

			if (!isset($object->{$child_name}))
			{
				$object->{$child_name} = $child_object;
			}
			else if ($object->{$child_name}->__sxml_array)
			{
				$object->{$child_name}->push($child_object);
			}
			else
			{
				$prev_child_object = $object->{$child_name};
				$object->{$child_name} = new Raw_Data();
				$object->{$child_name}->__sxml_array = true;
				$object->{$child_name}->push($prev_child_object);
				$object->{$child_name}->push($child_object);
			}
		}

		return $object;
	}
	else
	{
		return (string) $sxml;
	}
}

function spaceship($a, $b)
{
	if ($a < $b) return -1;
	if ($a > $b) return +1;
	return 0;
}

function yesval($x)
{
	return $x ? 'Yes' : 'No';
}