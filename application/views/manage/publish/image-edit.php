<?= $ci->load->view('manage/publish/partials/breadcrumbs') ?>
<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->m_content): ?>
					<h2>Edit Image</h2>
				<?php else: ?>
					<h2>Add New Image</h2>
				<?php endif ?>
			</div>
		</div>
	</header>

	<form class="tab-content required-form has-premium" method="post" action="manage/publish/image/edit/save/<?= @$vd->m_content->id ?>" id="content-form">
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

						<div class="row form-group">

							<div class="col-lg-4 col-xs-12 nopad-left">
								<div class="image-container image-upload-left nopad marbot-5">
									<input type="hidden" name="image_id" id="image-id" class="image_id required" 
										value="<?= @$image->id ?>" data-required-name="Image"
										data-required-use-parent="1" />
									<?php if ($image): ?>
									<img id="image-thumb"
										src="<?= Stored_Image::url_from_filename($image->variant('thumb')->filename) ?>" />
									<?php else: ?>
									<img id="image-thumb" class="loader blank" />
									<?php endif ?>
								</div>
							</div>
							
							<div class="col-lg-8 col-xs-12 image-upload-right">
								<div class="row form-group">
									<div id="content-image-upload" class="marbot-15">
										<div class="col-lg-12 file-upload-faker">
											<div class="fake row input-group not-row">
												<div class="text-input">
													<input type="text" placeholder="Select Image" class="form-control in-text col-lg-12 fake-text" />
												</div>
												<div class="input-group-btn">
													<button class="btn btn-primary nomar fake-button" type="button">Browse</button>
												</div>
											</div>
											<div class="real row">
												<input class="in-text col-lg-12 real-file required-no-submit" type="file" name="image" />
											</div>
										</div>
									</div>
								</div>

								<div class="row form-group not-row">
									<div class="col-lg-12 nopad">
										
										<input class="form-control in-text col-lg-12 required" type="text" name="title" 
											id="title" placeholder="Enter Title of Image"
											value="<?= $vd->esc(@$vd->m_content->title) ?>" 
											maxlength="<?= $ci->conf('title_max_length') ?>"
											data-required-name="Title" />
									</div>
								</div>
							</div>
						
							<script>
		
							$(function() {
								
								var ci_upload = $("#content-image-upload");
								
								ci_upload.find(".real-file").on("change", function() {
									
									var real_file = $(this);
									var fake_text = ci_upload.find(".fake-text");
									
									fake_text.removeClass("error");
									fake_text.val(real_file.val());
									real_file.attr("disabled", true);
									
									var new_image = $("img#image-thumb")
									var image_id_input = $("input#image-id");
									new_image.removeClass("blank");
									new_image.addClass("loader");
									new_image.removeAttr("src");
									image_id_input.val("");
									
									var on_upload = function(res) {
										
										if (res.status)
										{
											fake_text.val("");
											real_file.attr("disabled", false);
											new_image.removeClass("loader");
											new_image.attr("src", res.files.thumb);
											image_id_input.val(res.image_id);
										}
										else
										{
											fake_text.addClass("error");
											real_file.attr("disabled", false);
										}
										
									};

									var variants = ["finger", "thumb", 
										"view-cover", "view-full", "cover", 
										"cover-website"];

									real_file.ajax_upload({
										callback: on_upload,
										url: "manage/image/upload",
										data: { variants: variants }
									});
									
								});
								
							});
							
							</script>
						</div>
						
						<div class="row form-group">
							<div class="col-lg-12">
								<textarea class="form-control in-text col-lg-12 required" id="summary" name="summary"
										data-required-name="Summary" placeholder="Enter Summary of Image" rows="5"
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
									placeholder="Source / Photographer" />
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