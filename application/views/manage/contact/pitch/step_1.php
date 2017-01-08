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
				<h2>Great! Let's Select Your Content</h2>
			</div>
		</div>
	</header>

	

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<?= $ci->load->view('manage/contact/pitch/partials/progress-bar') ?>
					<form action="" method="post" class="writing-session-form required-form marbot-30 has-premium">
						<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
						<fieldset>
							<div class="row for-group">
								<div class="col-lg-6 col-lg-offset-3 ">
									<legend>Select the content you want to share 
										<a title="Choose the content you want to share with the media.
											This content is what we will revolve the pitch around." 
											rel="tooltip" class="tl" href="#">
											<i class="fa fa-fw fa-question-circle"></i>
										</a>
									</legend>
			
									<select name="content" id="select-content" data-size="5"
										data-show-subtext="true" data-required-use-parent="1"
										data-required-name="Content" data-live-search="true"
										class="form-control show-menu-arrow marbot-20 selectpicker">
										<?php foreach ($vd->content as $m_content): ?>
										<option value="<?= $m_content->id ?>"
											data-subtext="<?= Date::out($m_content->date_publish)->format('F jS Y') ?>"
											<?= value_if_test($m_content->id == @$vd->pw_raw_data->content_id, 'selected') ?>>
											<?= $vd->esc($m_content->title) ?> (<?= Model_Content::short_type($m_content->type) ?>)
										</option>
										<?php endforeach ?>
									</select>
									<script>
										$(function() {

											var select = $("#select-content");
											select.on_load_select({ size: 10 });
											window.on_load_select(function() {
												select.addClass("required");
												select.trigger("change");
											});

											select.on("change", function() {
												select.toggleClass("invalid", !select.val());
											});

										});
									</script>
											
								</div>
							</div>
							<div class="row for-group">
								<div class="col-lg-6 col-lg-offset-3">			
									<button class="btn btn-primary pull-right nomar" name="is_continue" value="1">
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
