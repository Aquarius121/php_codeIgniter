<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/writing-modals.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Review Pitch Writing</h1>
				</div>
			</div>
		</header>
	</div>
</div>
			
<div class="row-fluid">
	<div class="span12">
		<?= $this->load->view('admin/writing/pitch/sub_menu.php') ?>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid writing-orders-grid">
				<thead>
					
					<tr>
						<th class="left">Campaign Name</th>
						<th>Date Sent</th>
						<th>Writer</th>
						<th>Review</th>
					</tr>

				</thead>
				<tbody class="results">
					<?php foreach ($vd->results as $result): ?>
						<tr data-id="<?= $result->id ?>" class="result">
							<td class="left">
								<?php if ($result->delivery == Model_Pitch_Order::DELIVERY_RUSH): ?>
									<strong class="label-class status-false">RUSH</strong>
								<?php endif ?>
								<a href="<?= $result->url() ?>" target="_blank">
									<?= $vd->esc($vd->cut($result->title, 60)) ?>
								</a>								

								<div class="muted">
									<?php if ($result->order_type == Model_Pitch_Order::ORDER_TYPE_OUTREACH): ?>
										<?= $vd->esc($result->city) ?> | 
									<?php else: ?>
										<strong class="label-class status-alternative-2">WRITING</strong>
									<?php endif ?><span class="status-alternative"><?= $vd->esc($result->keyword) ?></span>
								</div>
								<div>
									<a data-id="<?= $result->order_id ?>" class="pw-order-detail" 
										data-modal="<?= $vd->pw_detail_modal_id ?>" href="#">Order Details</a>
								</div>
							</td>

							<td>
								<?php $dt_written = Date::out($result->date_written); ?>
								<?= $dt_written->format('M j, Y') ?>
							</td>

                            <td>
                            	<?= $vd->esc($result->writer->name()) ?>
                            </td>
                            
							<td>
								<a href="admin/writing/pitch/review_single/<?= $result->order_id ?>">Review</a>
							</td>					
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
	$loader->add('lib/bootbox.min.js');
	$loader->add('js/required.js');
	$loader->add('js/pitch_wizard.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>