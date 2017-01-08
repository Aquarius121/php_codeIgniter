<?= $this->view('email/container/header'); ?>
<div style="padding:0 20px;">

	<p>Hello <?= $vd->user->name ?>,</p>
	<p>This is an update from Newswire for the content you've been collaborating on as listed below.
		Click <a href="<?= $ci->website_url() ?>view/collab/<?= 
		$vd->m_collab->id ?>/<?= $vd->user->suid ?>">here</a> 
		to load the collaboration session.</p>
	<p style="color:#7E9EB3;border-bottom:1px solid #ccc;font-weight:bold;font-size:14px;padding:0 40px 20px 20px;">
		<?= $vd->esc($vd->content->title) ?>
	</p>	

	<?= $this->view('api/iella/other/collab/partials/approvals') ?>	
	<?= $this->view('api/iella/other/collab/partials/annotations') ?>
	<?= $this->view('api/iella/other/collab/partials/conversations') ?>

</div>
<div style="font-size:12px;color:#999;text-align:center;margin-top:40px;">
	To unsubscribe from these notifications please email us:<br>
	<?= $vd->esc($ci->conf('list_unsubscribe_email')) ?>
</div>
<?= $this->view('email/container/footer'); ?>