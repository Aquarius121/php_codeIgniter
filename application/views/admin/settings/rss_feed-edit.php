<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>RSS Feed</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?php if (@$vd->is_delete): ?>
<?= $ci->load->view('admin/settings/partials/rss_feed_delete_before') ?>
<?php endif ?>

<div class="row-fluid">
	<div class="span12">

		<div class="content">
			<form class="tab-content required-form" method="post" action="<?= $ci->uri->uri_string ?>">
				<input type="hidden" name="id" value="<?= @$vd->rss->id ?>" />
				<div class="row-fluid">

					<div class="span8 information-panel">
						<section class="form-section basic-information">
							<h2>Basic Information</h2>
							<ul>
								<li>
									<div class="row-fluid">
										<div class="span12 placeholder-container">
											<input class="in-text span12 required has-placeholder" type="text" 
												name="name" placeholder="Feed Name"
												data-required-name="Feed Name"
												value="<?= $vd->esc(@$vd->rss->name) ?>" />
											<strong class="placeholder">Feed Name</strong>
										</div>
									</div>
								</li>

								<li>
									<div class="row-fluid">
										<div class="span12 placeholder-container">
											<input class="in-text span12 required has-placeholder" type="text" 
												name="title" placeholder="Feed Title"
												data-required-name="Feed Title"
												value="<?= $vd->esc(@$vd->rss->title) ?>" />
											<strong class="placeholder">Feed Title</strong>
										</div>
									</div>
								</li>

								<li>
									<div class="row-fluid">
										<div class="span12 placeholder-container">
											<input class="in-text span12 has-placeholder" type="text" 
												name="inews_link_text" placeholder="Newswire Link Text"
												data-required-name="Newswire Link Text"
												value="<?= $vd->esc(@$vd->rss->inews_link_text) ?>" />
											<strong class="placeholder">Newswire Link Text</strong>
										</div>
									</div>
								</li>

								<li class="marbot-20">
									<div id="marker-buttons" class="btn-group">
										<?php foreach ($vd->markers as $marker => $label): ?>
											<button class="btn btn-small btn-marker" 
												value="((<?= $vd->esc($marker) ?>))" type="button">
												<?= $vd->esc($label) ?>
											</button>
										<?php endforeach ?>
									</div>
									<textarea class="in-text in-content span12" id="footer_text"
										name="footer_text" placeholder="Footer Text"><?= 
											$vd->esc(@$vd->rss->footer_text) 
									?></textarea>
								</li>
								<script>

								$(function() {

									window.init_editor($("#footer_text"), {
										bodyClass: 'code-editor',
										disable_spell_check: true,
										height: 300,
										toolbar: [],
									});

									$("#marker-buttons .btn-marker").on("click", function() {
										var editor = CKEDITOR.instances["footer_text"];
										var create = CKEDITOR.plugins.placeholder.createPlaceholder;
										var text = $(this).val();
										create(editor, undefined, text);
									});

								});

								</script>

								<li class="marbot-20">
									<label class="checkbox-container inline">
										<input type="checkbox" value="1" name="is_spin_footer_text" class="selectable"
											<?= value_if_test(@$vd->rss->is_spin_footer_text, 'checked')?>>
										<span class="checkbox"></span>
										Spin Footer Text
									</label>
								</li>

								<li>
									<div class="row-fluid">
										<div class="span12 placeholder-container">
											<input class="in-text span12 required has-placeholder" type="text" 
												name="item_count" placeholder="Number of Items"
												data-required-name="Number of Items"
												value="<?= $vd->esc(@$vd->rss->item_count) ?>" />
											<strong class="placeholder">Number of Items</strong>
										</div>
									</div>
								</li>

								<li class="marbot">
									<div class="row-fluid">
										<div class="span4">
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_include_contact_info"
													<?= value_if_test(@$vd->rss->is_include_contact_info, 'checked') ?> />												
												<span class="checkbox"></span>
												Include Contact Information
											</label>
										</div>
									</div>
								</li>

								<li class="marbot">
									<div class="row-fluid">
										<div class="span4">
											<label class="checkbox-container inline">
												<input type="hidden" name="is_full_text" value="1" />
												<input type="checkbox" value="1" disabled
													class="selectable" id="is_full_text" checked />												
												<span class="checkbox"></span>
												Include Entire Content (Full Text)
											</label>
										</div>
									</div>
								</li>

								<!-- <li id="num_of_chars">
									<div class="row-fluid">
										<div class="span6 placeholder-container">
											<input class="in-text span12 required has-placeholder" type="text" 
												name="min_num_of_chars" id="min_num_of_chars"
												placeholder="Minimum Number of Characters"
												data-required-name="Minimum Number of Characters"
												value="<?= $vd->esc(@$vd->rss->min_num_of_chars) ?>" />
											<strong class="placeholder">Minimum Number of Characters</strong>
										</div>

										<div class="span6 placeholder-container">
											<input class="in-text span12 required has-placeholder" type="text" 
												name="max_num_of_chars" id="max_num_of_chars" 
												placeholder="Maximum Number of Characters"
												data-required-name="Maximum Number of Characters"
												value="<?= $vd->esc(@$vd->rss->max_num_of_chars) ?>" />
											<strong class="placeholder">Maximum Number of Characters</strong>
										</div>
									</div>
								</li> -->

							</ul>
						</section>
					</div>

					<aside class="span4 aside aside-fluid">
						<div id="locked_aside">
							<div class="aside-properties">
								<section class="ap-block">
									<div class="marbot-15"></div>	
									<ul>

										<li>
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_show_inews_logo" class="selectable"
													<?= value_if_test(@$vd->rss->is_show_inews_logo, 'checked')?>>
												<span class="checkbox"></span>
												Show Newswire Logo
											</label>
										</li>

										<li>
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_all_premium" class="selectable"
													<?= value_if_test(@$vd->rss->is_all_premium, 'checked')?>>
												<span class="checkbox"></span>
												Premium Content Only
											</label>
										</li>

										<li>
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_show_publish_date" class="selectable"
													<?= value_if_test(@$vd->rss->is_show_publish_date, 'checked')?>>
												<span class="checkbox"></span>
												Show Publish Date
											</label>
										</li>

										<li>
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_show_logo" class="selectable"
													<?= value_if_test(@$vd->rss->is_show_logo, 'checked')?>>
												<span class="checkbox"></span>
												Show Logo
											</label>
										</li>

										<li>
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_show_related_images" class="selectable"
													<?= value_if_test(@$vd->rss->is_show_related_images, 'checked')?>>
												<span class="checkbox"></span>
												Show Related Images
											</label>
										</li>

										<li>
											<label class="checkbox-container inline required" id="content_label"
												data-required-name="Include PRs or Include News">
												<input type="checkbox" value="1" name="is_include_prs" 
													class="selectable" id="is_include_prs"
												<?= value_if_test(@$vd->rss->is_include_prs, 'checked')?>>
												<span class="checkbox"></span>
												Include PRs
											</label>
										</li>

										<li>
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_include_news" 
													class="selectable" id="is_include_news"
												<?= value_if_test(@$vd->rss->is_include_news, 'checked')?>>
												<span class="checkbox"></span>
												Include News
											</label>
										</li>

										<li>
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_tracking_enabled" class="selectable"
													<?= value_if_test(@$vd->rss->is_tracking_enabled, 'checked') ?>>
												<span class="checkbox"></span>
												Tracking Pixel
											</label>
										</li>

										<li class="marbot-15">
											<label class="checkbox-container inline">
												<input type="checkbox" value="1" name="is_enabled" class="selectable"
												<?= value_if_test(@$vd->rss->is_enabled, 'checked')?>
												<?= value_if_test( ! @$vd->rss->id, 'checked') ?>>
												<span class="checkbox"></span>
												Enabled
											</label>
										</li>

										<li>
											<div class="row-fluid">
												<button type="submit" name="save" value="1" id="save_button"
													class="span12 bt-orange">Save</button>
											</div>
										</li>

									</ul>
								</section>
							</div>
						</div>
					</aside>
					
					<script>

					$(function() {

						var options = { offset: { top: 20 } };
						$.lockfixed("#locked_aside", options);

						// var is_full_text_radio = $("#is_full_text");
						var is_all_categories_radio = $("#is_all_categories");

						// var is_full_text_change = function() {

						// 	if (is_full_text_radio.is(":checked")) {
						// 		$("#min_num_of_chars").removeClass("required");
						// 		$("#max_num_of_chars").removeClass("required");
						// 		$("#num_of_chars").slideUp("slow");
						// 	} else {
						// 		$("#min_num_of_chars").addClass("required");
						// 		$("#max_num_of_chars").addClass("required");
						// 		$("#num_of_chars").slideDown("slow");
						// 	}

						// };

						var is_all_categories_change = function() {

							if (is_all_categories_radio.is(":checked")) {
								$("#categories_li").slideUp("slow");
								$("#categories_li select")
									.removeClass("required");
							} else {
								$("#categories_li").slideDown("slow");
								$("#categories_li select")
									.addClass("required");
							}

						};

						// is_full_text_radio.on("change", is_full_text_change);
						is_all_categories_radio.on("change", is_all_categories_change);
						// is_full_text_change();
						
						$("#categories_li select").selectpicker({ size: 10 })
						is_all_categories_change();

						var is_include_prs_radio = $("#is_include_prs");
						var is_include_news_radio = $("#is_include_news");

						var is_content_selected = function() {
							if (is_include_prs_radio.is(":checked") || is_include_news_radio.is(":checked"))
							     $("#content_label").removeClass("required");
							else $("#content_label").addClass("required");
						};

						is_include_prs_radio.on("change", is_content_selected);
						is_include_news_radio.on("change", is_content_selected);
						is_content_selected();

					});


					</script>
					
				</div>
			</form>
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>