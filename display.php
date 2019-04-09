<html>
<head>
<title>Pokémon Event Locator/Subscription Tool</title>
	<meta name="viewport" content="width=device-width, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
	<link href="resources/css/pokecal.css" rel="stylesheet" />

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
	<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
</head>

<body>
<?php

include_once("resources/php/helpers.php");
include_once("vendor/peterkahl/php/flagMaster.php");

use peterkahl\flagMaster\flagMaster;

$filter = buildSearchFilter();
$tournaments = getFilteredTournamentData($filter);
$maxMapCount = 50;

$url = str_replace("display.php", "calendar.php", $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

?>
<div class="container p-3">

<div class="card border-dark">
<div class="card-header text-light bg-danger">	
<h1 class="d-none d-sm-none d-md-block">Pokémon Event Locator/Subscription Tool</h1>
<h4 class="d-block d-sm-block d-md-none"><center>Pokémon Event Locator/Subscription Tool</center></h4>
</div>

<div class="card-body">
<div class="card-text">
<p>
The link to your calendar is below! Click on the "Subscribe" link to subscribe directly to this calendar or the "Download"
link to download the calendar to your device. Or alternatively keep scrolling to view the events online! Please note that
the map locations are based on the coordinates provided by the Pokemon.com website.
</p>

<?php
	if ( count($tournaments) > $maxMapCount ) {
		$skipMapWithDescriptions = true;
?>
<p>
As your calendar has more than <?php echo $maxMapCount; ?> events on it then only the first <?php echo $maxMapCount; ?> 
events without a description will display a mini map, however you can still load a full map on Google Maps.
</p>
<?php
	} else {
		$skipMapWithDescriptions = false;
	}
?>
</div>
</div>

<div class="card-footer">
<div class="row">
	<div class="col-12 col-sm-4">
		<center>
			<a href="webcal://<?php echo $url; ?>">Subscribe To This Calendar</a>
		</center>
	</div>
	<div class="col-12 col-sm-4">
		<center>
			<a href="https://<?php echo $url; ?>">Download This Calendar To My Device</a>
		</center>
	</div>
	<div class="col-12 col-sm-4">
		<center>
			<a href="index.php">Get Another Calendar</a>
		</center>
	</div>
</div>
</div>
</div>

<div class="card-columns small my-3">
<?php
$mapCount = 0;

foreach ( $tournaments as $tournament ) {
	$url = "https://www.pokemon.com/us/play-pokemon/pokemon-events/" . preg_replace("/(..)(..)(......)/", "$1-$2-$3", $tournament["tournamentID"]) . "/";
	
	$hasDescription = trim(implode("<br /><br />", $tournament["details"])) != "";
	
	$baseClass = "";
	
	if ( strpos($tournament["product"], "Trading Card Game") !== false ) {
		$baseClass = " bg-primary text-light";
		$emoji = "🎲";
	} elseif ( strpos($tournament["product"], "Video Game") !== false ) {
		$baseClass = " bg-success text-light";
		$emoji = "🎮";
	}

	$address = "";
	foreach ( array("venueName", "addressLine1", "addressLine2", "city", "provinceState", "countryName", "postalZipCode") as $field ) {
		if ( isset($tournament[$field]) && $tournament[$field] != "" ) {
			$address .= $tournament[$field] . ", ";
		}
	}
	$address = preg_replace("/, $/", "", $address);
	
?>
<div class="card border-dark">
	<div class="card-header<?php echo $baseClass; ?>">
		<?php echo $emoji . " " . $tournament["premierEvent"]; ?><br />
		<?php echo flagMaster::emojiFlag($tournament["countryCode"]) . " " . $tournament["tournamentName"]; ?>
	</div>
	<div class="card-body">
		<h4 class="card-title"><?php echo $tournament["venueName"]; ?></h4>
		<h6 class="card-subtitle mb-2 text-muted"><?php echo date('F jS Y', $tournament["date"]); ?></h4>
		<p class="card-text">
			<?php echo implode("<br /><br />", $tournament["details"]); ?>
		</p>
		<a target="_blank" href="<?php echo $url; ?>" class="card-link">View on Pokemon.com</a>
		<?php
			if ( isset($tournament["website"]) ) {
		?>
		<a target="_blank" href="<?php echo $tournament["website"]; ?>" class="card-link">Event Website</a>
		<?php
			}
		?>
		<?php
			if ( ! $skipMapWithDescriptions || ($mapCount < $maxMapCount && ! $hasDescription) ) {
		?>
		<p>
			<div class="map" id="map<?php echo $tournament["tournamentID"]; ?>"></div>
		</p>
		<?php
			} else {
		?>
		<br />
		<?php
			}
		?>
		<a target="_blank" href="http://www.google.com/maps/place/<?php echo $tournament["coordinates"][0]; ?>,<?php echo $tournament["coordinates"][1]; ?>" class="card-link">View on Google Maps</a>

		<?php
			if ( ! $skipMapWithDescriptions || ($mapCount < $maxMapCount && ! $hasDescription) ) {
				$mapCount++;
		?>
		<script>
			$(document).ready(function() {
				var map<?php echo $tournament["tournamentID"]; ?> = L.map('map<?php echo $tournament["tournamentID"]; ?>').setView([<?php echo $tournament["coordinates"][0]; ?>, <?php echo $tournament["coordinates"][1]; ?>], 16);
				
				var marker<?php echo $tournament["tournamentID"]; ?> = L.marker([<?php echo $tournament["coordinates"][0]; ?>, <?php echo $tournament["coordinates"][1]; ?>]).addTo(map<?php echo $tournament["tournamentID"]; ?>);
				
				L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=<?php echo MAPBOX_TOKEN; ?>', {
				    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
				    maxZoom: 18,
				    id: 'mapbox.streets',
				    accessToken: '<?php echo MAPBOX_TOKEN; ?>'
				}).addTo(map<?php echo $tournament["tournamentID"]; ?>);
			});
		</script>
		<?php
			}
		?>
	</div>
	<div class="card-footer">
		<?php echo $address; ?>
	</div>
</div>
<?php
}
?>
</div>
</div>

<script>

</script>
</body>

</html>