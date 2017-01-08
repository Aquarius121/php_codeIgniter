// DEPENDS: jquery.deserialize(.min).js
// DEPENDS: jquery.serializeobject(.min).js

(function(window, $) {

	var loaded_ts = + (new Date());
	var next_autosave_id = 1;
	var forms = {};
	
	var generate_id = function(form) {

		var id = "as" + next_autosave_id++;
		form.data("autosave-id", id);
		return id;

	};

	var initialize = function(form, id) {

		forms[id] = {};
		forms[id].interval = form.data("autosave-interval");
		forms[id].context = form.data("autosave-context");
		forms[id].url = form.data("autosave-url");

		var autosave_ms = 1000 * forms[id].interval;
		if (autosave_ms < 1000) 
			autosave_ms = 1000;

		if (!forms[id].interval) return;
		setInterval(function() {
			autosave(form);
		}, autosave_ms);

	};

	var autosave = function(form, options) {		

		var id = form.data("autosave-id");
		if (!id) id = generate_id(form);
		if (options) forms[id] = options;
		if (forms[id] === undefined)
			initialize(form, id);
		if (!forms[id].url)
			return;

		// update all instances of CKEDITOR before saving
		if (window.CKEDITOR !== undefined && window.CKEDITOR.env.isCompatible)
				for (var idx in window.CKEDITOR.instances)
					window.CKEDITOR.instances[idx].updateElement();

		var disabled = form.find(":input:disabled");
		disabled.prop("disabled", false);
		var form_data = form.serializeObject();
		disabled.prop("disabled", true);
		
		for (var idx in window.autosave.on_save)
			form_data = window.autosave.on_save[idx].call(form, form_data);

		var post_data = {
			context: forms[id].context,
			form: JSON.stringify(form_data)
		};

		$.post(forms[id].url, post_data, function(r) {
			if (r.context) {
				forms[id].context = r.context;
				form.data("autosave-context", r.context);
			}
		});

	};

	$(function() {

		var autosave_forms = $(".autosave-form");
		autosave_forms.each(function() {
			var form = $(this);
			var id = generate_id(form);
			initialize(form, id);
		});

		var autosave_buttons = $(".autosave-button");
		autosave_buttons.on("click", function() {
			var button = $(this);
			var form = button.parents(".autosave-form");
			form.autosave();
		});

		$(window).unload(function() {
			var now_ts = + (new Date());
			if (now_ts > loaded_ts + (60 * 1000))
				autosave_forms.autosave();
		});

	});

	window.autosave = autosave;
	window.autosave.on_save = [];
	$.fn.autosave = function(options) {
		this.each(function() {
			var form = $(this);
			autosave(form, options);
		});
	};

})(window, jQuery);