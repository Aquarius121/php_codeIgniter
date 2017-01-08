<div class="panel panel-default quick-stats">
	<div class="panel-heading">
		<h3 class="panel-title">Quick Stats 
		<small><a href="manage/analyze/content/pr/published">View all Stats</a></small></h3>
	</div>
	<div class="panel-body">
		<ul>
			<li>Press Release Views 
				<small class="text-muted smaller pad-10h">Week</small>
				<span><?= $vd->pr_hits_week ?></span></li>
			<li class="<?= value_if_test(!$vd->is_stat_muted, 'divider') ?>">Press Release Views 
				<small class="text-muted smaller pad-10h">Month</small>
				<span><?= $vd->pr_hits_month ?></span></li>
			<?php if (!$vd->is_stat_muted): ?>
			<li>Newsroom Views 
				<small class="text-muted smaller pad-10h">Week</small>
				<span><?= $vd->nr_hits_week ?></span></li>
			<li>Newsroom Views 
				<small class="text-muted smaller pad-10h">Month</small> 
				<span><?= $vd->nr_hits_month ?></span></li>
			<?php endif ?>
		</ul>
	</div>
</div>