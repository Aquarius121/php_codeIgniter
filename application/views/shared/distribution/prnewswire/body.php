<?php

lib_autoload('php_query');

// convert <br> to <p> where possible
$content = $vd->content->content;
$br_pattern = '#</?br\s*/?>(&nbsp;|\s)*</?br\s*/?>#i';
$content = preg_replace($br_pattern, '</p><p>', $content);
$doc = phpQuery::newDocumentHTML($content);

$dt_date_publish = Date::out($vd->content->date_publish);
$date_str = $dt_date_publish->format('M jS, Y');

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
pq('a', $doc)->attr('target', '_blank');

// regenerate html
echo $doc->__toString();

?>

<?= $ci->load->view('shared/distribution/prnewswire/related_files',
		array('result' => $vd->content)); ?>
<?= $ci->load->view('shared/distribution/prnewswire/related_images',
		array('result' => $vd->content)); ?>
<?= $ci->load->view('shared/distribution/prnewswire/related_links',
		array('result' => $vd->content)); ?>
<?= $ci->load->view('shared/distribution/prnewswire/related_video',
		array('result' => $vd->content)); ?>

<p>
	This content was issued through the press release distribution service at Newswire.com.
	For more info visit: <a href="http://www.newswire.com/">http://www.newswire.com/</a>.
</p>