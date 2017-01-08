<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Canned Message</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<?php if ($vd->is_delete): ?>
<?= $ci->load->view('admin/settings/partials/canned_delete_before') ?>
<?php endif ?>

<div class="row-fluid">
	<div class="span12">		
		
		<div class="content">
			<form class="tab-content required-form" method="post" action="<?= $ci->uri->uri_string ?>">
				<div class="row-fluid">					
					<div class="span12">
						<section class="form-section basic-information">
							<h2>Basic Information</h2>
							<ul>
								<li>
									<input class="in-text span12 required" type="text" 
										name="title" placeholder="Title"
										data-required-name="Title"
										value="<?= $vd->esc(@$vd->canned->title) ?>" />
								</li>
								<li class="marbot-20">
									<textarea class="in-text in-content span12 required" id="content"
										name="content" placeholder="Content"><?= 
											$vd->esc(@$vd->canned->content) 
									?></textarea>
									<script>
										
									$(function() {

										var content = $("#content");
										window.init_editor(content, {
											fillEmptyBlocks: false,
											height: 500,
										});

									});

									</script>
								</li>
								<li>
									<button type="submit" name="save" value="1"
										class="bt-orange">Save Message</button>
								</li>
							</ul>							
						</section>
					</div>
				</div>
			</form>
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