<script>
defer(function() {

	var textbox_html =  $(".text_box_div").html();
	var prev_td_html = "";
	var field_name = "";
	var text_box_span = "";
	var times_called = 0;

	$("a.inline-edit").on("click", function(ev) {
		ev.preventDefault();
		var _this = $(this);
		var id = _this.data("id");
		var modal_id = "<?= $vd->company_modal_id ?>";
		var content_url = "admin/nr_builder/<?= $vd->nr_source ?>/edit/" + id + "/1";

		var modal = $("#" + modal_id);
		var modal_content = modal.find(".modal-content");
		modal_content.load(content_url, function() {
			modal.modal('show');
		});	

	});	

	$(".fail a").on("click", function(ev){
		ev.preventDefault();
		times_called++;
		if ($("#instant_edit_text").length && times_called > 1) 
			return;
		var _this = $(this);
		prev_td_html = _this.parent().html();
		//alert(prev_td_html);
		var field = _this.data("field");
		field_name = field;
		var id = _this.data("id");
		var company_name = $("#company_name_"+id).html();
		var title = _this.data("title");
		var edit_title = "Edit "  + title;
		var td_id = _this.data("tdid");
		var modal_text_box_div = $(".text_box_div");
		text_box_span = "text_span_"+id+"_"+field;
		if (field != "address" && field != "about_the_company")
		{
			$("#"+text_box_span).html(textbox_html);
			modal_text_box_div.html('');
		}
		else
		{
			$("#instant_company_name").html(company_name);
			$("#modalLabel").html(edit_title);
			modal_text_box_div.html('');
		}

		var instant_edit_text = $("#instant_edit_text");
		$("#selectable-form")[0].reset();
		instant_edit_text.attr("placeholder", title);
		instant_edit_text.val('');

		$("#instant_edit_company_id").val(id);
		$("#instant_edit_field").val(field);
		$("#instant_td_id").val(td_id);		

		if (field == "address")
			$("#instant_edit_address_section").removeClass("dnone");
		else
			$("#instant_edit_address_section").addClass("dnone");

		if (field == "about_the_company")
		{
			$("#instant_edit_about").removeClass("dnone");
			$("#instant_edit_text_div").addClass("dnone");
		}
		else
		{
			$("#instant_edit_about").addClass("dnone");
			$("#instant_edit_text_div").removeClass("dnone");
		}
		instant_edit_text.focus();
	});

	var ajax_post_form = function()
	{
		var instant_edit_text = $("#instant_edit_text");
		if (instant_edit_text.length && instant_edit_text.val() == "")
		{
			$("#"+text_box_span).html('');
			return;
		}

		var url = "admin/nr_builder/<?= $vd->nr_source ?>/instant_edit_save";
		$.ajax({
			type: "POST",
			url: url,
			data: $("#selectable-form").serialize(), 
			success: function(data)
			{
				var field_val = "";

				if (field_name == "address" || field_name == "about_the_company")
					$("#instant_edit_modal").modal('toggle');
				
				if (field_name == "about_the_company")
					field_val = $("#instant_edit_short_description").val();

				else if (field_name == "address")
					field_val = $("#instant_edit_address").val();

				else
					field_val = $("#instant_edit_text").val().trim();
				
				if (field_val != "")
				{
					var html = "<a title='" + field_val + "'";
					html += " class='tl'><i class='icon-ok'></i></a>";
					var td_id = $("#instant_td_id").val();
					var td = $("#"+td_id);
					td.addClass("success");
					td.removeClass("fail");
					td.html(html);
				}
				else
				{
					$("#"+text_box_span).html('');
					return;
				}
			}
		});
	};

	$("#instant_save_button").on("click", function() {
		ajax_post_form();
		return false;
	});

});
</script>