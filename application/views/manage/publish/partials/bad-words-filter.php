<script>

// this really needs to be tidied up a bit
// perhaps with some standard storage 
// instead of a custom system

$(function() {

	var filter_data = <?= json_encode(Model_Setting::value('bad_words_filter')) ?>;

	if (!filter_data) return;
	if (!filter_data.filters) return;
	if (!filter_data.filters.length) return;
	var filters = filter_data.filters;

	var create_info_alert = function(matched, message) {
		if (!message) message = filter_data.default_message;
		var e_info = $.create("div");
		var e_match = $.create("strong");
		var e_message = $.create("span");
		e_info.addClass("alert alert-info");
		e_info.addClass("bad-words-alert")
		e_info.append(e_match);
		e_info.append(e_message);
		e_match.text(matched.toUpperCase());
		e_match.addClass("bad-words-match");
		e_message.text(message);
		return e_info;		
	};

	window.required_js_bad_words_filter = function(value) {
		
		var alert;
		var regex;
		var match;
		var response = {
			text: "contains text that is blocked",
			valid: true
		};

		// container to hold all alerts
		response.after = $.create("div");
		
		for (var i = 0; i < filters.length; i++) {

			var filter = filters[i];

			if (filter.string !== undefined) {
				// convert to regex for partial match
				filter.regex = RegExp.quote(filter.string);
			} else if (filter.term !== undefined) {
				// convert to regex with word boundaries
				filter.regex = "\\b" + RegExp.quote(filter.term) + "\\b";
			}

			if (filter.regex !== undefined) {
				regex = new RegExp(filter.regex, "i");
				if (match = value.match(regex)) {
					response.valid = false;
					alert = create_info_alert(match[0], filter.message);
					response.after.append(alert);
				}
			}

		}

		return response;

	};

	required_js.add_callback("bad-words-filter",
		window.required_js_bad_words_filter);

});

</script>