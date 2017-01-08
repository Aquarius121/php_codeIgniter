<?php 

lib_autoload('php_query');

$content = $m_content->content;
// convert <br> to <p> where possible
$br_pattern = '#</?br\s*/?>(&nbsp;|\s)*</?br\s*/?>#i';
$content = preg_replace($br_pattern, '</p><p>', $content);
$doc = phpQuery::newDocumentHTML($content);

// paragraphs with 150+ characters
// are considered to be nice
$paragraphs_nice_size = 150;
$normalized_p_count = 0;

// * remove empty paragraphs
// * count paragraphs with size
$paragraphs = pq('p', $doc);
foreach ($paragraphs as $_paragraph)
{
	$paragraph = pq($_paragraph);
	$inner_html = $paragraph->html();
	$inner_text = $paragraph->text();
	if (strlen($inner_text) > $paragraphs_nice_size)
	     $normalized_p_count += 1;
	else $normalized_p_count += 0.5;
	if (test_for_empty_html($inner_html))
		$paragraph->remove();
}

// render the date line 
$date_line = $ci->load->view('browse/view/partials/date_line', 
	array('m_content' => $m_content)); 

// insert the date line
$paragraphs = pq('p', $doc);
$_paragraph = $paragraphs->eq(0);
$_paragraph->prepend($date_line);

/* 

	// render the supporting quote
	$quote_view = 'manage/contact/partials/html-content/supporting-quote';
	$quote = $ci->load->view_return($quote_view, array('m_content' => $m_content));

	// insert the supporting quote
	// $paragraphs = pq('p', $doc);
	$sp_offset = $m_content->summary ? -1 : 0; 
	if (($normalized_p_count + $sp_offset) >= 4)
		  $paragraphs->eq(2)->before($quote);
	else if (($normalized_p_count + $sp_offset) >= 2)
	     $paragraphs->eq(1)->before($quote);
	else $paragraphs->eq(0)->before($quote);

*/

// regenerate html
echo $doc->__toString();

?>