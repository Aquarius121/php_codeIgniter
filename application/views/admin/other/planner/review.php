<div class="row-fluid marbot-20">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>PR Planner Review</h1>
				</div>			
				<div class="span6">
					<div class="pull-right">
						<a href="planner/one/<?= $vd->planner->id ?>"
							class="nomarbot btn">Edit</a>
						<?php if (!$vd->planner->claim_user_id): ?>
						<a href="admin/other/planner/claim/<?= $vd->planner->id ?>"
							class="nomarbot btn btn-info">Claim</a>
						<?php endif ?>
					</div>
				</div>	
			</div>
		</header>
	</div>
</div>

<section class="row-fluid planner marbot-50">
	<div class="span12">
		<ul class="planner-review-list">
			<li class="single">
				<div class="name">Contact Name</div>
				<div class="value"><?= $vd->esc($vd->rdata->contact_name) ?></div>
			</li>
			<li class="single">
				<div class="name">Company Name</div>
				<div class="value"><?= $vd->esc($vd->rdata->company_name) ?></div>
			</li>
			<li class="single">
				<div class="name">Email</div>
				<div class="value"><?= $vd->esc($vd->rdata->email) ?></div>
			</li>
			<li class="single">
				<div class="name">Phone</div>
				<div class="value"><?= $vd->esc($vd->rdata->phone) ?></div>
			</li>
			<li class="single">
				<div class="name">Company or Individual</div>
				<div class="value"><?= $vd->esc($vd->rdata->company_or_individual) ?></div>
			</li>
			<li class="single">
				<div class="name">Private or Public</div>
				<div class="value"><?= $vd->esc($vd->rdata->private_or_public) ?></div>
			</li>
			<li class="single">
				<div class="name">Submit PR for clients?</div>
				<div class="value
					<?= value_if($vd->rdata->is_agency == 'Yes', 'status-true strong') ?>
					<?= value_if($vd->rdata->is_agency == 'No', 'status-false strong') ?>">
					<?= $vd->esc($vd->rdata->is_agency) ?>
			</li>
			<li class="single">
				<div class="name">Team size</div>
				<div class="value"><?= $vd->esc($vd->rdata->team_size) ?></div>
			</li>
			<li class="single">
				<div class="name">PR frequency</div>
				<div class="value"><?= $vd->esc($vd->rdata->how_often) ?></div>
			</li>			
			<li class="single">
				<div class="name">Need PR writing?</div>
				<div class="value
					<?= value_if($vd->rdata->need_writing == 'Yes', 'status-true strong') ?>
					<?= value_if($vd->rdata->need_writing == 'No', 'status-false strong') ?>">
					<?= $vd->esc($vd->rdata->need_writing) ?>
				</div>
			</li>
			<li class="single">
				<div class="name">Need media pitching?</div>
				<div class="value
					<?= value_if($vd->rdata->media_pitching == 'Yes', 'status-true strong') ?>
					<?= value_if($vd->rdata->media_pitching == 'No', 'status-false strong') ?>">
					<?= $vd->esc($vd->rdata->media_pitching) ?>
				</div>
			</li>
			<li class="single">
				<div class="name">Have a newsroom or similar?</div>				
				<div class="value
					<?= value_if($vd->rdata->have_newsroom == 'Yes', 'status-true strong') ?>
					<?= value_if($vd->rdata->have_newsroom == 'No', 'status-false strong') ?>">
					<?= $vd->esc($vd->rdata->have_newsroom) ?>
			</li>			
			<li class="single nomarbot">
				<div class="name">Do you use external media DB?</div>
				<div class="value
					<?= value_if($vd->rdata->use_external_media_database == 'Yes', 'status-true strong') ?>
					<?= value_if($vd->rdata->use_external_media_database == 'No', 'status-false strong') ?>">
					<?= $vd->esc($vd->rdata->use_external_media_database) ?>
			</li>
			<li>
				<div class="name">Which external media database?</div>
				<div class="value">
					<?php if ($vd->rdata->which_media_database == "Other"): ?>
						<?= $vd->esc($vd->rdata->which_media_database) ?>
						<br><?= $vd->esc($vd->rdata->which_media_database_other) ?>
					<?php else: ?>
						<?= $vd->esc($vd->rdata->which_media_database) ?>
					<?php endif ?>
				</div>
			</li>
			<li>
				<div class="name">Industries</div>
				<div class="value">
					<?php if (is_array($vd->rdata->industries)): ?>
						<?php foreach ($vd->rdata->industries as $val): ?>
							<?= $vd->esc($val) ?><br>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			</li>
			<li>
				<div class="name">Press release goals</div>
				<div class="value">
					<?php if (is_array($vd->rdata->press_release_goals)): ?>
						<?php foreach ($vd->rdata->press_release_goals as $val): ?>
							<?= $vd->esc($val) ?><br>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			</li>
			<li>
				<div class="name">Target audience</div>
				<div class="value">
					<?php if (is_array($vd->rdata->target_audience)): ?>
						<?php foreach ($vd->rdata->target_audience as $val): ?>
							<?= $vd->esc($val) ?><br>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			</li>
			<li>
				<div class="name">Social media platforms</div>
				<div class="value">
					<?php if (is_array($vd->rdata->social_platforms)): ?>
						<?php foreach ($vd->rdata->social_platforms as $val): ?>
							<?= $vd->esc($val) ?><br>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			</li>
		</ul>
	</div>
</section>