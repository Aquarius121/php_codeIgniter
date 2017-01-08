<?php if ($vars->result->website && $vars->result->is_website_valid): ?>
	<td class="success" id="web_good_td_<?= $vars->result->source_company_id ?>">
		<a title="Name Valid" href="<?= $vars->result->website ?>"
			target="_blank" class="tl"><i class="icon-ok"></i></a>
	</td>
<?php else: ?>

	<td id="web_good_td_<?= $vars->result->source_company_id ?>">
		<?php if ($vars->result->website && !$vars->result->is_website_valid): ?>
			<button type="button" class="btn btn-small web_good btn-success marbot-5" 
				data-id="<?= $vars->result->source_company_id ?>">
				Web Good
			</button>
		<?php endif ?>

		<?php if ($vars->result->website && !$vars->result->is_website_valid): ?>
			<button type="button" class="btn btn-small web_bad btn-danger" 
				data-id="<?= $vars->result->source_company_id ?>">
				Web Bad
			</button>
	<?php endif ?>
	</td>
<?php endif ?>

<script>
$(function() {

	var ajax_post_web_status = function(source_company_id, is_website_valid)
	{
		var url = "admin/nr_builder/<?= $vd->nr_source ?>/update_web_status";
		$.ajax({
			type: "POST",
			url: url,
			data: { <?= $vd->nr_source ?>_company_id: source_company_id, is_website_valid: is_website_valid },
			success: function(d)
			{
				if (d)
				{
					var good_web_td = "#web_good_td_"+source_company_id;
					$(good_web_td).html('');
					
					if (!is_website_valid)
					{
						var company_web_span = "#td_website_"+source_company_id;
						$(company_web_span).addClass('fail');
						$(company_web_span).html("<i class='icon-remove'></i>");
						$(good_web_td).addClass('fail');
						$(good_web_td).html("<i class='icon-remove'></i>");
					}
					else
					{
						$(good_web_td).addClass('success');
						$(good_web_td).html("<i class='icon-ok'></i>");
					}
				}
				else
				{
					alert('Error updating website status');
				}
				
			}
		});
	};

	$(".web_good").on("click", function(){
		var _this = $(this);
		var source_company_id = _this.data("id");
		ajax_post_web_status(source_company_id, 1);
	});

	$(".web_bad").on("click", function(){
		var _this = $(this);
		var source_company_id = _this.data("id");
		ajax_post_web_status(source_company_id, 0);
	});

});
</script>