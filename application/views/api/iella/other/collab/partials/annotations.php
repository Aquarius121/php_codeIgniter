<?php if (count($vd->diff->annotations)): ?>
<h4 style="color:#1357a8;margin:20px 0 15px 0;">Annotations</h4>
<p>The following new annotations have been created.</p>
<div style="padding:10px 20px">
	<?php foreach ($vd->diff->annotations as $k => $annotation): ?>
		<div style="border:1px solid #ddd;border-radius:4px;margin-bottom:15px;">
			<div style="color:#444;padding:5px 8px 6px 8px;border-bottom:1px solid #ddd;">
				<?= $vd->esc($vd->cut($annotation->text, 500)) ?>
			</div>
			<div style="color:#7E9EB3;padding:5px 8px 6px 8px;font-size:14px;overflow:hidden;text-align:right;">
				<?= $vd->esc($vd->users[$annotation->suid]->name) ?>,
				<?= Date::difference_in_words(Date::utc($annotation->date_created)) ?>
			</div>
		</div>		
		<?php if ($k === 9): ?>
			<p>... and <?= count($vd->diff->annotations)-10 ?> more annotations.</p>
			<?php break; ?>
		<?php endif ?>
	<?php endforeach ?>
</div>
<?php endif ?>