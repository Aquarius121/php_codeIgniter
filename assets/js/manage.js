// ckeditor => init_editor
(function(e) {
	
	var __elements = [];
	var __options = [];
	var __callbacks = [];
	var __loaded = false;
	var __load_started = false;
	var __load_counter = 0;
	
	window.init_editor = function(elements, options, callback) {

		var dependency_loaded = function() {
			if (--__load_counter == 0)
				editor_loaded();
		};

		var editor_loaded = function() {
			__loaded = true;
			CKEDITOR.timestamp = ASSETS_VERSION;
			for (var idx in __elements)
				init_editor_elements(__elements[idx], __options[idx], __callbacks[idx]);
		};
		
		var init_editor_elements = function(elements, options, callback) {
			elements.each(function() {

				CKEDITOR.replace(this, options);

				if (!options.disable_spell_check) {
					nanospell.ckeditor(this.id, {
						autostart: true,
						dictionary: "en_US",
						server: "custom", 
						ignore_non_alpha: true,
						ignore_block_caps: true
					});
				}

				if (callback === undefined) return;
				callback.call(CKEDITOR.instances[this.id]);

			});
		};
		
		if (!__load_started) {

			__load_started = true;

			$(function() {

				(function(wrjs) {
					if (!wrjs) return;
					wrjs.on_before_submit.push(function() {
						if (window.CKEDITOR.env.isCompatible)
							for (var idx in window.CKEDITOR.instances)
								window.CKEDITOR.instances[idx].updateElement();
					});
				})(window.required_js);

				var cke_url = CKEDITOR_BASEPATH + "/ckeditor.js?" + ASSETS_VERSION;
				var ajax_opt = { dataType: "script", cache: true, url: cke_url };
				$.ajax(ajax_opt).done(function() {

					var nanospell_url = CKEDITOR_BASEPATH + "/nanospell/autoload.js?" + ASSETS_VERSION;
					var ajax_opt = { dataType: "script", cache: true, url: nanospell_url };
					$.ajax(ajax_opt).done(dependency_loaded);
					__load_counter++;

				});

			});
			
		}
		
		if (!__loaded) {	
			__elements.push(elements);
			__options.push(options);
			__callbacks.push(callback);
			return;
		}
		
		init_editor_elements(elements, options, callback);
		return;
		
	};
	
})();

// ajax file upload
(function(undefined) {
	
	var xhr_create = function() {
		
		if (window.XMLHttpRequest !== undefined)
			return new window.XMLHttpRequest();
		if (!window.ActiveXObject) return null;
		try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (err) {}
		try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch (err) {}
		return null;
		
	};
	
	$.fn.ajax_upload = function(ex_options) {
		
		if (window.FormData === undefined)
			return false;
		
		var options = {
			url: null,
			callback: null,
			fd: new window.FormData(),
			progress: null,
			data: {}
		};
		
		$.extend(options, ex_options);
		
		for (var i = 0; i < this[0].files.length; i++)
			options.fd.append(this.attr("name"), this[0].files[i]);
		
		for (var idx in options.data) {
			if ($.isArray(options.data[idx])) {
				for (var i = 0; i < options.data[idx].length; i++) 
					options.fd.append(idx + "[]", options.data[idx][i]);
			} else {
				options.fd.append(idx, options.data[idx]);
			}
		}
		
		return $.ajax({
			url: options.url,
			data: options.fd,
			processData: false,
			contentType: false,
			type: "POST",
			success: options.callback,
			xhr: function() {
				var xhr = xhr_create();
				xhr.upload.addEventListener("progress", function(ev) {
					if (options.progress && ev.lengthComputable) 
						options.progress(ev);
				}, false);
				return xhr;
			}
		});
		
	};
	
})();
	
$.fn.limit_length = function(length, status, status_number) {
	
	var _this = this;
	
	var _remain = function(value) {
		return length - value.length;
	};
	
	var _color = function(remain) {
		if (status) status.toggleClass("low-remain", remain < 0.025 * length)
		if (status) status.toggleClass("no-remain", remain <= 0)
	};

	var _norm = function(val) {
		return val.replace(/\s/g, "");
	};
	
	// warning to the user that 
	// the length has been cut off
	// at a time they might not expect
	var name = _this.data("name");
	var message = "<strong>Warning!</strong> \
		The <strong>" + name + "</strong> text has been cut off.";

	if (_this.val().length > length && window.bootbox) {
		window.bootbox.alert({
			className: "bootbox-danger",
			message: message
		});
	}
	
	_this.off("change.limit-length");
	_this.off("keypress.limit-length");
	_this.off("keyup.limit-length");
	_this.off("paste.limit-length");

	_this.attr("maxlength", length);
	_this.on("change.limit-length", function() {
		var value = _this.val();
		if (_remain(value) < 0) {
			if (status_number) 
				status_number.text(0);
			value = value.substr(0, length);
			_this.val(value);
			_color(0);
		} else {
			var remain = _remain(value);
			if (status_number) 
				status_number.text(remain);
			_color(remain);
		}
	});
	
	_this.on("keypress.limit-length", function(ev) {
		var value = _this.val();
		if (ev.ctrlKey) return;
		if (ev.which === 0 || 
			 ev.which === 8 || 
			 ev.which === 46) return;
		if (_remain(value) <= 0)
			return false;
	});
	
	_this.on("keyup.limit-length", function() {
		var value = _this.val();
		var remain = _remain(value);
		if (status_number) 
			status_number.text(remain);
		_color(remain);
	});

	_this.on("paste.limit-length", function(ev) {
		var expectedData = null;
		if (ev.originalEvent.clipboardData !== undefined &&
			 ev.originalEvent.clipboardData.getData !== undefined)
			expectedData = ev.originalEvent.clipboardData.getData("text");
		setTimeout(function() {
			if ((expectedData && _norm(_this.val()).indexOf(_norm(expectedData)) == -1) ||
				(!expectedData && _this.val().length >= length)) {
				if (window.bootbox) {
					window.bootbox.alert({
						className: "bootbox-danger",
						message: message
					});
				}
			}
		}, 0);
	});
	
	_this.trigger("change");
	
};

jQuery.fn.select_text = function() {
	
	var doc = document, 
		element = this[0],
		selection,
		range;
	
	if (doc.body.createTextRange) {
		range = document.body.createTextRange();
		range.moveToElementText(element);
		range.select();
	} else if (window.getSelection) {
		selection = window.getSelection();
		range = document.createRange();
		range.selectNodeContents(element);
		selection.removeAllRanges();
		selection.addRange(range);
	}
	
};

// floating text
$(function() {

	var $floatingText = null;

	window.enableFloatingText = function(text) {
		window.disableFloatingText();
		$floatingText = $.create('div');
		$floatingText.addClass('floating-text');
		$floatingText.text(text);
		$(document.body).append($floatingText);
	};

	window.disableFloatingText = function() {
		if (!$floatingText) return;
		$floatingText.remove();
		$floatingText = null;
	};

	var moveText = function(ev) {
		if (!$floatingText) return;
		$floatingText.css('left', this.pageX);
		$floatingText.css('top', this.pageY);
	};

	$(document).on('mousemove', function(ev) {
		if (!$floatingText) return;
		rate_limit(moveText, 0, 0, ev);
	});

});

// other funcs
$(function() {

	// word count regex for form fields, other
	window.word_count_regex = /([a-z0-9]\S*(\s+[^a-z0-9]*|$))/ig;
	
	var update_placeholder = function() {
		var _this = $(this);
		var has_value = !!($.trim(_this.val()));
		var placeholder = _this.next(".placeholder");
		placeholder.toggleClass("active", has_value);
	};
	
	var placeholders = $(".has-placeholder");
	placeholders.on("change", update_placeholder);
	placeholders.each(update_placeholder);
	
	window.selectable_results_bind_reset = function() {
		var selectable_results = $("#selectable-results");
		var filter = ".checkbox-container input[type=checkbox]";
		selectable_results.on("change", filter, function() {
			if ($(this).is(":checked")) return;
			selectable_results.find("#all-checkbox")
				.prop("checked", false);
		});
	};

	window.selectable_results_bind_reset();

	// add http:// prefix to a url not 
	// starting with a valid protocol
	var url_boxes = $("input.url");
	var pattern = /^(http:|https:|mailto:)/i;
	url_boxes.on("change", function() {
		var _this = $(this);
		var value = _this.val();
		if (!value) return;
		if (value.substr(0, 2) === "//")
			_this.val("http://" + value.substr(2));
		else if (!pattern.test(value))
			_this.val("http://" + value);
	});

});

$(function() {

	// ------------------------------------
	// ------------------------------------
	//   version: manage25 ONLY
	// ------------------------------------
	// ------------------------------------

	// not version 2.5? exit
	if (!$(".manage25").length)
		return;

	// activate boostrap tooltips
	$("[rel='tooltip']").tooltip();

	// find out which responsive class we are using
	// currently from the list: xs, sm, md, lg
	window.find_bootstrap_environment = function() {
		var envs = ['xs', 'sm', 'md', 'lg'];
		var $el = $('<div>');
		$el.appendTo($('body'));
		for (var i = envs.length - 1; i >= 0; i--) {
			var env = envs[i];
			$el.addClass('hidden-'+env);
			if ($el.is(':hidden')) {
				$el.remove();
				return env;
			}
		}
	};

	// is this a desktop environment?
	// based on the bootstrap responsive class
	window.is_desktop = function() {
		var env = find_bootstrap_environment()
		if (env == 'lg' || env == 'md')
			return true;
		return false;
	};

});

