(function() {
	
	var filter_lists = {
		"beats": "/list_beats",
		"countries": "/list_countries",
		"coverages": "/list_coverages",
		"localities": "/list_localities",
		"media_types": "/list_media_types",
		"regions": "/list_regions",
		"roles": "/list_roles",
	};

	var client = function() {
		// url of the client can common 
		// or newsroom hosts but should
		// use current host to not be blocked
		this.url = "shared/media_database";
		this.options = {};
		this.options.request_index = 0;
		this.options.chunk_size = 20;
		this.options.sort_column = null;
		this.options.sort_reverse = false;
		this.options.unique_only = false;
		this.options.pictures_only = false;
		this.reset();
	};
	
	client.prototype.reset = function() {
		this.options.filters = {};
		this.options.search = null;
		this.options.chunk = 1;
	};
	
	client.prototype.chunk = function(value, callback) {
		this.options.chunk = value;
		this.execute(callback);
	};
	
	client.prototype.refresh = function(callback) {
		this.execute(callback);
	};
	
	client.prototype.search = function(value, callback) {
		this.options.search = value;
		this.options.chunk = 1;
		this.execute(callback);
	};
	
	client.prototype.remove_filter = function(name, value) {
		if (this.options.filters[name] === undefined) return;
		var index = this.options.filters[name].indexOf(value);
		if (index == -1) return;
		this.options.filters[name].splice(index, 1);
	};
	
	client.prototype.add_filter = function(name, value) {
		if (this.has_filter(name, value)) return;
		if (this.options.filters[name] === undefined)
			this.options.filters[name] = [];
		this.options.filters[name].push(value);
	};

	client.prototype.has_filter = function(name, value) {
		if (this.options.filters[name] === undefined) return false;
		for (var i = 0; i < this.options.filters[name].length; i++)
			if (this.options.filters[name][i] == value)
				return true;
		return false;
	};
	
	client.prototype.execute = function(callback) {
		var _client = this;
		this.options.request_index += 1;
		var options = JSON.stringify(this.options);
		var data = { options: options };
		$.post(this.url + "/execute", data, function(response) {
			// latest request if the request index matches
			response.is_latest_request = response.request_index 
				== _client.options.request_index;
			callback(response);
		});
	};
	
	client.prototype.list_filter = function(name, data, callback) {
		if (!filter_lists[name]) return;
		if ($.isFunction(data)) { callback = data; data = null; }
		$.post(this.url + filter_lists[name], data, function(response) {
			if (!response) return;
			callback(response);
		});
	};

	client.prototype.match_filter = function(search, callback) {
		var data = { "search": search };
		$.post(this.url + "/list_matching_filters", data, function(response) {
			if (!response) return;
			callback(response);
		});
	};

	client.prototype.match_filter_increase_relevance = function(search, lclass, linked) {
		var data = { "search": search, "class": lclass, "linked": linked };
		$.post(this.url + "/match_filter_increase_relevance", data);
	};
	
	window.media_database = window.media_database || {};
	window.media_database.client = client;
	
})();