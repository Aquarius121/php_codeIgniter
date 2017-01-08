<table class="table table-striped table-bordered nomarbot table-condensed">
	<tr>
		<th class="ta-left">Pixel</th>
		<th class="ta-left">Views</th>
	</tr>
	<?php foreach($vd->sources as $source): ?>
	<tr>
		<td class="ta-left"><?= $vd->esc($source->uri) ?></td>
		<td class="ta-left"><?= (int) $source->views ?></td>
	</tr>
	<?php endforeach ?>
</table>