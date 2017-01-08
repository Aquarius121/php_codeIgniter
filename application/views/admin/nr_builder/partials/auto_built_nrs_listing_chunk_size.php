<div class="span12">
	<div class="pull-right">
		View: 
		<a href="admin/nr_builder/<?= $vd->nr_source ?>/set_listing_chunk_size/20/<?= $vd->tab_filter ?><?= 
			$vd->esc(gstring()) ?>"
			<?= value_if_test($vd->listing_size == 20, " class='strong' ") ?>>20</a> | 
		<a href="admin/nr_builder/<?= $vd->nr_source ?>/set_listing_chunk_size/50/<?= $vd->tab_filter ?><?= 
			$vd->esc(gstring()) ?>"
			<?= value_if_test($vd->listing_size == 50, " class='strong' ") ?>>50</a> | 
		<a href="admin/nr_builder/<?= $vd->nr_source ?>/set_listing_chunk_size/100/<?= $vd->tab_filter ?><?= 
			$vd->esc(gstring()) ?>"
			<?= value_if_test($vd->listing_size == 100, " class='strong' ") ?>>100</a> | 
		<a href="admin/nr_builder/<?= $vd->nr_source ?>/set_listing_chunk_size/475/<?= $vd->tab_filter ?><?= 
			$vd->esc(gstring()) ?>"
			<?= value_if_test($vd->listing_size == 475, " class='strong' ") ?>>475</a>
	</div>
</div>