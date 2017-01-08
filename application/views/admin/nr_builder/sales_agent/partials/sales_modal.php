<div class="content">
	
	<section class="form-section user-details">
		<h4 class="marbot-20">Sales Agent: <?= $vd->sales_agent->name() ?></h4>

		<div class="row-fluid">
			<div class="span12 relative">
				<?php foreach ($vd->results as $result): ?>
					<div>
						<strong>User: </strong>
						<a href="admin/users/view/<?= $result->user_id ?>" target="_blank">
							<?= $result->first_name ?> <?= $result->last_name ?>
						</a>
					</div>

					<div>
						<strong>Company Name: </strong>
						<a href="<?= $result->url('manage/newsroom/company') ?>" target="_blank">
							<?= $result->company_name ?>
						</a>
					</div>

					<div>
						<strong>Product: </strong>
						<?php foreach ($result->items as $item): ?>
							<?= $item->quantity ?> x 
							<?= $item->name ?>
						<?php endforeach ?> <br />
					</div>

					<div>
						<strong>Amount: </strong>
						$<?= $result->price ?>
					</div>
					<?php if ($result->is_renewal): ?>
						<div><strong>Renewal: </strong>
							Yes
						</div>
					<?php endif ?>


					<hr>
				<?php endforeach ?>
			</div>
		</div>
	</section>
</div>

