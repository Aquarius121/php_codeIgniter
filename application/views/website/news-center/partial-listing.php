<?php foreach ($vd->results as $result): ?>
<?= $ci->load->view("website/news-center/{$result->type}", 
	array('content' => $result)); ?>
<?php endforeach ?>