<main class="main planner-header">
	<div class="container">
		<div class="row">
			<div class="col-sm-1">
			</div>
			<div class="col-sm-10">
				<header class="main-header">
					<h1>Newswire Press Release Planner</h1>
					<p><strong>Step 4 of 7</strong></p>
				</header>
			</div>
			<div class="col-sm-1">
			</div>
		</div>
	</div>
</main>

<section class="container planner">
	<div>
		<div class="row checkbox-js">
			<div class="col-md-10 col-md-offset-1">
				<h2>Where is your target audience?</h2>
				<p class="sub-headline">Select all that apply</p>
				<div class="row">
					<div class="col-md-4">
						<a href="#" class="selectable">
							Local
							<input type="checkbox" name="target_audience[]" value="Local">
						</a>
					</div>
					<div class="col-md-4">
						<a href="#" class="selectable">
							Regional
							<input type="checkbox" name="target_audience[]" value="Regional">
						</a>
					</div>
					<div class="col-md-4">
						<a href="#" class="selectable">
							State
							<input type="checkbox" name="target_audience[]" value="State">
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<a href="#" class="selectable">
							National
							<input type="checkbox" name="target_audience[]" value="National">
						</a>
					</div>
					<div class="col-md-4">
						<a href="#" class="selectable">
							International
							<input type="checkbox" name="target_audience[]" value="International">
						</a>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="row radio-js">
			<div class="col-sm-12">
				<h2>Do you currently use an external media database?</h2>
				<div class="row">
					<div class="col-md-3 col-md-offset-3">
						<a href="#" class="selectable">
							Yes
							<input type="radio" name="use_external_media_database" value="Yes"
								class="toggle-which-database">
						</a>
					</div>
					<div class="col-md-3">
						<a href="#" class="selectable">
							No
							<input type="radio" name="use_external_media_database" value="No"
								class="toggle-which-database">
						</a>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="row toggled-which-database">
			<div class="col-md-12">
				<h2>Which database do you work with?</h2>
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<select class="form-control" name="which_media_database"
							id="which-media-database">
						 	<option selected>Please Select</option>
							<option value="Cision">Cision</option>
							<option value="My Media Info">My Media Info</option>
							<option value="A News Tip">A News Tip</option>
							<option value="Melt Water">Melt Water</option>
							<option value="Gorkana">Gorkana</option>
							<option value="Other">Other</option>
						</select>						
						<input type="text" name="which_media_database_other"
							id="which-media-database-other" class="form-control"
							placeholder="Enter name of other database you work with">
						<script>
							
						$(function() {

							var toggle = $(".toggle-which-database");
							var toggled = $(".toggled-which-database");

							var which = $("#which-media-database");
							var other = $("#which-media-database-other");

							toggle.on("change", function() {
								var enabled = toggle.filter(":checked").val() == "Yes";
								which.prop("disabled", !enabled);
								other.prop("disabled", !enabled);
								toggled.toggle(enabled);
								if (enabled) 
									which.trigger("change");
							});

							which.on("change", function() {
								var enabled = (which.val() == "Other");
								other.prop("disabled", !enabled);
							});

						});

						</script>
					</div>
				</div>
			</div>
		</div>
		<hr class="toggled-which-database">
		<div class="row">
			<div class="col-sm-12">
				<button class="btn btn-default btn-lg" type="submit"
					 name="next" value="three"><i class="fa fa-angle-left"></i> Previous</button>
				<button class="btn btn-success btn-lg" type="submit"
					 name="next" value="five">Continue <i class="fa fa-angle-right"></i></button>
			</div>
		</div>
	</div>
</section>