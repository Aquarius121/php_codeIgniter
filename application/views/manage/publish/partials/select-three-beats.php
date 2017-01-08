<?php $selected_beats = $vd->m_content ? $vd->m_content->get_beats() : array(); ?>
<?php for ($i = 1; $i <= max(3, count($selected_beats)); $i++): ?>
<?php $selected_beat_id = (int) @$selected_beats[$i-1]->id; ?>
<div class="row form-group">
	<div class="col-lg-12 select-category select-right">
		<select class="select-right form-control selectpicker show-menu-arrow category col-xs-12" 
			data-live-search="true"  data-size="10" data-required-use-parent="1"
			data-container="body" name="beats[]" data-required-name="Industry">
			<option class="selectpicker-default" title="Select Industry" value=""
				<?= value_if_test(!$selected_beat_id, 'selected') ?>>None</option>
			<?php foreach ($vd->beats as $group): ?>
			<?php if (!$group->is_listed) continue; ?>
			<optgroup label="<?= $vd->esc($group->name) ?>">
				<?php foreach ($group->beats as $beat): ?>
				<?php if (!$beat->is_listed) continue; ?>
				<option value="<?= $beat->id ?>"
					<?= value_if_test(($selected_beat_id === (int) $beat->id), 'selected') ?>>
					<?= $vd->esc($beat->name) ?>
				</option>
				<?php endforeach ?>
			</optgroup>
			<?php endforeach ?>
		</select>
	</div>
</div>
<?php endfor ?>