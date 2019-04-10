<?php
	
include_once("resources/php/config.php");
include_once("resources/php/helpers.php");

// This is the expected content type for an iCal file.
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="pokemon.ics"');

// We build the search filter based on the GET parameters and then find all the
// matching tournaments in the database.
$filter = buildSearchFilter();
$tournaments = getFilteredTournamentData($filter);

// This will dump all the tournament data out in iCal format.
echo makeCalendarHeader();
echo makeTimezoneData($tournaments);

foreach($tournaments as $data) {
	echo convertDataToIcal($data);
}

echo makeCalendarFooter();

?>