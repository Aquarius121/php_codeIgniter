<form method="post" id="filter-form" action="admin/nr_builder/stats" name="selectable_form">
	<div class="row-fluid">
		<div class="span12">
			<header class="page-header">
				<div class="row-fluid">
					<div class="span6">
						<h1>NR Builder Stats</h1>
					</div>
				</div>
			</header>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12">
			<div class="content">	
				<?= $this->load->view('admin/nr_builder/stats/filter') ?>
			</div>
		</div>
	</div>

	<?php if ($vd->load_stats_table): ?>
		<?= $this->load->view('admin/nr_builder/stats/table') ?>
	<?php endif ?>

	<?php if ($vd->load_single_stats): ?>
		<?php foreach ($vd->sources as $source): ?>
			<?php if (in_array($source, $vd->sources_selected)): ?>
				
				<?= $this->load->view('admin/nr_builder/stats/single', array('source' => $source)) ?>

			<?php endif ?>
		<?php endforeach ?>
	<?php endif ?>



	<script>
	defer(function(){

		$("a.stats-table-price").on("click", function(ev) {
			ev.preventDefault();
			var _this = $(this);
			var t_date = _this.data("date");
			var t_source = _this.data("source");
			var modal_id = "<?= $vd->t_modal_id ?>";
			var content_url = "admin/nr_builder/stats/transaction_detail/"+t_source+"/"+t_date;

			var modal = $("#" + modal_id);
			var modal_content = modal.find(".modal-content");
			modal_content.load(content_url, function() {
				modal.modal('show');
			});

		});

	});
	</script>	
</form>
