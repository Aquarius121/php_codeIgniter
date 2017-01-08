<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Edit Press Release</h1>
				</div>
				<div class="span6">
					<div class="pull-right">
					</div>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="content">
			<form class="tab-content required-form pr-form has-premium" method="post" 
                action="reseller/publish/edit_save" id="content-form">
				<input type="hidden" name="id" value="<?= @$vd->m_content->id ?>" />
				
				<div class="row-fluid">
					<div class="span8 information-panel">
						
						<section class="hidden">
							<input type="checkbox" name="is_premium" id="is-premium" value="1" checked />
						</section>
								
						<?= $ci->load->view('manage/publish/partials/pr-basic-information') ?>
						<?= $ci->load->view('manage/publish/partials/supporting-quote') ?>
						<?= $ci->load->view('manage/publish/partials/tags') ?>
                        <?= $ci->load->view('reseller/publish/partials/company-info') ?>
						<?= $ci->load->view('manage/publish/partials/web-images') ?>
						<?= $ci->load->view('manage/publish/partials/web-files') ?>
						<?= $ci->load->view('manage/publish/partials/relevant-resources') ?>
						<?= $ci->load->view('manage/publish/partials/web-video') ?>
						
					</div>
						
					<aside class="span4 aside aside-fluid">
						<div class="aside-properties" id="locked_aside">

							<?= $this->load->view('manage/publish/partials/status') ?>

							<section class="ap-block ap-properties">
								<ul>
									<?= $this->load->view('manage/publish/partials/select-category') ?>
									<script>
									
									$(function() {
										
										var selects = $("#locked_aside select.category");
										selects.on_load_select();
											
										$(window).load(function() {
											selects.eq(0).addClass("required");
										});
										
									});
									
									</script>
									<li>
										<?php if (@$vd->m_content->is_published || @$vd->m_content->is_under_review): ?>
										<div class="row-fluid">
											<div class="span5 offset3">
												<button type="submit" name="is_preview" value="1" 
													class="span11 bt-silver">Preview</button>
											</div>
											<div class="span4">
												<button type="submit" name="publish" value="1" 
													class="span12 bt-silver bt-orange">Save</button>
											</div>
										</div>
										<?php else: ?>
										<div class="row-fluid marbot">
											<div class="span7">
												<button type="submit" name="is_draft" value="1" 
													class="span12 bt-silver">Save Draft</button>
											</div>
											<div class="span5">
												<button type="submit" name="is_preview" value="1" 
													class="span12 bt-silver pull-right">Preview</button>
											</div>
										</div>										
										<div class="row-fluid">											
											<div class="span12">
												<button type="submit" name="publish" value="1" 
													class="span12 bt-orange pull-right">Publish</button>
											</div>
										</div>
										<?php endif ?>
									</li>
								</ul>
							</section>
							
						</div>
					</aside>
					
					<script>
					
					$(function() {
						
						var options = { offset: { top: 20 } };
						$.lockfixed("#locked_aside", options);
						
					});
					
					</script>
					
				</div>
			</form>
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootbox.min.js');
	$loader->add('js/required.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>