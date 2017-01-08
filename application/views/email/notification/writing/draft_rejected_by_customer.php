The following press release is rejected by the customer and needs your attention:<br />
<?= $vd->esc($preview_link) ?>
<br /><br />

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

Comments:<br />
<div style="color: #666; padding: 10px 0 0 10px">
	<?= $vd->esc($comments) ?>
</div>
<br />

Best Regards,<br />
Newswire Team