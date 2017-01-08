<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<h2>Report Settings</h2>
			</div>
		</div>
	</header>

	<form class="tab-content required-form" method="post" action="manage/analyze/settings/save">

	<div class="row">
		<div class="col-lg-8 col-md-7 form-col-1">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<?php if ($this->newsroom->is_active): ?>
					<fieldset class="inline-radios">
						<legend>Newsroom Stats Report</legend>						
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12" type="text" 
									name="overall_email" 
									placeholder="Email Addresses (Comma Separated)"
									value="<?= $vd->esc(@$vd->settings->overall_email) ?>" />
							</div>
						</div>
						<label class="radio-container">
							<input type="radio" name="overall_when" value="weekly" 
								<?= value_if_test((@$vd->settings->overall_when == 'weekly'), 'checked') ?> /> 
							<span class="radio"></span>
							<span class="muted">Once a</span><span> week</span>
						</label>
						<label class="radio-container">
							<input type="radio" name="overall_when" value="monthly" 
								<?= value_if_test((@$vd->settings->overall_when == 'monthly'), 'checked') ?> /> 
							<span class="radio"></span>
							<span class="muted">Once a</span><span> month</span>
						</label>
						<label class="radio-container">
							<input type="radio" name="overall_when" value="" 
								<?= value_if_test(!@$vd->settings->overall_when, 'checked') ?> /> 
							<span class="radio"></span>
							<span>Never</span>
						</label>							
					</fieldset>
					<?php endif ?>
						
					<fieldset class="nomarbot inline-radios">
						<legend>Press Release Stats Report</legend>
						<div class="row form-group">
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-10" type="text" 
									name="pr_email" 
									placeholder="Email Addresses (Comma Separated)"
									value="<?= $vd->esc(@$vd->settings->pr_email) ?>" />
							</div>
						</div>	
						<label class="radio-container">
							<input type="radio" name="pr_when" value="1" 
								<?= value_if_test((@$vd->settings->pr_when == '1'), 'checked') ?> /> 
							<span class="radio"></span>
							<span class="muted">After </span><span>1 day</span>
						</label>					
						<label class="radio-container">
							<input type="radio" name="pr_when" value="3" 
								<?= value_if_test((@$vd->settings->pr_when == '3'), 'checked') ?> /> 
							<span class="radio"></span>
							<span class="muted">After </span><span>3 days</span>
						</label>
						<label class="radio-container">
							<input type="radio" name="pr_when" value="7" 
								<?= value_if_test((@$vd->settings->pr_when == '7'), 'checked') ?> /> 
							<span class="radio"></span>
							<span class="muted">After </span><span>7 days</span>
						</label>
						<label class="radio-container">
							<input type="radio" name="pr_when" value="30" 
								<?= value_if_test((@$vd->settings->pr_when == '30'), 'checked') ?> /> 
							<span class="radio"></span>
							<span class="muted">After </span><span>30 days</span>
						</label>
						<label class="radio-container">
							<input type="radio" name="pr_when" value="" 
								<?= value_if_test(!@$vd->settings->pr_when, 'checked') ?> /> 
							<span class="radio"></span>
							<span>Never</span>
						</label>
					</fieldset>
						
				</div>
			</div>
		</div>


		<div class="col-lg-4 col-md-5 form-col-2">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="alert alert-info">
						<strong>Remember!</strong> You can view stats at any time
						using the control panel.
					</div>
					<div id="locked_aside">
						<button type="submit" name="publish" value="1" 
							class="btn btn-primary">Save</button>
						<button type="submit" name="test" value="1" 
							class="btn btn-default">Save and Test</button>
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

					
				</div>
			</div>
		</div>
	</div>
	</form>
</div>