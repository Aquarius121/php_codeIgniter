<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Coupons</h1>
				</div>
				<div class="span6">
					<a class="btn bt-silver pull-right" href="<?= $vd->store_base ?>/coupon/edit">New Coupon</a>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/virtual/store/coupon/partials/tabs') ?>
<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Coupon Code</th>
						<th>Expiry Date</th>
						<th>Action</th>						
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						
						<td class="left">
							<h3 class="nopadbot">
								<a href="<?= $vd->store_base ?>/coupon/edit/<?= $result->id?>">
									<?= $result->code ?>
								</a>
							</h3>
						</td>

						<td>
							<?php $dt_expires = Date::out($result->date_expires); ?>
							<?= $dt_expires->format('M j, Y') ?>
							<span class="muted"><?= $dt_expires->format('H:i') ?></span>
						</td>
						
						<td>
							<a href="<?= $vd->store_base ?>/coupon/edit/<?= $result->id?>">Edit</a> | 
							<?php if ($result->is_deleted): ?>
								<a href="<?= $vd->store_base ?>/coupon/restore/<?= $result->id?>">Restore</a>
							<?php else: ?>
								<a href="<?= $vd->store_base ?>/coupon/delete/<?= $result->id?>">Delete</a>
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
					Coupons
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>