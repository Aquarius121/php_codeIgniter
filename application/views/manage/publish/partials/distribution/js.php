<script>

defer(function() {

	var content_form = $("#content-form");
	var is_premium_save = $("#is-premium-save");
	var is_premium_switch = $("#is-premium-switch");
	var is_premium_radios = $(".is-premium-radio");
	var section_premium = $(".section-requires-premium");
	
	window.on_distribution_bundle_change = [];

	window.is_premium_enabled = function() {
		// checks for premium being enabled
		// but allows other options that include premium
		var selected = is_premium_radios.filter(":checked");
		if (!selected.size()) return true;
		return parseInt(selected.data("is-premium")) === 1;
	};

	window.distribution_bundle = function() {
		var radio = is_premium_radios.filter(":checked").eq(0);
		if (!radio.size()) return null;
		var data = radio.data();
		return {
			bundle: radio.val(),
			data: data,
			name: data.name,			
			radio: radio
		};
	};
	
	var switch_pr_type = function(is_premium) {
		content_form.toggleClass("has-premium", is_premium);
		content_form.toggleClass("has-basic", !is_premium);
	};
	
	var prevent_default = function(ev) {
		$(document.activeElement).blur();
		ev.preventDefault();
		return false;
	};
	
	var disable_features = function() {
		section_premium.find("input")
			.on("mousedown.feature-lock", prevent_default)
			.on("click.feature-lock", prevent_default)
			.on("focus.feature-lock", prevent_default)
			.prop("readonly", true)
			.addClass("disabled");
		section_premium.find("select")
			.on("mousedown.feature-lock", prevent_default)
			.on("click.feature-lock", prevent_default)
			.on("focus.feature-lock", prevent_default)
			.prop("readonly", true)
			.addClass("disabled");
		section_premium.find("button")
			.on("mousedown.feature-lock", prevent_default)
			.on("click.feature-lock", prevent_default)
			.on("focus.feature-lock", prevent_default)
			.prop("readonly", true)
			.addClass("disabled");
	};
	
	var enable_features = function() {
		section_premium.find("input")
			.off("mousedown.feature-lock")
			.off("click.feature-lock")
			.off("focus.feature-lock")
			.prop("readonly", false)
			.removeClass("disabled");
		section_premium.find("select")
			.off("mousedown.feature-lock")
			.off("click.feature-lock")
			.off("focus.feature-lock")
			.prop("readonly", false)
			.removeClass("disabled");
		section_premium.find("button")
			.off("mousedown.feature-lock")
			.off("click.feature-lock")
			.off("focus.feature-lock")
			.prop("readonly", false)
			.removeClass("disabled");
	};
	
	var handle_premium_mod = function() {

		var is_premium = is_premium_enabled();
		is_premium_save.prop("disabled", !is_premium);
		switch_pr_type(is_premium);
		if (is_premium) enable_features();
		else disable_features();

		var bundle = window.distribution_bundle();
		if (!bundle) return;
		$.each(window.on_distribution_bundle_change, function(i, callback) {
			callback.call(bundle ? bundle.radio : null, bundle);
		});
		
	};
	
	is_premium_radios.on("change", handle_premium_mod);
	$(function() { setTimeout(handle_premium_mod, 0); });
	
	content_form.on("click", "div.requires-premium a", function() {
		is_premium_switch.prop("checked", true).trigger("change");
	});

	var distribution_boxes = {
		'BASIC': '.distribution-BASIC',
		'PREMIUM': '.distribution-PREMIUM',
		'PRN': '.distribution-PREMIUM-PLUS',
		'PREMIUM-PLUS': '.distribution-PREMIUM-PLUS',
		'PREMIUM-PLUS-STATE': '.distribution-PREMIUM-PLUS-STATE',
		'PREMIUM-PLUS-NATIONAL': '.distribution-PREMIUM-PLUS-NATIONAL',
		'PREMIUM-FINANCIAL': '.distribution-PREMIUM-FINANCIAL',
	};

	var distribution = <?= json_encode($this->input->get('distribution')) ?>;
	if (distribution && distribution_boxes[distribution]) {
		for (var idx in distribution_boxes) {
			var distribution_box = $(distribution_boxes[idx]);
			var container = distribution_box.parents(".radio-container-box");
			container.addClass("hidden");
			if (distribution == idx) {
				distribution_box.prop("checked", true).trigger("change");
				container.removeClass("hidden");
			}
		}
	}

	var customization = $("#distribution-customization");
	var options_container = customization.find(".distribution-options");
	var options = options_container.find(".distribution-option");
	
	var switch_customization = function() {
		customization.addClass("hidden");
		options.detach();
		options.each(function() {
			var option = $(this);
			var distributions = option.data("distribution").split(/\s+/);
			$.each(distributions, function(i, distribution) {
				var selector = distribution_boxes[distribution];
				if ($(selector).is(":checked")) {
					options_container.append(option);
					customization.removeClass("hidden");
				}
			});
		});
	};

	is_premium_radios.on("change", switch_customization);
	$(function() { setTimeout(switch_customization, 0); });

	// ----------------------------------------
	// ----------------------------------------

	(function() {

		var distribution = $(".select-distribution");
		var radios = distribution.find("input[type=radio]");

		var render = function(element, force, instant) {
			var _this = $(element);
			var enabled = _this.is(":checked") || force;
			var container = _this.parents(".radio-container-box");
			var detail = container.find(".pr-type-detail");
			if (instant) {
				if (enabled) 
				     detail.show();
				else detail.hide();
			} else {
				if (enabled) 
				     detail.slideDown();
				else detail.slideUp();
			}
		};

		radios.on("change", function() {
			radios.each(function(i, element) {
				render(element);
			});
		});

		radios.each(function(i, element) {
			render(element, false, true);
		});

		// show premium plus text by default
		if (!radios.filter(":checked").size())
			render(radios.get(0), true, true);

	})();

});

</script>