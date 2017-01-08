<?php

	$loader = new Assets\CSS_Loader(
		$ci->conf('assets_base'), 
		$ci->conf('assets_base_dir'));
	$loader->add('css/pitch_wizard.css');
	$render_basic = $ci->is_development();
	echo $loader->render($render_basic);

?>

<div class="contact_preview">
	<h3>List Preview</h3>
	<table class="grid" id="selectable-results">
		<thead>
			<tr>
				<th>Outlet Name</th>
				<th>First Name</th>
				<th>Last Name</th>
        	    <th>Work Title</th>
            	<th>Email</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($vd->results as $result): ?>
			<tr>
				<td>
					<?php if ($result->company_name): ?>
					<?= $vd->esc($result->company_name) ?>
					<?php else: ?>
					<span>-</span>
					<?php endif ?>
				</td>

    	        <td>				
	               	<?= $vd->esc(@$result->first_name) ?>
				</td>

				<td>
					<?= $vd->esc(@$result->last_name) ?>
				</td>        
			
				<td>
					<?php if ($result->title): ?>
						<?= $vd->esc($result->title) ?>
					<?php else: ?>
						<span>-</span>
					<?php endif ?>
				</td>
	            <td>
    	        	<?= $vd->esc(@$result->email) ?>
        	    </td>

			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>