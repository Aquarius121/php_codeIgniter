<hr><!-- ============================== -->

<div>
	<strong style="font-weight:bold">Bundle</strong>
	<span><?= $vd->esc($vd->mDistBundle->name()) ?></span>
</div>

<div>
	<strong style="font-weight:bold">Bundle Confirmed</strong>
	<?php if ($vd->mDistBundle->is_confirmed): ?>
	<span class="status-true">Yes</span>
	<?php else: ?>
	<span class="status-false">No</span>
	<?php endif ?>
</div>

<?php if (($customization = $vd->mDistBundle->raw_data_object()->customization)): ?>
<div>
	<strong style="font-weight:bold">Bundle Customization</strong>
	<span>
		<?php foreach ($customization as $k => $v): ?>
			<?php if ($k === 'state'): ?>
				State: <?= $vd->esc(PRNewswire_Distribution::state($v)) ?> 
				[<?= $vd->esc($v) ?>]<br>
			<?php else: ?>
				<?= $vd->esc(ucwords($k)) ?>: 
				<?= $vd->esc($v) ?><br>
			<?php endif ?>
		<?php endforeach ?>
	</span>
</div>
<?php endif ?>

<hr><!-- ============================== -->

<div>
	<strong style="font-weight:bold">Plus Networks (Selected)</strong>
	<span><?= $vd->esc(comma_separate(array_map(function($n) { return $n->code(); }, $vd->mReleasePlus), true)) ?></span>
</div>
<div>
	<strong style="font-weight:bold">Plus Networks (Confirmed)</strong>
	<span><?= $vd->esc(comma_separate(array_filter(array_map(function($n) {
		return $n->is_confirmed ? $n->code() : false; }, $vd->mReleasePlus)), true)) ?></span>
</div>

<hr><!-- ============================== -->

<?php if ($vd->mExtras): ?>

	<?php $microlists = $vd->mExtras->filter(Model_Content_Distribution_Extras::TYPE_MICROLIST); ?>
	<?php if ($microlists): ?>
	<div>
		<strong style="font-weight:bold">Microlists (Selected)</strong>
		<span><?= implode('<br>', array_map(function($n) use ($vd) { 
			return $vd->esc(sprintf('%s [%s]', $n->data->name, $n->data->item_code)); }, $microlists)) ?></span>
	</div>
	<div>
		<strong style="font-weight:bold">Microlists (Confirmed)</strong>
		<span><?= implode('<br>', array_filter(array_map(function($n) use ($vd) { 
			return isset($n->data->is_confirmed) && $n->data->is_confirmed ? $vd->esc(sprintf('%s [%s]', 
			$n->data->name, $n->data->item_code)) : false; }, $microlists))) ?></span>
	</div>
	<hr><!-- ============================== -->
	<?php endif ?>

	<?php if (($prnImages = $vd->mExtras->filter(Model_Content_Distribution_Extras::TYPE_PRN_IMAGES))): ?>
	<?php $prnImagesData = Raw_Data::from_object(array_values($prnImages)[0]->data); ?>
	<div>
		<strong style="font-weight:bold">PRN Images (Selected)</strong>
		<span><?= $vd->esc(comma_separate((array) $prnImagesData->selected, true)) ?></span>
	</div>
	<div>
		<strong style="font-weight:bold">PRN Images (Confirmed)</strong>
		<span><?= $vd->esc(comma_separate((array) $prnImagesData->confirmed, true)) ?></span>
	</div>
	<div>
		<strong style="font-weight:bold">PRN Images (Credits)</strong>
		<span><?= (int) $prnImagesData->credits ?></span>
	</div>
	<hr><!-- ============================== -->
	<?php endif ?>

	<?php if (($prnVideo = $vd->mExtras->filter(Model_Content_Distribution_Extras::TYPE_PRN_VIDEO))): ?>
	<?php $prnVideoData = Raw_Data::from_object(array_values($prnVideo)[0]->data); ?>
	<div>
		<strong style="font-weight:bold">PRN Video (Selected)</strong>
		<?php if ($prnVideoData->is_selected): ?>
		<span class="status-true">Yes</span>
		<?php else: ?>
		<span class="status-false">No</span>
		<?php endif ?>
	</div>
	<div>
		<strong style="font-weight:bold">PRN Video (Confirmed)</strong>
		<?php if ($prnVideoData->is_confirmed): ?>
		<span class="status-true">Yes</span>
		<?php else: ?>
		<span class="status-false">No</span>
		<?php endif ?>
	</div>
	<hr><!-- ============================== -->
	<?php endif ?>

<?php endif ?>