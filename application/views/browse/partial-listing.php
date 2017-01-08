<?php foreach ($vd->results as $result): ?>
<?= $ci->load->view("browse/listing/{$result->type}", 
	array('content' => $result)); ?>
<?php endforeach ?>

<script>
$(function() {
	$('.section-share a').click(function(){
		var u = jQuery(this).attr('href');
		window.open(u,'sharer','toolbar=0,status=0,width=626,height=436');
		return false;
	});
});
</script>