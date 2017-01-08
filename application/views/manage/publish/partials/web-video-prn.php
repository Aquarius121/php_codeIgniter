<?php $extras = $vd->mcd_extras->filter(Model_Content_Distribution_Extras::TYPE_PRN_VIDEO); ?>
<?php $extra = count($extras) ? array_values($extras)[0] : new Raw_Data(); ?>
<?php $extra->data = Raw_Data::from_object($extra->data); ?>
<?php $is_selected = (bool) $extra->data->is_selected; ?>
<?php $is_confirmed = (bool) $extra->data->is_confirmed; ?>

<div class="meta-line web-video-prn-distribution 
	<?= value_if_test($is_confirmed, 'included') ?>">
	<label class="checkbox-container nomarbot">
		<input type="checkbox" class="form-control web-video-meta-prn"
			<?= value_if_test($is_selected, 'checked') ?>
			<?= value_if_test($is_confirmed, 'disabled') ?>
			name="prn_video_distribution" value="1"
			id="prn-video-distribution" />
		<span class="checkbox"></span>
		Include distribution to PR Newswire
		<strong class="smaller status-info checkbox-price">
			+ $<?= number_format($vd->item_prn_video_distribution->price, 2) ?>
		</strong>
	</label>
</div>

<script>

$(function() {

	var form = $("form.content-form");
	var checkbox = $("#prn-video-distribution");
	var provider = $("#web-video-provider");
	var videoid = $("#video-id");
	var is_confirmed = <?= json_encode($is_confirmed) ?>;

	window.on_distribution_bundle_change.push(function(bundle) {
		if (!bundle) return;
		form.toggleClass("web-video-has-prn-extension",
			!!bundle.data.includesPrnewswire);
	});

	provider.on("change", function() {
		var supported = provider.val() == <?= json_encode(Video::PROVIDER_YOUTUBE) ?>;
		if (supported && !is_confirmed) {
			checkbox.prop("disabled", false);
		} else if (!supported) {
			if (!is_confirmed) 
				checkbox.prop("checked", false);
			checkbox.prop("disabled", true);
		}
	});

	videoid.on("change", function() {
		var supported = String(videoid.val()).length > 0;
		if (supported && !is_confirmed) {
			checkbox.prop("disabled", false);
		} else if (!supported) {
			if (!is_confirmed) 
				checkbox.prop("checked", false);
			checkbox.prop("disabled", true);
		}
	});

	provider.trigger("change");
	videoid.trigger("change");

});

</script>