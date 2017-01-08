<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Review Lists</h1>
				</div>
			</div>
		</header>
	</div>
</div>
			
<div class="row-fluid">
	<div class="span12">
		<?= $this->load->view('admin/contact/pitch_wizard_order/sub_menu.php') ?>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th style="text-align:left">List Name <span class="btn-mini">(Location-Keyword-Order ID)</span></th>
						<th>Date Assigned</th>
						<th>Date Completed</th>
						<th>List Builder</th>
						<th>Action</th>                        
					</tr>

				</thead>
				<tbody class="results">
					<form class="tab-content" method="post" id="import-form" action="manage/contact/import/save">
						<input type="hidden" class="required" id="stored-file-id"
							data-required-name="CSV File" name="stored_file_id" />
						<input type="hidden" id="filename" name="filename" />
					<?php foreach ($vd->results as $i => $result): ?>
						<tr data-id="<?= $result->id ?>" class="result">
							<td width="35%" style="text-align:left">
								<?php if ($result->delivery == Model_Pitch_Order::DELIVERY_RUSH): ?>
									<strong class="label-class status-false">RUSH</strong>
								<?php endif ?>
								<?php if ($result->order_type == Model_Pitch_Order::ORDER_TYPE_OUTREACH): ?>
									<?= $vd->esc($result->city) ?>, <?= $vd->esc($result->state_abbr) ?>
								<?php else: ?>
									<strong class="status-alternative-2">WRITING</strong>
								<?php endif ?> 
								<span class="status-alternative"><?= $vd->esc($result->keyword) ?></span> - 
                                <?= $result->order_id ?><br />
								<span >
									<a class="pw-order-detail" href="#"  
										data-modal="<?= $vd->pw_detail_modal_id ?>" 
										data-id="<?= $result->order_id ?>">View Details</a>
								</span>
								| <a target="_blank" href="<?= $result->url() ?>">View Content</a>
							</td>						
                            
							<td>
								<?php $order = Date::out($result->date_assigned); ?>
								<?= $order->format('M j, Y') ?>
							</td>
                            
							<td>
								<?php $completed = Date::out($result->date_list_submitted); ?>
								<?= $completed->format('M j, Y') ?>
							</td>
							<td>
								<?= $vd->esc($result->user->name()) ?>
							</td>

							<td>
								<a href='admin/contact/pitch_wizard_order/review_single_list/<?= $result->list_id ?>'
									>Review List</a>
							</td>
						</tr>
					<?php endforeach ?>                    
                    </form>
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