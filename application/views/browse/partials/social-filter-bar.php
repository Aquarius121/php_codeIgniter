<?php if ($vd->nr_profile && $vd->nr_profile->is_enable_social_wire): ?>
<div class="dcsns-toolbar">
	<ul class="option-set filter" id="dcsns-filter">
	
		<li class="active">
			<a data-filter=".ln-block" class="link-all social-filter"
				data-group="dc-filter" href="#filter">All</a>
		</li>
		
		<?php if ($vd->nr_profile->is_inc_facebook_in_soc_wire()): ?>
			<li class="f-facebook">
				<a data-filter=".ln-facebook" data-group="dc-filter" rel="facebook" 
					href="#filter" class="social-filter">
					<i class="fa fa-facebook"></i>
				</a>
			</li>
		<?php endif ?>

		<?php if ($vd->nr_profile->is_inc_twitter_in_soc_wire()): ?>
			<li class="f-twitter">
				<a data-filter=".ln-twitter" data-group="dc-filter" rel="twitter" 
					href="#filter" class="social-filter">
					<i class="fa fa-twitter"></i>
				</a>
			</li>
		<?php endif ?>

		<?php if ($vd->nr_profile->is_inc_gplus_in_soc_wire()): ?>
			<li class="f-google">
				<a data-filter=".ln-google" data-group="dc-filter" rel="google" 
					href="#filter" class="iso-active social-filter">
					<i class="fa fa-google"></i>
				</a>
			</li>
		<?php endif ?>

		<?php if ($vd->nr_profile->is_inc_pinterest_in_soc_wire()): ?>
			<li class="f-pinterest">
				<a data-filter=".ln-pinterest" 
					data-group="dc-filter" rel="pinterest" href="#filter" class="social-filter">
					<i class="fa fa-pinterest"></i>
				</a>
			</li>
		<?php endif ?>

		<?php if ($vd->nr_profile->is_inc_instagram_in_soc_wire()): ?>
			<li class="f-instagram">
				<a data-filter=".ln-instagram" 
					data-group="dc-filter" rel="instagram" href="#filter" class="social-filter">
					<i class="fa fa-instagram"></i>
				</a>
			</li>
		<?php endif ?>

		<?php if ($vd->nr_profile->is_inc_youtube_in_soc_wire()): ?>
			<li class="f-youtube">
				<a data-filter=".ln-youtube" 
					data-group="dc-filter" rel="youtube" href="#filter" class="social-filter">
					<i class="fa fa-youtube"></i>
				</a>
			</li>
		<?php endif ?>

		<?php if ($vd->nr_profile->is_inc_vimeo_in_soc_wire()): ?>
			<li class="f-vimeo">
				<a data-filter=".ln-vimeo" 
					data-group="dc-filter" rel="vimeo" href="#filter" class="social-filter">
					<i class="fa fa-vimeo"></i>
				</a>
			</li>
		<?php endif ?>

		<?php if ($vd->nr_profile->is_inc_linkedin_in_soc_wire()): ?>
			<li class="f-linkedin">
				<a data-filter=".ln-linkedin" 
					data-group="dc-filter" rel="linkedin" href="#filter" class="social-filter">
					<i class="fa fa-linkedin"></i>
				</a>
			</li>
		<?php endif ?>

	</ul>
</div>
<?php endif ?>

<script>

$(function() {

	var items_container = $("#ln-container");
	var a_social_filter = $("a.social-filter");
	var filter_ul = $("#dcsns-filter");

	var apply_filter = function(filter) {
		items_container.addClass("fade-loader");
		setTimeout(function() {
			items_container.children().addClass("hidden");
			$(filter).removeClass("hidden");
			if (items_container.hasClass("masonry-before"))
				items_container.masonry("destroy");
			items_container.imagesLoaded(function() {
				items_container.removeClass("fade-loader");
				items_container.addClass("masonry-before");
				items_container.masonry({
					itemSelector: filter,
					gutter: 20,
					columns: 3,
					transitionDuration: 0
				});
			});
		}, 200);
	};

	a_social_filter.on("click", function(ev) {
		ev.preventDefault();		
		var _this = $(this);
		filter_ul.children().removeClass("active");
		_this.parent("li").addClass("active");
		var filter = _this.data("filter");
		apply_filter(filter);
	});

	apply_filter(".ln-block");

});

</script>