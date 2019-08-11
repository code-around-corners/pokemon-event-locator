<?php
	
include_once("resources/php/helpers.php");
include_once("resources/php/config.php");

const VALID_API_CALLS = array(
	"listEvents" 		=> "getEventList",
	"listPeriods"		=> "getPeriods",
);

const ISO_2_TO_3 = array(
	"BD" => "BGD", "BE" => "BEL", "BF" => "BFA", "BG" => "BGR", "BA" => "BIH", "BB" => "BRB", "WF" => "WLF", "BL" => "BLM", "BM" => "BMU",
	"BN" => "BRN", "BO" => "BOL", "BH" => "BHR", "BI" => "BDI", "BJ" => "BEN", "BT" => "BTN", "JM" => "JAM", "BV" => "BVT", "BW" => "BWA",
	"WS" => "WSM", "BQ" => "BES", "BR" => "BRA", "BS" => "BHS", "JE" => "JEY", "BY" => "BLR", "BZ" => "BLZ", "RU" => "RUS", "RW" => "RWA",
	"RS" => "SRB", "TL" => "TLS", "RE" => "REU", "TM" => "TKM", "TJ" => "TJK", "RO" => "ROU", "TK" => "TKL", "GW" => "GNB", "GU" => "GUM",
	"GT" => "GTM", "GS" => "SGS", "GR" => "GRC", "GQ" => "GNQ", "GP" => "GLP", "JP" => "JPN", "GY" => "GUY", "GG" => "GGY", "GF" => "GUF",
	"GE" => "GEO", "GD" => "GRD", "GB" => "GBR", "GA" => "GAB", "SV" => "SLV", "GN" => "GIN", "GM" => "GMB", "GL" => "GRL", "GI" => "GIB",
	"GH" => "GHA", "OM" => "OMN", "TN" => "TUN", "JO" => "JOR", "HR" => "HRV", "HT" => "HTI", "HU" => "HUN", "HK" => "HKG", "HN" => "HND",
	"HM" => "HMD", "VE" => "VEN", "PR" => "PRI", "PS" => "PSE", "PW" => "PLW", "PT" => "PRT", "SJ" => "SJM", "PY" => "PRY", "IQ" => "IRQ",
	"PA" => "PAN", "PF" => "PYF", "PG" => "PNG", "PE" => "PER", "PK" => "PAK", "PH" => "PHL", "PN" => "PCN", "PL" => "POL", "PM" => "SPM",
	"ZM" => "ZMB", "EH" => "ESH", "EE" => "EST", "EG" => "EGY", "ZA" => "ZAF", "EC" => "ECU", "IT" => "ITA", "VN" => "VNM", "SB" => "SLB",
	"ET" => "ETH", "SO" => "SOM", "ZW" => "ZWE", "SA" => "SAU", "ES" => "ESP", "ER" => "ERI", "ME" => "MNE", "MD" => "MDA", "MG" => "MDG",
	"MF" => "MAF", "MA" => "MAR", "MC" => "MCO", "UZ" => "UZB", "MM" => "MMR", "ML" => "MLI", "MO" => "MAC", "MN" => "MNG", "MH" => "MHL",
	"MK" => "MKD", "MU" => "MUS", "MT" => "MLT", "MW" => "MWI", "MV" => "MDV", "MQ" => "MTQ", "MP" => "MNP", "MS" => "MSR", "MR" => "MRT",
	"IM" => "IMN", "UG" => "UGA", "TZ" => "TZA", "MY" => "MYS", "MX" => "MEX", "IL" => "ISR", "FR" => "FRA", "IO" => "IOT", "SH" => "SHN",
	"FI" => "FIN", "FJ" => "FJI", "FK" => "FLK", "FM" => "FSM", "FO" => "FRO", "NI" => "NIC", "NL" => "NLD", "NO" => "NOR", "NA" => "NAM",
	"VU" => "VUT", "NC" => "NCL", "NE" => "NER", "NF" => "NFK", "NG" => "NGA", "NZ" => "NZL", "NP" => "NPL", "NR" => "NRU", "NU" => "NIU",
	"CK" => "COK", "XK" => "XKX", "CI" => "CIV", "CH" => "CHE", "CO" => "COL", "CN" => "CHN", "CM" => "CMR", "CL" => "CHL", "CC" => "CCK",
	"CA" => "CAN", "CG" => "COG", "CF" => "CAF", "CD" => "COD", "CZ" => "CZE", "CY" => "CYP", "CX" => "CXR", "CR" => "CRI", "CW" => "CUW",
	"CV" => "CPV", "CU" => "CUB", "SZ" => "SWZ", "SY" => "SYR", "SX" => "SXM", "KG" => "KGZ", "KE" => "KEN", "SS" => "SSD", "SR" => "SUR",
	"KI" => "KIR", "KH" => "KHM", "KN" => "KNA", "KM" => "COM", "ST" => "STP", "SK" => "SVK", "KR" => "KOR", "SI" => "SVN", "KP" => "PRK",
	"KW" => "KWT", "SN" => "SEN", "SM" => "SMR", "SL" => "SLE", "SC" => "SYC", "KZ" => "KAZ", "KY" => "CYM", "SG" => "SGP", "SE" => "SWE",
	"SD" => "SDN", "DO" => "DOM", "DM" => "DMA", "DJ" => "DJI", "DK" => "DNK", "VG" => "VGB", "DE" => "DEU", "YE" => "YEM", "DZ" => "DZA",
	"US" => "USA", "UY" => "URY", "YT" => "MYT", "UM" => "UMI", "LB" => "LBN", "LC" => "LCA", "LA" => "LAO", "TV" => "TUV", "TW" => "TWN",
	"TT" => "TTO", "TR" => "TUR", "LK" => "LKA", "LI" => "LIE", "LV" => "LVA", "TO" => "TON", "LT" => "LTU", "LU" => "LUX", "LR" => "LBR",
	"LS" => "LSO", "TH" => "THA", "TF" => "ATF", "TG" => "TGO", "TD" => "TCD", "TC" => "TCA", "LY" => "LBY", "VA" => "VAT", "VC" => "VCT",
	"AE" => "ARE", "AD" => "AND", "AG" => "ATG", "AF" => "AFG", "AI" => "AIA", "VI" => "VIR", "IS" => "ISL", "IR" => "IRN", "AM" => "ARM",
	"AL" => "ALB", "AO" => "AGO", "AQ" => "ATA", "AS" => "ASM", "AR" => "ARG", "AU" => "AUS", "AT" => "AUT", "AW" => "ABW", "IN" => "IND",
	"AX" => "ALA", "AZ" => "AZE", "IE" => "IRL", "ID" => "IDN", "UA" => "UKR", "QA" => "QAT", "MZ" => "MOZ"
);

$apiCommand = $_GET["command"];

header('Content-Type: application/json');

if ( isset(VALID_API_CALLS[$apiCommand]) ) {
	echo json_encode(VALID_API_CALLS[$apiCommand](), JSON_PRETTY_PRINT);
} else {
	echo json_encode([
		"result"	=> "error",
		"error"		=> "Invalid API call.",
		"status"	=> 400
	], JSON_PRETTY_PRINT);
}

function getEventList() {
	if ( ! isset($_GET["filters"]) ) {
		return [
			"result"	=> "error",
			"error"		=> "No filters have been specified.",
			"status"	=> 400
		];
	}

	$filter = buildSearchFilter();
	$tournaments = getFilteredTournamentData($filter);
	
	foreach($tournaments as &$tournament) {
		$tournament["isoCountryCode"] = ISO_2_TO_3[$tournament["countryCode"]];
	}
	
	return [
		"result"	=> "success",
		"status"	=> 200,
		"data"		=> $tournaments
	];
}

function getPeriods() {
	$season = "";
	$products = array();
	$premierGroups = array();
	$onlyFormat = false;
	
	$checkCount = 0;
	
	if ( isset($_GET["season"]) ) {
		$season = $_GET["season"];
		$checkCount++;
	}
	
	if ( isset($_GET["product"]) ) {
		$products = explode(",", $_GET["product"]);
		$checkCount++;
	}
	
	if ( isset($_GET["premierGroup"]) ) {
		$premierGroups = explode(",", $_GET["premierGroup"]);
		$checkCount++;
	}
	
	if ( isset($_GET["onlyFormat"]) ) {
		$onlyFormat = true;
		$checkCount++;
	}
	
	$mysqli = new mysqli(DB_HOST, DB_READ_USER, DB_READ_PASS, DB_NAME);

	$sql = "Select
		s.season,
	    s.startDate as seasonStartDate,
	    s.endDate as seasonEndDate,
	    p.id as periodId,
	    p.periodName,
	    p.startDate as periodStartDate,
	    p.endDate as periodEndDate,
	    p.premierGroups,
	    p.products,
	    p.isFormatPeriod,
	    p.isTravelAward
	From
		seasons s
	    	Inner Join periods p
	        	On s.id = p.seasonId";
	        	
	$result = $mysqli->query($sql);
	$periods = array();
	
	if ( $result->num_rows > 0 ) {
		while ( $period = $result->fetch_assoc() ) {
			$currentSeason = $period["season"];
			
			$matchCount = 0;

			if ( $season == $currentSeason ) {
				$matchCount++;
			}
			
			$validMatch = false;
			foreach($products as $product) {
				if ( stripos($period["products"], $product) !== false ) {
					$validMatch = true;
				}	
			}
			if ( $validMatch ) $matchCount++;
			
			$validMatch = false;
			foreach($premierGroups as $premierGroup) {
				if ( stripos($period["premierGroup"], $premierGroup) !== false ) {
					$validMatch = true;
				}	
			}
			if ( $validMatch ) $matchCount++;
			
			if ( $onlyFormat && $period["isFormatPeriod"] ) {
				$matchCount++;
			}
			
			if ( $matchCount == $checkCount ) {
				if ( ! isset($periods[$currentSeason]) ) {
					$periods[$currentSeason] = array(
						"startDate"		=> $period["seasonStartDate"],
						"endDate"		=> $period["seasonEndDate"],
						"periods"		=> array()
					);
				}
				
				$periods[$currentSeason]["periods"][$period["periodId"]] = array(
					"name"				=> $period["periodName"],
					"startDate"			=> $period["periodStartDate"],
					"endDate"			=> $period["periodEndDate"],
					"products"			=> json_decode($period["products"], true),
					"premierGroups"		=> json_decode($period["premierGroups"], true),
					"isTravelAward"		=> $period["isTravelAward"],
					"isFormatPeriod"	=> $period["isFormatPeriod"]
				);
			}
		}
	}
	
	return [
		"result"	=> "success",
		"status"	=> 200,
		"data"		=> $periods
	];	
}

?>