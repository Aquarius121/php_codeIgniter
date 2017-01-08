<?= $ci->load->view('manage/account/menu') ?>

<div class="container-fluid">

	<header>
		<div class="row">
			<div class="col-lg-12 page-title">
				<h2>Transactions</h2>
			</div>
		</div>
	</header>

	<?= $ci->load->view('manage/account/transaction/partials/list') ?>

</div>