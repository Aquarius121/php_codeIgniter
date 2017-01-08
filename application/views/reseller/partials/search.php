<?php 
								
$context_uri = $ci->uri->uri_string;

$search_classes = array(
	// reseller_editor
	'#^reseller/(dashboard|publish)(?!/pr)#' => array(
		'label' => 'iPublish Content', 
		'uri' => 'reseller/publish'),	
);

foreach ($search_classes as $k => $v)
{
	if (!preg_match($k, $context_uri))
		continue;
	
	?>
	<div class="span4">
		<section class="search-form-panel">
			<form method="get" 
				action="<?= $v['uri'] ?>">
				<input class="span12" id="search-box" type="search" name="terms" 
					placeholder="Search for <?= $v['label'] ?>"
					value="<?= $vd->esc($ci->input->get('terms')) ?>" />
				<button type="submit">Search</button>
			</form>
		</section>
	</div>
	<script>

	$(function() {
		$("#search-box").focus();
	});

	</script>
	<?php
	
	return;
}

?>