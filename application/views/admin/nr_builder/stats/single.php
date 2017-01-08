<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			<h2><?= Model_Company::full_source($source) ?></h2>
			<table class="grid">
				<thead>
					<tr>
						<th class="left">Date Ordered</th>
						<th>NR Export Date</th>
						<th>Company Name</th>
						<th>Sales Agent</th>
						<th>Product</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody class="results">
					<?php foreach ($vd->results[$source] as $result): ?>
						<tr class="result">
							<td class="left"><?= $result->date_created ?></td>	

							<td><?= $result->date_exported ?></td>

							<td>
								<a href="<?= $result->url('manage/newsroom/company') ?>" target="_blank">
									<?= $result->company_name ?>
								</a>
							</td>

							<td>
								<?php if (!empty($result->agent_first_name)): ?>
									<?=	$result->agent_first_name ?> <?=	$result->agent_last_name ?>
								<?php else: ?>
									-
								<?php endif ?>
							</td>

							<td>
								<?php foreach ($result->items as $item): ?>
									<?= $item->quantity ?> x 
									<?= $item->name ?>
								<?php endforeach ?>
							</td>

							<td>$<?= $result->price ?></td>
						</tr>
					<?php endforeach ?>
					<tr>
						<td colspan="4"></td>
						<td><strong>SubTotal</strong></td>
						<td><strong>$<?= $vd->results["{$source}_total"] ?></strong></td>
					</tr>
				</tbody>
			</table>
		
		</div>
	</div>
</div>