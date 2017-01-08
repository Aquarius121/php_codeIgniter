<header>
	<div class="row">
		<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 page-title">
			<h2>Press Releases</h2>
		</div>
		<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 actions">
			<ul class="list-inline actions">
				<?php if (Auth::user()->writing_credits()): ?>
					<li><a href="manage/writing/process" class="btn btn-default">Submit Writing Order</a></li>
				<?php else: ?>
					<li><a href="manage/writing/process" class="btn btn-default">Order PR Writing</a></li>
				<?php endif ?>
				<li><a href="manage/publish/pr/edit" class="btn btn-primary">Submit Press Release</a></li>
			</ul>
		</div>
	</div>
</header>