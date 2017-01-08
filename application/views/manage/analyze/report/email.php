<?= $ci->load->view('manage/analyze/report/partials/header.php', array('report_title' => 'Email Distribution Stats'), true) ?>
<link rel="stylesheet" href="<?= $vd->assets_base ?>css/manage-print.css?<?= $vd->version ?>" />

<div class="row bg-white">
	<div class="col-lg-12">
		<div class="content listing content-no-tabs">
			
			<div class="row not-row">				
				<div class="col-lg-12" id="double-stats-summary">
					<legend><?= $vd->esc($vd->campaign->name) ?></legend>
					<div class="stats-summary">
						<strong><?= $vd->views ?>+ Views</strong>* 
						<?php if ($vd->clicks): ?>
							and <strong><?= $vd->clicks ?> Clicks</strong>
						<?php endif ?>
					</div>
				</div>
			</div>
			
			<table class="table compact">
				<thead>
					
					<tr>
						<th class="left">Contact</th>
						<th>Company</th>
						<th>Viewed</th>
						<?php if ($vd->clicks): ?>
						<th>Clicked</th>
						<?php endif ?>
					</tr>
					
				</thead>
				<tbody>
					
					<?php foreach ($vd->results as $result): ?>
					<tr>
						<td class="left">
							<?php if ($result->first_name || $result->last_name): ?>
							<div class="marbot-2">
								<?= $vd->esc($result->first_name) ?>
								<?= $vd->esc($result->last_name) ?>
							</div>
							<div class="text-muted">
								<?php if ($result->company_id > 0): ?>
								<?= $vd->esc($result->email) ?>
								<?php else: ?>
								<?= $vd->esc($result->email->pre) ?><span class="email-obfuscated"><?= 
									$result->email->obfuscated ?></span><?= $vd->esc($result->email->post) ?>
								<?php endif ?>
							</div>
							<?php else: ?>
							<div>
								<?php if ($result->company_id > 0): ?>
								<?= $vd->esc($result->email) ?>
								<?php else: ?>
								<?= $vd->esc($result->email->pre) ?><span class="email-obfuscated"><?= 
									$result->email->obfuscated ?></span><?= $vd->esc($result->email->post) ?>
								<?php endif ?>
							</div>
							<?php endif ?>
						</td>
						<td>
							<?php if ($result->company_name): ?>
							<?= $vd->esc($result->company_name) ?>
							<?php else: ?>
							<span>-</span>
							<?php endif ?>
						</td>
						<td>
							<?php if ($result->clicked && !$result->viewed): ?>
								<strong class="status-info">Yes<sup>&dagger;</sup></strong>
							<?php elseif ($result->viewed): ?>
							<strong class="status-true">Yes</strong>
							<?php else: ?>
							<strong class="status-false">No*</strong>
							<?php endif ?>
						</td>
						<?php if ($vd->clicks): ?>
						<td>
							<?php if ($result->clicked): ?>
							<strong class="status-true">Yes</strong>
							<?php else: ?>
							<strong class="status-false">No</strong>
							<?php endif ?>
						</td>
						<?php endif ?>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
					
			<div class="clearfix">
				<div class="pull-left grid-report">
					* Some email clients do not allow views to be tracked.
				</div>
				<div class="pull-right grid-report">Displaying 
					<?= count($vd->results) ?> Contacts</div>
				</div>
			</div>
		
		</div>
	</div>
</div>

<img class="sleep" src="sleep/2" />