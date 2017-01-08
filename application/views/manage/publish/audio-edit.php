<?= $ci->load->view('manage/publish/partials/breadcrumbs') ?>
<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->m_content): ?>
				<h2>Edit Audio</h2>
				<?php else: ?>
				<h2>Add New Audio</h2>
				<?php endif ?>
			</div>
		</div>
	</header>

	<form class="tab-content required-form has-premium" method="post" action="manage/publish/audio/edit/save/<?= @$vd->m_content->id ?>" id="content-form">
	<div class="row">
		<div class="col-lg-8 col-md-7 form-col-1">
			<div class="panel panel-default">
				<div class="panel-body">

					<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
					
					<?php if ($vd->m_content && !$vd->duplicate): ?>
					<input type="hidden" name="id" value="<?= $vd->m_content->id ?>" />
					<?php endif ?>
					
					<?php 

						$render_basic = $ci->is_development();

						$loader = new Assets\CSS_Loader(
							$ci->conf('assets_base'), 
							$ci->conf('assets_base_dir'));
						$loader->add('lib/mediaelement/mediaelementplayer.css');
						echo $loader->render($render_basic);

						$loader = new Assets\JS_Loader(
							$ci->conf('assets_base'), 
							$ci->conf('assets_base_dir'));
						$loader->add('lib/mediaelement/mediaelement-and-player.min.js');
						$ci->add_eob($loader->render($render_basic));

					?>

					<fieldset class="basic-information">							
						<legend>Basic Information</legend>
						<div class="row form-group">
							<div id="uploaded-audio-player" class="col-lg-12 <?= value_if_test(@$vd->m_content, 'enabled') ?>">
								<?php if (@$audio): ?>
								<audio src="<?= $vd->esc(Stored_File::url_from_filename($audio->filename)) ?>" />
								<script>
								
								$(function() {
									
									var audio = $("#uploaded-audio-player audio");
									audio.mediaelementplayer({
										audioWidth: '100%'
									});

								});
								
								</script>
								<?php endif ?>
							</div>
						</div>

						<input type="hidden" id="stored-file-id" name="stored_file_id" 
							value="<?= $vd->esc(@$vd->m_content->stored_file_id) ?>"
							class="required" data-required-name="Audio File" />
								
						<div id="audio-upload-status">
							<div class="alert alert-warning">
								<strong>Patience!</strong>
								The upload process can take several minutes. <br />
								You can continue to fill out the form while you wait.
							</div>
						</div>
						<div id="audio-upload-error">
							<div class="alert alert-danger">
								<strong>Error!</strong>
								<span></span>
							</div>
						</div>
									
						<div class="row form-group" id="content-audio-upload">
							<div class="col-lg-12 file-upload-faker">
								<div class="fake row input-group not-row">
									<div class="text-input">
										<input type="text" placeholder="Select Audio File (MP3)" class="form-control in-text col-lg-12 fake-text" />
									</div>
									<div class="input-group-btn">
										<button class="btn btn-primary fake-button nomar" type="button">Browse</button>
									</div>
								</div>
								<div class="real row">
									<input class="form-control in-text col-lg-12 real-file required-no-submit" type="file" name="audio" 
										accept="<?= $vd->esc(implode(',', $ci->supported_mime_types())) ?>" />
								</div>
							</div>
						</div>
						<script>

						$(function() {
							
							var ci_upload = $("#content-audio-upload");
							var upload_status = $("#audio-upload-status");
							var upload_error = $("#audio-upload-error");
							var upload_player = $("#uploaded-audio-player");
							var stored_file_id_input = $("input#stored-file-id");
							
							ci_upload.find(".real-file").on("change", function() {
								
								var real_file = $(this);
								var fake_text = ci_upload.find(".fake-text");
								
								fake_text.removeClass("error");
								fake_text.val(real_file.val());
								real_file.attr("disabled", true);
								
								upload_player.removeClass("enabled");
								upload_error.removeClass("enabled");
								upload_status.addClass("enabled");
								stored_file_id_input.val("");
								
								var on_upload = function(res) {
									
									upload_status.removeClass("enabled");
									
									if (res && res.status) {
										
										fake_text.val("");
										real_file.attr("disabled", false);
										stored_file_id_input.val(res.stored_file_id);
										stored_file_id_input.trigger("change");
										upload_player.addClass("enabled");
										
										var audio = $.create("audio");
										audio.attr("src", res.audio_url);
										upload_player.empty().append(audio);
										audio.mediaelementplayer({
											audioWidth: '100%'
										});
										
									} else {
										
										error_text = ((!res || !res.error) 
											? "Upload Failed"
											: res.error);
											
										fake_text.addClass("error");
										real_file.attr("disabled", false);
										upload_error.find("span").text(error_text);
										upload_error.addClass("enabled");
										
									}
									
								};
								
								real_file.ajax_upload({
									callback: on_upload,
									url: "manage/publish/audio/upload"
								});
								
							});
							
						});
						
						</script>		
						
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12 required" type="text" name="title" 
									id="title" placeholder="Enter Title of Audio"
									value="<?= $vd->esc(@$vd->m_content->title) ?>" 
									maxlength="<?= $ci->conf('title_max_length') ?>"
									data-required-name="Title" />
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-12">
								<textarea class="form-control in-text col-lg-12 required" id="summary" name="summary"
									data-required-name="Summary" placeholder="Enter Summary of Audio" rows="5"
									><?= $vd->esc(@$vd->m_content->summary) ?></textarea>
								<p class="help-block" id="summary_countdown_text">
									<span id="summary_countdown"></span> Characters Left</p>
								<script>
								
								$(function() {

									$("#summary").limit_length(<?= $ci->conf('summary_max_length') ?>, 
										$("#summary_countdown_text"), 
										$("#summary_countdown")
									);
								});
								
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
									placeholder="Source / Audiographer" />
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
			$loader->add('lib/jquery.lockfixed.js');
			$render_basic = $ci->is_development();
			$ci->add_eob($loader->render($render_basic));

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