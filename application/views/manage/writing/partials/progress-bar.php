<div class="row marbot-50">
	<h1 class="ta-center">Submit PR Writing Details</h1>
</div>

<div class="row not-row bs-wizard nav-activate" data-nav-selector=">div">

	<div class="col-xs-3 bs-wizard-step <?= value_if_test($vd->m_wr_order || ($vd->step > 1), 'complete') ?>" 
		data-on="process/<?= $vd->m_wr_session->id ?>/1$">
		<div class="text-center bs-wizard-stepnum">
			<a href="manage/writing/process/<?= $vd->m_wr_session->id ?>/1">Company</a>
		</div>
		<div class="progress"><div class="progress-bar"></div></div>
		<span class="bs-wizard-dot"></span>
	</div>

	<div class="col-xs-3 bs-wizard-step <?= value_if_test($vd->m_wr_order || ($vd->step > 2), 'complete') ?>"
		data-on="process/<?= $vd->m_wr_session->id ?>/2$">
		<div class="text-center bs-wizard-stepnum">
			<a href="manage/writing/process/<?= $vd->m_wr_session->id ?>/2">Details</a>
		</div>
		<div class="progress"><div class="progress-bar"></div></div>
		<span class="bs-wizard-dot"></span>
	</div>

	<div class="col-xs-3 bs-wizard-step <?= value_if_test($vd->m_wr_order || ($vd->step > 3), 'complete') ?>" 
		data-on="process/<?= $vd->m_wr_session->id ?>/3$">
		<div class="text-center bs-wizard-stepnum">
			<a href="manage/writing/process/<?= $vd->m_wr_session->id ?>/3">Media</a>
		</div>
		<div class="progress"><div class="progress-bar"></div></div>
		<span class="bs-wizard-dot"></span>
		<span class="bs-wizard-dot"></span>
	</div>

	<div class="col-xs-3 bs-wizard-step <?= value_if_test($vd->m_wr_order, 'complete') ?>"
		data-on="process/<?= $vd->m_wr_session->id ?>/4$">
		<div class="text-center bs-wizard-stepnum">
			<a href="manage/writing/process/<?= $vd->m_wr_session->id ?>/4">Submit</a>
		</div>
		<div class="progress"><div class="progress-bar"></div></div>
		<span class="bs-wizard-dot"></span>
		<span class="bs-wizard-dot"></span>
	</div>

</div>
