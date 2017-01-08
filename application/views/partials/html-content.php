<?php 

lib_autoload('php_query');

// convert <br> to <p> where possible
$br_pattern = '#</?br\s*/?>(&nbsp;|\s)*</?br\s*/?>#i';
$content = preg_replace($br_pattern, '</p><p>', $content);
$doc = phpQuery::newDocumentHTML($content);

// remove empty paragraphs
$paragraphs = pq('p', $doc);
foreach ($paragraphs as $_paragraph)
{
	$paragraph = pq($_paragraph);
	$inner_html = $paragraph->html();
	if (test_for_empty_html($inner_html))
		$paragraph->remove();
}

// all links open in new tab
$alinks = pq('a', $doc);
$alinks->attr('target', '_blank');

// regenerate html
echo $doc->__toString();
