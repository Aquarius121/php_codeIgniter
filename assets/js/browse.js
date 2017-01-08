$(function() {

	$("a.email-obfuscated").each(function() {
		var _this = $(this);
		if (!_this.hasClass("email-obfuscated")) return;
		_this.removeClass("email-obfuscated");
		var value = _this.attr("href");
		value = value.replace(/^mailto:/, "");
		var chars = value.split("");
		chars.reverse();
		value = chars.join("");
		value = "mailto:" + value;
		_this.attr("href", value);
	});

	$(".accordian").each(function() {
		var accordian = $(this);
		var toggle = accordian.find(".accordian-toggle");
		var content = accordian.find(".accordian-content");
		var parent = accordian.parents(".accordian-section");
		var others = parent.find(".accordian-content");
		toggle.on("click", function() {
			if (accordian.hasClass("open")) {
				content.slideUp(function() {
					accordian.removeClass("open");
				});
			} else {
				others.not(content).slideUp(function() {
					$(this).parent(".accordian")
						.removeClass("open");
				});
				content.slideDown(function() {
					accordian.addClass("open");
				});
			}
		});
	});

	$(".section-share a").click(function(){
		var u = jQuery(this).attr("href");
		window.open(u, "sharer", "toolbar=0,status=0,width=626,height=436");
		return false;
	});

	(function() {
		var containers = $(".bs3-container");
		var aside = $(".aside.aside-sidebar");
		if (!aside.size()) return;
		if (!aside.find(".aside-accordians").children().size() &&
			 !aside.find(".aside-social-list ul").children().size())
			containers.addClass("mini-container");
	})();

	if (window.devicePixelRatio > 1) {
		$(".has-2x").each(function() {
			var _this = $(this);
			var url = _this.data("url-2x");
			_this.attr("src", url);
		});
	}
	
	(function() {
		
		var _window = $(window);
		var _document = $(document);
		var _window_height = _window.height();
		var _limit_reached = false;
		var _request_active = false;
		var _list_container = $("#ln-container");
		var _loader = $("#ln-container-loader");
		if (!_list_container.hasClass("columnize") ||
			 !_list_container.size())
			return;

		var _columnized = _list_container.columnize({
			columns: 3,
			margin: 20,
			width: 200
		});

		var _columnized_update = function() {
			_columnized.update();
		};

		$(window).on("resize", function() {
			rate_limit(_columnized_update, 500);
		});
		
		var render_content = function(res) {
			
			_loader.hide();
			_request_active = false;
			if (!res) return _limit_reached = true;
			var elements = $(res.data);

			if (res.pixel) {
				var pixel = $.create("img");
				pixel.addClass("stat-pixel");
				pixel.attr("src", res.pixel);
				$(document.body).append(pixel);
			}

			elements.each(function() {
				var _this = $(this);
				_this.imagesLoaded(function() {
					_columnized.append(_this.get(0));
					_this.hide();
					_this.fadeIn(1000);
				});
			});
			
		};
		
		var request_content = function() {
			
			if (_request_active) return;
			if (_limit_reached) return;
			_request_active = true;
			
			var offset = _columnized.children.length;
			if (offset === 0) return;
			
			_loader.show();
			var data = { partial: true, offset: offset };			
			$.get(null, data, render_content);
			
		};
		
		var perform_check = function() {
			
			var scrollTop = _window.scrollTop();
			if (_document.height() - scrollTop < 
			    _window_height * 2) {
				request_content();
			}
			
		};
		
		_window.on("scroll", perform_check);
		if (_document.height() < _window_height * 2) 
			request_content();
		
	})();
	
	$.fn.lightbox = function() {
		
		var create_box = function() {
			
			var _this = $(this);
			
			// hide elements that contain flash content
			$(".has-flash-content").addClass("lightbox-hidden");
			
			// remove any existing
			$("#lightbox").remove();
			
			var href = _this.attr("href");
			var caption_text = _this.data("caption");
			var caption = $.create("div");
			
			if ($.trim(caption_text))
			{
				caption.addClass("caption");
				caption.text(caption_text);
			}
			
			var container = $.create("div");
			container.attr("id", "lightbox");
			var back = $.create("div");
			back.addClass("back");
			container.append(back);
			var boxz = $.create("div");
			boxz.addClass("boxz");
			var box = $.create("div");
			box.addClass("box");
			boxz.append(box);
			container.append(boxz);
			
			var cached = $.create("img");
			$(cached).on("load", function() {
				
				box.append(cached);
				box.append(caption);
				box.addClass("loaded");
				
				// the maximum width/height of the image (with 60 side)
				cached.css("max-width", ($(window).width() - 120));
				var max_height = ($(window).height() - 120);
				
				do {
					
					cached.css("max-height", max_height);
					box.css("width", cached.width());
					box.css("height", cached.height() + caption.outerHeight());
					max_height = max_height * 0.9;
				
				// reduce size until both caption + image within limits
				} while (box.outerHeight() > $(window).height() - 100) 
				
			});
			
			$(document.body).append(container);
			var close_lightbox = function() {
				$(".has-flash-content").removeClass("lightbox-hidden");
				container.remove();
			};
			
			container.on("click", close_lightbox);
			cached.on("click", close_lightbox);
			box.on("click", function() { return false; });
			
			setTimeout(function() {
				cached.attr("src", href);
				back.addClass("on");
				box.addClass("on");
			}, 0);
			
		};
		
		$(this).on("click", function() {
			create_box.call(this);
			return false;
		});
		
	};
	
	$(".use-lightbox").lightbox();
	
	(function() {
		
		var width = 640;
		var height = 480;
		
		var features = {
			directories: "no",
			location: "no",
			resizable: "yes",
			scrollbars: "no",
			status: "no",
			toolbar: "no"
		};
		
		$(".share-window").on("click", function(ev) {
			
			var _this = $(this);
			var url = _this.attr("href");
			if (!url) return;
			
			if (_this.data("width")) width = parseInt(_this.data("width"));
			if (_this.data("height")) height = parseInt(_this.data("height"));
						
			features.screenX = (window.screen.width / 2) - ((width / 2) + 10);
			features.screenY = (window.screen.height / 2) - ((height / 2) + 50);
			features.left = features.screenX;
			features.top = features.screenY;
			features.width = width;
			features.height = height;
			
			var features_arr = [];
			for (var idx in features)
				features_arr.push([idx, features[idx]].join("="))
			var features_str = features_arr.join(",");
			
			window.open(url, "_blank", features_str);
			ev.preventDefault();
			return false;
			
		});
		
	})();

});