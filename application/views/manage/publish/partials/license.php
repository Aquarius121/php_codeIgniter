<div class="row form-group" id="select-license">
	<div class="col-lg-12">
		<select class="form-control selectpicker show-menu-arrow col-lg-12" name="license">
			<option class="selectpicker-default" title="Select License" value=""
				<?= value_if_test(!@$vd->m_content->license, 'selected') ?>>None</option>
			<?php foreach ($licenses as $license): ?>
			<option value="<?= $vd->esc($license) ?>"
				<?= value_if_test((@$vd->m_content->license === $license), 'selected') ?>>
				<?= $vd->esc($license) ?>
			</option>
			<?php endforeach ?>
		</select>
	</div>
</div>