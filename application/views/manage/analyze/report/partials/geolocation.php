<div id="ln-container" class="columnize">
	<?php foreach ($vd->countries as $country): ?>
	<div class="location ln-block with-border">
		<div class="country">
			<img src="<?= $vd->assets_base . $country->flag ?>" />
			<span class="name">
				<?= $vd->esc($country->country_name) ?>
			</span>
			<span class="visits">
				(<?= $vd->esc($country->count) ?>)
			</span>
		</div>
		<div class="regions">
			<?php foreach ($country->regions as $region): ?>
			<div class="region">
				<span class="name">
					<?= $vd->esc($region->region_name) ?>
				</span>
				<span class="visits">
					(<?= $region->count ?>)
				</span>
			</div>
			<?php endforeach ?>
		</div>
	</div>
	<?php endforeach ?>
</div>

<script src="<?= $vd->assets_base ?>js/columnize.js?<?= $vd->version ?>"></script>
<script>
	
$(function() {

	var items_container = $("#ln-container");
	if (!items_container.hasClass("columnize") ||
		 !items_container.size()) return;
	
	var _columnized = items_container.columnize({
		columns: 2,
		harder: true,
		margin: 20,
	});

});

</script>