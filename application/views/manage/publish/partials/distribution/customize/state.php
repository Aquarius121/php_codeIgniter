<div class="distribution-option form-group" data-distribution="PREMIUM-PLUS-STATE">
	<h4 class="distribution-options-heading">State Newsline</h4>
	<div class="row">
		<div class="col-lg-6">
			<?php $cdb_raw = $vd->m_content ? $vd->m_content->distribution_bundle()->raw_data_object() : null; ?>
			<?php $state = isset($cdb_raw->customization->state) ? $cdb_raw->customization->state : null; ?>
			<select name="distcust[state]" id="dist-cust-state"
				class="form-control selectpicker show-menu-arrow"
				data-required-name="State" data-container="body"
				data-required-use-parent="1">
				<option class="selectpicker-default" title="Select State Newsline" value=""
					<?= value_if_test(!$state, 'selected') ?>>None</option>
				<?php foreach (PRNewswire_Distribution::states() as $code => $name): ?>
					<option value="<?= $vd->esc($code) ?>"
						<?= value_if_test($state == $code, 'selected') ?>>
						<?= $vd->esc($name) ?></option>
				<?php endforeach ?>
			</select>
			<script>
				
				$(function() {

					var select = $("#dist-cust-state");
					select.on_load_select();

					$(window).load(function() {
						select.addClass("required");
					});
					
				});

			</script>
			<p class="help-block">Select the distribution state for state newsline.</p>
		</div>
	</div>
</div>
