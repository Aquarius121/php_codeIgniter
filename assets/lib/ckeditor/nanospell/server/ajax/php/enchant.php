<?php

$raw_post_data = file_get_contents('php://input');
$post_data = json_decode($raw_post_data);
$response = new stdClass();

if ($post_data && $post_data->method === 'spellcheck')
{
	$response->id = $post_data->id;
	$response->result = new stdClass();

	$broker = enchant_broker_init();
	$dict = enchant_broker_request_dict($broker, $post_data->params->lang);

	foreach ($post_data->params->words as $word)
	{
		$suggestions = array();
		$correct = enchant_dict_quick_check($dict, $word, $suggestions);
		if ($correct) continue;
		$response->result->{$word} = 
			array_slice($suggestions, 0, 8);
	}

	$expires_secs = -86400;
	$expires_time = time() + $expires_secs;
	$expires_date = gmdate(DateTime::RFC1123, $expires_time);
}

if ($post_data && $post_data->method === 'listdicts')
{
	$response->id = $post_data->id;
	$response->result = new stdClass();
	$response->result->dicts = array();
	$response->result->dicts[] = 'en_US';
	$response->result->dicts[] = 'en_GB';
}

header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Expires: {$expires_date}");
header("Pragma: no-cache");

echo json_encode($response);