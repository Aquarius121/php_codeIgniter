<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid <?= value_if_test(count($vd->sources_selected) > 7, 'grid-small-font') ?>
				<?= value_if_test(count($vd->sources_selected) > 4 && count($vd->sources_selected) <= 7, 'grid-medium-font') ?>
				<?= value_if_test(count($vd->sources_selected) < 3, 'grid-half-wide') ?>">
				<thead>
					<tr>
						<th class="left"></th>
						<?php foreach ($vd->sources as $source): ?>
							<?php if (in_array($source, $vd->sources_selected)): ?>
								<th><?= Model_Company::full_source($source) ?></th>
							<?php endif ?>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr class="result">
						<td class="left">
							<?= $result['date'] ?>
						</td>	

						<?php foreach ($vd->sources as $source): ?>
							<?php if (in_array($source, $vd->sources_selected)): ?>
								<td>
									<?php if (@$result[$source]['count']): ?>
										<a href="#" data-date="<?= $result['date_ymd'] ?>" 
											data-source="<?= $source ?>" class="stats-table-price">
											<?= @$result[$source]['count'] ?> | 
											$<?= @$result[$source]['total_price'] ?>
										</a>
									<?php else: ?>
										0
									<?php endif ?>
								</td>
							<?php endif ?>
						<?php endforeach ?>
					</tr>
					<?php endforeach ?>

					<?php if (count($vd->results)): ?>
					<tr>
						<td class="left"><strong>Total<strong></td>
						<?php foreach ($vd->sources as $source): ?>
							<?php if (in_array($source, $vd->sources_selected)): ?>
								<td>
									<?php if (@$vd->sums[$source]['count']): ?>
										<strong>
											<?= @$vd->sums[$source]['count'] ?> | 
											$<?= @$vd->sums[$source]['price'] ?>
										</strong>
									<?php else: ?>
										<strong>0</strong>
									<?php endif ?>
								</td>
							<?php endif ?>
						<?php endforeach ?>
					</tr>
					<?php endif ?>
				</tbody>
			</table>
		
		</div>
	</div>
</div>