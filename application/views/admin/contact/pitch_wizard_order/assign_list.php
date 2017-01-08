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
					<h1>Assign Lists</h1>
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
					</tr>

				</thead>
				<tbody class="results">
					<?php foreach ($vd->results as $result): ?>
						<tr data-id="<?= $result->id ?>" class="result">
							<td width="45%" style="text-align:left">
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
								<div >
									<a class="pw-order-detail" href="#" 
										data-modal="<?= $vd->pw_detail_modal_id ?>" 
										data-id="<?= $result->order_id ?>">View Details</a>
								</div>
							</td>
							<td class="ta-right">
								<div class="marbot-5">
									Details Received:
									<?php $dt_ordered = Date::out($result->date_created); ?>
									<?= $dt_ordered->format('M j, Y') ?>&nbsp;
									<span class="muted"><?= $dt_ordered->format('H:i') ?></span>
								</div>
								<form method="post" action="">
									<input type="hidden" name="list_id" value="<?= $result->list_id?>" />
									<div>
										<select class="in-table-dd show-menu-arrow ta-left
											selectpicker select-writer nomarbot" name="list_builder_id">
											<option value="">Select List Builder</option>
											<?php foreach ($vd->admin_list as $admin): ?>
											<option value="<?= $admin->id ?>">
												<?= $vd->esc($admin->name()) ?>
											</option>
											<?php endforeach ?>
										</select>
									</div>
								</form>
							</td>                          					
						</tr>
					<?php endforeach ?>
                    <script>                   					
                    $(function() {								
                        window.on_load_select(function() {
                            var dd_buttons = $("div.select-writer > button.dropdown-toggle");
                            dd_buttons.each(function() {
                                var dd_button = $(this);
                                var as_button = $.create("button");
                                as_button.attr("type", "submit");
                                as_button.prop("disabled", true);
                                as_button.addClass("assign");
                                as_button.addClass("btn-info");
                                as_button.addClass("btn");
                                as_button.text("Save");
                                dd_button.after(as_button);
                            });
                            
                            var dd_select = $("select.select-writer");
                            dd_select.on("change", function() {
                                var _this = $(this);
                                var as_button = _this.parent().find("button.assign");
                                as_button.prop("disabled", !_this.val);
                            });
                            
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