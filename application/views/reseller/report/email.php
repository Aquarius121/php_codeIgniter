<p><strong>Press Release Report</strong></p>
<p>
	For: <em><?= $vd->esc($result->title) ?></em><br />
	<?php $dt_publish = Date::utc($result->date_publish) ?>
	Date: <em><?= $dt_publish->format('M j, Y') ?></em><br />
	See attached file.
</p>