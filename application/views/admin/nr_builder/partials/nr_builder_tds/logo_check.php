<?php if ($vars->result->logo_image_path && $vars->result->is_logo_valid): ?>
	<td class="success" id="logo_good_td_<?= $vars->result->source_company_id ?>">
		<a title="<?= $vars->result->website ?>" href="<?= $vars->result->website ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>

	<td id="logo_good_td_<?= $vars->result->source_company_id ?>">
		<?php if ($vars->result->logo_image_path && !$vars->result->is_logo_valid): ?>
			<button type="button" class="btn btn-small logo_good btn-success marbot-5" 
				data-id="<?= $vars->result->source_company_id ?>">
				Logo Good
			</button>
		<?php endif ?>

		<?php if ($vars->result->logo_image_path && !$vars->result->is_logo_valid): ?>
			<button type="button" class="btn btn-small logo_bad btn-danger" 
				data-id="<?= $vars->result->source_company_id ?>">
				Logo Bad
			</button>
	<?php endif ?>
	</td>
<?php endif ?>

<script>
$(function() {

	var ajax_post_logo_status = function(source_company_id, is_logo_valid)
	{
		var url = "admin/nr_builder/<?= $vd->nr_source ?>/update_logo_status";
		$.ajax({
			type: "POST",
			url: url,
			data: { <?= $vd->nr_source ?>_company_id: source_company_id, is_logo_valid: is_logo_valid },
			success: function(d)
			{
				if (d)
				{
					var good_logo_td = "#logo_good_td_"+source_company_id;
					$(good_logo_td).html('');

					var bad_logo_td = "#logo_bad_td_"+source_company_id;
					$(bad_logo_td).html('');

					if (!is_logo_valid)
					{
						var logo_td = "#td_logo_"+source_company_id;
						$(logo_td).html('');
					}
				}
				else
				{
					alert('Error updating logo status');
				}
			}
		});
	};

	$(".logo_good").on("click", function(){
		var _this = $(this);
		var source_company_id = _this.data("id");
		ajax_post_logo_status(source_company_id, 1);
	});

	$(".logo_bad").on("click", function(){
		var _this = $(this);
		var source_company_id = _this.data("id");
		ajax_post_logo_status(source_company_id, 0);
	});

});
</script>