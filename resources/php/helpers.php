<?php
	
include_once("resources/php/config.php");

function camelCase($str, array $noStrip = []) {
    $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
    $str = trim($str);
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);
    $str = lcfirst($str);

    return $str;
}

function buildSearchFilter() {
	$filter = array();
	
	if ( isset($_GET["countryName"]) )							$filter["countryName"] = $_GET["countryName"];
	if ( isset($_GET["provinceState"]) )							$filter["provinceState"] = $_GET["provinceState"];
	if ( isset($_GET["product"]) )								$filter["product"] = $_GET["product"];
	if ( isset($_GET["category"]) )								$filter["category"] = $_GET["category"];
	if ( isset($_GET["premierEvent"]) )							$filter["premierEvent"] = $_GET["premierEvent"];
	if ( isset($_GET["premierGroup"]) )							$filter["premierGroup"] = $_GET["premierGroup"];
	if ( isset($_GET["premierOnly"]) )							$filter["premierOnly"] = true;
	if ( isset($_GET["excludePremier"]) )							$filter["excludePremier"] = true;
	if ( isset($_GET["startDate"]) && $_GET["startDate"] != "" )	$filter["startDate"] = $_GET["startDate"];
	if ( isset($_GET["endDate"]) && $_GET["endDate"] != "" )		$filter["endDate"] = $_GET["endDate"];
	
	return $filter;
}

function getSingleTournamentData($tournamentID) {
	$mysqli = new mysqli(DB_HOST, DB_READ_USER, DB_READ_PASS, DB_NAME);
	$sql = "Select eventJson, lastUpdated From events Where tournamentID = " . $tournamentID . ";";
	$result = $mysqli->query($sql);
	$data = null;
	
	if ( $result->num_rows == 1 ) {
		$tournament = $result->fetch_assoc();
		$data = json_decode($tournament["eventJson"], true);
		$data["lastUpdated"] = $tournament["lastUpdated"];
	}
	
	$result->free();
	$mysqli->close();
	
	return $data;
}

function getFilteredTournamentData($filters) {
	$sql = "Select tournamentID, eventJson, lastUpdated From events Where 1=1";
	
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
			$tournaments[count($tournaments)] = $data;
		}
	}
	
	$result->free();
	$mysqli->close();
	
	return $tournaments;
}

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
	$ical .= "DESCRIPTION:" . implode("\\n\\n", $data["details"]) . "\r\n";	
	$ical .= "END:VEVENT\r\n";
	
	return $ical;
}

function makeCalendarHeader() {
	$ical = "BEGIN:VCALENDAR\r\n";
	$ical .= "VERSION:2.0\r\n";
	$ical .= "PRODID:-//Code Around Corners//Pokemon Calendar Subscription Tool v3.00//EN\r\n";
	$ical .= "CALSCALE:GREGORIAN\r\n";
	$ical .= "METHOD:PUBLISH\r\n";
	$ical .= "X-WR-CALNAME:Pokemon Events\r\n";
	$ical .= "X-WR-CALDESC:Pokemon Events\r\n";
	
	return $ical;
}

function makeCalendarFooter() {
	return "END:VCALENDAR\r\n";
}

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

function getTimezoneData($timezone) {
	$mysqli = new mysqli(DB_HOST, DB_UPDATE_USER, DB_UPDATE_PASS, DB_NAME);

	$sql = "Select vTimezone From timezones Where timezone = '" . $timezone . "';";
	$result = $mysqli->query($sql);
	$data = null;
	
	if ( $result->num_rows == 1 ) {
		$data = $result->fetch_assoc();
		$timezoneData = $data["vTimezone"];
	} else {
		$timezoneData = file_get_contents("http://tzurl.org/zoneinfo-outlook/" . $timezone);	
		$timezoneData = preg_replace("/^.*BEGIN\:VTIMEZONE/si", "BEGIN:VTIMEZONE", $timezoneData);
		$timezoneData = preg_replace("/END\:VTIMEZONE.*$/si", "END:VTIMEZONE\r\n", $timezoneData);
		
		$sql = "Insert Into timezones ( timezone, vTimezone ) Values ( '" . $timezone . "', '";
		$sql .= $mysqli->real_escape_string($timezoneData) . "' );";
		
		$mysqli->query($sql);
	}
	
	$result->free();
	$mysqli->close();
	
	return $timezoneData;
}

function getDistinctList($fieldName) {
	$mysqli = new mysqli(DB_HOST, DB_READ_USER, DB_READ_PASS, DB_NAME);
	$sql = "Select Distinct " . $fieldName . " From events Where " . $fieldName . " <> '';";
	
	$result = $mysqli->query($sql);
	$list = array();
	
	if ( $result->num_rows > 0 ) {
		while ( $data = $result->fetch_assoc() ) {
			$list[count($list)] = $data[$fieldName];
		}
	}
	
	$result->free();
	$mysqli->close();
	
	asort($list);
	
	return $list;
}

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

function fixProvinces() {
	$sql = "Update events Set provinceState = Case ";
	$sql .= "When provinceState = 'Vic' Then 'Victoria' ";
	$sql .= "When provinceState = 'NSW' Then 'New South Wales' ";
	$sql .= "When provinceState = 'NT' Then 'Northern Territory' ";
	$sql .= "When provinceState = 'QLD' Then 'Queensland' ";
	$sql .= "When provinceState = 'Select a State' Then '' ";
	$sql .= "When provinceState = 'Tas' Then 'Tasmania' ";
	$sql .= "When provinceState = 'WA' Then 'Western Australia' ";
	$sql .= "When provinceState = 'ACT' Then 'Australian Capital Territory' ";
	$sql .= "Else provinceState End ";
	$sql .= "Where countryName = 'Australia';";

	$mysqli = new mysqli(DB_HOST, DB_UPDATE_USER, DB_UPDATE_PASS, DB_NAME);
	$mysqli->query($sql);
	$mysqli->close();
}

function addPremierGroups() {
	$sql = "Update events Set premierGroup = Case ";
	$sql .= "When premierEvent Like '%Regional%' Then 'Regional Championship' ";
	$sql .= "When premierEvent Like '%Special%' Then 'Special Championship' ";
	$sql .= "When premierEvent Like '%Cup%' Then 'League Cup' ";
	$sql .= "When premierEvent Like '%League%Challenge%' Then 'League Challenge' ";
	$sql .= "When premierEvent Like '%Premier%Challenge%' Then 'Premier Challenge' ";
	$sql .= "When premierEvent Like '%Midseason%Showdown%' Then 'Midseason Showdown' ";
	$sql .= "When premierEvent Like '%Prerelease%' Then 'Prerelease' ";
	$sql .= "Else '' End ";
	$sql .= "Where premierEvent <> '';";

	$mysqli = new mysqli(DB_HOST, DB_UPDATE_USER, DB_UPDATE_PASS, DB_NAME);
	$mysqli->query($sql);
	$mysqli->close();
}

?>