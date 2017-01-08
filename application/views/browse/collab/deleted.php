<!doctype html>
<html lang="en">

	<head>
		
		<title>
			Newswire Content Collaboration
		</title>
		
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width" />
		<base href="<?= $ci->env['base_url'] ?>" />
		
		<link rel="stylesheet" href="<?= $vd->assets_base ?>lib/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="<?= $vd->assets_base ?>css/base.css?<?= $vd->version ?>" />
		<link rel="stylesheet" href="<?= $vd->assets_base ?>css/browse.css?<?= $vd->version ?>" />
		<link rel="stylesheet" href="<?= $vd->assets_base ?>css/raw.css?<?= $vd->version ?>" />
		
		<script src="<?= $vd->assets_base ?>lib/jquery.js"></script>
		<script src="<?= $vd->assets_base ?>lib/jquery.create.js"></script>
		
	</head>
	
	<body class="collab">
		<div class="container">
			<div class="clearfix">
				<div class="alert alert-danger pull-left">
					<strong>Error!</strong> The content owner has deleted this revision.
				</div>
			</div>
		</div>
	</body>

</html>