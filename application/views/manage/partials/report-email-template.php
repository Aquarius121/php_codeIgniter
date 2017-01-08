<p>
	Hi,
</p>
<?php if ($report->type === Report_Email::TYPE_PR): ?>
<p>
	Thank you for using Newswire.
	Your Distribution Report is ready to download and review.
	Please see the attached PDF report for your release:
</p>
<?php elseif ($report->type === Report_Email::TYPE_OVERALL):  ?>
<p>
	Thank you for using Newswire.
	Your newsroom report is ready to download and review.
	Please see the attached PDF report:
</p>
<?php endif ?>
<p>
	For: <em><?= $vd->esc($report->context) ?></em><br />
	Date: <em><?= date('M j, Y') ?></em><br />
	See attached file.
</p>
<p>
	Should you need any additional assistance,
	please call 1-800-713-7278.
</p>
<p>
	Thank you.
</p>