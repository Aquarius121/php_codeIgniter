<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<style>

			body {
				font-family: Calibri, sans-serif;
				font-size: 12pt;
				color: #333;
			}

			h1 {
				color: #000;
				font-size: 16pt;
			}

		</style>
	</head>
	<body>
		<h1><?= $vd->esc($vd->content->title) ?></h1>
		<p class="article-summary" style="font-weight: bold;">
			<?= $vd->esc($vd->content->summary) ?>
		</p>
		<div class="html-content">
			<?php

			lib_autoload('php_query');

			// convert <br> to <p> where possible
			$content = $vd->content->content;
			$br_pattern = '#</?br\s*/?>(&nbsp;|\s)*</?br\s*/?>#i';
			$content = preg_replace($br_pattern, '</p><p>', $content);
			$doc = phpQuery::newDocumentHTML($content);

			$dt_date_publish = Date::out($vd->content->date_publish);
			$date_str = $dt_date_publish->format('M jS, Y');

			if ($vd->content->location)
			     $date_line = sprintf('%s - %s - ', $vd->content->location, $date_str);
			else $date_line = sprintf('%s - ', $date_str);

			// remove empty paragraphs
			$paragraphs = pq('p', $doc);
			foreach ($paragraphs as $_paragraph)
			{
				$paragraph = pq($_paragraph);
				$inner_html = $paragraph->html();
				if (test_for_empty_html($inner_html))
					$paragraph->remove();
			}

			// insert the date line
			$paragraphs = pq('p', $doc);
			$_paragraph = $paragraphs->eq(0);
			$_paragraph->prepend($date_line);

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

		</div>
		<!--
		<p class="muted">
			This content was issued through the press release
			service at Newswire.com. For more info visit: 
			<a href="http://www.newswire.com/">www.newswire.com</a>
		</p>
		-->
	</body>
</html>