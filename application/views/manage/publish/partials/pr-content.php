<div class="row form-group no-mb-unless-last">
	<div class="col-lg-12 cke-container editor-GUI" id="content-container">
		<?php if (Auth::is_admin_online()): ?>
		<div class="relative clearfix">
			<div class="rocker" id="editor-rocker">
				<input type="checkbox" id="editor-rocker-GUI" class="default" data-label="GUI Editor" />
				<input type="checkbox" id="editor-rocker-HTML" data-label="HTML Editor" />
			</div>
		</div>
		<?php endif ?>
		<?php $content_req_callback = ''; ?>
		<textarea class="form-control in-text in-content col-lg-12 
			<?= value_if_test(!Auth::user()->raw_data_object()->disable_pr_body_validation, 'required-callback') ?>" 
			data-required-name="Content Body" id="content" name="content" placeholder="Press Release Body"
			data-required-callback="content-min-words content-max-chars content-bad-words-filter
				content-max-links-free content-max-links-premium">
			<?= $ci->load->view('partials/html-content', array('content' => @$vd->m_content->content)) ?>
		</textarea>
		<script>
		
		$(function() {
			
			var date = $("#publish-date");
			var location = $("#location");
			var summary = $("#summary");
			var title = $("#title");
			var content = $("#content");
			var content_container = $("#content-container");

			<?php if (Auth::is_admin_online()): ?>
			<?php // ------------------------------------ ?>
			<?php // ------------------------------------ ?>

			var originalHTML = content.val();
			var editorObject = null;

			var EDITOR_GUI = "GUI";
			var EDITOR_HTML = "HTML";
			
			var rocker_GUI = $("#editor-rocker-GUI");
			var rocker_HTML = $("#editor-rocker-HTML");
			var rocker = $("#editor-rocker");
			var activeEditor = EDITOR_GUI;

			rocker_GUI.on("select", function() {				
				if (!rocker_GUI.is(":checked")) return;
				if (activeEditor == EDITOR_GUI) return;
				activeEditor = EDITOR_GUI;
				destroy_editor_HTML();
				content_container.removeClass("editor-HTML");
				content_container.addClass("editor-GUI");				
				pure(content.val(), function(value) {
					content.val(value);
					init_editor_GUI();
				});
			});

			rocker_HTML.on("select", function() {
				if (!rocker_HTML.is(":checked")) return;
				if (activeEditor == EDITOR_HTML) return;
				activeEditor = EDITOR_HTML;
				destroy_editor_GUI();
				content_container.removeClass("editor-GUI");
				content_container.addClass("editor-HTML");
				pure(content.val(), function(value) {
					
					content.val(value);
					init_editor_HTML();

					bootbox.confirm({					
						message: "Please select the HTML source.",						
						buttons: {
							cancel: { label: "GUI Editor", className: "btn-primary" },
							confirm: { label: "Original", className: "btn-default" }
						},
						callback: function (result) {
							if (!result) return;
							content.val(originalHTML);
							editorObject.setValue(originalHTML, -1);
						}
					});

				});				
			});

			window.required_js.on_before_submit.push(function() {
				if (activeEditor == EDITOR_HTML)
					content.val(editorObject.getValue());
			});

			<?php // ------------------------------------ ?>
			<?php // ------------------------------------ ?>
			<?php endif ?>

			var pure = function(value, callback) {

				$.post("manage/publish/common/pure", { value: value }, function(res) {
					return callback(res.value);
				});

			};

			var init_editor_GUI = function() {

				window.init_editor(content, { height: 500, fillEmptyBlocks: false }, function() {
			
					var _this = this;		
					var content_word_text = $("#content_word_text");
					var content_word_count = $("#content_word_count");
					var date_line_placeholder = null;
					var document_element = null;
					var additional_words_notified = false;

					var show_word_count = function() {
						var text = convert_to_text_format(_this.getData());
						var match = text.match(word_count);
						var count = match ? match.length : 0;
						content_word_text.toggleClass("status-true", count >= min_word_count);
						content_word_count.text(count);
					};

					var check_for_additional_words = function(test) {

						if (additional_words_notified) return;
						
						var _title = title.val();
						var _summary = summary.val();
						var _body = convert_to_text_format(_this.getData());
						var considered = [_title, _summary, _body];
						considered = considered.join(" ");
						var match = considered.match(word_count);
						words = match ? match.length : 0;

						var bundle = window.distribution_bundle();
						if (bundle && bundle.data && bundle.data.prnewswireWords
							&& bundle.data.prnewswireWordsNotification) {
							var included = bundle.data.prnewswireWords;
							if (words > included) {
								additional_words_notified = true;
								if (!test) {
									var message = bundle.data.prnewswireWordsNotification;
									bootbox.alert(message);
								}
							}
						}

					};

					_this.on("contentDom", function() {
						check_for_additional_words(true);
						_this.document.on("keyup", function(ev) {
							window.rate_limit(show_word_count, 500);
							window.rate_limit(check_for_additional_words, 2500);
						});
					});

					var update_pr_date_line = function() {

						var location_str = location.val();
						var date_val = date.val() || undefined;
						var date_str = moment(date_val).format("LL");
						var format = null;

						if (location_str)
						     format = "{{location}}, {{date}} (Newswire) -";
						else format = "{{date}} (Newswire) -";

						var text = format.format({
							location: location_str, 
							date: date_str
						});

						// attempt to re-create the date line if required
						if (!date_line_placeholder || !document_element.find(date_line_placeholder).length)
							create_pr_date_line(false);

						// update the date line text
						if (date_line_placeholder)
							date_line_placeholder.text(text);

					};

					// trigger update PR line when elements change
					location.on("change", update_pr_date_line);
					date.on("change", update_pr_date_line);

					var ck_html_processor = function() {

						if (!document_element) return;
						if (!date_line_placeholder) return;
						date_line_placeholder.detach();

						// remove any empty paragraphs
						document_element.find("p").each(function() {
							var paragraph = $(this);
							var html = paragraph.html();
							if (/^(&nbsp;|\s|<\/?br\s*\/?>)*$/i.test(html))
								paragraph.remove();
						});

						// locate or create the first paragraph
						var paragraphs = document_element.find("p");
						var paragraph = paragraphs.eq(0);
						if (!paragraph.length) {
							paragraph = $.create("p");
							paragraph.html("&#8203;");
							document_element.find("body")
								.prepend(paragraph);
						}

						// insert date line into the first paragraph
						paragraph.prepend(date_line_placeholder);

					};

					var create_pr_date_line = function(update) {

						// create the placeholder element
						var create_placeholder = CKEDITOR.plugins.placeholder.createPlaceholder;
						date_line_placeholder = $(create_placeholder(_this, undefined, "").$);
						date_line_placeholder.css("margin-right", "5px");
						date_line_placeholder.addClass("remove-on-save");
						date_line_placeholder.addClass("readonly");
						document_element = date_line_placeholder.parents("html");
						ck_html_processor();
						if (update) update_pr_date_line();

					};

					_this.on("blur", ck_html_processor);
					_this.on("blur", check_for_additional_words);
					_this.on("instanceReady", function() {
						
						setTimeout(function() { 
							var $window = $(window);
							var top = $window.scrollTop();
							create_pr_date_line(true);
							$window.scrollTop(top);
						}, 0);						

						var link_button = $("#content-container .cke_button__link");
						link_button_handler = link_button[0].onclick;
						link_button.removeAttr("onclick");
						link_button.on("click", function(ev) {
							// max number of links is 3 for premium, 0 otherwise
							var is_premium = window.is_premium_enabled();
							var max = is_premium ? <?= 
								$ci->conf('press_release_links_premium') ?> : <?= 
								$ci->conf('press_release_links_basic') ?>;
							var value = _this.getData();
							var a_links = value.match(/(<a[^>]*>)/gi);
							var count = a_links ? a_links.length : 0;
							if (count < max) return link_button_handler.call(this);
							// show an alert about reaching limit
							bootbox.alert("You are limited to <strong>" + 
								max + "<\/strong> embedded links with a " + 
								(is_premium ? "premium" : "basic") +
								" press release.");
						});

					});
					
					show_word_count();
					
				});

			};

			var destroy_editor_GUI = function() {

				if (!window.CKEDITOR.instances.content) return;
				window.CKEDITOR.instances.content.updateElement();
				window.CKEDITOR.instances.content.destroy();

			};

			var init_editor_HTML = function() {

				var relative = $.create("div").addClass("html-editor-container");
				var absolute = $.create("pre").addClass("html-editor");
				relative.append(absolute);
				content.after(relative);

				ace.config.set("basePath", <?= json_encode('assets/lib/ace-editor') ?>);
				
				editorObject = ace.edit(absolute[0]);
				editorObject.setTheme("ace/theme/chrome");
				editorObject.getSession().setMode("ace/mode/html");
				editorObject.getSession().setUseWrapMode(true);
				editorObject.setHighlightActiveLine(true);
				editorObject.setValue(html_beautify(content.val()), -1);
				editorObject.container = relative;

			};

			var destroy_editor_HTML = function() {

				content.val(editorObject.getValue());
				editorObject.destroy();
				editorObject.container.remove();
				
			};

			var min_word_count = <?= (int) $ci->conf('press_release_min_words') ?>;
			var convert_to_text_format = function(value) {
				value = value.replace(/<[^>]*>/g, " ");
				value = value.replace(/&nbsp;/g, " ");
				return value;
			};
			
			// the word regex used for counting words
			var word_count = window.word_count_regex;
			
			required_js.add_callback("content-min-words", function(value) {
				value = convert_to_text_format(value);
				var response = { valid: false, text: "must have at least <?= 
					$ci->conf('press_release_min_words') ?> words" };
				var match = value.match(word_count);
				var count = match ? match.length : 0;
				response.valid = count >= min_word_count;
				return response;
			});
			
			required_js.add_callback("content-max-chars", function(value) {
				value = convert_to_text_format(value);
				var response = { valid: false, text: "must not exceed <?= 
					$ci->conf('press_release_max_length') ?> characters" };
				response.valid = value.length <= <?= 
					$ci->conf('press_release_max_length') ?>;
				return response;
			});

			required_js.add_callback("content-bad-words", function(value) {
				return check_bad_words(value);
			});
			
			required_js.add_callback("content-max-links-free", function(value) {
				if (window.is_premium_enabled()) return { valid: true };
				var response = { valid: false, text: "can have at most <?= 
					$ci->conf('press_release_links_basic') ?> external links" };
				var a_links = value.match(/(<a[^>]*>)/gi);
				response.valid = !a_links || a_links.length <= <?= 
					$ci->conf('press_release_links_basic') ?>;
				return response;
			});
			
			required_js.add_callback("content-max-links-premium", function(value) {
				if (!window.is_premium_enabled()) return { valid: true };
				var response = { valid: false, text: "can have at most <?= 
					$ci->conf('press_release_links_premium') ?> external links" };
				var a_links = value.match(/(<a[^>]*>)/gi);
				response.valid = !a_links || a_links.length <= <?= 
					$ci->conf('press_release_links_premium') ?>;
				return response;
			});

			required_js.add_callback("content-bad-words-filter", function(value) {
				value = convert_to_text_format(value);
				return window.required_js_bad_words_filter(value);
			});

			init_editor_GUI();
			
		});
		
		</script>
		<p class="help-block ta-right nomarbot content-word-count" id="content_word_text">
			<span id="content_word_count">0</span> Words (<?= 
				$ci->conf('press_release_min_words') ?> Minimum Words Required)</p>
	</div>
</div>
<?php if (Auth::is_admin_online()): ?>
<div class="row">
	<div class="col-lg-12">
		<label class="checkbox-container">
			<input type="checkbox" value="1" name="is_nofollow_enabled"
				<?= value_if($vd->m_content && $vd->m_content->is_nofollow_enabled, 'checked') ?>>
			<span class="checkbox"></span>
			Enable <code>nofollow</code> attribute on links.
		</label>
	</div>		
</div>
<div class="row">
	<div class="col-lg-12">
		<label class="checkbox-container">
			<input type="checkbox" name="preserve_original_content"
				id="preserve-original" value="1" />
			<span class="checkbox"></span>
			Preserve original content &nbsp;
			<span class="smaller status-info-muted">(discards changes)</span>
		</label>
	</div>		
</div>
<?php endif ?>