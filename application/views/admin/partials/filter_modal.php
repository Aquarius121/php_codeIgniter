<ul class="add-filter-tabs admin-filter" id="admin-add-filter-tabs">
	<li class="active" data-filter="user">USER</li>
	<li data-filter="company">COMPANY</li>
	<li data-filter="site">SITE</li>
</ul>

<ul class="add-filter-tab-content admin-filter" id="admin-add-filter-tab-content">
	<li id="aaf-user" class="active clearfix"><?= $ci->load->view('admin/partials/find_user_modal') ?></li>
	<li id="aaf-company" class="clearfix"><?= $ci->load->view('admin/partials/find_company_modal') ?></li>
	<li id="aaf-site" class="clearfix"><?= $ci->load->view('admin/partials/find_site_modal') ?></li>
</ul>

<script>
	
$(function() {

	var filters = <?php 

	$filters = array();
	$context_uri = $ci->uri->uri_string;
	$search_classes = $ci->config->item('admin', 'search_bar');

	foreach ($search_classes as $k => $v)
	{
		if (preg_match($k, $context_uri))
		{
			if (isset($v['filters']) && $v['filters'])
				$filters = $v['filters'];
			break;
		}
	}

	echo json_encode($filters);

	?>;

	var aaf_user = null;
	var aaf_company = null;
	var aaf_site = null;

	var tab_bar = $("#admin-add-filter-tabs");
	var tab_content = $("#admin-add-filter-tab-content");
	var tab_buttons = tab_bar.children();
	var tab_pages = tab_content.children();

	var enable_filter = function(gstring, url) {
		if (!gstring) return;
		if (!url) url = window.location.href;
		if (url.indexOf("?") === -1)
		     url = url + "?" + gstring;
		else url = url + gstring;
		window.location = url;
	};

	tab_buttons.each(function(index) {
		var _this = $(this);
		if (filters.indexOf(_this.data("filter")) === -1) {
			var index = tab_buttons.index(this);
			tab_buttons.eq(index).remove();
			tab_pages.eq(index).remove();
			tab_buttons = tab_bar.children();
			tab_pages = tab_content.children();
		}
	});

	var cancel_button = $("#admin-add-filter-cancel-button");
	var confirm_button = $("#admin-add-filter-confirm-button");

	tab_buttons.on("click", function() {
		var index = tab_buttons.index(this);
		tab_pages.removeClass("active");
		tab_pages.eq(index).addClass("active");
		tab_buttons.removeClass("active");
		tab_buttons.eq(index).addClass("active");
		confirm_button.prop("disabled", true);
	});

	cancel_button.on("click", function() {
		cancel_button.parents(".modal").modal("hide");
	});

	confirm_button.on("click", function() {

		if (aaf_user.hasClass("active")) {
			var selected = aaf_user.find(".find-selected:checked");
			var gstring = selected.data("gstring");
			// remove any existing filter of this type
			var url = window.location.href;
			url = url.replace(/&?filter_user=[\-\d]+/gi, "");
			url = url.replace(/&?filter_company=[\-\d]+/gi, "");
			enable_filter(gstring, url);
			return;
		}

		if (aaf_company.hasClass("active")) {
			var selected = aaf_company.find(".find-selected:checked");
			var gstring = selected.data("gstring");
			// remove any existing filter of this type
			var url = window.location.href;
			url = url.replace(/&?filter_user=[\-\d]+/gi, "");
			url = url.replace(/&?filter_company=[\-\d]+/gi, "");
			enable_filter(gstring, url);
			return;
		}

		if (aaf_site.hasClass("active")) {
			var selected = aaf_site.find(".find-selected:checked");
			var gstring = selected.data("gstring");
			// remove any existing filter of this type
			var url = window.location.href;
			url = url.replace(/&?filter_site=[\-\d]+/gi, "");
			enable_filter(gstring, url);
			return;
		}

	});

	tab_buttons.eq(0).trigger("click");
	aaf_company = $("#aaf-company");
	aaf_site = $("#aaf-site");
	aaf_user = $("#aaf-user");

});

</script>