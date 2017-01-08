<select name="category_id" id="category_id"
	class="selectpicker show-menu-arrow span12 marbot-15 pull-right">
	<option value="" class="status-false" selected>None</option>
	<?php foreach ($vd->cb_cat as $cat): ?>
	<option value="<?= $cat->id ?>"
		<?= value_if_test($cat->id == @$vd->selected_cat_id, 'selected')?>
		><?= $vd->esc($cat->name) ?></option>
	<?php endforeach ?>
</select>