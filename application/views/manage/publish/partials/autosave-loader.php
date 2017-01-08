<script>

$(function() {

	setTimeout(function() {

		var form = $("#content-form");
		var data = <?= json_encode($vd->autosave->raw_data()) ?>;
		$("#relevant-resources input").attr("disabled", false);

		form.deserialize(data, {
			change: function() {
				$.fn.change.call($(this));
			},
		});

		var images = data._images;
		var files = data._files;
		var beats = data._beats;
		var microlists = data._microlists;
		var distcust = data._distcust;

		var load_images = function() {
		
			if (!images || 
				 !images.length)
				return;

			var images_ul = $("#web-images-list");
			var li_containers = images_ul.find("li.web-images-item-li");
			
			$.each(images, function(i, image) {

				var li_container = li_containers.eq(i);
				var container = li_container.find("a.images-list-item");
				var preview_image = container.find(".s-existing img");
				var image_id_input = container.find("input.image_id");
				var meta_input = container.find("input.meta_data");

				var web_image_meta = li_container.find("div.web-image-meta");
				var web_image_meta_alt = web_image_meta.find("input.web-image-meta-alt");
				var web_image_meta_caption = web_image_meta.find("textarea.web-image-meta-caption");
				var prn_checkbox = web_image_meta.find("input.web-image-meta-prn");

				li_container.removeClass("s-select");
				container.removeClass("s-select");

				new_preview_image = $.create("img");
				new_preview_image.attr("src", image.thumb_src);
				image_id_input.val(image.id);
				preview_image.replaceWith(new_preview_image);

				preview_image = new_preview_image;
				li_container.addClass("s-existing");
				container.addClass("s-existing");

				web_image_meta_alt.val(image.alt);
				web_image_meta_caption.val(image.caption);

				prn_checkbox.prop("checked", !!image.prn_is_checked);
				prn_checkbox.trigger("change");

			});

		};

		var load_files = function() {
		
			if (!files || 
				 !files.length)
				return;

			var cf_upload = $("#content-file-upload");
			var containers = cf_upload.find("div.web-file");
			
			$.each(files, function(i, file) {

				var container = containers.eq(i);
				var fake_text = container.find(".fake-text");
				var stored_file_id_input = container.find(".stored-file-id");
				var stored_file_name_input = container.find(".stored-file-name");
				var remove_button = container.find(".remove-button");

				remove_button.attr("disabled", false);
				fake_text.removeClass("progress");

				stored_file_id_input.val(file.stored_id);
				stored_file_name_input.val(file.stored_name);
				fake_text.val(file.stored_name);

			});	

		};

		load_images();
		load_files();

		if (distcust && distcust.state) {
			$("#dist-cust-state")
				.val(distcust.state)
				.trigger("change");
		}

		window.on_load_select(function() {

			// now loading categories/beats 
			var add_industry = $(".add-industry");
			var beats_to_add = beats.length - 3;
			if (beats_to_add > 0) 
				for (i = 0; i < beats_to_add; i++)
					add_industry.trigger("click");

			var beat_selects = $(".select-category select");
			for (i = 0; i < beats.length; i++) 
				beat_selects.eq(i).val(beats[i]).trigger("change");
			for (; i < beat_selects.length; i++)
				beat_selects.eq(i).val("").trigger("change");

			// now loading microlists
			for (i = 0; i < microlists.length; i++) {
				var last = $("select.microlist-select").last();
				last.val(microlists[i]).trigger("change");
			}

		});

	}, 0);

});

</script>