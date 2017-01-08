<?php if ($ci->newsroom->is_active): ?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Views for <?= $vd->esc($ci->newsroom->company_name) ?></h3>
			</div>
			<div class="panel-body">
				<div class="chart">
					<?= $vd->chart ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif ?>