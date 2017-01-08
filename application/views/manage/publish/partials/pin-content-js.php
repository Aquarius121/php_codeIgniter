<script>

(function() {
	if (window.bootbox !== undefined) return;
	var element = document.createElement("script");
	var src = <?= json_encode(concat($vd->assets_base, "lib/bootbox.min.js")) ?>;
	element.setAttribute("src", src);
	document.body.appendChild(element);
})();

$(function(){
	
	$("li.pin-content a").on("click", function(ev) {

		ev.preventDefault();
		var _this = $(this);
		var content_id = _this.data("content-id");
		var url = "manage/publish/common/pin";	
		$.get(url, { content_id: content_id, priority: 5 }, function(res) {

			if (res.success)  {

				var message = $.create("div");
				var select = $.create("select");
				select.addClass("col-md-7 col-sm-12");
				
				select.append($.create("option").val(10).text("Priority 10 (Highest)"));
				select.append($.create("option").val(9) .text("Priority 9  (High)"));
				select.append($.create("option").val(8) .text("Priority 8  (High)"));
				select.append($.create("option").val(7) .text("Priority 7  (High)"));
				select.append($.create("option").val(6) .text("Priority 6  (High)"));
				select.append($.create("option").val(5) .text("Priority 5  (Normal)").attr("selected", true));
				select.append($.create("option").val(4) .text("Priority 4  (Low)"));
				select.append($.create("option").val(3) .text("Priority 3  (Low)"));
				select.append($.create("option").val(2) .text("Priority 2  (Low)"));
				select.append($.create("option").val(1) .text("Priority 1  (Lowest)"));

				var text = $.create("p");
				text.text("The content has been pinned to the top of the newsroom.");
				var options = $.create("div");
				options.addClass("row");
				message.append(text);
				message.append(options);
				options.append(select);

				var modal = bootbox.alert(message.html());

				select = $(modal).find("select");
				select.on_load_select()
				select.on("change", function() {
					if (!select.val()) return;
					$.get(url, { 
						content_id: content_id,
						priority: select.val(),
					});
				});

				var _this_li = _this.parent();
				var ul = _this_li.parent();
				var unpin = ul.find("li.unpin-content");
				unpin.removeClass("hidden");
				_this_li.addClass("hidden");

			} else {

				bootbox.alert("error occured");

			}

		});

	});

	$("li.unpin-content a").on("click", function(ev) {

		ev.preventDefault();
		var _this = $(this);
		var content_id = _this.data("content-id");
		var url = "manage/publish/common/pin/remove";	
		$.get(url, { content_id: content_id }, function(res) {

			if (res.success) {

				bootbox.alert("The content has been unpinned.");
				var _this_li = _this.parent();
				var ul = _this_li.parent();
				var pin = ul.find("li.pin-content");
				pin.removeClass("hidden");
				_this_li.addClass("hidden");

			} else {

				bootbox.alert("error occured");

			}

		});

	});

})

</script>