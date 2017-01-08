<?= $ci->load->view('manage/publish/partials/breadcrumbs') ?>
<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->m_content): ?>
					<h2>Edit News Content</h2>
				<?php else: ?>
					<h2>Add News Content</h2>
				<?php endif ?>
			</div>
		</div>
	</header>

	<form class="tab-content required-form has-premium" method="post"
		action="manage/publish/news/edit/save/<?= @$vd->m_content->id ?>" id="content-form">
	
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
							<div class="col-lg-12">
								<input class="form-control in-text col-lg-12 required" type="text" name="title" 
									id="title" placeholder="Enter Title of News Content"
									maxlength="<?= $ci->conf('title_max_length') ?>"
									value="<?= $vd->esc(@$vd->m_content->title) ?>" data-required-name="Title" />
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-12">		
								<textarea class="form-control in-text col-lg-12 required" id="summary" name="summary" 
									data-required-name="Summary" placeholder="Enter Summary of News Content" rows="5"
									><?= $vd->esc(@$vd->m_content->summary) ?></textarea>
								<p class="help-block" id="summary_countdown_text">
									<span id="summary_countdown"></span> Characters Left</p>
								<script>
								
								defer(function() {

									$("#summary").limit_length(<?= $ci->conf('summary_max_length') ?>, 
										$("#summary_countdown_text"), 
										$("#summary_countdown"));
								});
								
								</script>
							</div>
						</div>

						<div class="row form-group">
							<div class="col-lg-12">
								
								<textarea class="in-text in-content col-lg-12 required" id="content"
									data-required-name="Content Body" name="content" 
									placeholder="News Content Body"><?= 
										$ci->load->view('partials/html-content', 
											array('content' => @$vd->m_content->content)) 
								?></textarea>
								<script>
								defer(function() {
									window.init_editor($("#content"), { height: 400 });
								});								
								</script>
							</div>
						</div>
					</fieldset>

					<?= $ci->load->view('manage/publish/partials/tags') ?>
					<?= $ci->load->view('manage/publish/partials/web-images') ?>						
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
						<?= $this->load->view('manage/publish/partials/select-three-beats') ?>
						<?= $this->load->view('manage/publish/partials/publish-date') ?>
						
						<script>
						
						$(function() {
							
							var selects = $("#locked_aside select.category");
							selects.on_load_select();
								
							$(window).load(function() {
								selects.eq(0).addClass("required");
							});
							
						});
						
						</script>

						<?= $ci->load->view('manage/publish/partials/save-buttons') ?>

						<div class="marbot-20"></div>
						<div class="ta-center">
							Want to add a News (content) from another site? 
							<a href="manage/publish/news/edit/external">Click here.</a>
						</div>
						
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