<div class="panel-group" id="accordion">

	<?php $action_id = 0 ?>
	<?php foreach ($vd->actions as $action): ?>
		
		<div class="panel panel-default">
			<div class="panel-heading accordion-toggle" data-toggle="collapse" data-parent="#accordion" 
				data-target="#collapse-<?= $action->id ?>">
				<h4 class="panel-title">
					<?php $action_date = Date::out($action->date_action_taken) ?>
					<?= $action_date->format('d/m/Y') ?>
					<span class="text-muted smaller"><?= $action_date->format('H:i') ?></span>
					<i class="fa fa-fw fa-chevron-down indicator pull-right"></i>
				</h4>
			</div>

			<div id="collapse-<?= $action->id ?>" class="panel-collapse collapse">
				<div class="panel-body1 pad-20">
					<?= $ci->load->view('manage/contact/partials/list_history_action_detail', 
						array('action' => $action)) ?>
				</div>
			</div>
		</div>
			
	<?php endforeach ?>
	
</div>   



<script>

$(function() {

	var toggle_chevron = function (e) {
		$(e.target)
			.prev('.panel-heading')
			.find("i.indicator")
			.toggleClass('fa-chevron-down fa-chevron-up');
	};
		
	$('#accordion').on('hidden.bs.collapse', toggle_chevron);
	$('#accordion').on('shown.bs.collapse', toggle_chevron);
	
});

</script>