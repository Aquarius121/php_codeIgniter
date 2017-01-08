<ul class="breadcrumb">
	<li><a href="manage/newsroom">Company Newsroom</a> <span class="divider">&raquo;</span></li>
	<li><a href="manage/newsroom/contact">Company Contacts</a> <span class="divider">&raquo;</span></li>
	<?php if (@$vd->contact): ?>
	<li class="active"><?= $vd->esc($vd->contact->name) ?></li>
	<?php else: ?>
	<li class="active">New Contact</li>
	<?php endif ?>
</ul>

<div class="container-fluid">

	<header>
		<div class="row">
			<div class="col-lg-6">
			<?php if (@$vd->contact): ?>
				<h2>Edit Contact</h2>
				<?php else: ?>
				<h2>Add Contact</h2>
			<?php endif ?>
			</div>
		</div>
	</header>	

	<form class="tab-content required-form" method="post" action="manage/newsroom/contact/edit/save">

	<div class="row">
		<div class="col-lg-8 col-md-7 content form-col-1">
			<div class="panel panel-default">
				<div class="panel-body">
					<input type="hidden" name="contact_id" value="<?= @$vd->contact->id ?>" />

					<fieldset class="form-section basic-information">
						<legend>Basic Information</legend>
					
						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 required" type="text" 
									name="first_name" placeholder="First Name"
									data-required-name="First Name"
									value="<?= $vd->esc(@$vd->contact->first_name) ?>" />
							</div>
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 required" type="text" 
									name="last_name" placeholder="Last Name"
									data-required-name="Last Name"
									value="<?= $vd->esc(@$vd->contact->last_name) ?>" />
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12" type="text" 
									name="title" placeholder="Contact Title"
									value="<?= $vd->esc(@$vd->contact->title) ?>" />
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Contact Bio</legend>
						<div class="row form-group">
							<div class="col-lg-12 cke-container">
							
								<textarea class="in-text in-content col-lg-12" id="description"
									name="description" placeholder="Contact Description"><?= 
									$vd->esc(@$vd->contact->description) 
								?></textarea>

								<script>
								defer(function(){
									window.init_editor($("#description"), { height: 400 });
								})
								</script>

								<p class="help-block">Describe or talk about this contact.</p>
							</div>
						</div>

					</fieldset>
							
					<fieldset class="contact-picture">
						<legend>Contact Picture</legend>
						<input type="hidden" id="contact-image-id" name="image_id" 
							value="<?= @$vd->contact->image_id ?>" />
						<div class="row form-group">
							<div class="col-lg-4 image-upload-left image-container scaled nopad marbot-10">
								<?php if (@$vd->contact->image_id): ?>
									<?php $lo_im = Model_Image::find($vd->contact->image_id); ?>
									<?php if ($lo_im): ?>
										<?php $lo_variant = $lo_im->variant('thumb'); ?>
										<?php $lo_url = Stored_Image::url_from_filename($lo_variant->filename); ?>
										<img id="contact-image-thumb" src="<?= $lo_url ?>" />
									<?php else: ?>
										<img id="contact-image-thumb" class="loader blank" />		
									<?php endif ?>
								<?php else: ?>
									<img id="contact-image-thumb" class="loader blank" />
								<?php endif ?>
							</div>
							<div class="col-lg-8">
								<div class="row form-group">
									<div class="col-lg-12">
										<div class="row no-overflow marbot-5" id="contact-image-upload">
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
													<input class="in-text col-lg-12 real-file required-no-submit" type="file" name="image" />
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 nopad-left">
												<button type="button" class="file-upload-faker-button btn btn-default col-lg-12 remove-button">
													Remove
												</button>
											</div>
										</div>
										<p class="help-block">
											We recommend a <span class="darker">minimum size of
											200x200</span>. The image will be resized if needed. 
											A head shot will work best within the newsroom interface.
										</p>
									</div>
								</div>
							</div>
						</div>
						<script>
	
						defer(function() {

							var ci_upload = $("#contact-image-upload");
							
							ci_upload.find(".real-file").on("change", function() {
								
								var real_file = $(this);
								var fake_text = ci_upload.find(".fake-text");
								var ci_thumb = $("#contact-image-thumb");
								
								fake_text.removeClass("error");
								fake_text.val(real_file.val());
								real_file.attr("disabled", true);
								ci_thumb.removeClass("blank");
								ci_thumb.addClass("loader");
								ci_thumb.removeAttr("src");
								
								var image_id_input = $("input#contact-image-id");
								
								var on_upload = function(res) {
									
									if (res.status)
									{
										real_file.attr("disabled", false);
										image_id_input.val(res.image_id);
										ci_thumb.removeClass("loader");
										ci_thumb.attr("src", res.files["thumb"]);
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
									data: { variants: ["contact", "contact-cover", 
										"finger", "thumb"] }
								});
								
							});

							ci_upload.find(".remove-button").on("click", function() {
								
								$("input#contact-image-id").val("");
								ci_upload.find(".fake-text").val("");
								
								var ci_thumb = $("#contact-image-thumb");
								ci_thumb.addClass("loader blank");
								ci_thumb.removeAttr("src");
								
							});
							
						});
						
						</script>
					</fieldset>
							
					<fieldset class="contact-information">
						<legend>Contact Information</legend>
						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12" type="email" 
									name="email" placeholder="Email Address"
									value="<?= $vd->esc(@$vd->contact->email) ?>" />
								<p class="help-block">Email Address</p>
							</div>
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 url" type="url" 
									name="website" placeholder="Website"
									value="<?= $vd->esc(@$vd->contact->website) ?>" />
								<p class="help-block">Website Address</p>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12" type="text" 
									name="phone" placeholder="Phone Number"
									value="<?= $vd->esc(@$vd->contact->phone) ?>" />
								<p class="help-block">Phone Number</p>
							</div>
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12" type="text" 
									name="skype" placeholder="Skype"
									value="<?= $vd->esc(@$vd->contact->skype) ?>" />
								<p class="help-block">Skype Username</p>
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 facebook-profile-id" type="text" 
									name="facebook" placeholder="Facebook" 
									value="<?= $vd->esc(@$vd->contact->facebook) ?>" />
								<p class="help-block">Facebook Username or Page</p>
							</div>
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 twitter-profile-id" type="text" 
									name="twitter" placeholder="Twitter" 
									value="<?= $vd->esc(@$vd->contact->twitter) ?>" />
								<p class="help-block">Twitter Username</p>
							</div>
						</div>
						
						<div class="row form-group">
							<div class="col-lg-6">
								<input class="form-control in-text col-lg-12 linkedin-profile-id" type="text" 
									name="linkedin" placeholder="LinkedIn"
									value="<?= $vd->esc(@$vd->contact->linkedin) ?>" />
								<p class="help-block">LinkedIn Profile ID</p>
							</div>
						</div>		
					</fieldset>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-5 form-col-2">
			<div class="panel panel-default" id="locked_aside">
				<div class="panel-body">

					<div class="row form-group">
						<div class="col-lg-12">
							<div class="alert alert-info" id="main-contact-alert">
								<strong>Notice!</strong> You can have at most one press contact. 
								Selecting press contact below will convert all other contacts to normal.
							</div>
							<?php 
							
							$is_main_contact = !$ci->newsroom->company_contact_id && !$vd->contact; 
							$is_main_contact = $is_main_contact || ($vd->contact && 
								$vd->contact->id == $ci->newsroom->company_contact_id); 
							
							?>
							<select class="form-control selectpicker show-menu-arrow col-lg-12" name="is_main_contact" id="is-main-contact">
								<option <?= value_if_test($is_main_contact, 'selected') ?> value="1">
									Press Contact
								</option>
								<option <?= value_if_test(!$is_main_contact, 'selected') ?> value="0">
									Normal Contact
								</option>
							</select>
							<script>

							defer(function() {
								
								var is_main_contact = $("#is-main-contact");
								is_main_contact.on_load_select();
								
								<?php if (!$is_main_contact): ?>
									
								var alert = $("#main-contact-alert");
								is_main_contact.on("change", function() {
									alert.toggleClass("enabled", $(this).val());
								});
								
								<?php endif ?>
								
							});
							
							</script>
						</div>
					</div>
									
					<div class="row form-group">
						<div class="col-lg-7 col-sm-7 col-xs-7">
							<button type="submit" name="is_preview" value="1" 
								class="col-lg-12 btn btn-default">Preview</button>
						</div>
						<div class="col-lg-5 col-sm-5 col-xs-5">
							<button type="submit" name="publish" value="1" 
								class="col-lg-12 btn btn-primary nomar pull-right">Save</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php 

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('js/required.js');
			$loader->add('lib/jquery.lockfixed.js');
			$render_basic = $ci->is_development();
			$ci->add_eob($loader->render($render_basic));

		?>						
						
		<script>

		$(function() {

			if (is_desktop()) {
				var options = { offset: { top: 100 } };
				$.lockfixed("#locked_aside", options);
			}

		});
		
		</script>
						
				
	</div>
</div>
