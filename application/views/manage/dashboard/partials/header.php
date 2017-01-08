<header>
	<div class="row">
		<div class="col-md-6 page-title">
			<h2><?= $vd->esc($ci->newsroom->company_name) ?> Dashboard</h2>
		</div>
		<div class="col-md-6 actions">
			<ul class="list-inline actions">
				<?php if ($vd->writing_credits->available): ?>
				<li><a type="button" class="btn btn-default" href="manage/writing/process">Submit Writing Order</a></li>
				<?php endif ?>
				<li><a type="button" class="btn btn-primary" href="manage/publish/pr/edit">Submit Press Release</a></li>
			</ul>
		</div>
	</div>
</header>