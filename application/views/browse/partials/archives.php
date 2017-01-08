<?php if (!$ci->is_common_host && count($vd->nr_listed_archives)): ?>
<section class="accordian al-block al-adr">
	<h3 class="accordian-toggle">
		<i class="accordian-icon"></i>
		Archives
	</h3>
	<ul class="accordian-content links-list nav-activate">
		<?php foreach ($vd->nr_listed_archives as $date): ?>
		<li>
			<a data-on="^browse/month/<?= $date->format('Y/m') ?>"
				href="browse/month/<?= $date->format('Y/m') ?>">
				<i class="fa fa-hand-right"></i>
				<?= $date->format('F Y') ?>
			</a>
		</li>
		<?php endforeach ?>
	</ul>
</section>
<?php endif ?>