<?php
include("../interface/strava.php");
$club_id = 165;
$simple_name = "bikecommuting";
$club_name = "/r/".$simple_name;
?>
<!DOCTYPE html>
<head>
<title><?php echo $club_name ?> strava monthly tally</title>
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">	
</head>
<body>
	<div class="container">
	<h1><a href="http://reddit.com<?php echo $club_name ?>/"><?php echo $club_name ?></a> strava monthly <a href="./">tally</a><br>
		<a href="./"><small>< back to statti.co/<?php echo $simple_name ?></small></a></h1>
	<hr>
	<?php
	include("../interface/data.php")
	?>
	<hr>
	<div>
	<p><strong>View <a href="http://statti.co/bikecommuting/">/r/bikecommuting on statti.co</a> instead</strong> | Built with <a href="http://getbootstrap.com">Bootstrap</a> by Tom Newby (<a href="http://reddit.com/u/tomtomau">/u/tomtomau</a>)</p>
	</div>
	</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script src="js/bootstrap.js">
</script>
</body>
</html>