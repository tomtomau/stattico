<?php
include("../interface/strava.php");
$club_id = 25148;
$simple_name = "bikecommuting";
$club_name = "/r/".$simple_name;
?>
<!DOCTYPE html>
<head>
<title><?php echo $club_name ?> strava monthly tally</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">	
</head>
<body>
	<div class="container">
	<h1><a href="http://reddit.com<?php echo $club_name ?>/"><?php echo $club_name ?></a> strava monthly <a href="./">tally</a><br>
		<a href="../"><small>< back to statti.co</small></a></h1>
	<blockquote>
		It had been considered that a monthly/yearly/whatever-ly tally for the
		<a href="http://strava.com/clubs/<?php echo $club_id ?>">strava group</a> would prove handy 
		- a cool way to show how many miles/km's we were getting in each month as we make our way to work and such! Find out more about this on the <a href="">reddit post</a>.
	</blockquote>
	<hr>
	<h3>Monthly Tallies</h3>
	<?php
	include("../interface/list.php");
	?>
	<hr>
	<div>
	<p><strong>View <a href="../bicycling/">/r/bicycling on statti.co</a> instead</strong> | Built with <a href="http://getbootstrap.com">Bootstrap</a> by Tom Newby (<a href="http://reddit.com/u/tomtomau">/u/tomtomau</a>)</p>
	</div>
	</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script src="js/bootstrap.js">
</script>
</body>
</html>