<!DOCTYPE html>
<html>
<head>
	<!--

			##########################################
			##########################################
			##########################################
			##########################################
			##########################################

			Please view this document in your browser. 

			##########################################
			##########################################
			##########################################
			##########################################
			##########################################

	-->
	<title><?= $title ?></title>
	<style> 

	body  {
		background: #EEE;
		margin: 0;
		padding: 20px;
	}

	#debug {
		width: 800px;
	}

	</style>
</head>
<body>
	<div id="debug"></div>
	<script><?= $ci->load->view_raw('shared/report/json.js') ?></script>
	<script>

	var options = {
		maxDepth: 6,
		maxString: 500,
	};

	var data = <?= (false === ($json = json_encode($data)) ? json_encode(json_last_error_msg()) : $json) ?>;
	var pretty = prettyPrint(data, options);
	document.getElementById("debug")
		.appendChild(pretty);

	</script>
</body>
</html>