Hello <?= $vd->customer_name ?>.
<br /><br />
You placed a PR Writing and Distribution order on <?= $vd->reseller_website ?> 
but have not provided details yet. Please go here:
<br /><br />
<?= $vd->writing_orders_detail_link ?>
<br /><br />
You will need to enter a code here which is <strong><code><?= $vd->writing_order_code ?></code></strong>.
Please fill the details as soon as possible.
<br /><br />
Best Regards,<br />
<?= $vd->reseller_company_name ?> Team