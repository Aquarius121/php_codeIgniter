<?php $extras = $vd->mcd_extras->filter(Model_Content_Distribution_Extras::TYPE_PRN_IMAGES); ?>
<?php $extra = count($extras) ? array_values($extras)[0] : new Raw_Data() ?>
<?php $extra->data = Raw_Data::from_object($extra->data); ?>
<?php $credits = (int) $extra->data->credits; ?>

<script>
	
$(function() {

	var form = $("form.content-form");
	var images = $("#web-images-list");
	var credits = <?= json_encode($credits) ?>;

	var update_price_display = function() {
		images.toggleClass("has-prn-credits", credits > 0);
	};

	images.find(".web-image-prn-distribution input").on("change", function() {
		var checkbox = $(this);
		var meta = checkbox.parents(".web-image-prn-distribution");
		meta.toggleClass("active", checkbox.is(":checked"));
		if (!checkbox.is(":checked") && meta.hasClass("included")) {
			meta.removeClass("included"); credits++;
			update_price_display();
		} else if (checkbox.is(":checked")
			 && !meta.hasClass("included")  
			 && !meta.hasClass("bundled")
			 && credits > 0) {
			meta.addClass("included"); credits--;
			update_price_display();
		}
	}).trigger("change");

	images.find(".images-list-item-remove").on("click", function() {
		var input = $(this).parents(".web-images-item-li")
			.find(".web-image-prn-distribution input");
		if (!input.is(":checked")) return;
		input.prop("checked", false);
		input.trigger("change");
	});

	window.on_distribution_bundle_change.push(function(bundle) {
		if (!bundle) return;
		form.toggleClass("web-images-has-prn-extension", !!bundle.data.includesPrnewswire);
		var featured_prn = images.find("li.featured .web-image-prn-distribution");
		var featured_prn_checkbox = featured_prn.find("input[type=checkbox]");
		featured_prn.toggleClass("bundled", !!bundle.data.includesPrnewswireFeatured);
		featured_prn_checkbox.prop("disabled", !!bundle.data.includesPrnewswireFeatured);
		if (bundle.data.includesPrnewswireFeatured) 
			featured_prn_checkbox.prop("checked", true).trigger("change");
	});

	update_price_display();

});

</script>