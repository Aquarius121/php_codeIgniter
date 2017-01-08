<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/writing-modals.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<style>

.grid li {
	border-left: none !important;
}

</style>
<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Rejected Lists</h1>
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
						<th class="left">List Name</th>
						<th>Date Assigned</th>
						<th>List Builder</th>
						<th>Rejection Log</th>
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
                                <?= $result->order_id ?>
								<div>
									<span>
										<a class="pw-order-detail" href="#"  
											data-modal="<?= $vd->pw_detail_modal_id ?>" 
											data-id="<?= $result->order_id ?>">View Details</a>
									</span>
									<span class="reupload">
										| <a href='#' class="builder" data-id='<?= $result->order_id ?>'>
											Select MDB List</a>
										| <a href='#' class="status-info-muted csv" data-id='<?= $result->order_id ?>'>
											Upload CSV</a>
									</span>
								</div>
							</td>

							<td>
								<?php $order = Date::out($result->date_assigned); ?>
								<?= $order->format('M j, Y') ?>
							</td>
							<td>
								<?= $vd->esc($result->user->name()) ?>
							</td>
							<td class="rejection_log">
								<a href="#" data-id="<?= $result->list_id ?>">View Log</a>
							</td>
							<td class="review_td">
								<a href='admin/contact/pitch_wizard_order/review_single_list/<?= $result->list_id ?>'>
									Review List</a>
							</td>
						</tr>
						<?php endforeach ?>
					
					<script>	
					defer(function(){

						$(".reupload a.csv").on("click", function(ev) {
							ev.preventDefault();
							var id = $(this).data("id");
							var content_url = "admin/contact/pitch_wizard_order/load_upload_modal/" + id + "/1";
							var modal = $("#<?= $vd->upload_modal_id ?>");
							
							var modal_content = modal.find(".modal-content");
							modal_content.load(content_url, function() {
								modal.modal('show');
							});
						});

						$(".reupload a.builder").on("click", function(ev) {
							ev.preventDefault();
							var id = $(this).data("id");
							var modal = $(document.getElementById(<?= json_encode($vd->list_builder_modal_id) ?>));
							$("#builder-for-order-id").val(id);
							modal.modal("show");
						});	
						
						$(".rejection_log a").on("click", function(ev) {

							ev.preventDefault();
							var id = $(this).data("id");
							var content_url = "admin/contact/pitch_wizard_order/load_rejection_log_modal/" + id;
							var modal = $("#<?= $vd->rejection_modal_id ?>");
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
							
						});

					});
					</script>
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