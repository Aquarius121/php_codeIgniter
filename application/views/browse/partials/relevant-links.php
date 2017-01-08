<?php if (!$ci->is_common_host): ?>
<?php if (@$vd->nr_custom->rel_res_pri_link ||
          @$vd->nr_custom->rel_sec_pri_link ||
          @$vd->nr_custom->rel_ter_pri_link): ?>

<section class="accordian al-block">
	<h3 class="accordian-toggle">
		<i class="accordian-icon"></i>
		Relevant Links
	</h3>
	<ul class="accordian-content links-list">
		<?php if (@$vd->nr_custom->rel_res_pri_link): ?>
		<li>
			<a href="<?= $vd->esc($vd->nr_custom->rel_res_pri_link) ?>" rel="nofollow">
				<?= $vd->esc($vd->nr_custom->rel_res_pri_title) ?>
			</a>
		</li>
		<?php endif ?>
		<?php if (@$vd->nr_custom->rel_res_sec_link): ?>
		<li>
			<a href="<?= $vd->esc($vd->nr_custom->rel_res_sec_link) ?>" rel="nofollow">
				<?= $vd->esc($vd->nr_custom->rel_res_sec_title) ?>
			</a>
		</li>
		<?php endif ?>
		<?php if (@$vd->nr_custom->rel_res_ter_link): ?>
		<li>
			<a href="<?= $vd->esc($vd->nr_custom->rel_res_ter_link) ?>" rel="nofollow">
				<?= $vd->esc($vd->nr_custom->rel_res_ter_title) ?>
			</a>
		</li>
		<?php endif ?>
	</ul>
</section>

<?php endif ?>
<?php endif ?>