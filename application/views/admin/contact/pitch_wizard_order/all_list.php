<style>
.grid li {
	border-left:none !important;
}
</style>
<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>All Lists</h1>
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
						<th class="left">List Name <span class="btn-mini">(Location-Keyword-Order ID)</span></th>
						<th>List Builder</th>
						<th>Status</th>
					</tr>

				</thead>
				<tbody class="results">
					<?php foreach ($vd->results as $result): ?>
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
									<?= $result->order_id ?>
								<div>
									<span>
										<a class="pw-order-detail"  href="#"
											data-modal="<?= $vd->pw_detail_modal_id ?>"  
											data-id="<?= $result->order_id ?>">Details</a>  
									</span>
									| <a href="admin/contact/pitch_wizard_order/archived_list/archive/<?= $result->list_id ?>">
										Archive</a>
									| <a target="_blank" href="<?= $result->url() ?>">Content</a> 
									<?php if ($result->pw_list_status == Model_Pitch_List::STATUS_SENT_TO_ADMIN
											|| $result->pw_list_status == Model_Pitch_List::STATUS_ADMIN_REJECTED
											|| $result->pw_list_status == Model_Pitch_List::STATUS_SENT_TO_CUSTOMER): ?>
									|	<a href="admin/contact/pitch_wizard_order/review_single_list/<?=$result->list_id ?>"
											>View List</a>
									<?php endif ?>
								</div>
							</td>
							<td>
								<?php if (@$result->user): ?>
									<?= $result->user->name() ?>
								<?php else: ?>
									-
								<?php endif ?>
							</td>
							
							<td>
								<?php if ($result->pw_list_status == Model_Pitch_List::STATUS_NOT_ASSIGNED): ?>
									<a href="admin/contact/pitch_wizard_order/assign_list">
										<?= Model_Pitch_List::full_status($result->pw_list_status) ?>
									</a>
								<?php elseif ($result->pw_list_status == Model_Pitch_List::STATUS_ASSIGNED_TO_LIST_BUILDER): ?>
									<a href="admin/contact/pitch_wizard_order/pending_list">
										<?= Model_Pitch_List::full_status($result->pw_list_status) ?>
									</a>
								<?php elseif ($result->pw_list_status == Model_Pitch_List::STATUS_SENT_TO_ADMIN): ?>
									<a href="admin/contact/pitch_wizard_order/review_list">
										<?= Model_Pitch_List::full_status($result->pw_list_status) ?>
									</a>
								<?php elseif ($result->pw_list_status == Model_Pitch_List::STATUS_ADMIN_REJECTED): ?>
									<a href="admin/contact/pitch_wizard_order/rejected_list">
										<?= Model_Pitch_List::full_status($result->pw_list_status) ?>
									</a>                                    
								<?php else: ?>	
									<?= Model_Pitch_List::full_status($result->pw_list_status) ?>
								<?php endif ?>
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