<form action="reseller/account/branding" method="post" class="required-form">	
	<div class="row-fluid">
		<div class="span12">
			<header class="page-header">
				<div class="row-fluid">
					<div class="span6">
						<h1>Branding Details</h1>
					</div>
					<div class="span6">
					</div>
				</div>
			</header>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12">
			<div class="content content-no-tabs">
				<div class="row-fluid">
					<div class="span8 information-panel">
						
						<section class="form-section basic-information">
							<h2>Company Information</h2>
							<ul>
								<li>
									<div class="row-fluid">
										<div class="span6">
											<input type="text" placeholder="Company Name" class="in-text span12 required"
												name="company_name" data-required-name="Company Name"
												value="<?=$vd->esc($vd->reseller_details->company_name) ?>"
												 />
										</div>
										<div class="span6">
											<input type="url" placeholder="Website" class="in-text span12 required" 
												name="website" value="<?= $vd->esc($vd->reseller_details->website) ?>" 
												data-required-name="Website" />
										</div>
									</div>
								</li>
								<li>
									<div class="row-fluid">
										<div class="span12">
											<input type="text" placeholder="Business Paypal" 
												class="in-text span12" name="business_paypal" 
												value="<?= $vd->esc($vd->reseller_details->business_paypal) ?>" />
										</div>
									</div>
								</li>
							</ul>
						</section> 
						
						<section class="form-section basic-information">
							<h2>Company Logo</h2>
							<ul class="row-fluid">
								<li class="span12">
									<input type="hidden" id="logo-image-id" name="logo_image_id" 
										value="<?= @$vd->logo_image_id ?>" />
									<div class="row-fluid">
										<div class="span4 image-upload-left image-container scaled">
											<?php if ($vd->logo_image_id): ?>
											<?php $lo_im = Model_Image::find($vd->logo_image_id); ?>
											<?php $lo_variant = $lo_im->variant('header-thumb'); ?>
											<?php $lo_url = Stored_Image::url_from_filename($lo_variant->filename); ?>
											<img id="logo-image-thumb" src="<?= $lo_url ?>" />
											<?php else: ?>
											<img id="logo-image-thumb" class="loader blank" />
											<?php endif ?>
										</div>
										<div class="span8">
											<div class="row-fluid">
												<div class="span12">
													<div class="row-fluid no-overflow marbot-5" 
														id="logo-image-upload">
														<div class="span9 file-upload-faker">
															<div class="fake row-fluid">
																<div class="span8 text-input">
																	<input type="text" 
																		placeholder="Select Image" 
																		class="in-text span12 fake-text" />
																</div>
																<div class="span4">
																	<button class="btn span12 fake-button" 
																		type="button">Browse</button>
																</div>
															</div>
															<div class="real row-fluid">
																<input class="in-text span12 real-file required-no-submit" 
																	type="file" name="image" />
															</div>
														</div>
														<div class="span3">
															<button type="button" class="file-upload-faker-button
																btn span12 remove-button">
																Remove
															</button>
														</div>
													</div>
													<p class="help-block">
														<?php $v_header = $ci->conf('v_sizes', 'header') ?>
														We enforce a <span class="darker">maximum size of 
														<?= $v_header->width ?>x<?= $v_header->height ?></span>.
														The image will be resized if it exceeds that. The PNG32
														format is recommended and any transparency will be 
														preserved.
													</p>
												</div>
											</div>
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
												
												if (res.status)
												{
													real_file.attr("disabled", false);
													image_id_input.val(res.image_id);
													li_thumb.removeClass("loader");
													li_thumb.attr("src", res.files["header-thumb"]);
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
								</li>
							</ul>
							
						</section>
						
						<div class="marbot-20"></div>
						<input type="hidden" name="save" value="1" />
						<button type="submit" class="bt-publish bt-orange">
							Save Changes
						</button>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>