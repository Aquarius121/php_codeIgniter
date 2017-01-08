<script>

$(function() {
	
	var is_main_page = <?= json_encode((int) $vd->newsroom_main_page) ?>;
	var is_auto_built_unclaimed_nr = <?= json_encode((int) $vd->is_auto_built_unclaimed_nr) ?>;

	window.setTimeout(function() {

		var ln_container_loader = $("#ln-container-loader");
		var items_container = $("#ln-container");
		ln_container_loader.show();

		$.ajax({
			type: "POST",
			url: "browse/refresh_social_content",
			success: function(res) {

				ln_container_loader.hide();
				if (!res || !res.data)
					return;
					
				$(".social-stream").remove();
				
				var $boxes = $(res.data);
				var $otherboxes = $(".ln-block:not(.pinned)").detach();
				var $pinned_boxes = $(".ln-block.pinned").detach();

				items_container.masonry("destroy");
				items_container.empty();
				items_container.append($pinned_boxes);
				items_container.append($boxes);
				
				if (is_main_page) {

					if (is_auto_built_unclaimed_nr) 
					     items_container.prepend($otherboxes);
					else items_container.append($otherboxes);	
					var _columnized = items_container.columnize({
						columns: 3,
						margin: 20,
						width: 200
					});

				} else {

					items_container.append($otherboxes);
					items_container.imagesLoaded(function() {
						items_container.addClass("masonry-before");
						items_container.masonry({
							itemSelector: ".ln-block",
							gutter: 20,
							columns: 3,
							transitionDuration: 0
						});
					});

				}

				var filter_social_type = <?= json_encode($vd->filter_social_type) ?>;
				if (filter_social_type) {
					var selector = ("#dcsns-filter li.f-{{0}} > a")
						.format(filter_social_type);
					$(selector).trigger("click");
				}
				
			}
		});

	}, 100);
	
});

</script>