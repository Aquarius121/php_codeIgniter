<?php if ($vars->result->name && $vars->result->is_name_valid): ?>
	<td class="success" id="name_good_td_<?= $vars->result->source_company_id ?>">
		<a title="Name Valid" href="<?= $vars->result->website ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>

	<td id="name_good_td_<?= $vars->result->source_company_id ?>">
		<?php if ($vars->result->name && !$vars->result->is_name_valid): ?>
			<button type="button" class="btn btn-small name_good btn-success marbot-5" 
				data-id="<?= $vars->result->source_company_id ?>">
				Name Good
			</button>
		<?php endif ?>

		<?php if ($vars->result->name && !$vars->result->is_name_valid): ?>
			<button type="button" class="btn btn-small name_bad btn-danger" 
				data-id="<?= $vars->result->source_company_id ?>">
				Name Bad
			</button>
	<?php endif ?>
	</td>
<?php endif ?>

<script>
$(function() {

	var ajax_post_name_status = function(source_company_id, is_name_valid)
	{
		var url = "admin/nr_builder/<?= $vd->nr_source ?>/update_name_status";
		$.ajax({
			type: "POST",
			url: url,
			data: { <?= $vd->nr_source ?>_company_id: source_company_id, is_name_valid: is_name_valid },
			success: function(d)
			{
				if (d)
				{
					var good_name_td = "#name_good_td_"+source_company_id;
					$(good_name_td).html('');

					var bad_name_td = "#name_bad_td_"+source_company_id;
					$(bad_name_td).html('');

					if (!is_name_valid)
					{
						var company_name_span = "#company_name_"+source_company_id;
						$(company_name_span).html('');
					}
				}
				else
				{
					alert('Error updating name status');
				}
				
			}
		});
	};

	$(".name_good").on("click", function(){
		var _this = $(this);
		var source_company_id = _this.data("id");
		ajax_post_name_status(source_company_id, 1);
	});

	$(".name_bad").on("click", function(){
		var _this = $(this);
		var source_company_id = _this.data("id");
		ajax_post_name_status(source_company_id, 0);
	});

});
</script>