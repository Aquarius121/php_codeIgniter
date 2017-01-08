<div class="row-fluid">
	<div class="span12">
		<header class="page-header">
			<div class="row-fluid">
				<div class="span6">
					<h1><span class="status-info-muted">MDB</span> List Builder</h1>
				</div>
				<div class="span6">
					<div class="pull-right">
						<form action="admin/contact/list/builder/edit/save" method="post">
							<input type="text" placeholder="List Name" name="name" />
							<button type="submit" class="bt-publish bt-orange">
								Create List
							</button>
						</form>
					</div>
				</div>
			</div>
		</header>
	</div>
</div>

<?= $this->load->view('admin/partials/filters') ?>

<div class="row-fluid">
	<div class="span12">
		<div class="content listing">

			<table class="grid">
				<thead>
					
					<tr>
						<th class="left">Contact List</th>	
						<th>Details</th>
						<th>Creator</th>
					</tr>
					
				</thead>
				<tbody class="results">
					
					<?php foreach ($vd->results as $result): ?>
					<tr data-id="<?= $result->id ?>" class="result">
						<td class="left">
							<h3>
								<a class="view" href="admin/contact/list/builder/edit/<?= $result->id ?>">
									<?= $vd->esc($vd->cut($result->name, 45)) ?>
								</a>
							</h3>	
							<ul>
								<li><a href="admin/contact/list/builder/edit/<?= $result->id ?>">Edit</a></li>
								<li><a href="admin/contact/list/builder/delete/<?= $result->id ?>">Delete</a></li>
							</ul>
						</td>
						<td>
							<?php $created = Date::out($result->date_created); ?>
							<?= $created->format('M j, Y') ?>
							<div class="muted">
								<?= (int) $result->count ?> Contacts
							</div>
						</td>
						<?= $ci->load->view('admin/partials/owner-column', 
							array('result' => $result)); ?>
					</tr>
					<?php endforeach ?>

				</tbody>
			</table>
			
			<div class="clearfix">
				<div class="pull-left grid-report ta-left">
					All times are in UTC.
				</div>
				<div class="pull-right grid-report">
					Displaying <?= count($vd->results) ?> 
					of <?= $vd->chunkination->total() ?> 
					Results
				</div>
			</div>
			
			<?= $vd->chunkination->render() ?>
		
		</div>
	</div>
</div>