<div class="main-content">
	<section class="latest-news">
		
		<header class="ln-header">
			<h2>Company Contacts</h2>
		</header>

		<?php if ($vd->is_auto_built_unclaimed_nr && !count($vd->results)): ?>
			<?= $ci->load->view('browse/claim_nr/listing-contact') ?>
		<?php endif ?>

		<div id="ln-container">			
			<?php foreach ($vd->results as $result): ?>
			<?= $ci->load->view('browse/listing/contact', 
				array('contact' => $result)); ?>
			<?php endforeach ?>			
		</div>
		
	</section>
</div>