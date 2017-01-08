<main class="main planner-header">
	<div class="container">
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Newswire Press Release Planner</h1>
				</header>
			</div>
			<div class="col-sm-1">
			</div>
		</div>
	</div>
</main>

<?= $ci->load->view('website/partials/feedback') ?>

<section class="container planner marbot-50">
	<div class="row">
		<div class="col-md-12">
			<h2 class="marbot-60">
				Review Planner Entry
				&nbsp;&nbsp;&nbsp;
				<a href="planner/one/<?= $vd->planner->id ?>" class="nomarbot btn btn-success btn-sm-padding">Edit</a>
			</h2>
			<ul class="planner-review-list">
				<li>
					<div class="name">Contact Name</div>
					<div class="value"><?= $vd->esc($vd->rdata->contact_name) ?></div>
				</li>
				<li>
					<div class="name">Company Name</div>
					<div class="value"><?= $vd->esc($vd->rdata->company_name) ?></div>
				</li>
				<li>
					<div class="name">Email</div>
					<div class="value"><?= $vd->esc($vd->rdata->email) ?></div>
				</li>
				<li>
					<div class="name">Phone</div>
					<div class="value"><?= $vd->esc($vd->rdata->phone) ?></div>
				</li>
				<li>
					<div class="name">Company or Individual</div>
					<div class="value"><?= $vd->esc($vd->rdata->company_or_individual) ?></div>
				</li>
				<li>
					<div class="name">Private or Public</div>
					<div class="value"><?= $vd->esc($vd->rdata->private_or_public) ?></div>
				</li>
				<li>
					<div class="name">Submit PR for clients?</div>
					<div class="value"><?= $vd->esc($vd->rdata->is_agency) ?></div>
				</li>
				<li>
					<div class="name">Team size</div>
					<div class="value"><?= $vd->esc($vd->rdata->team_size) ?></div>
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
					<div class="name">Do you use external media DB?</div>
					<div class="value"><?= $vd->esc($vd->rdata->use_external_media_database) ?></div>
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
					<div class="name">Social media platforms</div>
					<div class="value">
						<?php if (is_array($vd->rdata->social_platforms)): ?>
							<?php foreach ($vd->rdata->social_platforms as $val): ?>
								<?= $vd->esc($val) ?><br>
							<?php endforeach ?>
						<?php endif ?>
					</div>
				</li>
				<li>
					<div class="name">Need PR writing?</div>
					<div class="value"><?= $vd->esc($vd->rdata->need_writing) ?></div>
				</li>
				<li>
					<div class="name">Need media pitching?</div>
					<div class="value"><?= $vd->esc($vd->rdata->media_pitching) ?></div>
				</li>
				<li>
					<div class="name">Have a newsroom or similar?</div>
					<div class="value"><?= $vd->esc($vd->rdata->have_newsroom) ?></div>
				</li>
			</ul>
		</div>
	</div>
</section>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/masonry.min.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<script>
	
	$(function() {

		var container = $(".planner-review-list");
		var _columnized = container.masonry({
			gutter: 0
		});

	});

</script>