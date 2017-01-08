<div class="row-fluid marbot-20">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Cat to Beat</h1>
				</div>				
				<div class="span6">
					<button id="add-button" class="btn btn-info pull-right">Add More</button>
					<script>

					defer(function() {

						var add_button = $("#add-button");
						var modal_id = <?= json_encode($vd->add_more_modal_id) ?>;
						var modal = $(document.getElementById(modal_id));
						
						add_button.on("click", function() {
							modal.modal("show");
						});

					});

					</script>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Category</th>
						<th class="left">Beat</th>
						<th>Remove</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr class="result">
						<td class="left">
							<div><?= $vd->esc($result->cat->name) ?></div>
							<div class="muted"><?= $vd->esc($result->cat->group_name) ?></div>
						</td>
						<td class="left">
							<div><?= $vd->esc($result->beat->name) ?></div>
							<div class="muted"><?= $vd->esc($result->beat->group_name) ?></div>
						</td>
						<td>
							<form action="admin/settings/cat_to_beat/delete" method="post">
								<input type="hidden" name="cat_id" value="<?= $result->cat->id ?>" />
								<input type="hidden" name="beat_id" value="<?= $result->beat->id ?>" />
								<a class="a-submit" href="#">Remove</a>
							</form>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<script>
			defer(function(){

				$(document).on("click", ".a-submit", function() {
					$(this).parents("form").submit();
					return false;
				});

			})
			</script>
		
		</div>
	</div>
</div>