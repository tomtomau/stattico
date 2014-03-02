<?php
include_once("includes.php");
include_once("strava.php");
$tally = listTally($club_id, $db_params);
?>
<table class="table table-responsive table-striped">
	<thead>
		<tr class="strong">
			<td>
				Month
			</td>
			<td>
				Total Distance
			</td>
			<td>
				Total # of Rides
			</td>
			<td>
				Last Updated
			</td>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($tally as $row){
			$date_str = date("F Y", strtotime($row{"date"}));
			// date to go into URL
			$date_url = date("Y-m", strtotime($row{"date"}));
			$total_distance = number_format($row{"total_distance"}/1000, 2) . " km";
			$total_rides = $row{"total_rides"};
			$timestamp = $row{"timestamp"};
			echo("<tr><td><a href=\"month.php?date=$date_url\">$date_str</a></td>
					<td>$total_distance</td>
					<td>$total_rides</td>
					<td>$timestamp</td>
					</tr>
					");
		}

		?>


	</tbody>
</table>