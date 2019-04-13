<?php
	
define("MAJOR_VERSION", 3);
define("MINOR_VERSION", 1);

include_once("resources/php/config.php");

// This lets us use the country codes to display the appropriate flag emoji.
include_once("vendor/peterkahl/php/flagMaster.php");
use peterkahl\flagMaster\flagMaster;

// This function gets used to standardise the field names from the Pokemon website.
function camelCase($str, array $noStrip = []) {
    $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
    $str = trim($str);
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);
    $str = lcfirst($str);

    return $str;
}

// Extract all the GET parameters and convert them into an array.
function buildSearchFilter() {
	$filter = array();
	
	if ( isset($_POST["filters"] ) ) {
		$filter = json_decode(base64_decode($_POST["filters"]), true);
	} else if ( isset($_GET["filters"] ) ) {
		$filter = json_decode(base64_decode($_GET["filters"]), true);
	} else {
		if ( isset($_POST["countryName"]) )							$filter["countryName"] = $_POST["countryName"];
		if ( isset($_POST["provinceState"]) )							$filter["provinceState"] = $_POST["provinceState"];
		if ( isset($_POST["product"]) )								$filter["product"] = $_POST["product"];
		if ( isset($_POST["category"]) )								$filter["category"] = $_POST["category"];
		if ( isset($_POST["premierEvent"]) )							$filter["premierEvent"] = $_POST["premierEvent"];
		if ( isset($_POST["premierGroup"]) )							$filter["premierGroup"] = $_POST["premierGroup"];
		if ( isset($_POST["premierOnly"]) )							$filter["premierOnly"] = true;
		if ( isset($_POST["excludePremier"]) )							$filter["excludePremier"] = true;
		if ( isset($_POST["showDeleted"]) )							$filter["showDeleted"] = true;
		if ( isset($_POST["useMiles"]) )								$filter["useMiles"] = true;
		if ( isset($_POST["startDate"]) && $_POST["startDate"] != "" )	$filter["startDate"] = $_POST["startDate"];
		if ( isset($_POST["endDate"]) && $_POST["endDate"] != "" )		$filter["endDate"] = $_POST["endDate"];
		if ( isset($_POST["latitude"]) )								$filter["latitude"] = $_POST["latitude"];
		if ( isset($_POST["longitude"]) )								$filter["longitude"] = $_POST["longitude"];
		if ( isset($_POST["radius"]) )								$filter["radius"] = $_POST["radius"];

		// We've moved everything to POST now but this will support older subscriptions. This dataset won't be updated
		// if new fields are added.
		if ( isset($_GET["countryName"]) )							$filter["countryName"] = $_GET["countryName"];
		if ( isset($_GET["provinceState"]) )							$filter["provinceState"] = $_GET["provinceState"];
		if ( isset($_GET["product"]) )								$filter["product"] = $_GET["product"];
		if ( isset($_GET["category"]) )								$filter["category"] = $_GET["category"];
		if ( isset($_GET["premierEvent"]) )							$filter["premierEvent"] = $_GET["premierEvent"];
		if ( isset($_GET["premierGroup"]) )							$filter["premierGroup"] = $_GET["premierGroup"];
		if ( isset($_GET["premierOnly"]) )							$filter["premierOnly"] = true;
		if ( isset($_GET["excludePremier"]) )							$filter["excludePremier"] = true;
		if ( isset($_GET["showDeleted"]) )							$filter["showDeleted"] = true;
		if ( isset($_GET["startDate"]) && $_GET["startDate"] != "" )	$filter["startDate"] = $_GET["startDate"];
		if ( isset($_GET["endDate"]) && $_GET["endDate"] != "" )		$filter["endDate"] = $_GET["endDate"];
	}
	
	return $filter;
}

// Extract all the tournaments matching the specified filters.
function getFilteredTournamentData($filters) {
	$sql = "Select tournamentID, eventJson, lastUpdated, deleted From events Where ";

	if ( isset($filters["showDeleted"]) ) {
		$sql .= "1=1";
	} else {
		$sql .= "deleted = 0";
	}
	
	if ( isset($filters["countryName"]) ) {
		$sql .= " And (1=0";
		
		foreach($filters["countryName"] as $countryName) {
			$sql .= " Or countryName = '" . $countryName . "'";
		}
		
		$sql .= ")";
	}

	if ( isset($filters["provinceState"]) ) {
		$sql .= " And (1=0";
		
		foreach($filters["provinceState"] as $provinceState) {
			$sql .= " Or provinceState = '" . $provinceState . "'";
		}
		
		$sql .= ")";
	}
	
	if ( isset($filters["product"]) ) {
		$sql .= " And (1=0";
		
		foreach($filters["product"] as $product) {
			$sql .= " Or product = '" . $product . "'";
		}
		
		$sql .= ")";
	}

	if ( isset($filters["category"]) ) {
		$sql .= " And (1=0";
		
		foreach($filters["category"] as $category) {
			$sql .= " Or category = '" . $category . "'";
		}
		
		$sql .= ")";
	}

	if ( isset($filters["premierEvent"]) ) {
		$sql .= " And (1=0";
		
		foreach($filters["premierEvent"] as $premierEvent) {
			$sql .= " Or premierEvent = '" . $premierEvent . "'";
		}
		
		$sql .= ")";
	}
	
	if ( isset($filters["premierGroup"]) ) {
		$sql .= " And (1=0";
		
		foreach($filters["premierGroup"] as $premierGroup) {
			$sql .= " Or premierGroup = '" . $premierGroup . "'";
		}
		
		$sql .= ")";
	}

	if ( isset($filters["premierOnly"]) ) {
		$sql .= " And premierEvent <> ''";
	} elseif ( isset($filters["excludePremier"]) ) {
		$sql .= " And premierEvent = ''";
	}
	
	if ( isset($filters["startDate"]) ) {
		$sql .= " And date >= '" . $filters["startDate"] . "'";
	}

	if ( isset($filters["endDate"]) ) {
		$sql .= " And date <= '" . $filters["endDate"] . "'";
	}
	
	$sql .= " Order By date, tournamentID;";
	
	$mysqli = new mysqli(DB_HOST, DB_READ_USER, DB_READ_PASS, DB_NAME);
	$result = $mysqli->query($sql);
	$tournaments = array();
	
	if ( $result->num_rows > 0 ) {
		while ( $tournament = $result->fetch_assoc() ) {
			$data = json_decode($tournament["eventJson"], true);
			$data["lastUpdated"] = $tournament["lastUpdated"];
			$data["deleted"] = $tournament["deleted"] == 1;
			
			$distanceCheck = true;
			
			if ( isset($filters["latitude"]) && isset($filters["longitude"]) ) {
				if ( $filters["latitude"] != "" && $filters["longitude"] != "" ) {
					$distance = calcCrow($data["coordinates"][0], $data["coordinates"][1], $filters["latitude"], $filters["longitude"]);
					$data["distanceToEvent"] = $distance;
					
					if ( $distance > $filters["radius"] ) {
						$distanceCheck = false;
					}
				}
			}
			
			if ( $distanceCheck ) {
				$tournaments[count($tournaments)] = $data;
			}
		}
	}
	
	$result->free();
	$mysqli->close();
	
	return $tournaments;
}

// Convert a single tournament into iCal format.
function convertDataToIcal($data) {
	$ical = "BEGIN:VEVENT\r\n";
	$ical .= "UID:" . $data["tournamentID"] . "@pokecal.codearoundcorners.com\r\n";
	$ical .= "DTSTAMP:" . preg_replace("/[^0-9T]/", "", str_replace(" ", "T", $data["lastUpdated"])) . "Z\r\n";
	$ical .= "SUMMARY:" . $data["tournamentName"] . "\r\n";

	$startTime = (int)preg_replace("/[^0-9]/", "", $data["registration"][0]);
	if ( strpos($data["registration"][0], "PM") !== false && $startTime < 1200 ) $startTime += 1200;
	if ( $startTime < 1000 ) $startTime = "0" . $startTime;
	$ical .= "DTSTART;TZID=" . $data["zoneName"] . ":" . date("Ymd", $data["date"]) . "T" . $startTime . "00\r\n";

	$endTime = (int)preg_replace("/[^0-9]/", "", $data["registration"][1]);
	if ( strpos($data["registration"][1], "PM") !== false && $endTime < 1200  ) $endTime += 1200;
	if ( $endTime < 1000 ) $endTime = "0" . $endTime;	
	$ical .= "DTEND;TZID=" . $data["zoneName"] . ":" . date("Ymd", $data["date"]) . "T" . $endTime . "00\r\n";
	
	$address = "";
	foreach ( array("venueName", "addressLine1", "addressLine2", "city", "provinceState", "countryName", "postalZipCode") as $field ) {
		if ( isset($data[$field]) && $data[$field] != "" ) {
			$address .= str_replace(",", "\\,", $data[$field]) . "\\, ";
		}
	}
	$ical .= "LOCATION:" . preg_replace("/\\\\, $/", "", $address) . "\r\n";
	
	$ical .= "GEO:" . $data["coordinates"][0] . "," . $data["coordinates"][1] . "\r\n";
	
	$url = "https://www.pokemon.com/us/play-pokemon/pokemon-events/" . preg_replace("/(..)(..)(......)/", "$1-$2-$3", $data["tournamentID"]) . "/";
	$ical .= "URL:" . $url . "\r\n";
	$ical .= "DESCRIPTION:" . str_replace("\\n\\n\\n\\n", "\\n\\n", implode("\\n\\n", $data["details"])) . "\r\n";	
	$ical .= "END:VEVENT\r\n";
	
	return $ical;
}

// Common iCal header.
function makeCalendarHeader() {
	$ical = "BEGIN:VCALENDAR\r\n";
	$ical .= "VERSION:2.0\r\n";
	$ical .= "PRODID:-//Code Around Corners//Pokemon Calendar Subscription Tool v" . getVersionNumber() . "//EN\r\n";
	$ical .= "CALSCALE:GREGORIAN\r\n";
	$ical .= "METHOD:PUBLISH\r\n";
	$ical .= "X-WR-CALNAME:Pokemon Events\r\n";
	$ical .= "X-WR-CALDESC:Pokemon Events\r\n";
	
	return $ical;
}

// Common iCal footer.
function makeCalendarFooter() {
	return "END:VCALENDAR\r\n";
}

// Extract all the necessary timezone information from the tournament list.
function makeTimezoneData($data) {
	$timezones = array();
	
	foreach ( $data as $item ) {
		$timezone = $item["zoneName"];
		$match = false;
		
		foreach ( $timezones as $tz ) {
			if ( $tz == $timezone ) {
				$match = true;
			}
		}
		
		if ( ! $match ) {
			$timezones[count($timezones)] = $timezone;
		}
	}

	$ical = "";
	
	foreach ( $timezones as $timezone ) {
		$ical .= getTimezoneData($timezone);
	}
	
	return $ical;
}

// Uses tzurl.org to create VTIMEZONE blocks.
function getTimezoneData($timezone) {
	$mysqli = new mysqli(DB_HOST, DB_READ_USER, DB_READ_PASS, DB_NAME);

	$sql = "Select vTimezone From timezones Where timezone = '" . $timezone . "';";
	$result = $mysqli->query($sql);
	$data = null;
	
	$data = $result->fetch_assoc();
	$timezoneData = $data["vTimezone"];
	
	$result->free();
	$mysqli->close();
	
	return $timezoneData;
}

// Get a list of all the distinct values for a specified field in the events table. Ignores blanks.
// The data is sorted alphabetically.
function getDistinctList($fieldName) {
	$mysqli = new mysqli(DB_HOST, DB_READ_USER, DB_READ_PASS, DB_NAME);
	$sql = "Select Distinct " . $fieldName . " From events Where " . $fieldName . " <> '' Order By " . $fieldName . ";";
	
	$result = $mysqli->query($sql);
	$list = array();
	
	if ( $result->num_rows > 0 ) {
		while ( $data = $result->fetch_assoc() ) {
			$list[count($list)] = $data[$fieldName];
		}
	}
	
	$result->free();
	$mysqli->close();
	
	return $list;
}

// Gets a list of all the provinces by country. Used to allow the selection box to filter the list
// based on the country chosen. Also helps in the event of the same province name being relevant to
// multiple countries.
function getProvinceList() {
	$mysqli = new mysqli(DB_HOST, DB_READ_USER, DB_READ_PASS, DB_NAME);
	$sql = "Select Distinct countryName, provinceState From events Where countryName <> '' And provinceState <> '' ";
	$sql .= "Order By countryName, provinceState;";
	
	$result = $mysqli->query($sql);
	
	$countryNames = array();
	
	if ( $result->num_rows > 0 ) {
		while ( $data = $result->fetch_assoc() ) {
			$countryName = $data["countryName"];
			if ( ! isset($countryNames[$countryName]) ) {
				$countryNames[$countryName] = array();
			}
			
			$countryNames[$countryName][count($countryNames[$countryName])] = $data["provinceState"];
		}
	}
	
	$result->free();
	$mysqli->close();
	
	return $countryNames;
}

// Simple function to return the current version number of the software, including the latest GitHub
// reference if available.
function getVersionNumber() {
	$major = MAJOR_VERSION;
	$minor = '00' . MINOR_VERSION;
	$minor = substr($minor, -2, 2);
	
	$version = $major . '.' . $minor;
	
	if ( ($git = @file_get_contents('.git/HEAD')) !== false ) {
		$file = substr($git, 5, strlen($git) - 6);
		$gitid = file_get_contents('.git/' . $file);
		$version .= '-' . substr($gitid, 0, 10);
	}
	
	return $version;
}

function outputHtmlHeader($includeSelect2 = false, $includeDatePicker = false, $includeLeaflet = false, $includeLocationPicker = false) {
	ob_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Pokémon Event Locator/Subscription Tool</title>
	
	<!-- Lock zooming on mobile devices -->
	<meta name="viewport" content="width=device-width, maximum-scale=1, minimum-scale=1, user-scalable=no"/>

	<!-- Site CSS scripts -->
	<link href="https://<? echo $_SERVER["HTTP_HOST"]; ?>/resources/css/pokecal.css" rel="stylesheet" />

	<!-- jQuery CDN -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>	

	<!-- Bootstrap CDN -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

<?	if ( $includeSelect2 ) { ?>
	<!-- Select2 CDN -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<?	} ?>

<?	if ( $includeDatePicker ) { ?>
	<!-- Bootstrap-Datepicker CDN -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
<?	} ?>

<?	if ( $includeLeaflet ) { ?>	
	<!-- Leaflet CDN -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
	<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
<?	} ?>

<?	if ( $includeLocationPicker ) { ?>
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css' rel='stylesheet' />
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.0.0/mapbox-gl-geocoder.min.js'></script>
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.0.0/mapbox-gl-geocoder.css' type='text/css' />
<?	} ?>
</head>
<?
	$htmlHeader = ob_get_contents();
	ob_end_clean();
	
	return $htmlHeader;
}

function outputFooter() {
	ob_start();
?>
	<div class="text-center text-light small">
		Developed by <a href="https://www.codearoundcorners.com/">Tim Crockford</a><span class="d-none d-sm-inline"> - </span>
		<br class="d-block d-sm-none" />
		Source Code available on <a href="https://github.com/timcrockford/pokemon-event-locator">GitHub</a>
	</div>
<?
	$outputFooter = ob_get_contents();
	ob_end_clean();
	
	return $outputFooter;
}

// Outputs a tournament in a card format to display online or via email.
// $renderMaps: 0 = all, 1 = no descriptions, 2 = none
function outputTournamentCard($tournament, $renderMaps, &$mapCount, $useMiles) {
	// We're storing the tournament ID as an integer, so we need to convert this back into the correct
	// format for the Pokemon website.
	$url = "https://www.pokemon.com/us/play-pokemon/pokemon-events/";
	$url .= preg_replace("/(..)(..)(......)/", "$1-$2-$3", $tournament["tournamentID"]) . "/";
	
	// We check if the event has any details against it. When we have too many events, we'll only show
	// maps on the events without descriptions.
	$hasDescription = trim(implode("", $tournament["details"])) != "";			

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
	ob_start();
?>
	<div class="card border-dark">
		<div class="card-header<? echo $headerClass; ?>">
<?		if ( $tournament["premierEvent"] != '' ) { ?>
			<? echo $emoji; ?> <? echo $tournament["premierEvent"]; ?><br />
<?		} ?>
			<? echo flagMaster::emojiFlag($tournament["countryCode"]); ?> <? echo $tournament["tournamentName"]; ?>
		</div>
		<div class="card-body<? echo $bodyClass; ?>">
			<h4 class="card-title"><? echo $tournament["venueName"]; ?></h4>
<?			if ( $tournament["deleted"] ) { ?>
			<span class='badge badge-danger badge-pill'>Cancelled</span>
<?			} ?>
<?			if ( isset($tournament["distanceToEvent"]) && ! isset($useMiles) ) { ?>
			<span class='badge badge-secondary badge-pill'>~<? echo round($tournament["distanceToEvent"], 0); ?>kms away</span>
<?			} ?>
<?			if ( isset($tournament["distanceToEvent"]) && isset($useMiles) ) { ?>
			<span class='badge badge-secondary badge-pill'>~<? echo round($tournament["distanceToEvent"] * 0.6214, 0); ?>mi away</span>
<?			} ?>
			<h6 class="card-subtitle mb-2<? echo ($tournament["deleted"] ? "" : " text-muted"); ?>"><? echo date('F jS Y', $tournament["date"]); ?></h6>
			<p class="card-text">
				<? echo preg_replace("/<p><\/p>/", "", "<p>" . implode("</p><p>", $tournament["details"]) . "</p>"); ?>
			</p>
			🍙 <a target="_blank" class="<? echo $bodyClass; ?>" href="<? echo $url; ?>" class="card-link">View on Pokemon.com</a>

<?			if ( isset($tournament["website"]) ) { ?>
			<br />
			🌎 <a target="_blank" class="<? echo $bodyClass; ?>" href="<? echo $tournament["website"]; ?>" class="card-link">Event Website</a>
<?			} ?>

<?			if ( $renderMaps == 0 || ($mapCount < MAX_MAP_COUNT && ! $hasDescription && $renderMaps == 1 ) ) { ?>
			<p>
				<div class="map" id="map<? echo $tournament["tournamentID"]; ?>"></div>
			</p>
<?			} else { ?>
			<br />
<?			} ?>

			🗺 <a target="_blank" class="<? echo $bodyClass; ?>" href="http://www.google.com/maps/place/<? echo $tournament["coordinates"][0]; ?>,<? echo $tournament["coordinates"][1]; ?>" class="card-link">View on Google Maps</a>

<?					if ( $renderMaps == 0 || ($mapCount < MAX_MAP_COUNT && ! $hasDescription && $renderMaps == 1 ) ) { ?>
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
<?php
	$cardContent = ob_get_contents();
	ob_end_clean();
	
	return $cardContent;
}

function getEmailContents($tournaments, $filter) {
	$url = $_SERVER["HTTP_HOST"] . "/display.php?filters=" . base64_encode(json_encode($filter));
	
	ob_start();
	echo outputHtmlHeader(false, false, false);
?>
<body>
	<div class="container p-1 mt-3">
		<div class="card border-dark mb-3">
			<div class="card-header text-light bg-danger">	
				<h4 class="text-center text-md-left">Pokémon Event Locator/Subscription Tool</h4>
			</div>
			
			<div class="card-body">
				<div class="card-text">
					Thank you for using this tool to help find your events! Below are all the events that
					matched the criteria you specified. You can also subscribe to this calendar using the
					links below, or view it online to get the latest updates.
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
						<a href="https://<? echo $_SERVER["HTTP_HOST"]; ?>/index.php?filters=<? echo base64_encode(json_encode($filter)); ?>">Edit Calendar Filters</a>
					</div>
				</div>
			</div>
		</div>
		<div class="card-columns">
<?
	$mapCount = 0;
	foreach ( $tournaments as $tournament ) {
		echo outputTournamentCard($tournament, 2, $mapCount, $filter["useMiles"]);
	}
?>
		</div>
		<? echo outputFooter(); ?>
	</div>
</body>
</html>
<?
	$emailContents = ob_get_contents();
	ob_end_clean();
	
	return $emailContents;
}

function sendEventEmail($tournaments, $toAddress, $filter) {
	$emailHtml = getEmailContents($tournaments, $filter);
	
	$subject = "Upcoming Pokemon Events";

	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=UTF-8';
	$headers[] = 'From: ' . SEND_FROM_NAME . ' <' . SEND_FROM_EMAIL . '>';
	
	mail($toAddress, $subject, $emailHtml, implode("\r\n", $headers));
}

function calcCrow($lat1, $lon1, $lat2, $lon2) {
	$R = 6371; // km
	$dLat = toRad($lat2 - $lat1);
	$dLon = toRad($lon2 - $lon1);
	$lat1 = toRad($lat1);
	$lat2 = toRad($lat2);
	
	$a = sin($dLat / 2) * sin($dLat / 2) + sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2); 
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	$d = $R * $c;
	
	return $d;
}

function toRad($Value) {
    return $Value * pi() / 180;
}

?>