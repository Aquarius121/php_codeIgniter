<section class="home-latest-news latest-news">
	<div class="container">
		
		<div class="row news-center-other-cats">
			
			<div class="col-xs-4">
				<?php $lines_out = -1; ?>
				<?php $divider = ceil($vd->lines_count / 3); ?>
				<?php foreach ($vd->beat_groups as $group): ?>
					<?php if (!$group->is_listed) continue; ?>
					<?php if (++$lines_out > 0 && $lines_out % $divider === 0): ?>
			<?/////?></div>
			<?/////?><div class="col-xs-4">
					<?php endif ?>
					<h3>
						<a href="newsroom/rss/<?= $group->slug ?>"><i class="fa fa-rss"></i></a>
						<a href="newsroom/<?= $group->slug ?>"><?= $vd->esc($group->name) ?></a>
					</h3>
					<?php foreach ($group->beats as $beat): ?>
						<?php if (!$beat->is_listed) continue; ?>
						<?php if ($beat->id == $beat->group_id) continue; ?>
						<?php if (++$lines_out > 0 && $lines_out % $divider === 0): ?>
			<?////////?></div>
			<?////////?><div class="col-xs-4">
						<?php endif ?>
						<h4>
							<a href="newsroom/rss/<?= $beat->slug ?>"><i class="fa fa-rss"></i></a>
							<a href="newsroom/<?= $beat->slug ?>"><?= $vd->esc($beat->name) ?></a>
						</h4>
					<?php endforeach ?>
				<?php endforeach ?>
			</div>
			
		</div>
		
	</div>
</section>