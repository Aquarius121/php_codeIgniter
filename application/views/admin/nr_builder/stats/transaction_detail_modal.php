
<div class="content">
	
	<section class="form-section user-details">
		<h4 class="marbot-20"><?= $vd->source_title ?> - <?= $vd->date_created ?></h4>

		<div class="row-fluid">
			<div class="span12 relative" id="select-country">
				<?php foreach ($vd->results as $result): ?>
					
					<div>
						<strong>Date Exported: </strong>
						<?= $result->date_exported ?>
					</div>

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

					<?php if (!empty($result->agent_first_name)): ?>
						<div><strong>Sales Agent: </strong>
							<?= $result->agent_first_name ?> <?= $result->agent_last_name ?> 
						</div>
					<?php endif ?>
					<hr>
				<?php endforeach ?>
			</div>
		</div>
	</section>
</div>

