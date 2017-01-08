<form method="post" id="selectable-form" action="admin/nr_builder/newswire_ca/export_to_csv">
<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span12">
					<h1>Website Source (<?= @$vd->web_source ?>)</h1>
				</div>
			</div>
		</header>
	</div>
</div>

<? //= $this->load->view('admin/nr_builder/newswire_ca/sub_menu') ?>
<? //= $this->load->view('admin/partials/filters') ?>


<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">ID</th>
						<th class="left">Company Name</th>
						<th class="left">Website</th>
					</tr>
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr class="result" id="row_<?= $result->id ?>">
						
						<td class="left">
							<?= $result->id ?>
						</td>
						<td class="left">
							<h3>
								<?= $vd->esc($result->name) ?>							
							</h3>
						</td>
						
						<td class="left">
							<a href="<?= $result->website ?>" target="_blank"><?= $result->website ?></a>
						</td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>

			<script>
				$(function(){

					$(".retry_logo").on("click", function(ev){
						var _this = $(this);
						ev.preventDefault();
						var id = _this.data("id");
						_this.html('');
						var msg_id = $("#message_"+id);
						msg_id.addClass("stats-loader");
						msg_id.html('&nbsp;');
						$.ajax({
							url: "admin/nr_builder/newswire_ca/retry_logo",
							data: {id: id},
							method: 'POST',
							success: function(status){
								var msg = "Failed to update logo";
								if (status == 1)
								{
									var html = "<div class='alert-success'>Logo updated</div>";
									msg_id.parent().html(html);
								}
								else
								{
									var error_id = "error_"+id;
									var link_id = $("#retry_logo_link_"+id);
									var img_path_link = $("#img_path_link_"+id);
									var t_box = $("#instant_edit_logo_div_"+id+" :text");
									var html = "<div class='alert-error' id='"+error_id+"'>Update Failed</div>";
									msg_id.html(html);
									msg_id.removeClass("stats-loader");
									_this.html('Retry');
									link_id.addClass("hidden");
									img_path_link.removeClass("hidden");
									t_box.val('');
								}
							}
						});
					});

					$(".img_path_link").on("click", function(ev){
						ev.preventDefault();
						var _this = $(this);	
						var id = _this.data('id');
						var edit_logo_div = $("#instant_edit_logo_div_"+id);
						var msg_id = $("#message_"+id);
						msg_id.html('');
						edit_logo_div.removeClass("hidden");
						_this.addClass("hidden");

					});

					$(".inline-edit-text").on("keyup", function(ev) {
						ev.preventDefault();
						if (ev.keyCode == 13){
							var _this = $(this);
							var id = _this.data('id');
							var logo_image_path = _this.val();
							ajax_post_form(id, logo_image_path);
						}
						return false;
					});

					$(".inline-edit-text").on("blur", function(ev) {						
						var id = $(this).data("id");
						hide_text_box(id);
						return false;
					});

					var hide_text_box = function(id){
						var instant_edit_logo_div = $("#instant_edit_logo_div_"+id);
						var retry_logo_link = $("#retry_logo_link_"+id);
						instant_edit_logo_div.addClass("hidden");
						retry_logo_link.removeClass("hidden");
					};
					

					var ajax_post_form = function(id, logo_image_path)
					{
						if (logo_image_path == "")
						{
							hide_text_box(id);
							return;
						}

						var msg_id = $("#message_"+id);
						var url = "admin/nr_builder/newswire_ca/instant_update_logo";
						$.ajax({
								type: "POST",
								url: url,
								data: {company_id: id, logo_image_path: logo_image_path},
								success: function(status)
								{
									//alert(status);
									var status_id = "status_"+id;
									if (status == 1)
									{
										var html = "<div class='alert-success' id='"+status_id+"'";
										html += ">Saved successfully</div>";
										msg_id.html(html);
									}
									else
									{										
										var html = "<div class='alert-error' id='"+status_id+"'>";
										html += "Save Failed</div>";
										msg_id.html(html);
										
									}

									setTimeout(function() {
										$("#"+status_id).slideUp('slow');
									}, 1000);

									hide_text_box(id);
								}
							});
					};
					
				});
			</script>
			
			<div class="clearfix">
				<div class="pull-left grid-report ta-left">
					All times are in UTC.
				</div>
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Companies
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>
</form>