<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Campaign Manager</h1>
				</div>
			</div>
		</header>
	</div>
</div>
			
<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-tabs nav-activate" id="tabs">
			<li><a data-on="^admin/contact/campaign/all" 
				href="admin/contact/campaign/all<?= $vd->esc(gstring()) ?>">All</a></li>
			<li><a data-on="^admin/contact/campaign/sent" 
				href="admin/contact/campaign/sent<?= $vd->esc(gstring()) ?>">Sent</a></li>
			<li><a data-on="^admin/contact/campaign/scheduled" 
				href="admin/contact/campaign/scheduled<?= $vd->esc(gstring()) ?>">Scheduled</a></li>
			<li><a data-on="^admin/contact/campaign/draft" 
				href="admin/contact/campaign/draft<?= $vd->esc(gstring()) ?>">Draft</a></li>
		</ul>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Campaign Name</th>
						<th>Spam Score</th>	
						<th>Details</th>
						<th>Owner</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left <?= value_if_test(@$result->pitch_order_id, 'userarea-pw-order') ?>">
							<div class="td-container">
							<h3>
								<a class="view" href="admin/contact/campaign/edit/<?= $result->id ?>" target="_blank">
									<?php if (@$result->pitch_order_id): ?>
										<?= $vd->esc($vd->cut($result->content_title, 45)) ?>
									<?php else: ?>
										<?= $vd->esc($vd->cut($result->name, 45)) ?>
									<?php endif ?>

								</a>
							</h3>	
							<ul>
								
								<?php if (@$result->pitch_order_id): ?>
									<li><a href="#" data-id="<?= $result->pitch_order_id ?>"
											data-modal="<?= $vd->pw_detail_modal_id ?>" 
											class="pw-order-detail">Order Details</a></li>
								<?php endif ?>
								<li><a href="admin/contact/campaign/edit/<?= $result->id ?>" target="_blank">Edit</a></li>
								<li><a href="admin/contact/campaign/delete/<?= $result->id ?>" target="_blank">Delete</a></li>
								<?php if ($result->is_sent): ?>
								<li><a href="admin/contact/campaign/stats/<?= $result->id ?>" target="_blank">Stats</a></li>
								<?php endif ?>
								<?php if ($result->content_id): ?>
								<li><a href="<?= Model_Content::permalink_from_id($result->content_id) ?>" 
									target="_blank">Content</a></li>
								<?php endif ?>
							</ul>
							</div>
						</td>
						<td>
							<?php if ($result->spam_score != -1): ?>
								<?php if ($result->spam_score >= $ci->conf('spam_score_threshold')): ?>
									<div class="status-false"><?= $result->spam_score ?></div>
								<?php else: ?>
									<div class="status-true"><?= $result->spam_score ?></div>
								<?php endif ?>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td>
							<?php $send = Date::out($result->date_send); ?>
							<?= $send->format('M j, Y') ?>&nbsp;
							<span class="muted"><?= $send->format('H:i') ?></span>
							<div class="muted">
								<?php if ($result->is_sent): ?>
								<span>Sent (<?= (int) $result->contact_count ?> Contacts)</span>								
								<?php elseif ($result->is_draft): ?>
								<span>Draft</span>
								<?php elseif ($result->is_send_active): ?>
								<span>Sending</span>
								<?php else: ?>
								<span>Scheduled</span>
								<?php endif ?>
							</div>
						</td>
						<?= $ci->load->view('admin/partials/owner-column', 
							array('result' => $result)); ?>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">
				<div class="pull-left grid-report ta-left">
					All times are in UTC.
				</div>
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Results
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('js/pitch_wizard.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>