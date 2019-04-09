<?php
	
include_once("resources/php/config.php");
include_once("resources/php/helpers.php");

//header("Content-Type: text/plain");
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="pokemon.ics"');

$filter = buildSearchFilter();
$tournaments = getFilteredTournamentData($filter);

echo makeCalendarHeader();
echo makeTimezoneData($tournaments);

foreach($tournaments as $data) {
	echo convertDataToIcal($data);
}

echo makeCalendarFooter();

?>