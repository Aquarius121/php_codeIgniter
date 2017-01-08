<main class="main planner-header">
	<div class="container">
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Newswire Press Release Planner</h1>
					<p><strong>Step 1 of 7</strong></p>
				</header>
			</div>
			<div class="col-sm-1">
			</div>
		</div>
	</div>
</main>

<section class="container planner">
	<div>
		<div class="row" id="company-or-individual">
			<div class="col-sm-12">
				<h2>Are you a company or an individual?</h2>
				<div class="row radio-js">
					<div class="col-md-3 col-md-offset-3">
						<a href="#" class="icon selectable">
							<img src="<?= $vd->assets_base ?>im/planner/icon-company.svg" alt="Company" />
							Company
							<input type="radio" name="company_or_individual" value="Company" checked>
						</a>
					</div>
					<div class="col-md-3">
						<a href="#" class="icon selectable">
							<img src="<?= $vd->assets_base ?>im/planner/icon-individual.svg" alt="Individual" />
							Individual
							<input type="radio" name="company_or_individual" value="Individual">
						</a>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="row" id="select-company-type">
			<div class="col-sm-12">
				<h2>Select your company type:</h2>
				<div class="row radio-js">
					<div class="col-md-3 col-md-offset-3">
						<a href="#" class="selectable">
							Private
							<input type="radio" name="private_or_public" value="Private">
						</a>
					</div>
					<div class="col-md-3">
						<a href="#" class="selectable">
							Public
							<input type="radio" name="private_or_public" value="Public">
						</a>
					</div>
				</div>
			</div>
		</div>
		<hr>		
		<script>
			
			$(function() {

				var cori = $("#company-or-individual input");
				var sct = $("#select-company-type");
				sct = sct.add(sct.next("hr"));

				var toggle = function() {
					var visible = (cori.filter(":checked").val() == "Company");
					sct.find("input").prop("disabled", !visible);
					sct.toggle(visible);
				};

				cori.on("change", toggle);
				toggle();

			});

		</script>
		<div class="row">
			<div class="col-sm-12">
				<h2>Do you submit Press Releases on behalf of clients?</h2>
				<div class="row radio-js">
					<div class="col-md-3 col-md-offset-3">
						<a href="#" class="selectable">
							Yes
							<input type="radio" name="is_agency" value="Yes">
						</a>
					</div>
					<div class="col-md-3">
						<a href="#" class="selectable">
							No
							<input type="radio" name="is_agency" value="No">
						</a>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-sm-12">
				<h2>How many people are in your PR/Marketing team?</h2>
				<div class="row">
					<div class="col-md-4 col-md-offset-4">
						<div class="input-group counter">
					<span class="input-group-btn">
					  <button type="button" class="btn btn-default btn-number btn-lg" disabled="disabled" data-type="minus" data-field="team_size">
						  <i class="fa fa-minus"></i>
					  </button>
					</span>
					<input type="text" name="team_size" class="form-control input-number input-lg" value="1" min="1" max="1000">
					<span class="input-group-btn">
					  <button type="button" class="btn btn-default btn-number btn-lg" data-type="plus" data-field="team_size">
						  <i class="fa fa-plus"></i>
					  </button>
					</span>
				</div>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-sm-12">
				<button class="btn btn-success btn-lg" type="submit" name="next" value="two">Continue</button>
			</div>
		</div>
	</div>
</section>

<script>
//plugin bootstrap minus and plus
//http://jsfiddle.net/laelitenetwork/puJ6G/
$(function() {

	$('.btn-number').click(function(e){
		e.preventDefault();
		
		fieldName = $(this).attr('data-field');
		type      = $(this).attr('data-type');
		var input = $("input[name='"+fieldName+"']");
		var currentVal = parseInt(input.val());
		if (!isNaN(currentVal)) {
			if(type == 'minus') {
				
				if(currentVal > input.attr('min')) {
					input.val(currentVal - 1).change();
				} 
				if(parseInt(input.val()) == input.attr('min')) {
					$(this).attr('disabled', true);
				}

			} else if(type == 'plus') {

				if(currentVal < input.attr('max')) {
					input.val(currentVal + 1).change();
				}
				if(parseInt(input.val()) == input.attr('max')) {
					$(this).attr('disabled', true);
				}

			}
		} else {
			input.val(0);
		}
	});
	$('.input-number').focusin(function(){
	   $(this).data('oldValue', $(this).val());
	});
	$('.input-number').change(function() {
		
		minValue =  parseInt($(this).attr('min'));
		maxValue =  parseInt($(this).attr('max'));
		valueCurrent = parseInt($(this).val());
		
		name = $(this).attr('name');
		if(valueCurrent >= minValue) {
			$(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
		} else {
			alert('Sorry, the minimum value was reached');
			$(this).val($(this).data('oldValue'));
		}
		if(valueCurrent <= maxValue) {
			$(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
		} else {
			alert('Sorry, the maximum value was reached');
			$(this).val($(this).data('oldValue'));
		}
		
		
	});
	$(".input-number").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
				 // Allow: Ctrl+A
				(e.keyCode == 65 && e.ctrlKey === true) || 
				 // Allow: home, end, left, right
				(e.keyCode >= 35 && e.keyCode <= 39)) {
					 // let it happen, don't do anything
					 return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});
});

</script>