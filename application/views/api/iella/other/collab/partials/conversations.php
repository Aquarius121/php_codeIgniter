<?php if (count($vd->diff->conversations)): ?>
<h4 style="color:#1357a8;margin:20px 0 15px 0;">Discussions</h4>
<p>
	<?php $count_comments = (int) array_reduce($vd->diff->conversations, 
		function($c, $i) { return $c + count($i->entries); }, 0) ?>
	<?php $count_threads = count($vd->diff->conversations) ?>
	There <?= value_if_test($count_threads == 1, 'is', 'are') ?> <?= $count_comments ?>
	new <?= value_if_test($count_threads == 1, 'comment', 'comments') ?> in 
	<?= $count_threads ?>
	discussion <?= value_if_test($count_threads == 1, 'thread', 'threads') ?>.
</p>
<div style="padding:10px 20px">
	<?php foreach ($vd->diff->conversations as $k => $cdiff): ?>
		<?php $annotation = $vd->annotations[$cdiff->suid][$cdiff->id] ?>
		<div style="border:1px solid #ddd;border-radius:4px;margin-bottom:15px;">
			<div style="padding:5px 8px 6px 8px;color:#999">
				<?= $vd->esc($vd->cut($annotation->text, 100)) ?>
			</div>
			<?php foreach ($cdiff->entries as $k => $entry): ?>
			<div style="color:#333;padding:5px 8px 6px 8px;border-top:1px solid #ddd;overflow:hidden;">
				<span style="float:right;width:auto;color:#7E9EB3;font-size:14px;margin:0 0 0 10px;">
					<?= $vd->esc($vd->users[$entry->suid]->name) ?>,
					<?= Date::difference_in_words(Date::utc($entry->date)) ?>
				</span>
				<?= $vd->esc($vd->cut($entry->message, 200)) ?>				
			</div>
			<?php if ($k === 4): ?>
				<div style="color:#999;padding:5px 8px 6px 8px;border-top:1px solid #ddd;overflow:hidden;">
					... and <?= count($cdiff->entries)-5 ?> more comments.
				</div>
				<?php break; ?>
			<?php endif ?>
			<?php endforeach ?>
		</div>		
		<?php if ($k === 4): ?>
			<p>... and <?= count($vd->diff->conversations)-5 ?> more discussions.</p>
			<?php break; ?>
		<?php endif ?>
	<?php endforeach ?>
</div>
<?php endif ?>