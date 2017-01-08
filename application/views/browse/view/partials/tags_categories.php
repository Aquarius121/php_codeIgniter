<?php $tags = $vd->m_content->get_tags(); ?>
<?php $beats = $vd->m_content->get_beats(); ?>
<?php if (!$tags && !$beats) return; ?>

<div class="row-fluid rb-categories-block">
	
	<?php if ($beats): ?>
	<?php if ($tags && $beats): ?>
	<div class="span6">
	<?php else: ?>
	<div class="span12">
	<?php endif ?>
		<section class="rb-categories">
			<h3>
				<i class="fa fa-list"></i> Categories:
			</h3>
			<p>
				<?php foreach ($beats as $k => $beat): ?>
					<a target="_blank" href="<?= $beat->url() ?>"><?= $vd->esc($beat->name) 
						?></a><?= ((($k + 1) < count($beats)) ? ', ' : '') ?>
				<?php endforeach ?>
			</p>
		</section>
	</div>
	<?php endif ?>
	
	<?php if ($tags): ?>
	<?php if ($tags && $beats): ?>
	<div class="span6">
	<?php else: ?>
	<div class="span12">
	<?php endif ?>
		<section class="rb-categories rb-tags">
			<h3> 
				<i class="fa fa-tags"></i> Tags:
			</h3>
			<p>
				<?php foreach($tags as $i => $tag) : ?>
					<a target="_blank" href="<?= Tag::url($tag) ?>"><?= $vd->esc($tag) ?></a><?=
					(($i === count($tags) - 1) ? '' : ', ') ?>
				<?php endforeach; ?>
			</p>
		</section>
	</div>
	<?php endif ?>
	
</div>