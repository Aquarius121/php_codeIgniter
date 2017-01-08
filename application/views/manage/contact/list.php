<div class="container-fluid">
	<header>
		<div class="row">

			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 page-title">
				<h2>Contacts Manager</h2>
			</div>

			<div class="ax-action-top col-lg-8 col-md-8 col-sm-12 col-xs-12">

				<div class="actions">
					<form action="manage/contact/list/edit/save" method="post" class="inline-header-element">
						<div class="input-group add-contact-list">
							<input type="text" class="form-control" placeholder="List Name" name="name">
   						<span class="input-group-btn">
				        		<button class="btn btn-success" type="submit">Create List</button>
   						</span>
						</div>
					</form>
					<a class="btn btn-default inline-header-element"
						href="manage/contact/import">Import</a>
				</div>
			</div>

		</div>
	</header>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel with-nav-tabs panel-default">
				<div class="panel-heading">
					<ul class="nav nav-tabs nav-activate tab-links ax-loadable" 
						data-ax-elements=".ax-search-form, .ax-action-top, #ax-chunkination, #ax-tab-content" id="tabs">
						<li><a data-on="^manage/contact/list" data-toggle="link"
							href="manage/contact/list">Lists</a></li>
						<li><a data-on="^manage/contact/contact" data-toggle="link"
							href="manage/contact/contact">Contacts</a></li>
					</ul>
				</div>
				<?= $ci->load->view('manage/contact/partials/list_listing') ?>
			</div>
		</div>
	</div>

	<div id="ax-chunkination">
		<div class="ax-loadable"
			data-ax-elements="#ax-chunkination, #ax-tab-content">
			<?= $vd->chunkination->render() ?>
		</div>

		<p class="pagination-info ta-center">
			Displaying <?= count($vd->results) ?> 
			of <?= $vd->chunkination->total() ?> Lists
		</p>
	</div>
</div>

<script>
$(function() {

	$(document).on("click", ".pw-order-detail", function(ev) {

		ev.preventDefault();
		var id = $(this).data("id");

		var content_url = "manage/contact/campaign/load_pw_order_detail_modal/" + id;
		var modal = $("#<?= $vd->pw_detail_modal_id ?>");

		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal('show');
		});
			
	});

});
</script>