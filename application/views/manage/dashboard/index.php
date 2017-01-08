<div class="container-fluid">

	<?= $ci->load->view('manage/dashboard/partials/header') ?>

	<div class="row">
		<div class="col-lg-6">
			<?= $ci->load->view('manage/dashboard/partials/membership-level') ?>
		</div>
		<div class="col-lg-6">
			<?= $ci->load->view('manage/dashboard/partials/quick-stats') ?>
		</div>
	</div>

	<?= $ci->load->view('manage/dashboard/partials/chart') ?>

	<div class="row">
		<div class="col-lg-12">
			<?= $ci->load->view('manage/dashboard/partials/submissions') ?>
		</div>
	</div>

</div>