<div class="container-fluid">
	<div class="panel panel-default <?= value_if_test(!empty($vd->wr_raw_data->editor_comments), 'form-col', 'form-col-1') ?>">
		<div class="panel-body">
			<div class="row">

				<div class="col-lg-12">
					<?= $ci->load->view('manage/writing/partials/progress-bar') ?>
					<header>
						<div class="row">
							<div class="col-lg-12 page-title">
								<h2>Press Release Media <span class="text-muted">(Optional)</span></h2>
							</div>
						</div>
					</header>
					<hr class="marbot-30" />
		
					<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
						<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
						<div class="row">
							<div class="<?= value_if_test(!empty($vd->wr_raw_data->editor_comments), 'col-lg-8 col-md-8 form-col-1', 'col-lg-12') ?> ">
									
								<?php $vd->image_item_count = 6; ?>
								<?php $vd->image_item_per_line = 3; ?>
								
								<div class="web-images-writing">
									<?= $ci->load->view('manage/publish/partials/web-images', array('meta_extension' => null)) ?>
								</div>
								<?= $ci->load->view('manage/publish/partials/web-files') ?>
								<div class="marbot-40"></div>
								<?= $ci->load->view('manage/publish/partials/relevant-resources') ?>
								<?= $ci->load->view('manage/publish/partials/web-video') ?>
								
								<fieldset class="form-section social-media-profiles">
									<legend>
										Social Media
										<a data-toggle="tooltip" class="tl" href="#" 
											title="<?= Help::SOCIAL_PROFILES ?>">
											<i class="fa fa-fw fa-question-circle"></i>
										</a>	
									</legend>
									
									<div class="row form-group">
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 placeholder-container">
											<input class="has-placeholder form-control in-text col-xs-12 facebook-profile-id" type="text" 
												name="soc_facebook" placeholder="Facebook" 
												value="<?= $vd->esc(@$vd->m_profile->soc_facebook) ?>" />
											<strong class="placeholder">Facebook Username or Page</strong>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 placeholder-container">
											<input class="has-placeholder form-control in-text col-xs-12 twitter-profile-id" type="text" 
												name="soc_twitter" placeholder="Twitter" 
												value="<?= $vd->esc(@$vd->m_profile->soc_twitter) ?>" />
											<strong class="placeholder">Twitter Username</strong>
										</div>
									</div>
									<div class="row form-group">
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 placeholder-container">
											<input class="has-placeholder form-control in-text col-xs-12 gplus-profile-id" type="text" 
												name="soc_gplus" placeholder="Google Plus" 
												value="<?= $vd->esc(@$vd->m_profile->soc_gplus) ?>" />
											<strong class="placeholder">Google Plus ID</strong>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 placeholder-container">
											<input class="has-placeholder form-control in-text col-xs-12 youtube-profile-id" type="text" 
												name="soc_youtube" placeholder="YouTube Username" 
												value="<?= $vd->esc(@$vd->m_profile->soc_youtube) ?>" />
											<strong class="placeholder">YouTube Username</strong>
										</div>
									</div>
									<div class="row form-group marbot-20">
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 placeholder-container">
											<input class="has-placeholder form-control in-text col-xs-12 linkedin-profile-id" type="text" 
												name="soc_linkedin" placeholder="LinkedIn Profile" 
												value="<?= $vd->esc(@$vd->m_profile->soc_linkedin) ?>" />
											<strong class="placeholder">LinkedIn Profile</strong>
										</div>
									</div>
									
								</fieldset>
															
								<div class="row form-group">
									<div class="col-lg-12">
										<button type="submit" name="is_continue" value="1"
											class="btn btn-primary marbot-30">Continue</button>
									</div>
								</div>
							</div>

							<div class="col-lg-4 col-md-4 form-col-2">
								<div class="aside_tips" id="locked_aside">
									<?= $ci->load->view('manage/writing/partials/editor-comments') ?>
								</div>
							</div>
						</div>
					</form>

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
		</div>
		
	</div>
</div>