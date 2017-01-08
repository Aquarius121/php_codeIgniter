<?= $ci->load->view('manage/publish/partials/breadcrumbs') ?>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->m_content): ?>
				<h2>Edit Video</h2>
				<?php else: ?>
				<h2>Add New Video</h2>
				<?php endif ?>
			</div>
	</header>

	<form class="tab-content required-form has-premium" method="post" action="manage/publish/video/edit/save/<?= @$vd->m_content->id ?>" id="content-form">
	<div class="row">
		<div class="col-lg-8 col-md-7 form-col-1">
			<div class="panel panel-default">
				<div class="panel-body">
					<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
					
					<?php if ($vd->m_content && !$vd->duplicate): ?>
					<input type="hidden" name="id" value="<?= $vd->m_content->id ?>" />
					<?php endif ?>

					<fieldset class="basic-information">
						<legend>Basic Information</legend>
						<input value="<?= $vd->esc(@$vd->m_content->external_author) ?>"
							type="hidden" id="external-author" name="external_author" />
						<input value="<?= $vd->esc(@$vd->m_content->external_duration) ?>"
							type="hidden" id="external-duration" name="external_duration" />
						<div class="row form-group">
							<div class="col-lg-4 image-container image-upload-left nopad marbot-5">
								<input type="hidden" name="image_id" id="image-id" class="image_id required" 
									value="<?= @$image->id ?>" data-required-name="Video"
									data-required-use-parent="1" />
								<?php if ($image): ?>
								<img id="image-thumb"
									src="<?= Stored_Image::url_from_filename($image->variant('thumb')->filename) ?>" />
								<?php else: ?>
									<img id="image-thumb" class="loader blank" />
									<?php endif ?>
							</div>
							<div class="col-lg-8 image-upload-right">
								<div id="select-video" class="row form-group">
									<div class="col-lg-4">
										<select class="form-control selectpicker show-menu-arrow col-lg-12" name="external_provider">
											<?php foreach ($providers as $provider): ?>
											<option value="<?= $vd->esc($provider) ?>"
												<?= value_if_test((@$vd->m_content->external_provider === $provider), 'selected') ?>>
												<?= $vd->esc(Video::get_provider_name($provider)) ?>
											</option>
											<?php endforeach ?>
										</select>
									</div>
									<div class="col-lg-8">
										<input class="form-control in-text col-lg-12 required" type="text" name="external_video_id" 
											id="video-id" placeholder="Enter Video URL"
											value="<?= $vd->esc(@$vd->m_content->external_video_id) ?>" 
											data-required-name="Video Source" />
									</div>
								</div>

								<div class="row form-group">
									<div class="col-lg-12">

										<input class="form-control in-text col-lg-12 required" type="text" name="title" 
											id="title" placeholder="Enter Title of Video"
											maxlength="<?= $ci->conf('title_max_length') ?>"
											value="<?= $vd->esc(@$vd->m_content->title) ?>" 
											data-required-name="Title" />
									</div>
								</div>
							</div>
							
							<script>
			
							$(function() {
								
								var select_video = $("#select-video");
								var video_id_input = $("#video-id");
								var provider_select = select_video.find("select");
								var video_props = video_id_input.add(provider_select);
								
								var title_input = $("input#title");
								var summary_ta = $("textarea#summary");
								
								var external_author_input = $("#external-author");
								var external_width_input = $("#external-width");
								var external_height_input = $("#external-height");
								var external_duration_input = $("#external-duration");
								
								provider_select.on_load_select();
								
								video_props.on("change", function() {
									
									// not entered id so wait
									if (!video_id_input.val())
										return;
									
									var post_data = video_props.serialize();
									var new_image = $("img#image-thumb")
									var image_id_input = $("input#image-id");
									new_image.removeClass("blank");
									new_image.addClass("loader has-loader");
									new_image.removeAttr("src");
									image_id_input.val("");	
									
									external_author_input.val("");
									external_width_input.val("");
									external_height_input.val("");
									external_duration_input.val("");
									
									$(".required-error").remove();
									
									var on_upload = function(res) {
										
										if (res === null) {
		
											var required_error = $.create("div");
											required_error.addClass("alert alert-danger");
											required_error.addClass("required-error");
											
											error_html = "<strong>Error!<\/strong> The " 
												+ "video information is not correct.";
												
											required_error.html(error_html);
											select_video.parent().before(required_error);
											
										} else {
											
											new_image.removeClass("loader has-loader");
											new_image.attr("src", res.image_url);
											image_id_input.val(res.image_id);
											video_id_input.val(res.video_id);
											if (!res.video_data) return;
											
											if (!title_input.val()) {
												title_input.val(res.video_data.title);
												title_input.trigger("change");
											}
											
											if (!summary_ta.val()) {
												summary_ta.val(res.video_data.description);
												summary_ta.trigger("change");
											}
											
											external_author_input.val(res.video_data.author);
											external_width_input.val(res.video_data.width);
											external_height_input.val(res.video_data.height);
											external_duration_input.val(res.video_data.duration);
											
										}
										
									};
									
									$.post("manage/publish/video/resolve_video", 
										post_data, on_upload);
									
								});
								
							});
								
							</script>
						</div>

						<div class="row form-group">
							<div class="col-lg-12">
								<textarea class="form-control in-text col-lg-12 required" id="summary" name="summary"
									data-required-name="Summary" placeholder="Enter Summary of Video" rows="5"
									><?= $vd->esc(@$vd->m_content->summary) ?></textarea>
								<p class="help-block" id="summary_countdown_text">
									<span id="summary_countdown"></span> Characters Left</p>
								<script>
								
								$(function() {
									$("#summary").limit_length(<?= $ci->conf('summary_max_length') ?>, 
										$("#summary_countdown_text"), 
										$("#summary_countdown")
									);
								})								
								
								</script>
							</div>
						</div>
					</fieldset>

					<?= $ci->load->view('manage/publish/partials/tags') ?>		
					<?= $ci->load->view('manage/publish/partials/relevant-resources') ?>
					<?= $ci->load->view('manage/publish/partials/social-media') ?>
						
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-5 form-col-2">
			<div class="panel panel-default" id="locked_aside">
				<div class="panel-body">
					<fieldset class="ap-block ap-properties nomarbot">

						<?= $this->load->view('manage/publish/partials/status') ?>
						<?= $this->load->view('manage/publish/partials/license') ?>
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control col-lg-12 in-text" type="text" name="source" 
									value="<?= $vd->esc(@$vd->m_content->source) ?>" 
									placeholder="Source / Videographer" />
							</div>
						</div>

						<?php if (!@$vd->m_content->is_published): ?>
							<?= $this->load->view('manage/publish/partials/publish-date') ?>
						<?php endif ?>
																
						<?= $ci->load->view('manage/publish/partials/save-buttons') ?>
							
					</fieldset>							
				</div>
			</div>
		</div>

		<?php 

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('js/required.js');
			$loader->add('lib/bootbox.min.js');
			$render_basic = $ci->is_development();
			echo $loader->render($render_basic);

		?>

		<script>

		$(function() {

			if (is_desktop())
			{
				var options = { offset: { top: 100 } };
				$.lockfixed("#locked_aside", options);
			}

		});

		</script>

	</div>
	</form>
</div>