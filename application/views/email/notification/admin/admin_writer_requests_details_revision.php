Writer <strong><?= $vd->esc($writer_name) ?></strong> has requested that PR details be revised by the customer.
<br /><br />

For 
<?php if ($reseller): ?>
	reseller 
	<strong><?= $vd->esc(@$reseller->name()) ?></strong>
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

Your action is needed to proceed further.<br />
<a href="<?= $ci->website_url('admin/writing/admin_editor/pending_writing') ?>">
	View Pending Tasks
</a>
<br />
<br />

Best Regards, <br />
Newswire Team