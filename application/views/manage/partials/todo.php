<?php if ($vd->bar && $vd->bar->count_not_done()): ?>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<i class="fa fa-list-ul"></i> 
		<span class="badge"><?= $vd->bar->count_not_done() ?></span>
		<b class="caret"></b>
	</a>
	<ul class="dropdown-menu">
		<li class="dropdown-header">Tasks To Do</li>
			<?= $ci->load->view('manage/partials/todo-bar',
				array('bar' => $vd->bar)); ?>
	</ul>
</li>
<?php endif ?>