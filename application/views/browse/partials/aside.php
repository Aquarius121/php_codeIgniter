<?php if ($vd->m_content && $vd->m_content->is_advert_supported()): ?>
	<?= $ci->load->view_html('partials/google-adverts/160-90') ?>
<?php endif ?>

<?php if ($vd->is_auto_built_unclaimed_nr): ?>
<?= $ci->load->view('browse/partials/unclaimed-about') ?>
<?php endif ?>

<div class="aside-accordians-toggle">
	<i class="fa fa-bars"></i>
</div>

<div class="aside-accordians">

	<?php if (!$ci->is_common_host): ?>
		<?= $ci->load->view('browse/partials/content-types') ?>
	<?php endif ?>

	<?php if ($vd->nr_contact): ?>
		<?= $ci->load->view('browse/partials/contact') ?>
	<?php endif ?>

	<?= $ci->load->view('browse/partials/about-company') ?>
	<?= $ci->load->view('browse/partials/address') ?>

	<?= $ci->load->view('browse/partials/relevant-links') ?>
	<?= $ci->load->view('browse/partials/archives') ?>

</div>

<?= $ci->load->view('browse/subscribe/subscription-link') ?>
<?= $ci->load->view('browse/partials/socials') ?>

<script>
	
(function() {

	$(function() {

		var toggle = $(".aside-accordians-toggle");
		toggle.on("click", function() {
			toggle.parent().toggleClass("show-accordians");
		});

	});

	defer(function() {

		window.__on_nav_callback.push(function() {

			var aside_newsroom = $(".aside-newsroom");
			// var aside_about = $(".aside-about-company");
			// var aside_press = $(".aside-press-contact");

			if (aside_newsroom.find("li.active").length) {
				aside_newsroom.addClass("open");
				return;
			}

			// if (aside_about.length) {
			// 	aside_about.addClass("open");
			// 	return;
			// }

			// if (aside_press.length) {
			// 	aside_press.addClass("open");
			// 	return;
			// }

			aside_newsroom.addClass("open");
			return;

		});

	});

})();

</script>