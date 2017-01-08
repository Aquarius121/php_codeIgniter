<div class="panel panel-default companies-managed">
	<div class="panel-heading">
		<h3 class="panel-title">Companies Managed <small><a href="manage/companies">View all Companies</a></small></h3>
	</div>
	<div class="panel-body">
		<ul class="row">

			<?php foreach ($vd->user_newsrooms as $nr): ?>
			<li class="col-lg-3 col-md-4">
				<div class="company clearfix company-manage-link"
					data-manage-link="<?= $nr->url('manage') ?>">
					<?php if ($nr->color): ?>
					<span class="company-icon company-color" style="background: <?= $nr->color ?>">
					<?php else: ?>
					<span class="company-icon company-color">
					<?php endif ?>
						<?= $nr->abbr(2) ?>
					</span>
					<h4><?= $vd->esc($nr->company_name) ?></h4>
					<a class="company-newsroom-link"
						href="<?= $nr->url() ?>"
						target="_blank">
						View Newsroom
					</a>
				</div>
			</li>			
			<?php endforeach ?>

			<script>

			$(function() {

				$(".company-manage-link").on("click", function(ev) {
					if ($(ev.target).is(".company-newsroom-link")) return;
					var elm = $(this);
					var url = elm.data("manage-link");
					window.location = url;
				});

			});

			</script>
			
			<li class="col-lg-3 col-md-4">
				<div class="company clearfix company-add-link">
					<span class="company-icon company-color"><i class="fa fa-plus"></i></span>
					<h4>New Company</h4>
					<a href="#<?= $vd->new_company_modal_id ?>"
						data-toggle="modal">Add Newsroom</a>
				</div>
			</li>

			<script>

			$(function() {

				$(".company-add-link").on("click", function(ev) {
					if ($(ev.target).is("a")) return;
					$(this).children("a").trigger("click");
				});

			});

			</script>

		</ul>
	</div>
</div>