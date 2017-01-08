<form method="post" id="selectable-form" action="admin/nr_builder/<?= $vd->nr_source ?>/export_auto_built_nrs_to_csv">
	<div class="row-fluid">
		<div class="span12">
			<header class="page-header">
				<div class="row-fluid">
					<div class="span5">
						<h1>Auto Built NRs (<?= $vd->nr_source_title ?>)</h1>
					</div>

					<div class="span7">
						<?= $ci->load->view('admin/nr_builder/partials/export_buttons_header') ?>
					</div>
				</div>
			</header>
		</div>
	</div>

	<?= $this->load->view('admin/nr_builder/partials/sub_menu') ?>
	<?= $this->load->view('admin/partials/filters') ?>
	<?= $this->load->view('admin/nr_builder/partials/auto_built_nrs_active_filters') ?>

	<div class="row-fluid">
		<div class="span12">
			<div class="content listing">
				<div class="row-fluid">
					<?= $this->load->view('admin/nr_builder/partials/auto_built_nrs_listing_chunk_size') ?>
					<?= $this->load->view('admin/nr_builder/partials/auto_built_nrs_filters') ?>
				</div>
				
				<?= $this->load->view('admin/nr_builder/partials/auto_built_nrs_results') ?>
			
			</div>
		</div>
	</div>
</form>