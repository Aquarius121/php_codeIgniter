<?php 
								
$context_uri = $ci->uri->uri_string;
$search_classes = $ci->config->item('manage', 'search_bar');

foreach ($search_classes as $k => $v)
{
	if (!preg_match($k, $context_uri))
		continue;
	
	?>
		<div class="ax-search-form">
			<form method="get" class="navbar-form navbar-search"
				action="<?= $v['uri'] ?>">

				<input class="form-control" id="search-box" type="search" name="terms" 
					placeholder="Search for <?= $v['label'] ?>"
					value="<?= $vd->esc($ci->input->get('terms')) ?>" />

			</form>
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