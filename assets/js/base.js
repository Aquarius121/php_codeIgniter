// callback when nav-activate returns
window.__on_nav_callback = 
	window.__on_nav_callback || [];

// callback when modifier changes
window.__modifier_callbacks = 
	window.__modifier_callbacks || [];

// make sure we can still do this
window.__console = function() {
	console.log(arguments);
};

$(function() {

	if (window.bootbox !== undefined && 
		 window.bootbox.setDefaults !== undefined)
		bootbox.setDefaults({ animate: false });
	
	$.fn.reverse = [].reverse;
	
	var html = $(document.body.parentNode);
	var modifier_status = {};
	window.modifiers = modifier_status;
	modifier_status.alt_down = false;
	modifier_status.ctrl_down = false;
	modifier_status.shift_down = false;

	$(window).on("blur", function() {
		html.removeClass("alt-down");
		html.removeClass("ctrl-down");
		html.removeClass("shift-down");
		modifier_status.alt_down = false;
		modifier_status.ctrl_down = false;
		modifier_status.shift_down = false;
		for (var idx in window.__modifier_callbacks)
			window.__modifier_callbacks[idx].call(modifier_status);
	});
	
	$(document).on("keydown", function(ev) {
		if (ev.which == 16) html.addClass("shift-down");
		if (ev.which == 17) html.addClass("ctrl-down");
		if (ev.which == 18) html.addClass("alt-down");
		if (ev.which == 16) modifier_status.shift_down = true;
		if (ev.which == 17) modifier_status.ctrl_down = true;
		if (ev.which == 18) modifier_status.alt_down = true;		
		for (var idx in window.__modifier_callbacks)
			window.__modifier_callbacks[idx].call(modifier_status);
	});
	
	$(document).on("keyup", function(ev) {
		if (ev.which == 16) html.removeClass("shift-down");
		if (ev.which == 17) html.removeClass("ctrl-down");
		if (ev.which == 18) html.removeClass("alt-down");
		if (ev.which == 16) modifier_status.shift_down = false;
		if (ev.which == 17) modifier_status.ctrl_down = false;
		if (ev.which == 18) modifier_status.alt_down = false;	
		for (var idx in window.__modifier_callbacks)
			window.__modifier_callbacks[idx].call(modifier_status);
	});

	$(window).load(function() {
		html.addClass("loaded");
	});
	
	// view tooltips on hover
	var tt_options = { container: "body", trigger: "hover" };
	if ($.fn.tooltip !== undefined)
	$(".tl").tooltip(tt_options).on("click", function() {
		if ($(this).attr("href") === "#")
			return false;
	});

	$(document).on("click", "a", function(ev) {
		if (this.getAttribute("href") === "#") 
			ev.preventDefault();
	});

	// security and performance concerns
	// https://jakearchibald.com/2016/performance-benefits-of-rel-noopener/
	$("a").each(function() {
		// only applies to links in new window (ignoring iframe)
		// only applies to links to external websites? 
		if (this.getAttribute("target") !== "_blank" && 
			 /^(https?:)?\/\//.test(this.getAttribute("href"))) {
			var rel = this.getAttribute("rel");
			if (rel) rel = "noopener "  + rel;
			else rel = "noopener";
			this.setAttribute("rel", rel);
		}
	});
	
	// enable click of radio container box 
	$(document).on("click", ".radio-container-box", function(ev) {
		var $this = $(this);
		if ($this.hasClass("no-click-to-radio")) return;
		var input = $this.find("input[type=radio]");
		var label = input.parents("label.radio-container");
		if (input.is(ev.target)) return;
		if (label.is(ev.target)) return;
		if ($.contains(label.get(0), ev.target)) return;
		if (input.is(":disabled")) return;
		if (input.hasClass("disabled")) return;
		input.prop("checked", true).trigger("change");
	});
	
	// enable click of checkbox container box 
	$(document).on("click", ".checkbox-container-box", function(ev) {
		var $this = $(this);
		if ($this.hasClass("no-click-to-checkbox")) return;
		var input = $this.find("input[type=checkbox]");
		var label = input.parents("label.checkbox-container");
		if (input.is(ev.target)) return;
		if (label.is(ev.target)) return;
		if ($.contains(label.get(0), ev.target)) return;
		if (input.is(":disabled")) return;
		if (input.hasClass("disabled")) return;
		input.prop("checked", !input.is(":checked"))
			.trigger("change");
	});

});

(function() {

	window.nav_activate = function() {
	
		var base_url = $("base").attr("href");
		var absolute_url = window.location.href;
		var local_url = absolute_url.substr(base_url.length);
		
		$(".nav-activate").each(function() {

			var _nav = $(this);
			var selector = _nav.data("nav-selector");
			var use_parent = !selector;
			if (!selector) selector = "a";
			var selection = _nav.find(selector);
			var activeClass = _nav.data("nav-class");
			if (!activeClass) activeClass = "active";
			if (_nav.hasClass("nav-reverse"))
				selection.reverse();
			var activated = false;

			selection.each(function() {

				var _this = $(this);
				var _target = null;
				var target_selector = _this.data("nav-target");
				
				if (target_selector) {
					_target = $(target_selector);
					if (!_target.exists())
						_target = null;
				}

				if (!_target) {
					if (use_parent) 
					     _target = _this.parent();
					else _target = _this;
				}

				_target.removeClass(activeClass);

				if (!activated) {
					var pattern_str = _this.data("on");
					if (!pattern_str) return;
					var pattern = new RegExp(pattern_str);
					if (pattern.test(local_url)) {
						activated = true;
						_target.addClass(activeClass);
					}
				}

			});
		});
		
		for (var idx in window.__on_nav_callback)
			window.__on_nav_callback[idx].call(null, local_url);

	};

	$(function() {
		$(window.nav_activate);
	});

})();

(function($) {

	var re = /([^\?&=]+)=?([^&]*)/g;
	var decode = function (str) { 
		return decodeURIComponent(str.replace(/\+/g, " "));
	};

	$.parseParams = function(query) {
		var params = {}, e;
		while (e = re.exec(query)) { 
			var k = decode(e[1]), 
			    v = decode(e[2]);
			params[k] = v;
		}
		return params;
	};

})(jQuery);

// parse comma delim values like tags
$.parse_comma_delim = function(str) {
	
	var exploded = str.split(",");
	var listed = [];

	for (var i = 0, term; i < exploded.length; i++)
		if ((term = $.trim(exploded[i]))) 
			if (listed.indexOf(term) < 0) 
				listed.push(term);

	return listed;

};

// to match application/classes/tag.php
window.TAG_uniform = function(value) {
	value = value.toLowerCase();
	value = value.replace(/[^a-z0-9]/i, "-");
	value = value.replace(/--*/i, "-");
	value = value.replace(/(^-|-$)/i, "");
	return value;
};

// selector has matches?
$.fn.exists = function() {
	return this.length > 0;
}

String.prototype.format = function() {
	var args = arguments;
	if (typeof args[0] === 'object')
		args = args[0];
	return this.replace(/\{\{([a-z0-9_]+)\}\}/gi, function(match, name) { 
		return args[name] !== undefined
			? args[name]
			: match
		;
	});
};

window.formatString = function(string, args) {
	return string.format(args);
};

Number.prototype.pad = function(places) {
	var zero = places - this.toString().length + 1;
	return Array(+(zero > 0 && zero)).join("0") + this;
};

RegExp.quote = function(str) {
	// quote value for regex: http://goo.gl/xPGwip
	return str.replace(/[.?*+^$[\]\\(){}|-]/g, "\\$&");
};

window.construct_query_string = function(data, append) {
	if (append && window.location.search) {
		var extra = $.parseParams(window.location.search);
		for (var k in extra) 
			if (data[k] === undefined)
				data[k] = extra[k];
	} // if ----------------
	return '?' + $.param(data);
};

(function() {
	
	var __next = function(func, time, context) {
		return function() {
			func.__rate_limit_next = undefined;
			__call(func, time, context);
		};		
	};

	var __timer = function(func, time) {
		func.__rate_limit_timer = setTimeout(function() {
			if (func.__rate_limit_next) 
			     func.__rate_limit_next();
			else func.__rate_limit_enabled = false;
		}, time);
	};

	var __call = function(func, time, context) {
		__timer(func, time);
		if (context) func.call(context);
		else func.call(func);
	};
	
	var rate_limit = function(func, time, initial_wait, context) {
		if (time <= 0) time = 0;
		if (func.__rate_limit_enabled === true) {
			func.__rate_limit_next = __next(func, time, context);
		} else if (initial_wait === true) {
			func.__rate_limit_next = __next(func, time, context);
			__timer(func, time);
		} else {
			func.__rate_limit_enabled = true;
			func.__rate_limit_next = undefined;
			__call(func, time, context);
		}
	};
	
	var rate_limit_reset = function(func) {
		if (func.__rate_limit_timer !== undefined)
			window.clearTimeout(func.__rate_limit_timer);
		func.__rate_limit_next = undefined;
		func.__rate_limit_enabled = false;
	};
	
	window.rate_limit = rate_limit;
	window.rate_limit_reset = rate_limit_reset;
	
})();

window.wait_for_document_load = function() {
	
	var context = $("html");
	context.hide();
	$(window).on("load", function() {
		context.show();
	});
	
};

(function($) {

	window.addEventListener("popstate", function(ev) {
		
		if (!ev.state) return;
		if (!ev.state.ax) return;
		ev.preventDefault();

		if (ev.state.init)
		     window.location = ev.state.url;
		else ax_request(ev.state);

	});

	var ax_request = function(state) {

		var url = state.url;
		var elements = state.elements;

		$.each(elements, function(i, selector) {
			var $element = $(selector);
			$element.addClass("ax-loader");
		});

		var headers = { "X-AX-Elements": elements };
		var on_success = function(res) {
			if (!res) window.location = url;
			if (!res.elements) window.location = url;
			window.nav_activate();
			$.each(res.elements, function(selector, html) {
				var $element = $(selector);
				$element.html(html).removeClass("ax-loader");
			});
		};

		$.ajax({
			headers: headers,
			success: on_success,
			url: url,
		});

	};

	var ax_click_handler = function(ev) {
		
		if (window.modifiers.alt_down ||
			 window.modifiers.ctrl_down ||
			 window.modifiers.shift_down)
			return;

		ev.preventDefault();

		var _ax_link = $(this);
		var _ax_element = _ax_link;
		if (!_ax_element.hasClass("ax-loadable"))
			_ax_element = _ax_element.parents(".ax-loadable").eq(0);
		var elements = _ax_element.data("ax-elements");
		elements = elements.split(/\s*,\s*/);
		var url = _ax_element.data("ax-href");
		if (!url) url = _ax_link.attr("href");
		_ax_link.blur();

		var state = {
			ax: true,
			elements: elements,
			url: url,
		};

		if (window.history && window.history.pushState) 
			window.history.pushState(state, 
				document.title, url);

		ax_request(state);

	};

	$(document).on("click", ".ax-loadable a", ax_click_handler);
	$(document).on("click", ".ax-loadable button", ax_click_handler);
	$(document).on("click", "a.ax-loadable", ax_click_handler);
	$(document).on("click", "button.ax-loadable", ax_click_handler);

	$(function() {

		var state = { 
			ax: true,
			init: true, 
			url: new String(document.location)
		};

		if (window.history && window.history.replaceState)
			window.history.replaceState(state, 
				document.title, state.url);

	});

})(jQuery);

(function($, undefined) {
	
	// create elements without raw html
	$.create = function(tag, properties) {
		var element = $(document.createElement(tag));
		if (properties === undefined) return element;
		for (var idx in properties) 
			element.attr(idx, properties[idx]);
		return element;
	};

	// chained version of create
	$.fn.create = function(tag, properties) {
		var element = $.create(tag, properties);
		element.prevObject = this;
		this.append(element);
		return element;
	};
		
})(jQuery);

// test basic objects for equal values 
// ignoring any functions that exist
window.compareObjects = function(left, right, i) {

	if (left === right)
		return true;

	if (typeof left !== 'object') return false;
	if (typeof right !== 'object') return false;
	if (left === null) return false;
	if (right === null) return false;

	for (i in left) {
		if (!left.hasOwnProperty(i)) continue;
		var leftType = typeof left[i];
		var rightType = typeof right[i];
		if (leftType !== rightType) return false;
		if (!right.hasOwnProperty(i)) return false;
		switch (leftType) {
			case 'object':
				if (!window.compareObjects(left[i], right[i]))
					return false;
				break;
			case 'function':
				break;
			default:
				if (left[i] !== right[i])
					return false;
		}
	}

	for (i in right) {
		if (!right.hasOwnProperty(i)) continue;
		if (typeof left[i] === 'undefined' || !left.hasOwnProperty(i))
			return false;
	}

	return true;

}