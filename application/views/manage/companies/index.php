<?php $credits_total = Auth::user()->newsroom_credits_total(); ?>
<?php $credits_available = Auth::user()->newsroom_credits_available(); ?>

<?php if ($credits_total): ?>
<div id="out-of-credits" class="below-header-feedback
	<?= value_if_test($credits_available, 'hidden') ?>">	
	<div class="alert alert-warning">
		<strong>Attention!</strong>
		You've used all available newsroom credits. 
		<a href="manage/upgrade">Purchase</a> more credits.
	</div>
</div>
<?php endif ?>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 page-title">
				<h2>
					Manage Companies
				</h2>
			</div>

			<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
				<div class="actions">
					<form action="manage/companies/create" method="post" class="inline-header-element">
						<div class="input-group">
							<input type="text" placeholder="Company Name" name="company_name" class="form-control" />
							<div class="input-group-btn">
								<button type="submit" class="btn btn-success">Create</button>
							</div>
						</div>
					</form>
					<a href="manage/companies/download" class="btn btn-default inline-header-element">Export</a>
				</div>
			</div>
		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">

				<?php if ($vd->has_archived || $vd->is_archived_list): ?>
				<div class="panel-heading">
					<ul id="tabs" class="nav nav-tabs nav-activate ax-loadable"
						data-ax-elements="#ax-chunkination, #ax-tab-content">
						<li><a data-on="^manage/companies(/[0-9]+)?$" href="<?= gstring('manage/companies') ?>">Companies</a></li>
						<li><a data-on="^manage/companies/archived" href="<?= gstring('manage/companies/archived') ?>">Archived</a></li>
					</ul>
				</div>	
				<div class="tab-content" id="ax-tab-content">
					<div class="tab-pane fade in active">			
				<?php endif ?>

				<!-- tab content -->
					<!-- tab pane -->
						<div class="table-responsive">
							<table class="table newsroom-list 
								<?= value_if_test(!$credits_available, 'locked') ?>">
								<thead>
									
									<tr>
										<th class="left">Company Name</th>
										<th>Press Contact</th>
										<th>Newsroom</th>
									</tr>
									
								</thead>
								<tbody>
									
									<?php foreach ($vd->results as $k => $result): ?>
									<tr class="newsroom-activation-status <?= value_if_test(
										$result->is_active, 'active', 'inactive') ?>">
										<td class="left">
											<h3>
												<a href="<?= $result->url('manage/dashboard') ?>">
													<?= $vd->esc($result->company_name) ?></a>
												<?php if (!$k && !$vd->chunkination->offset() 
														&& $result->order_default >= 0 
														&& !$vd->is_archived_list): ?>
												<span>&nbsp;(Default)</span>
												<?php endif ?>
												<?php if ($result->is_reseller_controlled): ?>
												<span class="text-muted">&nbsp;(Reseller)</span>
												<?php endif ?>
											</h3>
											<ul class="actions">
												<?php if ($result->is_archived): ?>
												<li><a href="manage/companies/archive/<?= $result->company_id ?>">Restore</a></li>
												<li><a class="delete-confirm" href="manage/companies/delete/<?= $result->company_id ?>">Delete</a></li>
												<?php else: ?>
												<li><a href="<?= $result->url('manage/newsroom/company') ?>">Edit</a></li>
												<li><a href="manage/companies/set_default/<?= $result->company_id ?>">Set Default</a></li>								
												<li><a href="manage/companies/archive/<?= $result->company_id ?>">Archive</a></li>
												<?php endif ?>
											</ul>
										</td>						
										<td>
											<?php if (@$result->m_contact): ?>
											<div><?= $vd->esc($result->m_contact->name) ?></div>
											<div class="text-muted"><?= $vd->esc($result->m_contact->email) ?></div>
											<?php else: ?>
											<span>-</span>
											<?php endif ?>
										</td>
										<td>
											<span class="active-text">Active</span>
											<span class="inactive-text">Inactive</span>
											<?php if (!$result->is_archived): ?>
											(<a class="newsroom-activation" href="#"><!-- 
											--><input type="hidden" name="company_id" value="<?= $result->company_id ?>" /><!--
										   --><span class="inactive-text">Activate</span><!--
											--><span class="active-text">Deactivate</span><!--
											--></a>)
											<?php endif ?>
											<?php if ($result->is_archived): ?>
											<span class="active-text">
												(<a href="<?= $result->url() ?>">View</a>)
											</span>
											<?php else: ?>
											<div><a href="<?= $result->url() ?>">View Newsroom</a></div>
											<?php endif ?>
										</td>
									</tr>
									<?php endforeach ?>

								</tbody>
							</table>
						</div>

				<?php if ($vd->has_archived || $vd->is_archived_list): ?>				
					</div> <!-- tab pane -->
				</div> <!-- tab content -->
				<?php endif ?>

			</div>
		</div>
	</div>

	<div id="ax-chunkination">
		<div class="ax-loadable" 
			data-ax-elements="#ax-chunkination, #ax-tab-content">
			<?= $vd->chunkination->render() ?>
		</div>

		<p class="pagination-info ta-center">Displaying <?= count($vd->results) ?> 
			of <?= $vd->chunkination->total() ?> Companies</p>
	</div>
			
</div>

<?php 

	$loader = new Assets\JS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('lib/bootbox.min.js');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<script>
	
$(function() {
	
	var buttons = $(".newsroom-activation");
	var out_of_credits = $("#out-of-credits");
	var newsroom_list = $(".newsroom-list");
	var quick_activate_newsroom = $(".quick-activate-newsroom");
	
	buttons.on("click", function() {
		
		var _this = $(this);
		var container = _this.parents(".newsroom-activation-status");
		var data = _this.find("input").serialize();
		container.addClass("has-loader");
		
		$.post("manage/companies/activation", data, function(res) {
			
			container.removeClass("has-loader");
			out_of_credits.toggleClass("hidden", !res.is_at_limit);
			container.toggleClass("active", res.is_active);
			container.toggleClass("inactive", !res.is_active);
			newsroom_list.toggleClass("locked", res.is_at_limit);
			_this.blur();
			
		});
		
		return false;
		
	});
	
	quick_activate_newsroom.on("click", function() {
		
		data = { company_id: quick_activate_newsroom.data("upgrade-company") };
		$.post("manage/companies/activation", data, function(res) {
			window.location = quick_activate_newsroom.data("upgrade-redirect");
		});
		
		return false;
		
	});

	// ----------------------------------------

	var delete_links = $(".delete-confirm");
	var delete_message = "<div class=\"ta-center marbot-20\"><h3 class=\"status-false\">\
		Caution!<\/h3><br \/>Are you sure you want to delete the company?<br \/>\
		<span class=\"status-false\">This action cannot be reversed!<\/span><\/div>";

	delete_links.on("click", function(ev) {
		var confirm_action = $(this).attr("href");
		ev.preventDefault();
		bootbox.confirm(delete_message, function(confirmed) {
			if (confirmed) window.location = confirm_action;
		});
	});
	
});
	
</script>