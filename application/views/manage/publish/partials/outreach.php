<fieldset class="form-section media-outreach section-requires-premium">
	
	<legend>
		Media Outreach
		<a data-toggle="tooltip" class="tl" href="#" 
			title="Send an email campaign out to journalists. You can customize
				the campaign and add additional media contacts after saving. ">
			<i class="icon-question-sign"></i>
		</a>	
	</legend>

	<div class="header-help-block">Send an email campaign to journalists.</div>
	<?= $ci->load->view('manage/publish/partials/requires-premium') ?>

	<div class="checkbox-container-box marbot-10 no-click-to-checkbox"
		data-required-callback="outreach-industries"
		data-required-name="Media Outreach">
		<label class="checkbox-container louder">
			<input type="checkbox" name="outreach_email_send" value="1" id="outreach-send-email"
				<?= value_if_test($vd->m_content_bundled_campaign, 'disabled') ?>
				<?= value_if_test($vd->m_content_bundled_campaign || 
					($vd->m_content && $vd->m_content->outreach_email_send), 'checked') ?> />
			<span class="checkbox"></span>
			Create an email campaign based on this press release.
		</label>
		<p class="text-muted">
			<strong class="status-black">Includes <span class="status-true">free</span> media outreach credits.</strong>
			An email campaign will be created for the press release and media
			contacts will be selected from our database based from the industries 
			chosen by you. 
		</p>
		<label class="checkbox-container inline small-checkbox">
			<input type="checkbox" name="outreach_email_country" value="<?= Model_Country::ID_UNITED_STATES ?>"
				<?= value_if_test($vd->m_content_bundled_campaign, 'disabled') ?>
				<?= value_if_test($vd->m_content && $vd->m_content->outreach_email_country 
						== Model_Country::ID_UNITED_STATES, 'checked') ?>
				id="include-contacts-us-only" />
			<span class="checkbox"></span>
			<span>Include contacts from the USA only.</span>
		</label>
		<script>
			
			defer(function() {

				var sendbox = $("#outreach-send-email");
				var include = $("#include-contacts-us-only");

				sendbox.on("change", function() {
					if (sendbox.is(":checked"))
						  include.prop("disabled", false);
					else include.prop("disabled", true);
				}).trigger("change");

			});

		</script>
	</div>

	<?php /* ?>
	
	<div id="outreach-select-industries" class="hidden">
		<!-- <label>
			<span class="ta-left">Select Outreach Industries</span>
			<span class="fl-right ta-right status-black">
				Selected: <span id="included-industries-count"><?= 
					count((array) @$vd->m_content->outreach_beats) ?></span>
			</span>
		</label> -->
		<ul class="distribution-list below-label select-industry marbot-20" id="select-industry">
			<?php foreach ($vd->outreach_industries as $group): ?>
				<li class="select-industry-container">
					<div class="select-industry-group">
						<div class="select-industry-group-expander"></div>
						<div class="select-industry-cbox">
							<label class="checkbox-container small-checkbox">
								<input class="group-cbox" type="checkbox" />
								<span class="checkbox"></span>
							</label>
						</div>
						<div class="select-industry-info">
							<span class="group-name"><?= $vd->esc($group->name) ?></span>
							<span class="group-beat-counter">
								(<span class="group-beat-counter-selected">0</span>/<?= count($group->beats) ?>)
							</span>
						</div>
					</div>
					<ul class="select-industry-group-beats">
						<?php foreach ($group->beats as $beat): ?>
						<li data-index="<?= $vd->esc($beat->id) ?>" class="select-industry-beat">
							<div class="select-industry-cbox">
								<label class="checkbox-container small-checkbox">
									<input class="beat-cbox" type="checkbox" name="outreach_email_beats[]" value="<?= $beat->id ?>"
										<?= value_if_test($vd->m_content_bundled_campaign && 
												in_array($beat->id, (array) $vd->m_content_bundled_campaign
												->raw_data()->beats), 'checked') ?> />
									<span class="checkbox"></span>
								</label>
							</div>
							<div class="select-industry-info">
								<span class="beat-name"><?= $vd->esc($beat->name) ?></span>
							</div>
						</li>
						<?php endforeach ?>
					</ul>
				</li>
			<?php endforeach ?>
		</ul>
		<p class="help-block">
			To view a list of known outlets for each industry please see our
			<a href="manage/contact/media_database" target="_blank">media database</a>. 
		</p>
	</div>

	<script>

	$(function() {

		var send_checkbox = $("#outreach-send-email");
		var send_checkbox_box = send_checkbox.parents(".checkbox-container-box");
		var outreach_industries = $("#outreach-select-industries");
		send_checkbox.on("change", function() {
			var is_checked = send_checkbox.is(":checked");
			send_checkbox_box.toggleClass("no-border-radius-bottom", is_checked);
			outreach_industries.toggleClass("hidden", !is_checked);
		});

		// set initial status 
		send_checkbox.trigger("change");
		send_checkbox_box.addClass("required-callback");

		required_js.add_callback("outreach-industries", function(value) {
			var response = { valid: false, text: "must have at least 1 selected industry" };
			response.valid = (!send_checkbox.is(":checked")) || 
				outreach_industries.find(".beat-cbox:checked").length;
			return response;
		});

		// var min_recommended = 1;
		// var max_recommended = 4;

		var select_industry = $("#select-industry");
		// var counter = $("#included-industries-count");

		var compute_stats = function() {

			var count_selected = select_industry.find("input.beat-cbox:checked").size();
			// var in_recommended = count_selected >= min_recommended
			// 	               && count_selected <= max_recommended;
			
			// counter.parent()
			// 	.toggleClass("status-true",   in_recommended)
			// 	.toggleClass("status-false", !in_recommended);
			// counter.text(count_selected);

			if (count_selected >= 1) {
				var error = send_checkbox_box.prev(".required-error");
				error.remove();
			}

		};

		select_industry.on("click", ".select-industry-group-expander", function(ev) {

			var gcontainer = $(this).parents(".select-industry-group");
			gcontainer.toggleClass("active");
			ev.preventDefault();
			return false;

		});

		select_industry.on("click", ".select-industry-group", function(ev) {

			var target = $(ev.target);
			if (!target.hasClass("checkbox") && 
				 !target.hasClass("checkbox-container") && 
				 !target.is("input")) {
				var gcbox = $(this).find("input.group-cbox");
				if (gcbox.is(":disabled")) return;
				if (gcbox.hasClass("disabled")) return;
				gcbox.prop("checked", !gcbox.is(":checked"));
				gcbox.trigger("change");
			}

		});

		select_industry.on("click", ".select-industry-beat", function(ev) {

			var target = $(ev.target);
			if (!target.hasClass("checkbox") && 
				 !target.hasClass("checkbox-container") && 
				 !target.is("input")) {
				var bcbox = $(this).find("input.beat-cbox");
				if (bcbox.is(":disabled")) return;
				if (bcbox.hasClass("disabled")) return;
				bcbox.prop("checked", !bcbox.is(":checked"));
				bcbox.trigger("change");
			}

		});

		select_industry.on("change", "input.group-cbox", function() {

			var _this = $(this);
			var checked = _this.is(":checked");
			var container = _this.parents("li.select-industry-container");
			container.toggleClass("selected", checked);

			var bcbox = container.find("input.beat-cbox");
			bcbox.prop("checked", checked);
			bcbox.parents(".select-industry-beat")
				.toggleClass("selected", checked);

			// remove partial selection
			_this.next().removeClass("partial-select");

			// update the counter for this group
			var beats_ul = container.find("ul.select-industry-group-beats");
			var gcontainer = beats_ul.prev(".select-industry-group");
			var gcount_selected = beats_ul.find("input.beat-cbox:checked").length;
			var gcounter_container = gcontainer.find(".group-beat-counter");
			gcounter_container.toggleClass("has-selected-beats", gcount_selected > 0);
			var gcounter = gcontainer.find(".group-beat-counter-selected");
			gcounter.text(gcount_selected);		
			
			// update selected stats
			compute_stats();

		});

		select_industry.on("change", "input.beat-cbox", function() {

			var _this = $(this);
			var checked = _this.is(":checked");
			var container = _this.parents(".select-industry-beat");
			container.toggleClass("selected", checked);

			// * update the parent cbox
			// * update the parent counter
			var beats_ul = container.parents("ul.select-industry-group-beats");
			var gcontainer = beats_ul.prev(".select-industry-group");
			var gcbox = gcontainer.find("input.group-cbox");
			var gcbox_checked = gcbox.is(":checked");
			var gcount_selected = beats_ul.find("input.beat-cbox:checked").length;
			var gcount_total = beats_ul.find("input.beat-cbox").length;
			var is_group_all_selected = gcount_selected >= gcount_total;
			var gcounter_container = gcontainer.find(".group-beat-counter");
			gcounter_container.toggleClass("has-selected-beats", gcount_selected > 0);
			var gcounter = gcontainer.find(".group-beat-counter-selected");
			gcounter.text(gcount_selected);

			// show partially selected if not all and at least 1
			gcbox.next().toggleClass("partial-select", 
				!is_group_all_selected && gcount_selected > 0);

			if (gcbox_checked != is_group_all_selected) {
				gcbox.parents("li.select-industry-container")
					.toggleClass("selected", is_group_all_selected);
				gcbox.prop("checked", is_group_all_selected);
			}

			// update selected stats
			compute_stats();

		});

		select_industry.find("input.beat-cbox:checked").trigger("change");
		select_industry.addClass("computed");

	});

	</script>

	<?php */ ?>

</fieldset>