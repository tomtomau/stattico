<?php
if(!isset($_GET["date"])){
	header("Location: ./");
	die();
}
$date_str = date("Y-m-t", strtotime($_GET["date"]));
$data = monthData($club_id, $db_params, $date_str);
if(!$data){
	// bad requests
	header("Location: ./");
	die();
}
// Calculate month string
$date_timestamp = strtotime($data{"date"});
$month = date("F Y", $date_timestamp);
$timestamp = $data{"timestamp"};
?>

<h3>Stats for <?php echo($month) ?></h3>
	<p>Updated at <?php echo($timestamp) ?></p>
	
	<div class="row">
		<div class="col-md-4 col-sm-6 text-center">
			<h1><small class="text-muted">TOTAL DISTANCE</small><br><br>
			<?php echo number_format(($data{"total_distance"}/1000), 2); ?> km</h1>
		</div>
		<div class="col-md-4 col-sm-6 text-center">
			<h1><small class="text-muted">TOTAL RIDES</small><br><br>
			<?php echo $data{"total_rides"}; ?>
			</h1>
		</div>
		<div class="col-md-4 col-sm-6 text-center">
			<h1><small class="text-muted">TOTAL ATHLETES</small><br><br>
			<?php echo $data{"total_athletes"}; ?>
			</h1>
		</div>
		<div class="col-md-4 col-sm-6 text-center">
			<h1><small class="text-muted">AVERAGE DISTANCE</small><br><br>
			<?php echo $data{"average_distance"} ?>
			</h1>
		</div>
		<div class="col-md-4 col-sm-6 text-center">
			<h1><small class="text-muted">AVERAGE DAILY RIDES</small><br><br>
				<?php echo $data{"average_daily_rides"} ?> per day
			</h1>
		</div>
		<div class="col-md-4 col-sm-6 text-center">
			<h1><small class="text-muted">AVERAGE ATHLETE DISTANCE</small><br><br>
				<?php echo $data{"average_distance_athletes"} ?> km each
			</h1>
		</div>
	</div>