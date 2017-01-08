Writer <strong><?= $vd->esc($writer_name) ?></strong> has revised the press release titled:<br />
<div style="color: #666; padding: 10px 0 0 10px">
	<?= $vd->esc($pr_title) ?>
</div>
<br />

For  
<?php if ($reseller): ?>
	reseller
	<strong><?= $vd->esc($reseller->name()) ?></strong>
<?php endif ?>
customer: <br />
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