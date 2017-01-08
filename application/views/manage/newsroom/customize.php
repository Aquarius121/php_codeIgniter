<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6">
				<h2>Design / Customization</h2>
			</div>
		</div>
	</header>
	

	<form class="tab-content required-form" method="post" action="manage/newsroom/customize/save">

	<div class="row">
		<div class="col-lg-8 col-md-7 form-col-1">
			<div class="panel panel-default">
				<div class="panel-body">			
						
					<?php if (!$vd->custom): ?>
					<fieldset class="pad-15v">
						<div class="alert alert-success with-btn-left nomarbot">
							Want to use the default customization? Just press the button.
							<span class="pull-left">
								<a class="btn btn-xs btn-primary" 
									href="manage/newsroom/customize/defaults">
									Use Defaults</a>
							</span>
						</div>
					</fieldset>
					<?php endif ?>

					<fieldset class="newsroom-name" id="newsroom-url">
						<legend>Newsroom Name</legend>
						<div class="row form-group">
							<div class="col-lg-12">

								<div class="input-group in-text-add-on">
									<input class="form-control in-text has-loader" type="text" id="newsroom-name"
										name="name" placeholder="Newsroom Name"
										value="<?= $vd->esc($ci->newsroom->name) ?>" />
									<span class="input-group-addon"><?= $ci->conf('host_suffix') ?></span>
								</div>

								<p class="help-block">
									This will change the newsroom URL. 
									The existing URL will no longer function. 
									<?php if (Auth::is_admin_online() && !$ci->newsroom->active): ?>
										<?php if (@$vd->m_pr_token): ?>
											<a target="_blank"
												href='<?= $ci->newsroom->url() ?>?preview=<?= $vd->m_pr_token->access_token ?>'>
												Private Preview Link.
											</a>
										<?php else: ?>
											<a href="manage/newsroom/customize/generate_private_preview/<?= 
												$ci->newsroom->company_id?>">
												Generate Private Preview Link.
											</a>
										<?php endif ?>
									<?php endif ?>
								</p>

							</div>
						</div>

						<?php if (Auth::is_admin_online()): ?>
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12" type="text" 
									placeholder="Newsroom Domain" name="newsroom_domain"
									value="<?= $vd->esc($ci->newsroom->domain) ?>" />
								<p class="help-block">
									This will set a custom domain for accessing the newsroom. 
									The DNS for the domain must be set such that an the host
									has a CNAME alias for <?= $ci->conf('common_host') ?>.
								</p>
							</div>
						</div>
						<?php else: ?>
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12" type="text" 
									disabled placeholder="Newsroom Domain"
									value="<?= $vd->esc($ci->newsroom->domain) ?>" />
								<p class="help-block">
									Please contact us if you would like to use your own domain. 
								</p>
							</div>
						</div>
						<?php endif ?>	
					</fieldset>
						
					<script>

					$(function() {
						
						var test_field = $("#newsroom-name");
						var current_value = null;	
						
						var perform_test_render = function(res) {
							
							test_field.removeClass("loader");
							test_field.toggleClass("success", res.available);
							test_field.toggleClass("error", !res.available);
							
						};
						
						var perform_test = function(value) {
							
							var post_data = {};
							post_data.name = value;
							test_field.addClass("loader");
							test_field.removeClass("success");
							test_field.removeClass("error");
							$.post("manage/newsroom/customize/name_test", 
								post_data, perform_test_render);
							
						};
						
						var schedule_test_check = function() {
							
							var value = test_field.val();
							if (current_value == value) return;
							perform_test(value);
							current_value = value;
							
						};
						
						var schedule_test = function() {
							
							var value = test_field.val();
							if (current_value != value) {
								test_field.removeClass("success");
								test_field.removeClass("error");	
							}
							
							setTimeout(schedule_test_check, 250);
							
						};
						
						test_field.on("keypress", schedule_test);
						test_field.on("change", schedule_test);
						
					});
						
					</script>					

					<fieldset class="company-logo">
						<legend>Company Logo</legend>
						<input type="hidden" id="logo-image-id" name="logo_image_id" 
							value="<?= @$vd->custom->logo_image_id ?>" />
						<div class="row form-group">
							<div class="col-lg-4 image-upload-left image-container nopad scaled marbot-5">
								<?php if ($vd->custom && $vd->custom->logo_image_id && 
											($lo_im = Model_Image::find($vd->custom->logo_image_id))): ?>
								<?php $lo_variant = $lo_im->variant('header-thumb'); ?>
								<?php $lo_url = Stored_Image::url_from_filename($lo_variant->filename); ?>
								<img id="logo-image-thumb" src="<?= $lo_url ?>" />
								<?php else: ?>
								<img id="logo-image-thumb" class="loader blank" />
								<?php endif ?>
							</div>
							<div class="col-lg-8">
								<div class="row">
									<div class="col-lg-12">
										<div class="row no-overflow marbot-5" id="logo-image-upload">
											<div class="col-lg-9 col-md-9 col-sm-9 col-xs-8 file-upload-faker">
												<div class="fake row input-group nomar-left">
													<div class="text-input">
														<input type="text" placeholder="Select Image" class="form-control in-text col-lg-12 fake-text" />
													</div>
													<div class="input-group-btn">
														<button class="btn btn-primary fake-button" type="button">Browse</button>
													</div>
												</div>
												<div class="real row">
													<input class="form-control in-text col-lg-12 real-file required-no-submit" type="file" name="image" />
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 nopad-left">
												<button type="button" class="file-upload-faker-button btn btn-default col-lg-12 remove-button">
													Remove
												</button>
											</div>
										</div>
										<p class="help-block">
											<?php $v_header = $ci->conf('v_sizes', 'header') ?>
											We recommend a <span class="darker">minimum size of 
											<?= $v_header->width ?>x<?= $v_header->height ?></span>.
											The image will be resized automatically. The PNG32
											format is recommended and any transparency will be preserved.
										</p>
									</div>
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-12">
								<label class="checkbox-container">
									<input type="checkbox" name="use_white_header" value="1" 
										<?= value_if_test(@$vd->custom->use_white_header, 'checked') ?> /> 
									<span class="checkbox"></span>
									<span>Optimize for a logo with a white (non-transparent) background. </span>
									
								</label>
								<p class="help-block">For best results, please use an image with a 
									transparent background and then untick this box.</p>
							</div>
						</div>
						<script>
	
						$(function() {
							
							var li_upload = $("#logo-image-upload");
							
							li_upload.find(".real-file").on("change", function() {
								
								var real_file = $(this);
								var fake_text = li_upload.find(".fake-text");
								var li_thumb = $("#logo-image-thumb");
								
								fake_text.removeClass("error");
								fake_text.val(real_file.val());
								real_file.attr("disabled", true);
								li_thumb.removeClass("blank");
								li_thumb.addClass("loader");
								li_thumb.removeAttr("src");
								
								var image_id_input = $("input#logo-image-id");
								
								var on_upload = function(res) {
									
									if (res.status) {
										real_file.attr("disabled", false);
										image_id_input.val(res.image_id);
										li_thumb.removeClass("loader");
										li_thumb.attr("src", res.files["header-thumb"]);
									} else {
										fake_text.addClass("error");
										real_file.attr("disabled", false);
									}
									
								};
								
								real_file.ajax_upload({
									callback: on_upload,
									url: "manage/image/upload",
									data: { variants: ["header", "header-thumb",
										"header-finger", "header-sidebar"] }
								});
								
							});

							li_upload.find(".remove-button").on("click", function() {
								
								$("input#logo-image-id").val("");
								li_upload.find(".fake-text").val("");
								
								var li_thumb = $("#logo-image-thumb");
								li_thumb.addClass("loader blank");
								li_thumb.removeAttr("src");
								
							});
							
						});
						
						</script>
					</fieldset>
						
					<fieldset class="headline marbot-30">
						<legend>Newsroom Title and Header</legend>
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12" type="text" 
									name="headline" placeholder="Newsroom Title"
									value="<?= value_if_test(@$vd->custom->headline, 
										$vd->esc(@$vd->custom->headline), 
										value_if_test(@$vd->is_paid_claimed_nr, 'The Company Newsroom of',
										"Company Newsroom of ".
										$vd->esc($ci->newsroom->company_name))) ?>" />
								<p class="help-block">
									Add a title to your newsroom to help describe your company or organization.
								</p>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12" type="text" 
									name="headline_prefix" placeholder="Header (Line 1)"
									value="<?= value_if_test(@$vd->custom->headline_prefix,
										$vd->esc(@$vd->custom->headline_prefix),
										value_if_test(@$vd->is_paid_claimed_nr, 'The Company Newsroom of', 
										'The Company Newsroom of')) ?>" />
								<p class="help-block">
									Change the first line of text in the newsroom header.
								</p>
							</div>
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12" type="text" 
									name="headline_h1" placeholder="Header (Line 2)"
									value="<?= value_if_test(@$vd->custom->headline_h1,
										$vd->esc(@$vd->custom->headline_h1),
										$vd->esc($ci->newsroom->company_name)) ?>" />
								<p class="help-block">
									Change the H1 title in the newsroom header.
									Defaults to the company name.
								</p>
							</div>
						</div>
					</fieldset>

					<fieldset class="labels marbot-30">
						<legend>Content Labels</legend>
						<div class="text-muted smaller marbot-20">
							You can customize the labels assigned to different types of content. 
							The values will be used within your newsroom on the sidebar and 
							at the top of each item of content where we display the type. 
							Please provide both the singular and plural version.
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 ta-center">
								Singular
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 ta-center">
								Plural
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->pr->singular) ?>"
									name="content_type_labels[pr][singular]" 
									placeholder="Press Release" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->pr->plural) ?>"
									name="content_type_labels[pr][plural]" 
									placeholder="Press Releases" />
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->news->singular) ?>"
									name="content_type_labels[news][singular]" 
									placeholder="News" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->news->plural) ?>"
									name="content_type_labels[news][plural]" 
									placeholder="News" />
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->event->singular) ?>"
									name="content_type_labels[event][singular]" 
									placeholder="Event" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->event->plural) ?>"
									name="content_type_labels[event][plural]" 
									placeholder="Events" />
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->image->singular) ?>"
									name="content_type_labels[image][singular]" 
									placeholder="Image" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->image->plural) ?>"
									name="content_type_labels[image][plural]" 
									placeholder="Images" />
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->video->singular) ?>"
									name="content_type_labels[video][singular]" 
									placeholder="Video" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->video->plural) ?>"
									name="content_type_labels[video][plural]" 
									placeholder="Videos" />
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->audio->singular) ?>"
									name="content_type_labels[audio][singular]" 
									placeholder="Audio" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->audio->plural) ?>"
									name="content_type_labels[audio][plural]" 
									placeholder="Audio" />
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->contact->singular) ?>"
									name="content_type_labels[contact][singular]" 
									placeholder="Contact" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->contact->plural) ?>"
									name="content_type_labels[contact][plural]" 
									placeholder="Contacts" />
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->blog->singular) ?>"
									name="content_type_labels[blog][singular]" 
									placeholder="Blog Post" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->blog->plural) ?>"
									name="content_type_labels[blog][plural]" 
									placeholder="Blog Posts" />
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->social->singular) ?>"
									name="content_type_labels[social][singular]" 
									placeholder="Social Wire" />
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->content_type_labels->social->plural) ?>"
									name="content_type_labels[social][plural]" 
									placeholder="Social Wire" />
							</div>
						</div>
					</fieldset>
						
					<fieldset class="marbot-30" id="relevant-links">
						<legend>Newsroom Links</legend>
						<div class="row form-group">
							<div class="col-lg-5 rr-title">
								<input class="form-control in-text col-lg-12" type="text" 
									value="<?= $vd->esc(@$vd->custom->rel_res_pri_title) ?>"
									name="rel_res_pri_title" 
									placeholder="Link Title" />
							</div>
							<div class="col-lg-7 rr-link">
								<input class="form-control in-text col-lg-12 url" type="url" 
									value="<?= $vd->esc(@$vd->custom->rel_res_pri_link) ?>"
									name="rel_res_pri_link" 
									placeholder="Link URL" />
							</div>
						</div>
							
						<div class="row form-group">
							<div class="col-lg-5 rr-title">
								<input class="form-control in-text col-lg-12" type="text"
									value="<?= $vd->esc(@$vd->custom->rel_res_sec_title) ?>" 
									name="rel_res_sec_title" 
									placeholder="Link Title" />
							</div>
							<div class="col-lg-7 rr-link">
								<input class="form-control in-text col-lg-12 url" type="url" 
									value="<?= $vd->esc(@$vd->custom->rel_res_sec_link) ?>"
									name="rel_res_sec_link" 
									placeholder="Link URL" />
							</div>
						</div>
							
						<div class="row form-group">
							<div class="col-lg-5 rr-title">
								<input class="form-control in-text col-lg-12" type="text"
									value="<?= $vd->esc(@$vd->custom->rel_res_ter_title) ?>" 
									name="rel_res_ter_title" 
									placeholder="Link Title" />
							</div>
							<div class="col-lg-7 rr-link">
								<input class="form-control in-text col-lg-12 url" type="url" 
									value="<?= $vd->esc(@$vd->custom->rel_res_ter_link) ?>"
									name="rel_res_ter_link" 
									placeholder="Link URL" />
							</div>
						</div>
					</fieldset>

					<fieldset class="background">
						<legend>Background</legend>
						<input type="hidden" id="back-image-id" name="back_image_id" 
							value="<?= @$vd->custom->back_image_id ?>" />
						<div class="row form-group">
							<div class="col-lg-4 image-upload-left image-container nopad marbot-5">
								<?php if (@$vd->custom->back_image_id): ?>
								<?php $ba_im = Model_Image::find($vd->custom->back_image_id); ?>
								<?php $ba_variant = $ba_im->variant('thumb'); ?>
								<?php $ba_url = Stored_Image::url_from_filename($ba_variant->filename); ?>
								<img id="back-image-thumb" src="<?= $ba_url ?>" />
								<?php else: ?>
								<img id="back-image-thumb" class="loader blank" />
								<?php endif ?>
							</div>
							<div class="col-lg-8 image-upload-right">
								<div class="row form-group">
									<div class="col-lg-12">
										<div class="row" id="back-image-upload">
											<div class="col-lg-9 col-md-9 col-sm-9 col-xs-8 file-upload-faker">
												<div class="fake row input-group nomar-left">
													<div class="text-input">
														<input type="text" placeholder="Select Image" class="form-control in-text col-lg-12 fake-text" />
													</div>
													<div class="input-group-btn">
														<button class="btn btn-primary fake-button" type="button">Browse</button>
													</div>
												</div>
												<div class="real row">
													<input class="form-control in-text col-lg-12 real-file required-no-submit" type="file" name="image" />
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 nopad-left">
												<button type="button" class="file-upload-faker-button btn btn-default col-lg-12 remove-button">
													Remove
												</button>
											</div>
										</div>
									</div>
								</div>
								
								<div id="select-back-repeat">
									<select class="form-control selectpicker show-menu-arrow col-lg-12" name="back_image_repeat">
										<option value="repeat"
											<?= value_if_test(@$vd->custom->back_image_repeat == 'repeat', 'selected') ?>>
											Tiled
										</option>
										<option value="no-repeat"
											<?= value_if_test(@$vd->custom->back_image_repeat == 'no-repeat', 'selected') ?>>
											Fixed Position
										</option>
									</select>									
								</div>
							</div>
						</div>
						<script>
	
						$(function() {
							
							var bi_upload = $("#back-image-upload");
							
							bi_upload.find(".real-file").on("change", function() {
								
								var real_file = $(this);
								var fake_text = bi_upload.find(".fake-text");
								var ba_thumb = $("#back-image-thumb");
								
								fake_text.removeClass("error");
								fake_text.val(real_file.val());
								real_file.attr("disabled", true);
								ba_thumb.removeClass("blank");
								ba_thumb.addClass("loader");
								ba_thumb.removeAttr("src");
								
								var image_id_input = $("input#back-image-id");
								
								var on_upload = function(res) {
									
									if (res.status)
									{
										real_file.attr("disabled", false);
										image_id_input.val(res.image_id);
										ba_thumb.removeClass("loader");
										ba_thumb.attr("src", res.files["thumb"]);
									}
									else
									{
										fake_text.addClass("error");
										real_file.attr("disabled", false);
									}
									
								};
								
								real_file.ajax_upload({
									callback: on_upload,
									url: "manage/image/upload",
									data: { variants: ["thumb"], size_limit: 524288 }
								});
								
							});

							bi_upload.find(".remove-button").on("click", function() {
								
								$("input#back-image-id").val("");
								bi_upload.find(".fake-text").val("");
								
								var ba_thumb = $("#back-image-thumb");
								ba_thumb.addClass("loader blank");
								ba_thumb.removeAttr("src");		
								
							});
							
						});
						
						</script>
					</fieldset>
						
					<fieldset>
						<legend>Google Analytics</legend>
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12 marbot-10" type="text" pattern="^UA\-\d+\-\d+$"
									name="ganal" placeholder="Tracking ID" 
									value="<?= $vd->esc(@$vd->custom->ganal) ?>" />
								<p class="help-block">
									Use Google Analytics for detailed newsroom statistics. 
								</p>
							</div>
						</div>
					</fieldset>

					<?php if (Auth::is_admin_online()): ?>
					<fieldset>
						<legend>Header Code Injection</legend>
						<div class="row form-group">
							<div class="col-lg-12">
								<textarea name="inject_pre_header" 
									class="form-control basic-code-editor"><?= 
									$vd->custom ? $vd->custom->raw_data_object()->inject_pre_header : null ?></textarea>
								<p class="help-block">
									This can be used to add additional HTML code (with CSS and JS) to the newsroom.
								</p>
							</div>
						</div>
					</fieldset>
					<?php endif ?>

				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-5 form-col-2">
			<div class="panel panel-default aside" id="locked_aside">
				<div class="panel-body">
					<div class="color-labels">								
						<div class="row form-group customize-colors row">
							<div class="col-lg-12" data-colorpicker-guid="1">
								<div class="color-label"><div>Link Color</div></div>
								<div class="input-group in-text-add-on">
									<input type="text" placeholder="Link Color" class="form-control in-text col-lg-10 color" name="link_color"
										value="<?= $vd->esc(@$vd->custom->link_color) ?>" pattern="^#[A-Fa-f0-9]{6}$" />
									<span class="color-pick input-group-addon"><i class="fa fa-fw fa-eyedropper"></i></span>
								</div>
							</div>
						</div>
							
						<div class="row form-group customize-colors row">
							<div class="col-lg-12" data-colorpicker-guid="2">
								<div class="color-label"><div>Link Hover Color</div></div>
								<div class="input-group in-text-add-on">
									<input type="text" placeholder="Link Hover Color" class="form-control in-text col-lg-10 color" name="link_hover_color"
										value="<?= $vd->esc(@$vd->custom->link_hover_color) ?>" pattern="^#[A-Fa-f0-9]{6}$" />
									<span class="color-pick input-group-addon"><i class="fa fa-fw fa-eyedropper"></i></span>
								</div>
							</div>
						</div>

						<div class="row form-group customize-colors row">
							<div class="col-lg-12" data-colorpicker-guid="3">
								<div class="color-label"><div>Text Color</div></div>
								<div class="input-group in-text-add-on">
									<input type="text" placeholder="Text Color" class="form-control in-text col-lg-10 color" name="text_color"
										value="<?= $vd->esc(@$vd->custom->text_color) ?>" pattern="^#[A-Fa-f0-9]{6}$" />
									<span class="color-pick input-group-addon"><i class="fa fa-fw fa-eyedropper"></i></span>
								</div>
							</div>
						</div>

						<div class="row form-group customize-colors row">
							<div class="col-lg-12" data-colorpicker-guid="4">
								<div class="color-label"><div>Secondary Color</div></div>
								<div class="input-group">
									<input type="text" placeholder="Secondary Color" class="form-control in-text col-lg-10 color" name="secondary_color"
										value="<?= $vd->esc(@$vd->custom->secondary_color) ?>" pattern="^#[A-Fa-f0-9]{6}$" />
									<span class="color-pick input-group-addon"><i class="fa fa-fw fa-eyedropper"></i></span>
								</div>
							</div>
						</div>

						<div class="row form-group customize-colors row">
							<div class="col-lg-12" data-colorpicker-trans="1" data-colorpicker-guid="5">
								<div class="color-label"><div>Background Color</div></div>
								<div class="input-group">
									<input type="text" placeholder="Background Color" class="form-control in-text col-lg-10 color" name="back_color"
										value="<?= $vd->esc(@$vd->custom->back_color) ?>" pattern="^(transparent|#[A-Fa-f0-9]{6})$" />
									<span class="color-pick input-group-addon"><i class="fa fa-fw fa-eyedropper"></i></span>
								</div>
							</div>
						</div>
						
						<div class="row form-group marbot-30">
							<div class="col-lg-12">
								<button type="button" id="reset-defaults"
									class="btn btn-default nomar pull-right">Reset Colors</a>
								<script>
								
								$(function() {
									
									var reset = $("#reset-defaults");
									reset.on("click", function() {
										$("input.color").val("");
									});
									
								});
								
								</script>
							</div>
						</div>
								
						<div class="row form-group">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
								<button type="submit" name="is_preview" value="1" 
									class="btn btn-default col-lg-12">Preview Newsroom</button>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 nomar">
								<button type="submit" name="publish" value="1" 
									class="col-lg-12 btn btn-primary nomar pull-right">Save</button>
							</div>
						</div>
								
					</div>
					
					<?php 

						$render_basic = $ci->is_development();

						$loader = new Assets\CSS_Loader(
							$ci->conf('assets_base'), 
							$ci->conf('assets_base_dir'));
						$loader->add('lib/bootstrap-colorpicker/css.css');						
						echo $loader->render($render_basic);

						$loader = new Assets\JS_Loader(
							$ci->conf('assets_base'), 
							$ci->conf('assets_base_dir'));
						$loader->add('lib/bootstrap-colorpicker/js.js');
						$loader->add('js/required.js');
						$ci->add_eob($loader->render($render_basic));

					?>
					<script>
					
					$(function() {
						
						$("input.color").each(function() {
							var _this = $(this);
							var container = _this.parent();
							container.colorpicker({ format: "hex" });
							container.colorpicker("setValue", _this.val());
						});
											
						$("span.color-pick").on("click", function() {
							$(this).prev().colorpicker("show");
						});
						
						$(".color-labels .color").on("focus", function() {
							$(this).prev().addClass("focus");
						}).on("blur", function() {
							$(this).prev().removeClass("focus");
						});

						$("a.help-block-link").on("click", function(ev) {
							ev.preventDefault();
							var modal = $("#<?= $vd->info_modal_id ?>");	
							var modal_content = modal.find(".modal-content");
							var div_id = $(this).data("div");
							var show_html = $('#'+div_id).html();							
							modal_content.html(show_html);	
							modal.modal('show');
						});
						
					});
					
					</script>
					
				</div>
			</div>
		</div>
	</div>
	
	</form>
</div>

<script>

$(function() {

	if (is_desktop()) {
		var options = { offset: { top: 80 } };
		$.lockfixed("#locked_aside", options);
	}

});

</script>