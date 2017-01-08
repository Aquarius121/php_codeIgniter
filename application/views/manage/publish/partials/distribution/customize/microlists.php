<?php $cart = Cart::instance(); ?>
<?php $selected = $vd->mcd_extras->filter(Model_Content_Distribution_Extras::TYPE_MICROLIST); ?>
<?php $content = $vd->m_content; ?>

<!-- microlists should be disabled if the content
     is already published or approved (because 
     that means it's already gone to PR Newswire) -->
<?php if (!count($selected) && $content && 
	($content->is_published || $content->is_approved))
	return; ?>

<div class="distribution-option form-group"	data-distribution="PREMIUM-PLUS PREMIUM-PLUS-STATE PREMIUM-PLUS-NATIONAL">
	<h4 class="distribution-options-heading">Microlists (Optional)</h4>
	<div class="row">
		<div class="col-lg-5">
			<div class="microselects-container">
				<?php foreach ($selected as $_): ?>
					<?= $ci->load->view('manage/publish/partials/distribution/customize/microlists-select', 
						array('selected' => $_)) ?>
				<?php endforeach ?>
				<!-- cannot add a new microlist if published or approved -->
				<?php if (!($content && ($content->is_published || $content->is_approved))): ?>
				<?= $ci->load->view('manage/publish/partials/distribution/customize/microlists-select',
					array('selected' => null)) ?>
				<?php endif ?>
			</div>
			<script>

			$(function() {

				var container = $(".microselects-container");
				var selects = $(".microlist-select");
				var last = selects.last();

				var when_selected = function() {					

					var _this = $(this);

					if (!_this.val()) {

						if (!_this.is(last))  {
							_this.selectpicker("destroy");
							selects.remove(_this);
							_this.remove();
						}

						return;

					}

					if (_this.is(last)) {
						last = _this.clone()
						container.append(last);
						selects = selects.add(last);
						last.on("change", when_selected);
						last.on_load_select();
					}

				};

				selects.on("change", when_selected);
				selects.on_load_select();				

			});

			</script>
			<p class="help-block">Add additional reach with PR Newswire microlists.</p>
		</div>
	</div>
</div>
