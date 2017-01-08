<?= $ci->load->view('manage/publish/partials/breadcrumbs') ?>

<div class="container-fluid">
	<header>
		<div class="row">
			<div class="col-lg-6 page-title">
				<?php if (@$vd->m_content): ?>
					<h2>Edit Press Release (External/Backdated)</h2>
				<?php else: ?>
					<h2>Add Press Release (External/Backdated)</h2>
				<?php endif ?>
			</div>
		</div>
	</header>

	<div class="content">
		<form class="tab-content required-form has-premium" method="post" action="manage/publish/pr/edit/save/external/<?= @$vd->m_content->id ?>" id="content-form">
			
			<input type="hidden" name="required_enforcer" class="required-enforcer" value="1" />
			
			<?php if ($vd->m_content && !$vd->duplicate): ?>
			<input type="hidden" name="id" value="<?= $vd->m_content->id ?>" />
			<?php endif ?>
			
			<?= $ci->load->view('manage/publish/partials/external-basic-information') ?>
			
		</form>
	</div>
</div>