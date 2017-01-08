<?= $ci->load->view('manage/publish/partials/bad-words-filter') ?>

<fieldset class="form-section basic-information">
	<legend class="marbot-10">
		Fill in Your Press Release Details
		<a data-toggle="tooltip" class="tl" href="#" 
			title="<?= Help::PR_BASIC ?>">
			<i class="fa fa-fw fa-question-circle"></i>
		</a>
	</legend>
	
	<div class="row form-group">
		<div class="col-lg-7 pr-location-information">
			<input class="form-control in-text col-lg-12 required required-callback" type="text" name="location" 
				id="location" placeholder="Location" maxlength="64"
				data-required-name="PR Location" data-required-callback="location-format"
				value="<?= value_if_test(isset($vd->m_content), 
					$vd->esc(@$vd->m_content->location),
					$vd->esc(@$vd->default_location)) ?>" />
			<script>

			$(function() {
				
				required_js.add_callback("location-format", function(value) {
					var response = { valid: false, text: "must be in the format shown below" };
					response.valid = /^[^,]*[^\s,],\s*[^\s,][^,]*$/.test(value);
					return response;
				});
				
			});
			
			</script>
			<p class="help-block smaller">
				Location: <span class="status-black">CITY, STATE</span> or 
				<span class="status-black">CITY, COUNTRY</span> only.
			</p>
		</div>
		<div class="col-lg-5 pr-date-information">
			<input type="hidden" name="is_publish_date_selected" value="<?= $vd->m_content ? 
				$vd->m_content->is_publish_date_selected : 0 ?>" id="publish-date-selected" />
			<?= $this->load->view('manage/publish/partials/inline-publish-date') ?>			
			<script>
				
			$(function() {

				var publish_date = $("#publish-date");
				var selected = $("#publish-date-selected");
				publish_date.on("change", function() {
					selected.val(1);
				});

				if (parseInt(selected.val()) == 0)
					publish_date.val("");

			});

			</script>
		</div>
	</div>
		
	<div class="row form-group">
		<div class="col-lg-12">
			<input class="form-control in-text col-lg-12 required required-callback" type="text" name="title" 
				id="title" placeholder="Enter Title (Headline) of Press Release" 
				maxlength="<?= $ci->conf('title_max_length') ?>" data-name="Title"
				value="<?= $vd->esc(@$vd->m_content->title) ?>" data-required-name="Title"
				data-required-callback="title-min-words bad-words-filter title-case-conversion" />
			<p class="help-block ta-right">
				<span class="pull-left" id="title-case-conversion">
					Automatic Title Case: 
					<span class="status-true enabled pointer">Enabled</span><span 
						class="status-false disabled hidden pointer">Disabled</span>.
				</span>
				<span id="title-length-text">
					<span id="title_words"></span> Words, 
					<span id="title_characters"></span> Characters (65 to 110 Recommended)
				</span>
			</p>

			<?php 

				$loader = new Assets\JS_Loader(
					$ci->conf('assets_base'), 
					$ci->conf('assets_base_dir'));
				$loader->add('lib/to_title_case.js');
				$render_basic = $ci->is_development();
				echo $loader->render($render_basic);

			?>

			<script>

			$(function() {

				var is_case_conversion_enabled = true;
				var case_conversion = $("#title-case-conversion");
				var case_conversion_enabled = case_conversion.children(".enabled");
				var case_conversion_disabled = case_conversion.children(".disabled");

				var title = $("#title");
				var title_characters = $("#title_characters");
				var title_words = $("#title_words");
				var title_length_text = $("#title-length-text");

				// the word regex used for counting words
				var word_count = window.word_count_regex;
				
				var render = function() {
					var value = title.val();
					var length = value.length;
					title_characters.text(length);
					var match = value.match(word_count);
					var count = match ? match.length : 0;
					title_words.text(count);
				};
				
				title.on("keyup", render);
				title.on("change", render);
				render();
				
				title.on("change", function() {
					if (is_case_conversion_enabled) 
						title.val(title.val().toTitleCase());
				});

				case_conversion_enabled.on("click", function() {
					case_conversion_enabled.addClass("hidden");
					case_conversion_disabled.removeClass("hidden");
					is_case_conversion_enabled = false;
				});

				case_conversion_disabled.on("click", function() {
					case_conversion_disabled.addClass("hidden");
					case_conversion_enabled.removeClass("hidden");
					is_case_conversion_enabled = true;
				});

				required_js.add_callback("title-min-words", function(value) {
					var response = { valid: false, text: "must have at least 4 words" };
					response.valid = /([a-z0-9]\S*(\s+[^a-z0-9]*|$)){4,}/i.test(value);
					return response;
				});

				required_js.add_callback("title-case-conversion", function(value) {
					if (is_case_conversion_enabled)
						title.val(title.val().toTitleCase());
					return { valid: true, text: null };
				});

				title.limit_length(<?= $ci->conf('title_max_length') ?>, title_length_text);
				
			});
			
			</script>
		</div>
	</div>
	<?php if (Auth::is_admin_online()): ?>
	<div class="row form-group">
		<div class="col-lg-12">
			<input class="form-control in-text col-lg-12" type="text" name="slug" 
				id="slug" placeholder="customize-the-slug-of-the-press-release" 
				maxlength="<?= Model_Content::MAX_SLUG_LENGTH ?>"
				value="<?= ($vd->m_content ? $vd->esc($vd->m_content->slug) : null) ?>" />
			<p class="help-block">This can cause some stats (facebook, twitter, etc) to reset.</p>
		</div>
	</div>
	<?php endif ?>
	
	<div class="row form-group">
		<div class="col-lg-12">
			<textarea class="form-control in-text col-lg-12 required-callback" id="summary" name="summary"
				rows="5" data-required-name="Subheadline" placeholder="Subheadline of Press Release (Optional)"
				data-required-callback="bad-words-filter" data-name="Subheadline"
				><?= $vd->esc(@$vd->m_content->summary) ?></textarea>
			<p class="help-block clearfix">
				<span class="pull-right" id="summary-words-text">
					<span id="summary-words">0</span> Words, 
					<span id="summary-characters">0</span> Characters (Limit 
						<span id="summary-max-length"><?= $ci->conf('summary_max_length') ?></span>)
				</span>
			</p>
			<script>
			
			$(function() {
				
				// the word regex used for counting words
				var word_count = window.word_count_regex;
				var summary = $("#summary");
				var summary_words = $("#summary-words");
				var summary_chars = $("#summary-characters");
				var summary_text = $("#summary-words-text");
				var summary_max_length = $("#summary-max-length");

				var show_word_count = function() {
					var text = summary.val();
					var match = text.match(word_count);
					var count = match ? match.length : 0;
					summary_words.text(count);
					summary_chars.text(text.length);
				};

				summary.on("change", show_word_count);
				summary.on("keyup", function(ev) {
					window.rate_limit(show_word_count);
				});

				var default_max_length = <?= (int) $ci->conf('summary_max_length') ?>;
				var prn_max_length = <?= (int) $ci->conf('prn_subheadline_max_length') ?>;
				
				summary.limit_length(default_max_length, summary_text);
				show_word_count();

				window.on_distribution_bundle_change.push(function(bundle) {
					if (bundle && bundle.data.includesPrnewswire) {
						summary.limit_length(prn_max_length, summary_text);
						summary_max_length.text(prn_max_length);
					} else {
						summary.limit_length(default_max_length, summary_text);
						summary_max_length.text(default_max_length);
					}
				});

			});
			
			</script>
		</div>
	</div>

	<?= $this->view('manage/publish/partials/pr-content') ?>
	
</fieldset>