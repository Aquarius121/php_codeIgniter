<?php if ($ci->is_development()) return; ?>
<?php if (Auth::is_from_secret()) return; ?>
<?php if (Auth::is_admin_mode()) return; ?>

<?= $ci->load->view('partials/track-order-google-analytics') ?>
<?= $ci->load->view('partials/track-order-google-adwords') ?>
<?= $ci->load->view('partials/track-order-tout') ?>
<?= $ci->load->view('partials/track-order-bing-ads') ?>
<?= $ci->load->view('partials/track-order-adroll') ?>