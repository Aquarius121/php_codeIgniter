<?php if ($contacts): ?>
	An industry outreach order has been created with <?= (int) $contacts ?> contacts.<br>
<?php else: ?>
	An industry outreach order has been created.<br>
<?php endif ?>

<br><br>
<code>
	<strong>campaign</strong>:
	<br> 
	<a href="<?= $newsroom->url() ?>manage/contact/campaign/edit/<?= $campaign->id ?>"
	 	style="text-decoration:none;display:inline-block;max-width:300px">
		<?= $newsroom->url() ?>manage/contact/campaign/edit/<?= $campaign->id ?>
	</a>
	<br><br>
	<strong>industries</strong>:
	<?php foreach ($beats as $group): ?>
		<?php foreach ($group->beats as $beat): ?>
			<br><?= $vd->esc($group->name) ?> 
				&raquo; <?= $vd->esc($beat->name) ?>
		<?php endforeach ?>
	<?php endforeach ?>
</code>