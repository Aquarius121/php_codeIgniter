<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<li><a data-on="^admin/nr_builder/<?= $vd->nr_source ?>/all" 
				href="admin/nr_builder/<?= $vd->nr_source ?>/all">NR Builder</a></li>
			<li><a data-on="^admin/nr_builder/<?= $vd->nr_source ?>/(auto_built_newsrooms|auto_built_nrs)"
				href="admin/nr_builder/<?= $vd->nr_source ?>/auto_built_nrs_not_exported">
					Auto Built NRs
					<span class="ab-not-exported-counter-48-hrs">
						<img src="assets/im/loader-line.gif">
					</span>
				</a></li>
			<li><a data-on="^admin/nr_builder/<?= $vd->nr_source ?>/paid_claims"
				href="admin/nr_builder/<?= $vd->nr_source ?>/paid_claims">
					Paid
				</a></li>

			<li><a data-on="^admin/nr_builder/<?= $vd->nr_source ?>/claim_submissions"
				href="admin/nr_builder/<?= $vd->nr_source ?>/claim_submissions_from_private_link">
					Claim Submissions
					<span class="claim-counter-48-hrs">
						<img src="assets/im/loader-line.gif">
					</span>
				</a></li>
			<li><a data-on="^admin/nr_builder/<?= $vd->nr_source ?>/verified_submissions"
				href="admin/nr_builder/<?= $vd->nr_source ?>/verified_submissions_not_exported">
					Verified
					<span class="verified-counter-48-hrs">
						<img src="assets/im/loader-line.gif">
					</span>
				</a></li>
		</ul>
	</div>
</div>


<div id="hover-container" class="hidden">
	<div id="hover-content" class="pad-20"></div>
</div>

<script>
$(function(){
	var results = $("#selectable-form");
	var hover_visible = false;
	var hover_container = $("#hover-container");
	var hover_content = $("#hover-content");
	
	var hover_hide = function() {
		hover_container.addClass("hidden");
	};
	
	results.on("mouseenter", "span.48-hrs-counter", function() {
		var _this = $(this);
		var offset = _this.offset();
		var _this_height = _this.outerHeight();
		hover_content.html(_this.data("title"));
		hover_container.removeClass("hidden");
		var height = hover_content.outerHeight();
		var scrollTop = $(window).scrollTop();
		var top = (offset.top - scrollTop);
		top += _this_height / 2;
		top -= height / 2;
		var left = (offset.left + 20);
		if (top < 20) top = 20;
		var window_height = $(window).height();
		if (top + height > window_height - 20) 
			top = window_height - height - 20;
		if (top < 20) top = 20;
		hover_container.css("top", top);
		hover_container.css("left", left);
	});
	
	results.on("mouseleave", "span.48-hrs-counter", function() {
		hover_hide();
	});
	
	window.__modifier_callbacks.push(function() {
		hover_hide();
	});
	

	$.get( "admin/nr_builder/<?= $vd->nr_source ?>/warning_counters", function( data ) {

		var ab_not_exported_counter_48_hrs = $(".ab-not-exported-counter-48-hrs");
		var claim_counter_48_hrs = $(".claim-counter-48-hrs");
		var verified_counter_48_hrs = $(".verified-counter-48-hrs");

		ab_not_exported_counter_48_hrs.html('');
		claim_counter_48_hrs.html('');
		verified_counter_48_hrs.html('');

		if (data.ab_not_exported_counter_48_hrs > 0)
		{
			var data_title_not_exp = data.ab_not_exported_counter_48_hrs + " record(s) 'not yet exported' ";
			data_title_not_exp += "for more than 48 working hours";
			var counter_span_not_exp = $.create("span");
			counter_span_not_exp.addClass("menu-count-warning");
			counter_span_not_exp.addClass("48-hrs-counter");
			counter_span_not_exp.attr("data-title", data_title_not_exp);
			counter_span_not_exp.html(data.ab_not_exported_counter_48_hrs);
			ab_not_exported_counter_48_hrs.append(counter_span_not_exp);
		}

		if (data.claim_counter_48_hrs > 0)
		{			
			var data_title_claim = data.claim_counter_48_hrs + " claim(s) pending for more than 48 working hours";
			var counter_span_claim = $.create("span");
			counter_span_claim.addClass("menu-count-warning");
			counter_span_claim.addClass("48-hrs-counter");
			counter_span_claim.attr("data-title", data_title_claim);
			counter_span_claim.html(data.claim_counter_48_hrs);
			claim_counter_48_hrs.append(counter_span_claim);
		}

		if (data.verified_counter_48_hrs > 0)
		{
			var data_title_verified = data.verified_counter_48_hrs + " verified claim(s) 'not yet exported' ";
			data_title_verified += "for more than 48 working hours";
			var counter_span_verified = $.create("span");
			counter_span_verified.addClass("menu-count-warning");
			counter_span_verified.addClass("48-hrs-counter");
			counter_span_verified.attr("data-title", data_title_verified);
			counter_span_verified.html(data.verified_counter_48_hrs);
			verified_counter_48_hrs.append(counter_span_verified);
		}
	});
})
</script>