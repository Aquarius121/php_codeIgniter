$(function() {

	if( $('.pp-elements') !== null ) {
		if( matchMedia( 'only screen and (max-width: 991px)' ).matches ) {
			var items = [];
			$('.pp-name').each(function() {
				items.push($(this).text());
			});

			$('.pp-elements:not(:first-child)').each(function() { 

				$(this).find('.pp-check').each(function(i, v) {
					var mark = $(this).html();

					$(this).html( mark + ' ' + (items[i]) );
				});
			});
		}
	}
	

	var msnry_block = $(".masonry");
	msnry_block.each(function() {
		var _this = $(this);
		$(this).imagesLoaded((function(_this) {
			return function() {
				_this.find("img").addClass("loaded");
				_this.masonry({
					itemSelector: ".news-item"
				});
			};
		})(_this));		
	});
	
	// --------------------------------------
	
	var _window = $(window);
	var _document = $(document.body);
	var footer = $(".footer");
	var update_footer = function() {
		if (_window.height() > _document.height())
		     footer.css("position", "fixed");
		else footer.css("position", "static");
	};
	
	_window.on("load", update_footer)
	_window.on("resize", update_footer);
	update_footer();
	
	// --------------------------------------
	
	window.__on_nav_callback = window.__on_nav_callback || [];
	window.__on_nav_callback.push(function() {
		
		var header = $("header.header");
		var has_second_nav = !!($("menu.navbar-nav > li.active > menu").size());
		header.toggleClass("navbar-fixed-top", !has_second_nav)
		header.toggleClass("fixed-submenu", has_second_nav);
		if (!has_second_nav) return;
			
		var enquire_match = function() {
			var nav = header.find(".nav > .active"),
				submenu = nav.find(".navbar-submenu"),
				nav_height = nav.height(),
				doc_top = $(document).scrollTop(),
				fixed_submenu_method;
			(fixed_submenu_method = function() {
				submenu.toggleClass("fixed", doc_top > nav_height);
			})();	
			$(window).scroll(function() {
				doc_top = $(document).scrollTop()
				fixed_submenu_method();
			});					
		};
		
		enquire.register("screen and (min-width: 768px)",
			{ match: enquire_match });
		
	});
	
});

// Old Profiles
/*$(document).ready(function(){
    $('div.thumb').each(function() {
        var $dropdown = $(this);

        $("div.thumbnail", $dropdown).click(function(e) {
            e.preventDefault();
            $div = $("div.caption", $dropdown);
            $div.toggle(100);
            $("div.caption").not($div).hide();
            return false;
        });
    });
});*/

// New Profiles
function initProfiles() {

	function getWindowWidth() {
		return Math.max( $(window).width(), window.innerWidth);
	}
	
	var windowWidth = getWindowWidth();
	var p_items = $('div.profile-item').length;
	
	if(windowWidth > 1199){
		var p_col = 4;
	} else if (windowWidth > 991) {
		var p_col = 3;
	} else if (windowWidth > 767) {
		var p_col = 2;
	} else {
		var p_col = 1;
	};
	
	$('div.profile-item').removeClass('active last');
	$('.col-md-12.profile-desc').remove();
	
	$('div.profile-item:nth-child('+ p_col +'n+1)').addClass('last');
	
	$('div.profile-item').each(function() {
		var p_number = $(this).data('profile-number');

		$('div.profile-item').each(function(i){
			$(this).attr('data-profile-number', (i+1));
		});
		
		if (p_number = p_items){
			$('div.profile-item[data-profile-number="'+ p_items +'"]').addClass('last');
		}

	});
	
	$(document).off('click', 'div.profile-item');
	$(document).off('click', 'a.close-btn');
	
	$(document).on('click', 'div.profile-item', function(e) {
		e.preventDefault();
		var p_content = $(this).children('div.caption').html();
		
		if($(this).hasClass('active')){
			$('div.profile-item').removeClass('active');
			$('.col-md-12.profile-desc').remove();
		} else {
			$('div.profile-item').removeClass('active');
			$('.col-md-12.profile-desc').remove();
			$(this).addClass('active');
			if($(this).hasClass('last')){
				$(this).after('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 profile-desc"><a href="#" class="close-btn">Close</a><div class="caption">'+ p_content +'</div></div>');
			} else {
				if($(this).next().hasClass('last')){
					$(this).next('.last').after('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 profile-desc"><a href="#" class="close-btn">Close</a><div class="caption">'+ p_content +'</div></div>');
				} else if($(this).next().next().hasClass('last')){
					$(this).next().next('.last').after('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 profile-desc"><a href="#" class="close-btn">Close</a><div class="caption">'+ p_content +'</div></div>');
				} else if($(this).next().next().next().hasClass('last')){
					$(this).next().next().next('.last').after('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 profile-desc"><a href="#" class="close-btn">Close</a><div class="caption">'+ p_content +'</div></div>');
				}
			}
		};
	});
	
	$(document).on('click', 'a.close-btn', function(e) {
		e.preventDefault();
		
		$('div.profile-item').removeClass('active');
		$('.col-md-12.profile-desc').remove();
	});

};

$(document).ready(function(){
	initProfiles();
});

$(window).on('resize', function() {
	initProfiles();
});


// Profile Thumbnail Hover
$(document).ready(function(){
	var profileThumbnail = $('.profile-item .thumbnail');
	var secondThumbnail = $('.profile-second-thumbnail');
	secondThumbnail.fadeOut(500);
	profileThumbnail.off('mouseover mouseleave')
	profileThumbnail.on({
		mouseover: function (event) {
			var src = $(this).find('.profile-second-thumbnail').data('src');
			$(this).find('.profile-second-thumbnail').css('background-image', 'url(' + src + ')');
			$(this).find('.profile-second-thumbnail').fadeIn(500);
		},
		mouseleave: function () {
			$(this).find('.profile-second-thumbnail').fadeOut(500);
		}
	});
});
					