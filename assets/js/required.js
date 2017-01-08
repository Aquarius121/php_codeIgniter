(function() {

	if (window.required_js)
		return;
	
	var callbacks = {};
	window.required_js = {};
	window.required_js.final = false;
	window.required_js.enabled = true;
	window.required_js.on_submit = null;
	window.required_js.on_fail = null;
	window.required_js.on_before_submit = [];
	window.required_js.add_callback = function(name, callback) {
		callbacks[name] = callback;
	};

	var wrjs = window.required_js;
	
	var allow_send = function(form) {
		// prevent the file upload again
		form.find(".required-no-submit").prop("disabled", true);
		form.find(".wrjs-button-mimic").remove();
	};

	var mimic_button = function(button) {
		if (button && button.exists()) {
			var value = String(button.val());
			if (!value.length) value = String(button.attr("value"));
			if (!value.length) value = String(button.text());
			if (!value.length) value = 1;
			var input = $.create("input");
			input.addClass("wjrs-button-mimic");
			input.attr("name", button.attr("name"));
			input.attr("type", "hidden");
			input.val(value);
			button.after(input);
			setTimeout(function() {
				input.remove(); }, 0);
		}
	};

	$(function() {
		
		var form = $(".required-form");
		var is_draft_save = false;
		var submit_button = null;
		var is_loader_test = false;
		
		var check_button_class = function() {
			
			var _this = $(this);
			submit_button = _this;
			var name = _this.attr("name");
			is_draft_save = name == "is_draft" || name == "is_preview";
			is_draft_save = is_draft_save || _this.hasClass("required-is-draft-save");
			if (name == "is_preview" || _this.hasClass("required-blank-target"))
				  var frame = "_blank";
			else var frame = "_self";
			var _form = _this.parents("form");
			_form.attr("target", frame);
			
		};
		
		// if we click save draft then allow save anyway
		form.find("button[type=submit]").on("click", check_button_class);
		form.find("input[type=submit]").on("click", check_button_class);
		
		form.on("submit", function(ev) {

			if (wrjs.final)
				return;
			
			var _this = $(this);
			
			// remove a hidden field that enforces required.js
			_this.find(".required-enforcer").remove();
			
			if (!is_loader_test) {
				_this.find(".required-error").remove();
				_this.find(".required-error-after").remove();
			} else {
				_this.find(".required-loader-error").remove();
			}

			if (wrjs.on_before_submit.length)
				for (var idx in wrjs.on_before_submit)
					wrjs.on_before_submit[idx].call(wrjs, _this);
			
			if (!wrjs.enabled || is_draft_save) {
				if (wrjs.on_submit) {
					var fini_cb = function() {
						wrjs.final = true;
						allow_send(form);
						mimic_button(submit_button);
						_this.submit();
						wrjs.final = false;
					};
					var on_submit = wrjs.on_submit;
					ev.button = submit_button[0];
					ev.is_draft_save = is_draft_save;
					if (on_submit.call(_this, ev, fini_cb) === false)
						return false;
				} else {
					allow_send(form, submit_button);
					return;
				}
			}

			var failed_loader_count = 0;
			var failed = false;

			var required_fields = _this.find(".required");
			var callback_fields = _this.find(".required-callback");
			
			if (is_loader_test) {
				required_fields = required_fields.filter(".had-loader");
				required_fields.removeClass("had-loader")
			}
			
			for (var i = 0; i < required_fields.size(); i++) {
				
				var required_eq = required_fields.eq(i);
				var has_loader = required_eq.hasClass("loader");
				if (required_eq.val() && !has_loader) 
					continue;

				// disabled field skips the check
				if (required_eq.hasClass("required-disabled"))
					continue;
				
				if (has_loader) {
					
					var required_error = $.create("div");
					required_error.addClass("alert alert-warning");
					required_error.addClass("required-error required-loader-error");
					var error_html = "<strong>Patience!<\/strong> You" 
						+ " must wait until this task and/or validation"
						+ " is finished.";
					required_error.html(error_html);
					if (required_eq.data("required-use-parent"))
					     required_eq.parent().before(required_error);
					else required_eq.before(required_error);
					required_eq.addClass("had-loader");
					failed_loader_count++;
					
				} else {

					var required_name = required_eq.data("required-name");
					if (!required_name) required_name = required_eq.attr("placeholder");
					
					var required_error = $.create("div");
					required_error.addClass("alert alert-danger");
					required_error.addClass("required-error");
					var error_html = "<strong>Required!<\/strong> The " 
						+ "<strong>" + required_name 
						+ "<\/strong> field must have a value.";
					required_error.html(error_html);
					if (required_eq.data("required-use-parent"))
					     required_eq.parent().before(required_error);
					else required_eq.before(required_error);
					
				}
				
				failed = true;

				var on_off_name = "change.required";
				required_eq.on(on_off_name, (function(error, on_off_name) {
					return function() {
						var _this = $(this);
						if (_this.val()) {
							error.remove();
							_this.off(on_off_name);
						}
					};
				})(required_error, on_off_name));
				
			}
			
			for (var i = 0; i < callback_fields.size(); i++) {
				
				var callback_eq = callback_fields.eq(i);
				var value = callback_eq.val();
				if (callback_eq.hasClass("required") && !value)
					continue;

				// disabled field skips the check
				if (callback_eq.hasClass("required-disabled"))
					continue;
				
				var callback_data = callback_eq.data("required-callback");
				var callback_names = callback_data.split(/\s+/);
				
				for (var j = 0; j < callback_names.length; j++) {
					
					var callback_name = callback_names[j];
					var callback = callbacks[callback_name];
					if (callback === undefined) continue;
					try { var response = callback.call(null, value); }
					catch (err) { /*response = { valid: false, text: "has an error" }; */}
					if (response.valid) continue;
					failed = true;

					var required_name = callback_eq.data("required-name");
					if (!required_name) required_name = callback_eq.attr("placeholder");
					
					var error_html = response.html;
					var callback_error = $.create("div");
					callback_error.addClass("alert alert-danger");
					callback_error.addClass("required-error");
					
					if (!error_html && response.text) {
						// use the standard error message 
						error_html = "<strong>Error!<\/strong> The " 
							+ "<strong>" + required_name
							+ "<\/strong> field " + response.text + ".";
					}

					callback_error.html(error_html);
					if (callback_eq.data("required-use-parent"))
					     callback_eq.parent().before(callback_error);
					else callback_eq.before(callback_error);

					// add additional content after the
					// error message. must be jQuery object.
					if (response.after && response.after instanceof window.jQuery) {
						response.after.addClass("required-error-after");
						callback_error.after(response.after);
					}

					var on_off_name = "change.required-callback-" + j;
					callback_eq.on(on_off_name, (function(callback, error, on_off_name) {
						return function() {
							var _this = $(this);
							try { var response = callback.call(null, _this.val()); } 
							catch (err) { response = { valid: false }; }
							if (!response.valid) return;
							// test for and remove any 
							// additional content after error
							var error_after = error.next();
							if (error_after.hasClass("required-error-after"))
								error_after.remove();
							error.remove();
							_this.off(on_off_name);
						};
					})(callback, callback_error, on_off_name));
					
				}
				
			}
			
			// bad performance to render again but shouldn't
			// happen often enough for it to matter
			if (!is_loader_test && failed_loader_count > 0) {
				setTimeout(function() {
					is_loader_test = true;
					submit_button.trigger("click");
					is_loader_test = false;
				}, 500);
			}			
			
			if (failed) {
				var first = $(".required-error").eq(0);
				var offset = first.offset();
				if (!offset) return false;
				var offset_top = offset.top - 100;
				$(window).scrollTop(offset_top);
				ev.preventDefault();				
				if (wrjs.on_fail)
					wrjs.on_fail();
				return false;
			}

			if (wrjs.on_submit) {
				var fini_cb = function() {
					wrjs.final = true;
					allow_send(form);
					mimic_button(submit_button);
					_this.submit();
					wrjs.final = false;
				};
				var on_submit = wrjs.on_submit;
				ev.button = submit_button[0];
				ev.is_draft_save = is_draft_save;
				if (on_submit.call(_this, ev, fini_cb) === false)
					return false;
			}

			allow_send(form, submit_button);
			
		});
		
	});

})();