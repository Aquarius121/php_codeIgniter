<div class="row">
	<div class="col-lg-10 col-lg-offset-1">

		<div class="row bs-wizard nav-activate" data-nav-selector=">div">

			<div class="col-xs-3 bs-wizard-step <?= value_if_test(($vd->step > 1), 'complete') ?>" 
				data-on="process/<?= $vd->m_pw_session->id ?>/1$">
				<div class="text-center bs-wizard-stepnum">
					<a href="manage/contact/pitch/process/<?= $vd->m_pw_session->id ?>/1">Select Content</a>
				</div>
				<div class="progress"><div class="progress-bar"></div></div>
				<span class="bs-wizard-dot"></span>
			</div>

			<div class="col-xs-3 bs-wizard-step <?= value_if_test(($vd->step > 2), 'complete') ?>"
				data-on="process/<?= $vd->m_pw_session->id ?>/2$">
				<div class="text-center bs-wizard-stepnum">
					<a href="manage/contact/pitch/process/<?= $vd->m_pw_session->id ?>/2">Choose Outreach</a>
				</div>
				<div class="progress"><div class="progress-bar"></div></div>
				<span class="bs-wizard-dot"></span>
			</div>

			<div class="col-xs-3 bs-wizard-step <?= value_if_test(($vd->step > 3), 'complete') ?>" 
				data-on="process/<?= $vd->m_pw_session->id ?>/3$">
				<div class="text-center bs-wizard-stepnum">
					<a href="manage/contact/pitch/process/<?= $vd->m_pw_session->id ?>/3">Pitch Details</a>
				</div>
				<div class="progress"><div class="progress-bar"></div></div>
				<span class="bs-wizard-dot"></span>
				<span class="bs-wizard-dot"></span>
			</div>

			<div class="col-xs-3 bs-wizard-step <?= value_if_test(($vd->step > 4), 'complete') ?>"
				data-on="process/<?= $vd->m_pw_session->id ?>/4$">
				<div class="text-center bs-wizard-stepnum">
					<a href="manage/contact/pitch/process/<?= $vd->m_pw_session->id ?>/4">Review Order</a>
				</div>
				<div class="progress"><div class="progress-bar"></div></div>
				<span class="bs-wizard-dot"></span>
				<span class="bs-wizard-dot"></span>
			</div>
		</div>

	</div>
</div>