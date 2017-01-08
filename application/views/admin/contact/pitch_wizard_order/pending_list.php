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
					<h1>Pending Lists</h1>
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
						<th>Date Ordered</th>
                        <th>Date Assigned</th>
					</tr>

				</thead>
				<tbody class="results">
					<?php foreach ($vd->results as $result): ?>
						<tr data-id="<?= $result->id ?>" class="result">
							<td width="35%" style="text-align:left">
								<?= $vd->esc($result->city) ?>, <?= $vd->esc($result->state_abbr) ?> - 
								<?= $vd->esc($result->keyword) ?> - 
								<?= $result->order_id ?>
							</td>
							<td>
								<?= $vd->esc($result->user->name()) ?>
							</td>

							<td>								
								<?php $order = Date::out($result->date_created); ?>
								<?= $order->format('M j, Y') ?>
							</td>
							<td>
								<?php $order = Date::out($result->date_assigned); ?>
								<?= $order->format('M j, Y') ?>
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
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>