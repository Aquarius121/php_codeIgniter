<form action="admin/settings/cat_to_beat/add" method="post">

	<div class="row-fluid">
		<div class="span6 ta-center placeholder-header">Category Name</div>
		<div class="span6 ta-center placeholder-header">Beat Name</div>
	</div>

	<?php for ($i = 1; $i <= 10; $i++): ?>
	<div class="row-fluid add-line">
		<div class="span6">
			<select class="span12" name="cat_id[]">
				<option title="Select Category" value="" disabled selected>Select Category</option>
				<?php foreach ($vd->cats as $group): ?>
				<?php if (!$group->is_listed) continue; ?>
				<optgroup label="<?= $vd->esc($group->name) ?>">
					<?php foreach ($group->cats as $cat): ?>
					<?php if (!$cat->is_listed) continue; ?>
					<option value="<?= $cat->id ?>">
						<?= $vd->esc($cat->name) ?>
					</option>
					<?php endforeach ?>
				</optgroup>
				<?php endforeach ?>
			</select>			
		</div>
		<div class="span6">
			<select class="span12" name="beat_id[]">
				<option title="Select Beat" value="" disabled selected>Select Beat</option>
				<?php foreach ($vd->beats as $group): ?>
				<optgroup label="<?= $vd->esc($group->name) ?>">
					<?php foreach ($group->beats as $beat): ?>
					<option value="<?= $beat->id ?>">
						<?= $vd->esc($beat->name) ?>
					</option>
					<?php endforeach ?>
				</optgroup>
				<?php endforeach ?>
			</select>
		</div>
	</div>
	<?php endfor ?>

	<a href="#" id="add-more-rows">Add Another</a>
	<div class="marbot-20"></div>

	<script>

	$(function() {

		var add_more_rows = $("#add-more-rows");
		add_more_rows.on("click", function() {
			var last_add_line = add_more_rows.prev(".add-line");
			last_add_line.after(last_add_line.clone());
			return false;
		});

	});

	</script>
</form>