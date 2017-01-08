<script>

$(function() {

	window.autosave.on_save.push(function(data) {
		
		var make_thumb_array = function() {
			
			var images = [];

			$("input.web-image-input").each(function() {

				var _this = $(this);

				if (_this.val()) {

					var image_id = _this.val();
					var container = _this.parent();
					var s_existing_img = container.find("span.s-existing img");
					var thumb_src = s_existing_img.attr("src");

					var li_container = container.parent();
					var web_image_meta = li_container.find("div.web-image-meta");
					var web_image_meta_alt = web_image_meta.find("input.web-image-meta-alt");
					var image_alt = web_image_meta_alt.val();
					var web_image_meta_caption = web_image_meta.find("textarea.web-image-meta-caption");
					var image_caption = web_image_meta_caption.val();

					var prn_is_checked = 0;
					var prn_checkbox = web_image_meta.find("input.web-image-meta-prn");
					if (prn_checkbox.is(":checked"))
						prn_is_checked = 1;

					var image = {
						id: image_id,
						thumb_src: thumb_src,
						alt: image_alt,
						caption: image_caption, 
						prn_is_checked: prn_is_checked,
					};

					images.push(image);

				}

			});

			data._images = images;

		};		
		
		var make_files_array = function() {

			var upload_faker = $(".file-upload-faker");
			var files = [];

			upload_faker.each(function() {
				var _this = $(this);
				var input_stored_id = _this.find("input.stored-file-id");
				var input_stored_name = _this.find("input.stored-file-name");
				var stored_file_id = input_stored_id.val();
				var stored_file_name = input_stored_name.val();
				if (stored_file_id) {
					files.push({
						stored_id: stored_file_id,
						stored_name: stored_file_name,
					});
				}
			});

			data._files = files;

		};

		var make_beats_array = function() {

			var beats_div = $(".select-beats");
			var beat_selects = beats_div.find("select.category");
			var beats = [];

			beat_selects.each(function() {
				var _this = $(this);
				var beat_id = _this.val();
				if (beat_id)
					beats.push(beat_id);
			});

			data._beats = beats;

		};
		
		var make_microlists_array = function() {

			var microlist_selects = $("select.microlist-select");
			var microlists = [];

			microlist_selects.each(function(){
				var _this = $(this);
				var microlist_id = _this.val();
				if (microlist_id)
					microlists.push(microlist_id);
			});

			data._microlists = microlists;

		};

		data._distcust = {};
		data._distcust.state = $("#dist-cust-state").val();

		make_thumb_array();
		make_files_array();
		make_beats_array();
		make_microlists_array();

		return data;

	});

});

</script>