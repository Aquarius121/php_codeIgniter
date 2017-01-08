<select class="form-control selectpicker show-menu-arrow microlist-select marbot" title=""
	name="microlist[]" data-container="body" data-live-search="true" data-size="10"
	<?= value_if_test($selected
		&& isset($selected->data->is_confirmed) 
		&& $selected->data->is_confirmed, 'disabled') ?>>
	<option class="selectpicker-default" title="Add Microlist Distribution" value=""
		<?= value_if_test(!$selected, 'selected') ?>>None</option>
	<?php foreach (PRNewswire_Distribution::microlists() as $code => $microlist): ?>
		<?php if (!$microlist->item) continue; ?>
		<option value="<?= $vd->esc($code) ?>" data-content="

				<!-- data-content html -->

				<div class=&quot;microlist-name&quot;>
					<strong><?= $vd->esc($microlist->group_name) ?></strong> 
					&raquo; <?= $vd->esc($microlist->name) ?>		
				</div>

				<div class=&quot;smaller&quot;>					
					<?php if ($selected 
						&& isset($selected->data->is_confirmed) 
						&& $selected->data->is_confirmed): ?>
						<strong class=&quot;status-true&quot;>CONFIRMED&nbsp;&nbsp;</strong>
					<?php else: ?>
						<strong class=&quot;status-info&quot;>$<?= 
							$microlist->item->price ?>&nbsp;&nbsp;</strong>
					<?php endif ?>
					<span class=&quot;status-muted&quot;><?= (int) $microlist->contact_count ?> Contacts</span>
				</div>

				<!-- data-content html -->

			" 
			<?= value_if_test($selected && $selected->data && 
				$selected->data->item_code == $code, 'selected') ?>>
				<?= $vd->esc($microlist->name) ?>
		</option>
	<?php endforeach ?>
</select>