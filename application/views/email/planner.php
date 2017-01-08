Hello.
<br><br>
A new PR Planner entry has been created. 
<br><br>
<a href="<?= $ci->website_url(sprintf('admin/other/planner/claim/%s', $vd->planner->id)) ?>">Claim and Review</a>
<br>
<a href="<?= $ci->website_url(sprintf('admin/other/planner/review/%s', $vd->planner->id)) ?>">Just Review</a>
<br><br>
<hr style="max-width:30%;" align="left">
<br>
<ul style="list-style:none;color:#666;margin:0;padding:0;">
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Contact Name</strong></div>
		<div><?= $vd->esc($vd->rdata->contact_name) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Company Name</strong></div>
		<div><?= $vd->esc($vd->rdata->company_name) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Email</strong></div>
		<div><?= $vd->esc($vd->rdata->email) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Phone</strong></div>
		<div><?= $vd->esc($vd->rdata->phone) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Company or Individual</strong></div>
		<div><?= $vd->esc($vd->rdata->company_or_individual) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Private or Public</strong></div>
		<div><?= $vd->esc($vd->rdata->private_or_public) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Submit PR for clients?</strong></div>
		<div><?= $vd->esc($vd->rdata->is_agency) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Team size</strong></div>
		<div><?= $vd->esc($vd->rdata->team_size) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Industries</strong></div>
		<div>
			<?php if (is_array($vd->rdata->industries)): ?>
				<?php foreach ($vd->rdata->industries as $val): ?>
					<?= $vd->esc($val) ?><br>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Press release goals</strong></div>
		<div>
			<?php if (is_array($vd->rdata->press_release_goals)): ?>
				<?php foreach ($vd->rdata->press_release_goals as $val): ?>
					<?= $vd->esc($val) ?><br>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>PR frequency</strong></div>
		<div><?= $vd->esc($vd->rdata->how_often) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Target audience</strong></div>
		<div>
			<?php if (is_array($vd->rdata->target_audience)): ?>
				<?php foreach ($vd->rdata->target_audience as $val): ?>
					<?= $vd->esc($val) ?><br>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Do you use external media DB?</strong></div>
		<div><?= $vd->esc($vd->rdata->use_external_media_database) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Which external media database?</strong></div>
		<div>
			<?php if ($vd->rdata->which_media_database == "Other"): ?>
				<?= $vd->esc($vd->rdata->which_media_database) ?>
				<br><?= $vd->esc($vd->rdata->which_media_database_other) ?>
			<?php else: ?>
				<?= $vd->esc($vd->rdata->which_media_database) ?>
			<?php endif ?>
		</div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Social media platforms</strong></div>
		<div>
			<?php if (is_array($vd->rdata->social_platforms)): ?>
				<?php foreach ($vd->rdata->social_platforms as $val): ?>
					<?= $vd->esc($val) ?><br>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Need PR writing?</strong></div>
		<div><?= $vd->esc($vd->rdata->need_writing) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Need media pitching?</strong></div>
		<div><?= $vd->esc($vd->rdata->media_pitching) ?></div>
	</li>
	<li style="margin:0;padding:0;margin-bottom:15px">
		<div><strong>Have a newsroom or similar?</strong></div>
		<div><?= $vd->esc($vd->rdata->have_newsroom) ?></div>
	</li>
</ul>