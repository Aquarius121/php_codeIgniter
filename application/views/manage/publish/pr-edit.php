<?= $ci->load->view('manage/publish/partials/breadcrumbs') ?>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if ($vd->m_content): ?>
					<h2>Edit Press Release</h2>
				<?php else: ?>
					<h2>Add New Press Release</h2>
				<?php endif ?>
			</div>
		</div>
	</header>

	<form class="tab-content required-form pr-form autosave-form content-form web-images-has-prn-extension"
		action="manage/publish/pr/edit/save/<?= $vd->m_content ? $vd->m_content->id : null ?>" id="content-form"
		data-autosave-context="<?= $vd->m_content ? $vd->m_content->id : null ?>"
		data-autosave-url="manage/publish/pr/autosave/create"
		data-autosave-interval="300" 
		method="post">
		
		<div class="content">
			<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
			<?= $ci->load->view('manage/publish/pr-edit-form') ?>
		</div>
	
		<?php 

			$render_basic = $ci->is_development();

			$loader = new Assets\JS_Loader(
				$ci->conf('assets_base'), 
				$ci->conf('assets_base_dir'));
			$loader->add('lib/jquery.deserialize.autosave.js');
			$loader->add('lib/jquery.serialize.autosave.js');
			$loader->add('js/autosave.js');
			$ci->add_eob($loader->render($render_basic));

		?>
		
		<?= $ci->load->view('manage/publish/partials/autosave-callback') ?>

		<?php if ($vd->autosave): ?>
		<?= $ci->load->view('manage/publish/partials/autosave-loader') ?>
		<?php endif ?>

	</form>
</div>
