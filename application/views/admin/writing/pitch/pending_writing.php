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
					<h1>Pending Pitch Writing</h1>
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
						<th>Writer Name</th>
						<th>Status</th>
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
							<?php if ($result->writer): ?>
								<?= $vd->esc($result->writer->name()) ?>
							<?php else: ?>
								<span>-</span>
							<?php endif ?>
						</td>

						<td>
							<?php if ($result->status == Model_Pitch_Order::STATUS_ASSIGNED_TO_WRITER): ?>
								Not Yet Written	
							<?php elseif ($result->status == Model_Pitch_Order::STATUS_WRITER_REQUEST_DETAILS_REVISION): ?>
								<a href="#" class="pending_log" data-id="<?= $result->order_id ?>">
									Writer Sent Comments</a>
							<?php elseif ($result->status == Model_Pitch_Order::STATUS_SENT_BACK_TO_WRITER): ?>
								Admin Commented Back
								<div>(Not Yet Written)</div>
							<? elseif ($result->status == Model_Pitch_Order::
														STATUS_SENT_TO_CUSTOMER_FOR_DETAIL_CHANGE): ?>
								Sent to Customer for Detail Change
							<?php elseif ($result->status == Model_Pitch_Order::STATUS_CUSTOMER_REVISE_DETAILS): ?>
								<a href="#" class="pending_log" data-id="<?= $result->order_id ?>">
									Customer Revised Details</a>
							<?php endif ?>
						</td>		
					</tr>
					<?php endforeach ?>

					<script>
					defer(function(){

						$("a.pending_log").on("click", function(ev) {
							ev.preventDefault();
							var id = $(this).data("id");
							var content_url = "admin/writing/pitch/pending_writing/load_pending_modal/" + id;
							var modal = $("#<?= $vd->pending_log_modal_id ?>");
							var modal_dialog = modal.find(".modal-dialog");
							var modal_content = modal.find(".modal-content");

							$.get(content_url, function(content) {

								var m_content = $.create('div');
								m_content.html(content);

								var m_body = m_content.find('.modal-body');
								var m_body_html = m_body.html();
								var m_footer = m_content.find('.modal-footer');
								var m_footer_html = m_footer.html();

								var footer = $.create('div');
								footer.addClass('modal-footer');
								footer.html(m_footer_html);

								modal_dialog.append(footer);
								modal_content.html(m_body_html);
								modal.modal('show');

							});

							//modal.load(content_url, function() {
								//modal.modal('show');
							//});
						});

					});
					</script>
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