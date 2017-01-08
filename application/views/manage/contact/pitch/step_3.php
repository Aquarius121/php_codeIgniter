<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="container-fluid">

	<header>
		<div class="row">
			<div class="col-lg-12">
				<h2>What's Your Story....</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<dic class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<?= $ci->load->view('manage/contact/pitch/partials/progress-bar') ?>

					<div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 form-col-1">
					
					<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
						<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
						<fieldset>
							<legend>What keyword best describes your product or services? 
								<a title="" class="tl" href="#" rel="tooltip"
									data-original-title="Choosing keywords will help us better
									target your media contacts for this pitch campaign. Please 
									choose up to three keywords separated by comma.">
									<i class="fa fa-fw fa-question-circle"></i>
								</a>
							</legend>

							<div class="row form-group">
								<div class="col-lg-12">
									<input type="text" class="form-control in-text required"  
										data-required-name="Keyword"
										name="keyword" id="keyword" placeholder="Keyword" 
										value="<?= $vd->esc(@$vd->pw_raw_data->keyword) ?>">
								</div>
							</div>
						</fieldset>
						
						<fieldset>
							<legend>Ok, please tell us what you want us to highlight 
								<a title="" class="tl" href="#" rel="tooltip"
									data-original-title="Provide us with the main idea of your story.">
									<i class="fa fa-fw fa-question-circle"></i>
								</a>
							</legend>

							<div class="row form-group">
								<div class="col-lg-12">
									<textarea id="pitch_highlight" name="pitch_highlight" 
										class="form-control in-text required"
										data-required-name="What to Highlight" rows="5"
										><?= $vd->esc(@$vd->pw_raw_data->pitch_highlight)?></textarea>
								</div>
							</div>
						</fieldset>

						<fieldset>
							<legend>
								Additional Comments <span>(Optional)</span>
									<a title="" class="tl" href="#" rel="tooltip"
										data-original-title="Please add any additional comments here.">
										<i class="fa fa-fw fa-question-circle"></i>
									</a>
							</legend>


							<div class="row form-group">
								<div class="col-lg-12">
									<textarea id="additional_comments" name="additional_comments" 
										class="form-control in-text col-lg-12 in-optional" rows="5"
										><?= $vd->esc(@$vd->pw_raw_data->additional_comments)?></textarea>
								</div>
							</div>
						</fieldset>

						<fieldset class="steps-footer">
							<div class="row">
								<div class="col-lg-3 col-xs-6">
									<a href="manage/contact/pitch/process/<?= $vd->m_pw_session->id ?>/2"
										class="col-lg-12 ta-center btn btn-default"><b>&laquo; Back</b></a>
								</div>
								
								<div class="col-lg-3 col-lg-offset-6 col-xs-6">
									<button type="submit" class="col-lg-12 btn btn-primary pull-right nomar" 
										name="is_continue" value="1">
										Continue &raquo;</button>
								</div>
							</div>
						</fieldset>	
					</form>
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
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>