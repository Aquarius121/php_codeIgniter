<?php $extras = $vd->mcd_extras->filter(Model_Content_Distribution_Extras::TYPE_PRN_IMAGES); ?>
<?php $extra = count($extras) ? array_values($extras)[0] : new Raw_Data() ?>
<?php $extra->data = Raw_Data::from_object($extra->data); ?>
<?php $selected = (array) $extra->data->selected; ?>
<?php $confirmed = (array) $extra->data->confirmed; ?>

<div class="meta-line web-image-prn-distribution 
	<?= value_if_test($image && in_array((int) $image->id, $confirmed), 'included') ?>">
	<label class="checkbox-container nomarbot">
		<input type="checkbox" class="form-control web-image-meta-prn"
			<?= value_if_test($image && in_array((int) $image->id, $selected), 'checked') ?>
			name="image_meta_data[prn][<?= $index ?>]" value="1" />
		<span class="checkbox"></span>
		Include distribution to PR Newswire
		<strong class="smaller status-info checkbox-price">
			+ $<?= number_format($vd->item_prn_image_distribution->price, 2) ?>
		</strong>
	</label>
</div>