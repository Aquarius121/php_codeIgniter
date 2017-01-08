<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<ul class="nav nav-tabs nav-activate pw_order_menu" id="tabs">
    <li><a data-on="^admin/contact/pitch_wizard_order/order/all" 
        href="admin/contact/pitch_wizard_order/order/all<?= $vd->esc(gstring()) ?>">Orders</a></li>
    <li><a data-on="^admin/contact/pitch_wizard_order/all_list" 
        href="admin/contact/pitch_wizard_order/all_list<?= $vd->esc(gstring()) ?>">All List</a></li>
    <li><a data-on="^admin/contact/pitch_wizard_order/assign_list" 
			href="admin/contact/pitch_wizard_order/assign_list<?= $vd->esc(gstring()) ?>">Assign List
			<?php if($vd->assign_count > 0): ?>
				<span class="menu-count"><?= $vd->assign_count ?></span>
			<?php endif ?>
		</a></li>    
    <li><a data-on="^admin/contact/pitch_wizard_order/upload_list" 
        	href="admin/contact/pitch_wizard_order/upload_list<?= $vd->esc(gstring()) ?>">Upload List
        	<?php if($vd->pending_count > 0): ?>
				<span class="menu-count"><?= $vd->pending_count ?></span>
			<?php endif ?>
        </a></li>
	<li><a data-on="^admin/contact/pitch_wizard_order/review_list|^admin/contact/pitch_wizard_order/review_single_list" 
		href="admin/contact/pitch_wizard_order/review_list<?= $vd->esc(gstring()) ?>">Review List
			<?php if($vd->review_count > 0): ?>
				<span class="menu-count"><?= $vd->review_count ?></span>
			<?php endif ?>
		</a></li>
        
	<li><a data-on="^admin/contact/pitch_wizard_order/rejected_list"
			href="admin/contact/pitch_wizard_order/rejected_list<?= $vd->esc(gstring()) ?>">Rejected List
				<?php if($vd->rejected_count > 0): ?>
					<span class="menu-count"><?= $vd->rejected_count ?></span>
				<?php endif ?>
		</a></li>
        
	<li><a data-on="^admin/contact/pitch_wizard_order/archived_list" 
        href="admin/contact/pitch_wizard_order/archived_list<?= $vd->esc(gstring()) ?>">Archived List</a></li>                    
</ul>