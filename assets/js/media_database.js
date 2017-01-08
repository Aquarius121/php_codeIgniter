$(function() {
	
	var search_form = $("#md-search-form");
	var results_relative = $("#md-results-relative");
	var search_box = $("#md-search-box");
	var filters_list = $("#md-filters-list");
	var filters_list_results = filters_list.children(".results");
	var match_filters = $("#md-match-filters");
	var match_filters_results = match_filters.children(".results");
	var results_container = $("#md-results");
	var chunkination_container = $("#md-chunkination");
	var add_filter_button = $("#md-add-filter-button");
	var clear_search_button = $("#md-clear-search-button");
	var clear_filter_button = $("#md-clear-filter-button");
	var submit_search_button = $("#md-search-submit-button");
	var submit_search_button_icon = $("#md-search-button");
	var add_filter_set_button = $("#md-add-filter-set-button");
	var add_filter_cancel_button = $("#md-add-filter-cancel-button");
	var add_filter_modal = $("#md-add-filter-modal");
	var add_filter_modal_content = add_filter_modal.find(".modal-content");
	var add_filter_tabs = $("#md-add-filter-tabs");
	var add_filter_tab_content = $("#md-add-filter-tab-content");
	var add_filter_tabs_children = add_filter_tabs.children();
	var add_filter_tab_content_children = add_filter_tab_content.children();
	var countries_content_tab = $("#md-aftc-countries");
	var regions_content_tab = $("#md-aftc-regions");
	var beats_content_tab = $("#md-aftc-beats");	
	var media_types_content_tab = $("#md-aftc-media-types");
	var roles_content_tab = $("#md-aftc-roles");
	var coverages_content_tab = $("#md-aftc-coverages");
	var localities_content_tab = $("#md-aftc-localities");
	var beats_search_box = $("#md-beats-search");
	var inject_or_set_filter = {};

	var filter_list_data = {
		"beats": undefined,
		"countries": undefined,		
		"coverages": undefined,
		"localities": undefined,
		"media_types": undefined,
		"regions": undefined,
		"roles": undefined,
	};
	
	var filter_list_index = {
		"beats": undefined,
		"countries": undefined,
		"coverages": undefined,
		"localities": undefined,
		"media_types": undefined,
		"regions": undefined,
		"roles": undefined,
	};
	
	var filter_list_labels = {
		"beats": "Beat",
		"countries": "Country",
		"coverages": "Coverage",
		"localities": "City",
		"media_types": "Media",
		"regions": "Region",
		"roles": "Role",
	};
	
	var mdob = window.__media_database_ob = {};
	mdob.has_select_all = false;
	
	var client = new media_database.client();
	mdob.client = window.__media_database_client = client;
	client.url = window.__media_database_url;
	
	var search_update = function() {
		search_form.addClass("loader");
		results_relative.addClass("loader");
		rate_limit(search_execute, 500);
	};
	
	var search_execute = function() {
		var value = search_box.val();
		client.search(value, render_results);
		search_form.addClass("loader");
		results_relative.addClass("loader");
	};
	
	var render_results = function(response) {
		if (!response.is_latest_request) return;
		mdob.latest_response = response;
		search_form.removeClass("loader");
		results_relative.removeClass("loader");
		results_container.addClass("loaded");
		chunkination_container.empty();
		chunkination_container.html(response.chunkination_html);
		results_container.empty();
		results_container.html(response.results_html);
		update_selected_count();
	};
	
	chunkination_container.on("click", "a.chunk", function() {
		$(window).scrollTop(0);
		var chunk = $(this).data("chunk");
		client.chunk(chunk, render_results);
	});
	
	add_filter_button.on("click", function() {
		add_filter_modal.modal("show");
	});
	
	add_filter_cancel_button.on("click", function() {
		add_filter_modal.modal("hide");
	});
	
	add_filter_tabs_children.on("click", function() {
		var _this = $(this);
		var index = add_filter_tabs_children.index(_this);
		var tab = add_filter_tab_content_children.eq(index);
		add_filter_tab_content_children.removeClass("active");
		add_filter_tabs_children.removeClass("active");
		_this.addClass("active");
		tab.addClass("active");
		// focus search field if exists
		tab.find(".md-filter-search").empty().focus();
		add_filter_modal_content.scrollTop(0);
	});
	
	window.__media_database_refresh = function() {
		search_form.addClass("loader");
		results_relative.addClass("loader");
		client.refresh(render_results);
	};
	
	// search_box.on("keypress", search_update);
	// search_box.on("change", search_update);

	var match_filter_update = function() {
		match_filters.addClass("filter-loader");
		rate_limit(match_filter_execute, 500);
	};
	
	var match_filter_execute = function() {
		var value = search_box.val();
		client.match_filter(value, render_match_filter);
		match_filters.addClass("filter-loader");
	};
	
	search_box.on("keypress", function(ev) {
		if (ev.which != 13) return match_filter_update();
		rate_limit_reset(match_filter_execute);
		rate_limit_reset(search_execute);
		ev.preventDefault();
		search_execute();
	});
	
	search_box.focus();
	// search_execute();
	
	submit_search_button.on("click", search_execute);
	submit_search_button_icon.on("click", search_execute);

	search_box.on("blur", function() {
		match_filters.addClass("hidden");
	});

	search_box.on("focus", function() {
		if (match_filters_results.children().size())
			match_filters.removeClass("hidden");
		match_filter_execute();
	});

	inject_or_set_filter.beats = function(ob) {
		if (filter_list_index.beats !== undefined && 
			 filter_list_index.beats[ob.id] !== undefined) {
			var item = filter_list_index.beats[ob.id];
			item.checkbox.prop("checked", true);
		}
	};

	inject_or_set_filter.countries = function(ob) {
		if (filter_list_index.countries !== undefined && 
			 filter_list_index.countries[ob.id] !== undefined) {
			var item = filter_list_index.countries[ob.id];
			item.checkbox.prop("checked", true);
		}
	};

	inject_or_set_filter.regions = function(ob) {
		if (filter_list_index.regions !== undefined && 
			 filter_list_index.regions[ob.id] !== undefined) {
			var item = filter_list_index.regions[ob.id];
			item.checkbox.prop("checked", true);
		} else {
			var regions_results = regions_content_tab.find(".results");
			if (filter_list_index.regions === undefined)
				filter_list_index.regions = {};
			filter_list_index.regions[ob.id] = ob;
			regions_results.prepend(render_list_regions_item(ob, true));
		}
	};

	inject_or_set_filter.localities = function(ob) {
		if (filter_list_index.localities !== undefined && 
			 filter_list_index.localities[ob.id] !== undefined) {
			var item = filter_list_index.localities[ob.id];
			item.checkbox.prop("checked", true);
		} else {
			var localities_results = localities_content_tab.find(".results");
			if (filter_list_index.localities === undefined)
				filter_list_index.localities = {};
			filter_list_index.localities[ob.id] = ob;
			localities_results.prepend(render_list_localities_item(ob, true));
		}
	};

	inject_or_set_filter.media_types = function(ob) {
		if (filter_list_index.media_types !== undefined && 
			 filter_list_index.media_types[ob.id] !== undefined) {
			var item = filter_list_index.media_types[ob.id];
			item.checkbox.prop("checked", true);
		}
	};

	inject_or_set_filter.roles = function(ob) {
		if (filter_list_index.roles !== undefined && 
			 filter_list_index.roles[ob.id] !== undefined) {
			var item = filter_list_index.roles[ob.id];
			item.checkbox.prop("checked", true);
		}
	};

	var render_match_filter = function(response) {
		match_filters.removeClass("filter-loader");
		match_filters_results.empty();
		match_filters.addClass("hidden");
		if (!response.results) return;
		var results = response.results;
		if (results.countries !== undefined)
			render_match_filter_countries(results.countries);
		if (results.regions !== undefined)
			render_match_filter_regions(results.regions);
		if (results.localities !== undefined)
			render_match_filter_localities(results.localities);
		if (results.media_types !== undefined)
			render_match_filter_media_types(results.media_types);
		if (results.roles !== undefined)
			render_match_filter_roles(results.roles);
		if (results.beats !== undefined)
			render_match_filter_beats(results.beats);
	};

	var render_match_filter_item = function(name, label, value, callback) {		
		match_filters.removeClass("hidden");
		var e_filter = $.create("span");
		var e_label = $.create("span");
		var e_value = $.create("span");
		var e_add = $.create("span");
		e_filter.addClass(name);
		e_filter.addClass("filter");
		e_label.addClass("f-label");
		e_value.addClass("f-value");
		e_add.addClass("f-add");
		e_filter.append(e_label);
		e_filter.append(e_value);
		e_filter.append(e_add);
		e_label.text(label.toLowerCase());
		e_value.text(value);
		match_filters_results.append(e_filter);
		e_add.on("click", function() {
			callback(e_filter);
			e_filter.addClass("added");
		});
		return e_filter;
	};

	var add_match_filter_beats = function(ob) {
		return function(e_filter) {
			if (e_filter.hasClass("added")) return;
			var search = search_box.val();
			client.match_filter_increase_relevance(search, "beats", ob.id);
			add_filter("beats", ob.id, ob.name);
			inject_or_set_filter.beats(ob);
			search_box.val("");
			search_execute();
		};
	};

	var render_match_filter_beats = function(beats) {
		for (var i = 0; i < beats.length; i++) {
			var beat = beats[i];
			var e_filter = render_match_filter_item( 
				"beats", filter_list_labels.beats,
				beat.name, add_match_filter_beats(beat));
			e_filter.toggleClass("added", client.has_filter("beats", beat.id));
		}
	};

	var add_match_filter_countries = function(ob) {
		return function(e_filter) {
			if (e_filter.hasClass("added")) return;
			var search = search_box.val();
			client.match_filter_increase_relevance(search, "countries", ob.id);
			add_filter("countries", ob.id, ob.name);
			inject_or_set_filter.countries(ob);
			search_box.val("");
			search_execute();
		};
	};

	var render_match_filter_countries = function(countries) {
		for (var i = 0; i < countries.length; i++) {
			var country = countries[i];
			var e_filter = render_match_filter_item( 
				"countries", filter_list_labels.countries,
				country.name, add_match_filter_countries(country));
			e_filter.toggleClass("added", client.has_filter("countries", country.id));
		}
	};

	var add_match_filter_regions = function(ob) {
		return function(e_filter) {
			if (e_filter.hasClass("added")) return;
			var search = search_box.val();
			client.match_filter_increase_relevance(search, "regions", ob.id);
			add_filter("regions", ob.id, ob.name);
			inject_or_set_filter.regions(ob);
			search_box.val("");
			search_execute();
		};
	};

	var render_match_filter_regions = function(regions) {
		for (var i = 0; i < regions.length; i++) {
			var region = regions[i];
			var e_filter = render_match_filter_item( 
				"regions", filter_list_labels.regions,
				region.name, add_match_filter_regions(region));
			e_filter.toggleClass("added", client.has_filter("regions", region.id));
		}
	};

	var add_match_filter_localities = function(ob) {
		return function(e_filter) {
			if (e_filter.hasClass("added")) return;
			var search = search_box.val();
			client.match_filter_increase_relevance(search, "localities", ob.id);
			add_filter("localities", ob.id, ob.name);
			inject_or_set_filter.localities(ob);
			search_box.val("");
			search_execute();
		};
	};

	var render_match_filter_localities = function(localities) {
		for (var i = 0; i < localities.length; i++) {
			var locality = localities[i];
			var e_filter = render_match_filter_item( 
				"localities", filter_list_labels.localities,
				locality.name, add_match_filter_localities(locality));
			e_filter.toggleClass("added", client.has_filter("localities", locality.id));
		}
	};

	var add_match_filter_media_types = function(ob) {
		return function(e_filter) {
			if (e_filter.hasClass("added")) return;
			var search = search_box.val();
			client.match_filter_increase_relevance(search, "media_types", ob.id);
			add_filter("media_types", ob.id, ob.media_type);
			inject_or_set_filter.media_types(ob);
			search_box.val("");
			search_execute();
		};
	};

	var render_match_filter_media_types = function(media_types) {
		for (var i = 0; i < media_types.length; i++) {
			var media_type = media_types[i];
			var e_filter = render_match_filter_item( 
				"media_types", filter_list_labels.media_types,
				media_type.media_type, add_match_filter_media_types(media_type));
			e_filter.toggleClass("added", client.has_filter("media_types", media_type.id));
		}
	};

	var add_match_filter_roles = function(ob) {
		return function(e_filter) {
			if (e_filter.hasClass("added")) return;
			var search = search_box.val();
			client.match_filter_increase_relevance(search, "roles", ob.id);
			add_filter("roles", ob.id, ob.role);
			inject_or_set_filter.roles(ob);
			search_box.val("");
			search_execute();
		};
	};

	var render_match_filter_roles = function(roles) {
		for (var i = 0; i < roles.length; i++) {
			var role = roles[i];
			var e_filter = render_match_filter_item( 
				"roles", filter_list_labels.roles,
				role.role, add_match_filter_roles(role));
			e_filter.toggleClass("added", client.has_filter("roles", role.id));
		}
	};

	// -------------------------------------------

	var beats_search = function() {

		var beats_results = beats_content_tab.find(".results");
		var value = beats_search_box.val();
		var terms = value.split(/\s+/);
		var clean_terms = [];

		for (var i = 0; i < terms.length; i++) {
			var term = terms[i].toLowerCase();
			if (term) clean_terms.push(term);
		}

		var has_search = !!clean_terms.length;
		beats_content_tab.toggleClass("has-search", 
			has_search);

		if (!has_search) {

			return beats_results.find("label.single").each(function() {
				$(this).removeClass("hidden-from-search");
			});
			
		}

		beats_results.find("label.single").each(function() {
			
			var _this = $(this);
			var is_hidden = false;
			var text = _this.children("span").text().toLowerCase();

			for (var i = 0; i < clean_terms.length; i++) {
				if (text.indexOf(clean_terms[i]) === -1) {
					is_hidden = true;
					break;
				}
			}

			_this.toggleClass("hidden-from-search",
				is_hidden);

		});

	};

	beats_search_box.on("keypress", beats_search);
	beats_search_box.on("change", beats_search);

	var render_list_beats = function(response) {

		var beats_results = beats_content_tab.find(".results");
		var groups = response.results;

		filter_list_data.beats = groups;
		filter_list_index.beats = {};
		filter_list_index.beats.groups = {};
		beats_results.empty();
		
		for (var i = 0; i < groups.length; i++) {
			
			var group = groups[i];
			var beats = group.beats;
			
			filter_list_index.beats.groups[group.id] = group;
			
			var item_container = $.create("label");
			item_container.addClass("group");
			
			var item_expander = $.create("i");
			item_expander.addClass("expander");
			item_container.append(item_expander);
			
			var item_checkbox = $.create("input");
			item_checkbox.attr("type", "checkbox");
			item_checkbox.data("id", group.id);
			item_checkbox.addClass("has-expander");
			item_container.append(item_checkbox);
			var item_text = $.create("span");
			item_text.text(group.name);
			item_container.append(item_text);
			beats_results.append(item_container);
			group.checkbox = item_checkbox;
			
			item_expander.on("click", function() {
				$(this).parent().toggleClass("expanded");
				return false;
			});
			
			(function(group) {
				item_checkbox.on("change", function() {
					var is_checked = $(this).is(":checked");
					var beats = group.beats;
					for (var j = 0; j < beats.length; j++) {
						beats[j].checkbox.prop("disabled", is_checked);
						beats[j].checkbox.prop("checked", is_checked);
					}
				});
			})(group);
			
			var group_container = $.create("div");
			group_container.addClass("group-container");
			beats_results.append(group_container);
			
			for (var j = 0; j < beats.length; j++) {
				var beat = beats[j];
				filter_list_index.beats[beat.id] = beat;
				var item_container = $.create("label");
				item_container.addClass("single");
				var item_checkbox = $.create("input");
				item_checkbox.attr("type", "checkbox");
				item_checkbox.data("id", beat.id);
				item_container.append(item_checkbox);
				var item_text = $.create("span");
				item_text.text(beat.name);
				item_container.append(item_text);
				group_container.append(item_container);
				beat.checkbox = item_checkbox;
			}
						
		}		
	};
	
	var render_list_media_types = function(response) {
		var media_types = response.results;
		filter_list_data.media_types = media_types;
		filter_list_index.media_types = {};
		media_types_content_tab.empty();
		for (var i = 0; i < media_types.length; i++) {
			var media_type = media_types[i];
			filter_list_index.media_types[media_type.id] = media_type;
			var item_container = $.create("label");
			item_container.addClass("group");
			var item_checkbox = $.create("input");
			item_checkbox.attr("type", "checkbox");
			item_checkbox.data("id", media_type.id);
			item_container.append(item_checkbox);
			var item_text = $.create("span");
			item_text.text(media_type.media_type);
			item_container.append(item_text);
			media_types_content_tab.append(item_container);
			media_type.checkbox = item_checkbox;
		}		
	};
	
	var render_list_roles = function(response) {
		var roles = response.results;
		filter_list_data.roles = roles;
		filter_list_index.roles = {};
		roles_content_tab.empty();
		for (var i = 0; i < roles.length; i++) {
			var role = roles[i];
			filter_list_index.roles[role.id] = role;
			var item_container = $.create("label");
			item_container.addClass("group");
			var item_checkbox = $.create("input");
			item_checkbox.attr("type", "checkbox");
			item_checkbox.data("id", role.id);
			item_container.append(item_checkbox);
			var item_text = $.create("span");
			item_text.text(role.role);
			item_container.append(item_text);
			roles_content_tab.append(item_container);
			role.checkbox = item_checkbox;
		}		
	};
	
	var render_list_coverages = function(response) {
		var coverages = response.results;
		filter_list_data.coverages = coverages;
		filter_list_index.coverages = {};
		coverages_content_tab.empty();
		for (var i = 0; i < coverages.length; i++) {
			var coverage = coverages[i];
			filter_list_index.coverages[coverage.id] = coverage;
			var item_container = $.create("label");
			item_container.addClass("group");
			var item_checkbox = $.create("input");
			item_checkbox.attr("type", "checkbox");
			item_checkbox.data("id", coverage.id);
			item_container.append(item_checkbox);
			var item_text = $.create("span");
			item_text.text(coverage.coverage);
			item_container.append(item_text);
			coverages_content_tab.append(item_container);
			coverage.checkbox = item_checkbox;
		}		
	};
	
	var render_list_localities_item = function(locality, checked) {
		
		filter_list_index.localities[locality.id] = locality;
		var item_container = $.create("label");
		item_container.addClass("group");
		var item_checkbox = $.create("input");
		item_checkbox.attr("type", "checkbox");
		item_checkbox.data("id", locality.id);
		item_checkbox.prop("checked", checked);
		item_container.append(item_checkbox);
		var item_text = $.create("span");
		item_text.text(locality.name);
		item_container.append(item_text);
		locality.checkbox = item_checkbox;
		if (locality.region) {
			var region = locality.region;
			var item_region = $.create("span");
			item_region.addClass("region");
			item_region.text(region.name);
			item_text.append(item_region);
		} else if (locality.country) {
			var country = locality.country;
			var item_country = $.create("span");
			item_country.addClass("country");
			item_country.text(country.name);
			item_text.append(item_country);
		}

		return item_container; 

	};

	var render_list_localities = function(response) {
	
		var localities = response.results;
		var localities_results = localities_content_tab.find(".results");
		var hidden_localities_index = {};
		
		localities_results.find("input").each(function() {
			var _this = $(this);
			var id = _this.data("id");
			var is_already = hidden_localities_index[id] !== undefined;
			if (_this.is(":checked") && !is_already) {
				var locality = filter_list_index.localities[id];
				hidden_localities_index[id] = locality;
				if (response.search)
				     _this.parent().addClass("hidden");
				else _this.parent().removeClass("hidden");
			} else {
				_this.parent().remove();
			}
		});	
		
		filter_list_data.localities = localities;
		filter_list_index.localities = {};
		$.each(hidden_localities_index, function(k, v) {
			filter_list_index.localities[k] = v;
		});
		
		for (var i = 0; i < localities.length; i++) {
			
			var locality = localities[i];
			if (filter_list_index.localities[locality.id] !== undefined) {
				if (!response.search) continue;
				var locality = filter_list_index.localities[locality.id];
				var item_container = locality.checkbox.parent().detach();
				localities_results.append(item_container);
				item_container.removeClass("hidden");
				continue;
			}
			
			localities_results.append(render_list_localities_item(locality));
			
		}	
		
		// remove any existing limit text
		localities_results.find(".limit-text").remove();
		
		if (localities.length == 0) {
			var limit_text = $.create("div");
			limit_text.addClass("limit-text muted");
			limit_text.text("No cities founds.");
			localities_results.append(limit_text);
		}
		
		if (localities.length == 100) {
			var limit_text = $.create("div");
			limit_text.addClass("limit-text search-more muted");
			limit_text.text("Search to find more cities.");
			localities_results.append(limit_text);
		}
			
	};	

	var render_list_regions_item = function(region, checked) {

		filter_list_index.regions[region.id] = region;
		var item_container = $.create("label");
		item_container.addClass("group");
		var item_checkbox = $.create("input");
		item_checkbox.attr("type", "checkbox");
		item_checkbox.data("id", region.id);
		item_checkbox.prop("checked", checked);
		item_container.append(item_checkbox);
		var item_text = $.create("span");
		item_text.text(region.name);
		item_container.append(item_text);
		region.checkbox = item_checkbox;
		if (region.country) {
			var country = region.country;
			var item_country = $.create("span");
			item_country.addClass("country");
			item_country.text(country.name);
			item_text.append(item_country);
		}

		return item_container;

	};
	
	var render_list_regions = function(response) {
	
		var regions = response.results;
		var regions_results = regions_content_tab.find(".results");
		var hidden_regions_index = {};
		
		regions_results.find("input").each(function() {
			var _this = $(this);
			var id = _this.data("id");
			var is_already = hidden_regions_index[id] !== undefined;
			if (_this.is(":checked") && !is_already) {
				var region = filter_list_index.regions[id];
				hidden_regions_index[id] = region;
				if (response.search)
				     _this.parent().addClass("hidden");
				else _this.parent().removeClass("hidden");
			} else {
				_this.parent().remove();
			}
		});
		
		filter_list_data.regions = regions;
		filter_list_index.regions = {};
		$.each(hidden_regions_index, function(k, v) {
			filter_list_index.regions[k] = v;
		});
		
		for (var i = 0; i < regions.length; i++) {
			
			var region = regions[i];
			if (filter_list_index.regions[region.id] !== undefined) {
				if (!response.search) continue;
				var region = filter_list_index.regions[region.id];
				var item_container = region.checkbox.parent().detach();
				regions_results.append(item_container);
				item_container.removeClass("hidden");
				continue;
			}

			regions_results.append(render_list_regions_item(region));
			
		}
		
		// remove any existing limit text
		regions_results.find(".limit-text").remove();
		
		if (regions.length == 0) {
			var limit_text = $.create("div");
			limit_text.addClass("limit-text muted");
			limit_text.text("No regions founds.");
			regions_results.append(limit_text);
		}
		
		if (regions.length == 100) {
			var limit_text = $.create("div");
			limit_text.addClass("limit-text search-more muted");
			limit_text.text("Search to find more regions.");
			regions_results.append(limit_text);
		}
			
	};	
	
	var render_list_countries = function(response) {
		
		var countries = response.results;
		filter_list_data.countries = countries;
		filter_list_index.countries = {};
		countries_content_tab.empty();
		var common_divider = false;
		
		for (var i = 0; i < countries.length; i++) {
			var country = countries[i];
			filter_list_index.countries[country.id] = country;
			if (!parseInt(country.is_common) && !common_divider) {
				countries_content_tab.append($.create("hr"));
				common_divider = true;				
			}
			var item_container = $.create("label");
			item_container.addClass("group");
			var item_checkbox = $.create("input");
			item_checkbox.attr("type", "checkbox");
			item_checkbox.data("id", country.id);
			item_container.append(item_checkbox);
			var item_text = $.create("span");
			item_text.text(country.name);
			item_container.append(item_text);
			countries_content_tab.append(item_container);
			country.checkbox = item_checkbox;
		}
		
	};
	
	client.list_filter("beats", render_list_beats);
	client.list_filter("media_types", render_list_media_types);
	client.list_filter("roles", render_list_roles);
	client.list_filter("countries", render_list_countries);
	client.list_filter("coverages", render_list_coverages);
	
	mdob.render_list_media_types = render_list_media_types;
	mdob.render_list_roles = render_list_roles;
	mdob.render_list_countries = render_list_countries;
	mdob.render_list_coverages = render_list_coverages;
	mdob.render_list_regions = render_list_regions;
	mdob.render_list_localities = render_list_localities;
	
	var clear_filters_list = function() {
		filters_list_results.empty();
		filters_list.addClass("hidden");
	};
	
	var add_filter_to_list = function(name, label, value, callback) {
		filters_list.removeClass("hidden");
		var e_filter = $.create("span");
		var e_label = $.create("span");
		var e_value = $.create("span");
		var e_remove = $.create("span");
		e_filter.addClass(name);
		e_filter.addClass("filter");
		e_label.addClass("f-label");
		e_value.addClass("f-value");
		e_remove.addClass("f-remove");
		e_filter.append(e_label);
		e_filter.append(e_value);
		e_filter.append(e_remove);
		e_label.text(label.toLowerCase());
		e_value.text(value);
		filters_list_results.append(e_filter);
		e_remove.on("click", function() {
			callback(e_filter);
			e_filter.remove();
			if (!filters_list_results.children().size())
				clear_filters_list();
		});
	};
	
	var add_filter = function(name, id, value) {
		client.add_filter(name, id);
		var label = filter_list_labels[name];
		add_filter_to_list(name, label, value, function() {

			if (filter_list_index[name] !== undefined && 
				 filter_list_index[name][id] !== undefined) {
				var item = filter_list_index[name][id];
				item.checkbox.prop("checked", false);
			}
			
			client.remove_filter(name, id);
			search_update();
			
		});
	};
	
	var extract_label_text = function(_this) {
		var item_text = _this.next("span").clone();
		item_text.children().remove();
		return item_text.text();
	};
	
	add_filter_set_button.on("click", function() {
		
		clear_filters_list();
		client.reset();
		
		countries_content_tab.find("input:checked:enabled").each(function() {
			var _this = $(this);
			var value = extract_label_text(_this);
			var id = _this.data("id");
			add_filter("countries", id, value); 
		});
		
		regions_content_tab.find("input:checked:enabled").each(function() {
			var _this = $(this);
			var value = extract_label_text(_this);
			var id = _this.data("id");
			add_filter("regions", id, value); 
		});
		
		localities_content_tab.find("input:checked:enabled").each(function() {
			var _this = $(this);
			var value = extract_label_text(_this);
			var id = _this.data("id");
			add_filter("localities", id, value); 
		});
			
		beats_content_tab.find("input:checked:enabled").each(function() {
			var _this = $(this);
			var value = extract_label_text(_this);
			var id = _this.data("id");
			if (_this.parent().hasClass("group")) {
				var group = filter_list_index.beats.groups[id];
				for (var j = 0; j < group.beats.length; j++) 
					client.add_filter("beats", group.beats[j].id);
				var label = filter_list_labels["beats"];
				add_filter_to_list("beats", label, value, function() {
					group.checkbox.prop("checked", false);
					group.checkbox.trigger("change");
					client.remove_filter("beats", id);
					for (var j = 0; j < group.beats.length; j++) 
						client.remove_filter("beats", group.beats[j].id);
					search_update();
				});
			} else {
				add_filter("beats", id, value);
			}
		});
			
		media_types_content_tab.find("input:checked:enabled").each(function() {
			var _this = $(this);
			var value = extract_label_text(_this);
			var id = _this.data("id");
			add_filter("media_types", id, value);
		});
		
		roles_content_tab.find("input:checked:enabled").each(function() {
			var _this = $(this);
			var value = extract_label_text(_this);
			var id = _this.data("id");
			add_filter("roles", id, value); 
		});
			
		coverages_content_tab.find("input:checked:enabled").each(function() {
			var _this = $(this);
			var value = extract_label_text(_this);
			var id = _this.data("id");
			add_filter("coverages", id, value); 
		});		

		search_execute();
		add_filter_modal.modal("hide");
		
	});
	
	clear_search_button.on("click", function() {

		search_box.val("");
		search_execute();
		
	});

	clear_filter_button.on("click", function() {
		
		countries_content_tab.find("input:checked").prop("checked", false);
		regions_content_tab.find("input:checked").prop("checked", false);
		beats_content_tab.find("input:checked").prop("checked", false);
		media_types_content_tab.find("input:checked").prop("checked", false);
		roles_content_tab.find("input:checked").prop("checked", false);
		localities_content_tab.find("input:checked").prop("checked", false);
		
		clear_filters_list();
		client.reset();
		search_execute();
		
	});
	
	results_container.on("click", "th.sortable", function() {
		
		var _this = $(this).children("i.sorter");
		var column = _this.data("column");
		if (column == client.options.sort_column) {
			_this.toggleClass("reverse");
			client.options.sort_reverse = 
				!client.options.sort_reverse
		} else {
			client.options.sort_column = column;
			client.options.sort_reverse = false;
		}
		
		search_execute();
		
	});
	
	results_container.on("change", ".has-select-all-option", function() {
		
		if ($(this).is(":checked")) return;
		var selectable_results = $("#selectable-results");
		selectable_results.removeClass("has-select-all");
		mdob.has_select_all = false;
		
	});
	
	var update_selected_count = function() {
		
		var selected = 0;		
		if (mdob.has_select_all) 
		     selected = Math.min(100000, mdob.latest_response.total);
		else selected = results_container.find("input.selectable:checked").size();
		$("#md-selected-contacts").text(selected);
		
	};
	
	results_container.on("change", "input.selectable", update_selected_count);
	mdob.update_selected_count = update_selected_count;
	
	// -------------------------------------------------------------------------------
	// this is the start of the locations specific code taken from the modal html 
	// and transferred to here so that we can have access to the scope
	// -------------------------------------------------------------------------------
	
	(function() {
			
		var locations = $("#md-aftc-locations");
		var header = $("#md-locations-header");
		var content = $("#md-locations-content");
		var tab_links = header.find("li");
		var next_links = $(".md-locations-next")
		var tab_contents = content.children("div");
		var active_tab = null;
		var active_list = null;
		var active_callback = null;
		
		var region_search = $("#md-region-search");
		var locality_search = $("#md-locality-search");
		var last_search = null;
					
		var fetch_results = function(list, data, callback) {
			
			if (!data) data = {};
			data.dependencies = {};
			data.dependencies.countries = [];
			data.dependencies.regions = [];
			data.dependencies.localities = [];
				
			countries_content_tab.find("input:checked:enabled").each(function() {
				var _this = $(this);
				var id = _this.data("id");
				data.dependencies.countries.push(id);
			});
			
			regions_content_tab.find("input:checked:enabled").each(function() {
				var _this = $(this);
				var id = _this.data("id");
				data.dependencies.regions.push(id);
			});	
			
			localities_content_tab.find("input:checked:enabled").each(function() {
				var _this = $(this);
				var id = _this.data("id");
				data.dependencies.localities.push(id);
			});
			
			mdob.client.list_filter(list, data, callback);
			
		};
		
		tab_links.on("click", function() {
			
			var _this = $(this);
			tab_links.removeClass("active");
			_this.addClass("active");
			next_links.toggleClass("disabled", 
				_this.hasClass("final"));
			tab_contents.addClass("hidden");
			locations.removeClass("loader");
			
			var index = tab_links.index(_this);
			var render_callback = _this.data("render-callback");
			
			last_search = null;
			rate_limit_reset(loc_search_execute);
			active_tab = tab_contents.eq(index);
			active_list = _this.data("list");
			active_callback = function(response) {
				// no results => on to next step
				if (!last_search && !response.results.length
				    && !next_links.hasClass("disabled")) {
					next_links.eq(0).trigger("click");
					return;
				}
				locations.removeClass("loader");
				mdob[render_callback](response);
			};
			
			add_filter_modal_content.scrollTop(0);
			active_tab.removeClass("hidden");
							
			if (!_this.data("stable")) {
				locations.addClass("loader");
				fetch_results(active_list, null, active_callback);
			}
							
		});
		
		next_links.on("click", function() {
			var active = tab_links.filter(".active");
			var next = active.next();
			if (next.size()) next.trigger("click");
		});			
		
		var loc_search_update = function(ev) {
			if (ev && ev.which == 13) 
				ev.preventDefault();
			rate_limit(loc_search_execute, 500);
			locations.addClass("loader");
		};
		
		var loc_search_execute = function() {
			var search = null;
			if (active_list == "regions") search = region_search.val();
			if (active_list == "localities") search = locality_search.val();
			if (search === last_search) {
				locations.removeClass("loader");
				return;
			}
			last_search = search;
			var data = { search: search };
			fetch_results(active_list, 
				data, active_callback);
		};
				
		region_search.on("keypress", loc_search_update);
		region_search.on("change", loc_search_update);
		locality_search.on("keypress", loc_search_update);
		locality_search.on("change", loc_search_update);			
			
	})();	
	
});