<tr>
	<td class="left">
		<h3>
			<a href="manage/publish/pr/edit/<?= $result->id ?>">
				<?= $vd->esc($vd->cut($result->title, 45)) ?>
			</a>
		</h3>
		<ul class="actions">
			<?php if ($result->slug): ?>
				<li><a href="<?= $ci->common()->url($result->url()) ?>" target="_blank">View</a></li>
			<?php endif ?>

			<li><a href="manage/publish/pr/edit/<?= $result->id ?>">Edit</a></li>
			<li><a href="manage/publish/collab/<?= $result->id ?>">Collaborate</a></li>
			
			<?php if (Auth::is_admin_online()): ?>
				<li><a href="manage/publish/pr/fork/<?= $result->id ?>">Fork</a></li>
			<?php endif ?>
			
			<li><a href="manage/publish/pr/delete/<?= $result->id ?>">Delete</a></li>
			<?php if (!Auth::user()->is_free_user() || ($result->is_premium && $result->is_published)): ?>
			<li><a href="manage/contact/campaign/edit/from/<?= $result->id ?>">Email</a></li>
			<?php endif ?>
			<li><a href="view/pdf/<?= $result->id ?>">PDF</a></li>
			<?php if ($result->is_published): ?>
			<li><a href="manage/analyze/content/view/<?= $result->id ?>">Stats</a></li>
			<?php endif ?>

			<?= $ci->load->view('manage/publish/partials/listing-pin-content', 
				array('result' => $result), true) ?>
			
		</ul>
	</td>
	<td>
		<?php if (!$result->is_draft): ?>
		<?php $publish = Date::out($result->date_publish); ?>
		<?= $publish->format('M j, Y') ?>&nbsp;
		<span class="text-muted"><?= $publish->format('H:i') ?></span>
		<?php else: ?>
		<span>-</span>
		<?php endif ?>
	</td>
	<td>
		<?php if ($result->is_premium): ?>

			<?php if ($result->distribution_bundle): ?>
			<div><span><?= $result->distribution_bundle->name() ?></span></div>
			<?php else: ?>
			<div><span>Premium</span></div>
			<?php endif ?>

			<?php if (isset($result->release_plus_set)): ?>
			<div class="small-text">
			<?php foreach ($result->release_plus_set as $release_plus): ?>
				<?php if ($release_plus->is_bundled) continue; ?>
				<?php if ($release_plus->is_confirmed): ?>
				<div class="normal-line">with <span class="status-info"><?= 
					$release_plus->name() ?></span></div>
				<?php else: ?>
				<div class="normal-line">with <span class="status-false"><?= 
					$release_plus->name() ?></span></div>
				<?php endif ?>
			<?php endforeach ?>
			</div>
			<?php endif ?>

		<?php else: ?>
			<span>Basic</span>
		<?php endif ?>
	</td>
	<td class="status">
		<?php if ($result->is_published): ?>
		<span class="label label-success">Published</span>
		<?php elseif ($result->is_under_review): ?>
		<span class="label label-info">Under Review</span>
		<?php elseif ($result->is_draft): ?>
			<span class="label label-default">Draft</span>
			<?php if ($result->is_rejected): ?>
			<div class="label label-danger">Rejected</div>
			<?php endif ?>
		<?php else: ?>
			<span class="label label-info">Scheduled</span>
		<?php endif ?>
	</td>
</tr>