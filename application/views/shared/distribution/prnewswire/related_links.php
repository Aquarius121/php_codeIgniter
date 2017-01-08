<?php if ($result->rel_res_pri_link || $result->rel_res_sec_link): ?>
	<p style="font-weight: bold;">Related Links</p>
	<?php if ($result->rel_res_pri_link): ?>
		<p>
			<a href="<?= $vd->esc($result->rel_res_pri_link) ?>" target="_blank">
				<?php if ($result->rel_res_pri_title): ?>
					<?= $vd->esc($result->rel_res_pri_title) ?>
				<?php else: ?>
					<?= $vd->esc($result->rel_res_pri_link) ?>
				<?php endif ?>
			</a>
		</p>
	<?php endif ?>
	<?php if ($result->rel_res_sec_link): ?>
		<p>
			<a href="<?= $vd->esc($result->rel_res_sec_link) ?>" target="_blank">
				<?php if ($result->rel_res_sec_title): ?>
					<?= $vd->esc($result->rel_res_sec_title) ?>
				<?php else: ?>
					<?= $vd->esc($result->rel_res_sec_link) ?>
				<?php endif ?>
			</a>
		</p>
	<?php endif ?>
	<p></p>
<?php endif ?>