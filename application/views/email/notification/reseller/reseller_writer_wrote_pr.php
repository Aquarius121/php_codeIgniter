Writer <strong><?= $vd->esc($writer_name) ?></strong> completed the pr titled:<br />
<div style="color: #666; padding: 10px 0 0 10px">
	<?= $vd->esc($pr_title) ?>
</div>
<br />

For customer: <br />
<div style="color: #666; padding: 10px 0 0 10px">
	<?= $vd->esc($customer_contact_name) ?>
	(<?= $vd->esc($customer_company_name) ?>)
	<br /><?= $vd->esc($customer_contact_email) ?>
</div>
<br />

Your action is needed to proceed further.
Please review it and approve or reject it here:
<br /><?= $vd->esc($preview_link) ?>
<br /><br />

Best Regards, <br />
Newswire Team