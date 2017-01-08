<div class="container-fluid">

	<header>
		<div class="row">
			<div class="col-md-6 page-title">
				<h2>Overview Dashboard</h2>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-6">
			<?= $ci->load->view('manage/dashboard/partials/membership-level') ?>
		</div>
		<div class="col-lg-6">
			<?= $ci->load->view('manage/dashboard/partials/quick-stats') ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<?= $ci->load->view('manage/dashboard/partials/companies') ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<?= $ci->load->view('manage/dashboard/partials/submissions') ?>
		</div>
	</div>

</div>