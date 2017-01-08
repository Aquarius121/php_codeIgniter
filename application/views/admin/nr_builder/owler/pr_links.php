<div id="feedback_area"></div>
<form class="row-fluid" action="<?= $ci->uri->uri_string ?>" method="post" id="company_edit_form">
	<div class="span12">		
		<div class="content">
			
			<div class="span12">
				<section class="form-section user-details">
					<h4 class="marbot-20"><?= $vd->company_name ?></h4>

					<div class="row-fluid">
						<div class="span12 relative" id="select-country">
							<?php foreach ($vd->results as $result): ?>
								<div class="marbot-20">
									<a href='<?= $result->actual_news_url ?>' 
										target='_blank'><?= $result->title ?></a>
								</div>
							<?php endforeach ?>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
</form>