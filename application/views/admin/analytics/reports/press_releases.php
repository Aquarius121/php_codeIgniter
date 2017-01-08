<style>

div.report-container table {
	border-collapse: collapse;
	border: none;
	margin-bottom: 20px;
	width: auto;
}

div.report-container table th,
div.report-container table td {
	border: 1px solid #ccc;
	padding: 2px 6px;	
	vertical-align: top;
	max-width: 350px;
}

div.report-container {
	font-family: sans-serif;
	padding: 10px 2px;	
}

div.report-container table th:first-child,
div.report-container table td:first-child {
	padding-right: 12px;
}

div.report-container div.muted {
	color: #999;
}

div.report-container h2 {
	color: #666;
	font-size: 18px;
	padding: 0;
	margin: 0 0 10px 0;
}

</style>

<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Press Releases</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="analytics-report-content">
<div class="report-container">
	
	<div id="select-month" class="row-fluid">
		<div class="span3">
			<select class="show-menu-arrow span12" name="year">
				<?php for ($i = 0; $i <= 12; $i++): ?>
				<?php $dt = Date::months(-$i); ?>
				<option value="admin/analytics/reports/press_releases/<?= $i ?>"
					<?= value_if_test($vd->selected_month == $dt->format('Ym'), 'selected') ?>>
					<?= $dt->format('F Y') ?>
				</option>
				<?php endfor ?>
			</select>
			<script>

			$(function() {
				
				var select = $("#select-month select");
				select.on_load_select({ size: 13 });

				select.on("change", function() {
					window.location = select.val();
				});
				
			});
			
			</script>
		</div>
	</div>

	<table cellpadding="2" border="1">
		<thead>
			<tr>			
				<th align="center" colspan="2">Basic</th>
				<th align="center" colspan="2">Premium</th>
			</tr>
			<tr>
				<th align="left">User</th>
				<th align="right">Published</th>
				<th align="left">User</th>
				<th align="right">Published</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left">* (<?= $vd->total_users_basic ?>)</td>
				<td align="right"><?= $vd->total_basic ?></td>				
				<td align="left">* (<?= $vd->total_users_premium ?>)</td>
				<td align="right"><?= $vd->total_premium ?></td>
			</tr>
			<?php for ($k = 0; $k < max(count($vd->active_users_basic), 
						count($vd->active_users_premium)); $k++): ?>
			<tr>
				<?php if (isset($vd->active_users_basic[$k])): ?>
					<?php $result = $vd->active_users_basic[$k]; ?>
					<td align="left">
						<a href="admin/publish?filter_user=<?= $result->id ?>" 
							class="add-filter-icon"></a>
						<a class="view" href="admin/users/view/<?= $result->id ?>">
							<?= $vd->esc($result->email) ?>
						</a>
					</td>				
					<td align="right">
						<?= $result->count ?>
					</td>
				<?php else: ?>
					<td>-</td>
					<td>-</td>
				<?php endif ?>
				<?php if (isset($vd->active_users_premium[$k])): ?>
					<?php $result = $vd->active_users_premium[$k]; ?>
					<td align="left">
						<a href="admin/publish/pr/published?filter_user=<?= $result->id ?>" 
							class="add-filter-icon"></a>
						<a class="view" href="admin/users/view/<?= $result->id ?>">
							<?= $vd->esc($result->email) ?>
						</a>
					</td>				
					<td align="right">
						<?= $result->count ?>
					</td>
				<?php else: ?>
					<td>-</td>
					<td>-</td>
				<?php endif ?>
			</tr>
			<?php endfor ?>
		</tbody>
	</table>

</div>
</div>