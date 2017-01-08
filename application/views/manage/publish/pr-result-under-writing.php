<?php $raw_data = $result->writing_session->raw_data(); ?>

<tr>
	<td class="left">
		<h3>
			<?php if (!empty($raw_data->primary_keyword)): ?>
			<div class="marbot-5">
				<?php if ($result->title): ?>
				<?= $vd->esc($vd->cut($result->title, 45)) ?>
				<?php else: ?>
				Writing Order in Progress
				<?php endif ?>
			</div>
			<div>
				<a class="tl status-text-muted" title="<?= $result->writing_session->nice_id() ?>">
					<?php $dt_created = Date::out($result->writing_session->date_created); ?>
					<?= $dt_created->format('Y-m-d') ?>
				</a>
				<span>|</span>
				<span class="status-alternative"><?= $vd->esc($raw_data->primary_keyword) ?></span>
			</div>
			<?php else: ?>
			<div>
				<a class="tl status text-muted" title="<?= $result->writing_session->nice_id() ?>">
					<?php $dt_created = Date::out($result->writing_session->date_created); ?>
					<?= $dt_created->format('Y-m-d') ?>
				</a>
				<span>|</span>
				<span>Writing Order in Progress</span>
			</div>
			<?php endif ?>
		</h3>		
		<ul class="actions">
			<?php if (Model_Writing_Session::is_preview_available($result->writing_session->status)): ?>
			<li><a href="view/preview/<?= $result->id ?>" target="_blank">View PR</a></li>
			<?php endif ?>
			<li><a href="manage/writing/process/<?= $result->writing_session->id ?>/1">Edit Details</a></li>
			<li><a href="manage/writing/process/<?= $result->writing_session->id ?>/4/review">Review Order</a></li>
		</ul>
	</td>
	<td>
		<span>-</span>
	</td>
	<td>
		Writing Order		
		<div class="text-muted">
			<?php if ($result->is_premium): ?>
			<span>Premium</span>
				<?php if (isset($result->release_plus_set)): ?>
				<div class="small-text">
				<?php foreach ($result->release_plus_set as $release_plus): ?>
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
		</div>
	</td>
	<td>
		<?php if ($result->writing_session->status == Model_Writing_Order::STATUS_NOT_ASSIGNED): ?>
			<span class="label label-info">Details Submitted</span>
		<?php elseif ($result->writing_session->status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER): ?>
			<a href="view/preview/<?= $result->id ?>" target="_blank"
				class="label label-success"><strong>Review Required</strong></a>
		<?php elseif ($result->writing_session->status == Model_Writing_Order::STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE): ?>
			<a href="manage/writing/process/<?= $result->writing_session->id ?>/1"
				class="label label-danger"><strong>Details Required</strong></a>
		<?php elseif ($result->writing_session->status == Model_Writing_Order::STATUS_CUSTOMER_REVISE_DETAILS): ?>
			Details Submitted
		<?php elseif (!$result->writing_session->writing_order_id): ?>
			<a href="manage/writing/process/<?= $result->writing_session->id ?>/1"
				class="label label-danger"><strong>Details Required</strong></a>
		<?php else: ?>
			<span class="label label-info">In Progress</span>
		<?php endif ?>
	</td>
</tr>