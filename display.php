<?php

include_once("resources/php/helpers.php");
include_once("resources/php/config.php");

// This lets us use the country codes to display the appropriate flag emoji.
include_once("vendor/peterkahl/php/flagMaster.php");
use peterkahl\flagMaster\flagMaster;

$filter = buildSearchFilter();
$tournaments = getFilteredTournamentData($filter);

if ( count($tournaments) > MAX_MAP_COUNT ) {
	$skipMapWithDescriptions = true;
} else {
	$skipMapWithDescriptions = false;
}

// The display and calendar scripts take identical GET parameters, so to display the calendar url
// we just change the script name from display.php to calendar.php.
$url = str_replace("display.php", "calendar.php", $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Pokémon Event Locator/Subscription Tool</title>
	
	<!-- Lock zooming on mobile devices -->
	<meta name="viewport" content="width=device-width, maximum-scale=1, minimum-scale=1, user-scalable=no"/>

	<!-- Site CSS scripts -->
	<link href="resources/css/pokecal.css" rel="stylesheet" />

	<!-- Bootstrap CDN -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	
	<!-- Leaflet CDN -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
	<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
</head>

<body>
	<div class="container p-3">
		<div class="card border-dark">
			<div class="card-header text-light bg-danger">	
				<h4 class="text-center text-md-left">Pokémon Event Locator/Subscription Tool</h4>
			</div>
			
			<div class="card-body">
				<div class="card-text">
					<p>
						The link to your calendar is below! Click on the "Subscribe" link to subscribe directly to this
						calendar or the "Download" link to download the calendar to your device. Or alternatively keep
						scrolling to view the events online! Please note that the map locations are based on the coordinates
						provided by the Pokemon.com website.
					</p>
					
<?					if ( $skipMapWithDescriptions ) { ?>
					<p>
						As your calendar has more than <? echo MAX_MAP_COUNT; ?> events on it then only the first 
						<? echo MAX_MAP_COUNT; ?> events without a description will display a mini map, however you can
						still load a full map on Google Maps.
					</p>
<?					} ?>

<?					if ( isset($filter["showDeleted"]) ) { ?>
					<p>
						You've opted to show deleted events. These will not show up in your subscribed calendar but are
						visible on the online display below.
					</p>
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
						<a href="index.php">Get Another Calendar</a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="card-columns small my-3">
<?php
		// This tracks how many maps we've output so we can stop at MAX_MAP_COUNT. Too many map calls causes the
		// browser to lock up for the user.
		$mapCount = 0;
		
		foreach ( $tournaments as $tournament ) {
			// We're storing the tournament ID as an integer, so we need to convert this back into the correct
			// format for the Pokemon website.
			$url = "https://www.pokemon.com/us/play-pokemon/pokemon-events/";
			$url .= preg_replace("/(..)(..)(......)/", "$1-$2-$3", $tournament["tournamentID"]) . "/";
			
			// We check if the event has any details against it. When we have too many events, we'll only show
			// maps on the events without descriptions.
			$hasDescription = trim(implode("<br /><br />", $tournament["details"])) != "";			

			// To help identify TCG vs VGC events, we add some extra styling to the card headers, as well as
			// adding an emoji icon.
			$headerClass = "";	
			if ( strpos($tournament["product"], "Trading Card Game") !== false ) {
				$headerClass = " bg-primary text-light";
				$emoji = "🎲";
			} elseif ( strpos($tournament["product"], "Video Game") !== false ) {
				$headerClass = " bg-success text-light";
				$emoji = "🎮";
			}

			$bodyClass = "";
			$footerClass = "";
			if ( $tournament["deleted"] ) {
				$emoji = "🚫";
				$headerClass = " bg-dark text-light";
				$bodyClass = " bg-secondary text-light";
				$footerClass = $headerClass;
			}
		
			// The address is stored across multiple fields, not all of which are required to be entered by
			// the organiser. To create a reasonable address, we concatenate as many of these together as we
			// can based on whether they have information in them or not.
			$address = "";
			foreach ( array("venueName", "addressLine1", "addressLine2", "city", "provinceState", "countryName", "postalZipCode") as $field ) {
				if ( isset($tournament[$field]) && $tournament[$field] != "" ) {
					$address .= $tournament[$field] . ", ";
				}
			}
			$address = preg_replace("/, $/", "", $address);			
?>
			<div class="card border-dark">
				<div class="card-header<? echo $headerClass; ?>">
<?				if ( $tournament["premierEvent"] != '' ) { ?>
					<? echo $emoji; ?> <? echo $tournament["premierEvent"]; ?><br />
<?				} ?>
					<? echo flagMaster::emojiFlag($tournament["countryCode"]) ?> <? echo $tournament["tournamentName"]; ?>
				</div>
				<div class="card-body<? echo $bodyClass; ?>">
					<h4 class="card-title"><? echo $tournament["venueName"]; ?></h4>
<?					if ( $tournament["deleted"] ) { ?>
					<span class='badge badge-danger badge-pill'>Cancelled</span>
<?					} ?>
					<h6 class="card-subtitle mb-2<? echo ($tournament["deleted"] ? "" : " text-muted"); ?>"><? echo date('F jS Y', $tournament["date"]); ?></h6>
					<p class="card-text">
						<? echo implode("<br /><br />", $tournament["details"]); ?>
					</p>
					🍙 <a target="_blank" class="<? echo $bodyClass; ?>" href="<? echo $url; ?>" class="card-link">View on Pokemon.com</a>

<?					if ( isset($tournament["website"]) ) { ?>
					<br />
					🌎 <a target="_blank" class="<? echo $bodyClass; ?>" href="<? echo $tournament["website"]; ?>" class="card-link">Event Website</a>
<?					} ?>

<?					if ( ! $skipMapWithDescriptions || ($mapCount < MAX_MAP_COUNT && ! $hasDescription) ) { ?>
					<p>
						<div class="map" id="map<? echo $tournament["tournamentID"]; ?>"></div>
					</p>
<?					} else { ?>
					<br />
<?					} ?>

					🗺 <a target="_blank" class="<? echo $bodyClass; ?>" href="http://www.google.com/maps/place/<? echo $tournament["coordinates"][0]; ?>,<? echo $tournament["coordinates"][1]; ?>" class="card-link">View on Google Maps</a>

<?					if ( ! $skipMapWithDescriptions || ($mapCount < MAX_MAP_COUNT && ! $hasDescription) ) { ?>
<?						$mapCount++; ?>

					<script>
						$(document).ready(function() {
							var map<? echo $tournament["tournamentID"]; ?> = L.map('map<? echo $tournament["tournamentID"]; ?>').setView([<? echo $tournament["coordinates"][0]; ?>, <? echo $tournament["coordinates"][1]; ?>], 15);
							
							var marker<? echo $tournament["tournamentID"]; ?> = L.marker([<? echo $tournament["coordinates"][0]; ?>, <? echo $tournament["coordinates"][1]; ?>]).addTo(map<? echo $tournament["tournamentID"]; ?>);
							
							L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?', {
								attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
							}).addTo(map<? echo $tournament["tournamentID"]; ?>);
						});
					</script>
<?					} ?>

				</div>
				<div class="card-footer<? echo $footerClass; ?>">
					<? echo $address; ?>
				</div>
			</div>
<?		} ?>
		</div>
		
		<div class="text-center text-light small">
			Developed by <a href="https://www.codearoundcorners.com/">Tim Crockford</a><span class="d-none d-sm-inline"> - </span>
			<br class="d-block d-sm-none" />
			Source Code available on <a href="https://github.com/timcrockford/pokemon-event-locator">GitHub</a>
		</div>
	</div>
</body>

</html>