$(function() {
	
	// list filters remove
	$(".list-filters a.remove").click(function(ev) {
		var list_filter = $(this).parents(".list-filter");
		var gstring = list_filter.data("gstring");
		gstring = gstring.replace(/[^a-z0-9=%_]/gi, "[^a-z0-9=%_]+");
		var pattern = new RegExp(gstring, "gi");
		window.location = window.location.href.replace(pattern, "");
		ev.preventDefault();
		return false;
	});
	
	// add-filter-icon handler
	$(".add-filter-icon").click(function(ev) {
		var _this = $(this);
		var gstring = _this.data("gstring");
		if (!gstring) return;
		var url = window.location.href;
		if (url.indexOf("?") === -1)
		     url = url + "?" + gstring;
		else url = url + gstring;
		window.location = url;
		ev.preventDefault();
		return false;
	});

});