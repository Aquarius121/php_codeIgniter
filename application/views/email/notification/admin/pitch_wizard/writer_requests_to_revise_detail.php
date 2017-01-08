Writer <strong><?= $vd->esc($writer_name) ?></strong> has requested that pitch wizard details need to be revised by the customer.
<br /><br />

For content: 
	<strong><?= $vd->esc($content_title) ?></strong>

<br /><br />

Comments:<br />
<div style="color: #666; padding: 10px 0 0 10px">
	<?= $vd->esc($comments) ?>
</div>
<br />

Your action is needed to proceed further.<br />
<a href="<?= $ci->website_url('admin/writing/pitch/pending_writing') ?>">
	View Pending Tasks
</a>
<br />
<br />

Best Regards, <br />
Newswire Team