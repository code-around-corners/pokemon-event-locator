<?php

include_once("resources/php/helpers.php");
include_once("resources/php/config.php");

$filter = buildSearchFilter();
$tournaments = getFilteredTournamentData($filter);

if ( isset($_POST["toAddress"]) ) {
	sendEventEmail($tournaments, $_POST["toAddress"], $filter);
}

if ( count($tournaments) > MAX_MAP_COUNT ) {
	$skipMapWithDescriptions = true;
} else {
	$skipMapWithDescriptions = false;
}

// The display and calendar scripts take identical GET parameters, so to display the calendar url
// we just change the script name from display.php to calendar.php.
$url = $_SERVER["HTTP_HOST"] . "/calendar.php?filters=" . base64_encode(json_encode($filter));

echo outputHtmlHeader(false, false, true);
?>
<body>
	<div class="container p-3">
		<div class="card border-dark">
			<div class="card-header text-light bg-danger">	
				<h4 class="text-center text-md-left">Pokémon Event Locator/Subscription Tool</h4>
			</div>
			
			<div class="card-body">
				<div class="card-text">
					The link to your calendar is below! Click on the "Subscribe" link to subscribe directly to this
					calendar or the "Download" link to download the calendar to your device. Or alternatively keep
					scrolling to view the events online! Please note that the map locations are based on the coordinates
					provided by the Pokemon.com website.
					
<?					if ( $skipMapWithDescriptions ) { ?>
					<br /><br />
					As your calendar has more than <? echo MAX_MAP_COUNT; ?> events on it then only the first 
					<? echo MAX_MAP_COUNT; ?> events without a description will display a mini map, however you can
					still load a full map on Google Maps.
<?					} ?>

<?					if ( isset($filter["showDeleted"]) ) { ?>
					<br /><br />
					You've opted to show deleted events. These will not show up in your subscribed calendar but are
					visible on the online display below.
<?					} ?>
				</div>
			</div>

			<div class="card-footer">
				<div class="row">
					<div class="col-12 col-sm-4 text-center">
						<a href="webcal://<? echo $url; ?>">Subscribe To This Calendar</a>
					</div>
					<div class="col-12 col-sm-4 text-center">
						<a href="https://<? echo $url; ?>">Download This Calendar To My Device</a>
					</div>
					<div class="col-12 col-sm-4 text-center">
						<a href="index.php?filters=<? echo base64_encode(json_encode($filter)); ?>">Edit Calendar Filters</a>
					</div>
				</div>
			</div>
		</div>

<?		if ( ! isset($_POST["toAddress"]) ) { ?>
		<div class="text-center mt-3">
			<div class="form-group">
				<form action="display.php" method="post">
					<label for="product" class="text-light mr-2"><b>Enter your email address:</b></label>
					<input class="mr-2" type="text" name="toAddress" id="toAddress">
					<input type="submit" value="Email Me This List" />
					<input type="hidden" name="filters" id="filters" value="<? echo base64_encode(json_encode($filter)); ?>" />
				</form>
			</div>
		</div>
<?		} else { ?>
		<div class="text-center mt-3 text-light small">
			An email has been sent to <? echo $_POST["toAddress"]; ?> with your calendar. Press the back button on your
			browser if you also wish to subscribe to this event list.
		</div>
<?		} ?>
		
		<div class="card-columns small my-3">
<?php
		// This tracks how many maps we've output so we can stop at MAX_MAP_COUNT. Too many map calls causes the
		// browser to lock up for the user.
		$mapCount = 0;
		
		foreach ( $tournaments as $tournament ) {
			echo outputTournamentCard($tournament, ($skipMapWithDescriptions ? 1 : 0), $mapCount, $filter["useMiles"]);
		}
?>
		</div>
		<? echo outputFooter(); ?>
	</div>
</body>

</html>