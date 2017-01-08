<?php
								
$context_uri = $ci->uri->uri_string;
$search_classes = $ci->config->item('admin', 'search_bar');

foreach ($search_classes as $k => $v)
{
	if (!preg_match($k, $context_uri))
		continue;
	
	?>
	<section class="search-form-panel full-width clearfix">
		<form method="get" class="panel-component search-box-component" 
			action="<?= $v['uri'] ?>">
			<?php foreach ((array) $ci->input->get() as $gk => $gv): ?>
			<?php if ($gk == 'filter_search') continue; ?>
			<input type="hidden" name="<?= $vd->esc($gk) ?>" 
				value="<?= $vd->esc($gv) ?>" />
			<?php endforeach ?>
			<input class="span12" id="search-box" type="search" name="filter_search" 
				placeholder="  Search for <?= $v['label'] ?>"
				value="<?= $vd->esc($ci->input->get('filter_search')) ?>" />
			<button type="submit" class="search-box-button">Search</button>
		</form>
		<div class="panel-component add-filter-component">
			<button id="admin-add-filter-button" class="btn btn-small"
				<?php if (isset($v['filters']) && count($v['filters'])): ?>
					class="btn btn-small"
				<?php else: ?>
					class="btn btn-small disabled"
					disabled
				<?php endif ?>
				>Add Filter</button>
		</div>
	</section>
	<script>

	$(function() {

		$("#search-box").focus();

	});

	</script>
	<?php
	
	return;
}

?>