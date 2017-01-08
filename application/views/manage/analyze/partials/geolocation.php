<?php foreach ($vd->countries as $country): ?>
<div class="location">
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