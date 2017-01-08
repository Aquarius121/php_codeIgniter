<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1>Canned Messages</h1>
				</div>				
				<div class="span6">
					<a href="admin/settings/canned/edit" 
						class="btn bt-silver pull-right">New Message</a>
				</div>
			</div>
		</header>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">
			
			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Message</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left">
							<h3>
								<a class="view" href="admin/settings/canned/edit/<?= $result->id ?>">
									<?= $vd->esc($vd->cut($result->title, 75)) ?>
								</a>
							</h3>
							<ul>
								<li><a href="admin/settings/canned/edit/<?= $result->id ?>">Edit</a></li>
								<li><a href="admin/settings/canned/delete/<?= $result->id ?>">Delete</a></li>
							</ul>
						</td>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
		
		</div>
	</div>
</div>