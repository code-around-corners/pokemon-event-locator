<?php

include_once("resources/php/helpers.php");

$countryNames = getDistinctList("countryName");
$countryProvinceStates = getProvinceList();
$countryProvincePostalCodes = getPostalZipCodeList();
$premierEvents = getDistinctList("premierEvent");
$premierGroups = getDistinctList("premierGroup");
$products = getDistinctList("product");
$categories = getDistinctList("category");

$filter = buildSearchFilter();

echo outputHtmlHeader(true, true, false, true);
?>
<body>
	<div class="container p-3">
		<form action="/display.php" method="post">
			<div class="card border-dark">
				<div class="card-header text-light bg-danger">
					<h4 class="text-center text-md-left"><i class="fas fa-calendar-alt fa-1x"></i> Pokémon Event Locator/Subscription Tool</h4>
				</div>
				<div class="card-body<? echo isset($_GET["filters"]) ? " d-none" : ""; ?>">
					<div class="card-text">
						Find using the official Pokemon.com locator too difficult to use? Try this one instead! Not only can you search
						easily for events but you can also subscribe to the calendar, making it really easy to keep up to date with events
						in your area. Please note that local leagues are not currently supported. Click Get My Calendar once you're happy
						with your current selections.
					</div>
				</div>
				<div class="card-footer">
					<div class="row">
						<div class="col-12 col-md-6 text-center">
							<input type="submit" value="Get My Calendar" />
						</div>
						<div class="col-12 col-md-6 text-center">
							<button type="button">
								<a class="text-dark" href="/index.php">Reset My Selections</a>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div id="accordion" role="tablist">
				<div class="card my-3 border-dark">
					<div class="card-header">
						<h5 class="mb-0">
							<a data-toggle="collapse" href="#collapseEvents" aria-expanded="true" aria-controls="collapseEvents">
								<i class="fas fa-filter fa-1x"></i> Event Filters
								<button type="button" class="close">
									<span aria-hidden="true"><i class="fas fa-bars fa-1x"></i></span>
								</button>
							</a>
						</h5>
					</div>
					<div id="collapseEvents" class="collapse show" role="tabpanel" aria-labelledby="collapseEvents">
						<div class="card-body">
							<p>
								Select the filters you want to use in the dropdown boxes below. You can only select a province or
								state filter if you have selected a single country. Premier Event Groups allow you to search for all
								events of a specific type, whereas Premier Event Types are the actual type shown on the official
								website, and are usually more granular.
							</p>
							<div class="row">
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="countryName"><b>Select Countries:</b></label>
										<select class="select2 form-control" id="countryName" name="countryName[]" multiple="multiple" width="100%">
<?										foreach($countryNames as $countryName) { ?>
											<option value="<? echo $countryName; ?>"><? echo $countryName; ?></option>
<?										} ?>
										</select>
									</div>
								</div>
		
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="provinceState"><b>Select Province/States:</b></label>
										<select class="select2 form-control" id="provinceState" name="provinceState[]" multiple="multiple" width="100%">
<?										foreach($countryProvinceStates as $countryName => $provinceStates) { ?>
											<optgroup label="<? echo $countryName; ?>">
<?											foreach($provinceStates as $provinceState) { ?>
												<option value="<? echo $provinceState; ?>"><? echo $provinceState; ?></option>
<?											} ?>
											</optgroup>
<?										} ?>
										</select>
									</div>
								</div>
						
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="postalZipCode"><b>Select Postal/Zip Codes:</b></label>
										<select class="select2 form-control" id="postalZipCode" name="postalZipCode[]" multiple="multiple" width="100%">
<?										foreach($countryProvincePostalCodes as $countryName => $provincePostalCodes) { ?>
<?											foreach($provincePostalCodes as $provinceName => $postalCodes) { ?>
											<optgroup label="<? echo $countryName . '/' . $provinceName; ?>">
<?												foreach($postalCodes as $postalZipCode) { ?>
												<option value="<? echo $postalZipCode; ?>"><? echo $postalZipCode; ?></option>
<?												} ?>
											</optgroup>
<?											} ?>											
<?										} ?>
										</select>
									</div>
								</div>

								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="premierGroup"><b>Select Premier Event Group:</b></label>
										<select class="select2 form-control" id="premierGroup" name="premierGroup[]" multiple="multiple" width="100%">
<?										foreach($premierGroups as $premierGroup) { ?>
											<option value="<? echo $premierGroup; ?>"><? echo $premierGroup; ?></option>
<?										} ?>
										</select>
									</div>
								</div>
						
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="premierEvent"><b>Select Premier Event Type:</b></label>
										<select class="select2 form-control" id="premierEvent" name="premierEvent[]" multiple="multiple" width="100%">
<?										foreach($premierEvents as $premierEvent) { ?>
											<option value="<? echo $premierEvent; ?>"><? echo $premierEvent; ?></option>
<?										} ?>
										</select>
									</div>
								</div>
						
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="product"><b>Select Game Type:</b></label>
										<select class="select2 form-control" id="product" name="product[]" multiple="multiple" width="100%">
<?										foreach($products as $product) { ?>
											<option value="<? echo $product; ?>"><? echo $product; ?></option>
<?										} ?>
										</select>
									</div>
								</div>
						
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="category"><b>Select Game Formats:</b></label>
										<select class="select2 form-control" id="category" name="category[]" multiple="multiple" width="100%">
<?										foreach($categories as $category) { ?>
											<option value="<? echo $category; ?>"><? echo $category; ?></option>;
<?										} ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card my-3 border-dark">
					<div class="card-header">
						<h5 class="mb-0">
							<a data-toggle="collapse" class="collapsed" href="#collapseMaps" aria-expanded="false" aria-controls="collapseMaps">
								<i class="fas fa-map-marked-alt fa-1x"></i> Map Location Filters
								<button type="button" class="close">
									<span aria-hidden="true"><i class="fas fa-bars fa-1x"></i></span>
								</button>
							</a>
						</h5>
					</div>
					<div id="collapseMaps" class="collapse" role="tabpanel" aria-labelledby="collapseMaps">
						<div class="card-body">
							<p>
								If you want to filter locations to within a certain radius of a location, you can enter the GPS coordinates into
								the text boxes below, or click the "Search For Coordinates" option to look up an address and populate them
								automatically. Select "Use Miles for Distance" to have the distance for each event return in miles rather than
								kilometers.
							</p>
							<div class="row">
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="latitude"><b>Enter Latitude:</b></label>
										<input class="form-control" id="latitude" name="latitude">
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="longitude"><b>Enter Longitude:</b></label>
										<input class="form-control" id="longitude" name="longitude">
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<label for="radius"><b>Enter Radius from Coordinates:</b></label>
										<select class="form-control" id="radius" name="radius" width="100%">
											<option value=999999>Any</option>
											<option value=10>10kms / 6mi</option>
											<option value=25>25kms / 15mi</option>
											<option value=40>40kms / 25mi</option>
											<option value=60>60kms / 37mi</option>
											<option value=100>100kms / 62mi</option>
											<option value=200>200kms / 124mi</option>
											<option value=300>300kms / 186mi</option>
											<option value=400>400kms / 249mi</option>
											<option value=500>500kms / 311mi</option>
										</select>
										<div class="form-check mt-1">
											<input class="form-check-input" type="checkbox" value="" id="useMiles" name="useMiles">
											<label class="form-check-label" for="useMiles">Use Miles for Distance</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer text-center">
							<a href="javascript:loadLocationPicker();">Search For Coordinates</a>
						</div>
					</div>
				</div>
				
				<div class="card my-3 border-dark">
					<div class="card-header">
						<h5 class="mb-0">
							<a data-toggle="collapse" class="collapsed" href="#collapseDates" aria-expanded="false" aria-controls="collapseDates">
								<i class="fas fa-clock fa-1x"></i> Event Date Filters
								<button type="button" class="close">
									<span aria-hidden="true"><i class="fas fa-bars fa-1x"></i></span>
								</button>
							</a>
						</h5>
					</div>
					<div id="collapseDates" class="collapse" role="tabpanel" aria-labelledby="collapseMaps">
						<div class="card-body">
							<p>
								Select the date range you want to use when searching for events. This can be in the past, however
								please note that we have only started collecting events from early April 2019. Alternatively use the
								Specific Date Range dropdown to prepopulate the date fields with some common date ranges such as IC
								travel award periods.
							</p>
							<div class="row">
								<div class="col-12 col-md-6 col-xl-3">
									<div class="form-group">
										<label for="startDate"><b>Select Start Date:</b></label>
										<input class="datepicker form-control" data-date-format="yyyy/mm/dd" id="startDate" name="startDate">
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-3">
									<div class="form-group">
										<label for="endDate"><b>Select End Date:</b></label>
										<input class="datepicker form-control" data-date-format="yyyy/mm/dd" id="endDate" name="endDate">
									</div>
								</div>
								<div class="col-12 col-md-12 col-xl-6">
									<div class="form-group">
										<label for="specificDateRange"><b>Use Specific Date Range:</b></label>
										<select class="form-control" id="specificDateRange" name="specificDateRange" width="100%">
											<option value=""></option>
											<optgroup label="World Championships">
												<option value="20190701-20200630">2020 World Championships</option>
												<option value="20180709-20190623">2019 World Championships</option>
											</optgroup>
											<optgroup label="2019/20 International Championship Travel Awards">
												<option value="20190429-20190623">2019/20 Latin American International Championships</option>
												<option value="20190701-20191117">2019/20 Oceania International Championships</option>
											</optgroup>
											<optgroup label="2019/20 League Cup Seasons">
												<option value="20190816-20191114">2019/20 Season 1 (Unified Minds)</option>
												<option value="20191115-20200220">2019/20 Season 2 (Cosmic Eclipse)</option>
												<option value="20200221-20200514">2019/20 Season 3</option>
												<option value="20200515-20200628">2019/20 Season 4</option>
											</optgroup>
											<optgroup label="2019/20 League Challenge Seasons">
												<option value="20190701-20191031">2019/20 Season 1 (Unified Minds)</option>
												<option value="20191101-20200131">2019/20 Season 2 (Cosmic Eclipse)</option>
												<option value="20200201-20200430">2019/20 Season 3</option>
												<option value="20200501-20200628">2019/20 Season 4</option>
											</optgroup>
											<optgroup label="2018/19 International Championship Travel Awards">
												<option value="20190218-20190428">2018/19 North American International Championships</option>
											</optgroup>
											<optgroup label="2018/19 League Cup Seasons">
												<option value="20180709-20181115">2018/19 Season 1 (Celestial Storm)</option>
												<option value="20181116-20190214">2018/19 Season 2 (Lost Thunder)</option>
												<option value="20190215-20190516">2018/19 Season 3 (Team Up)</option>
												<option value="20190517-20190623">2018/19 Season 4 (Unbroken Bonds)</option>	
											</optgroup>
											<optgroup label="2018/19 League Challenge Seasons">
												<option value="20180709-20181031">2018/19 Season 1 (Celestial Storm)</option>
												<option value="20181101-20190131">2018/19 Season 2 (Lost Thunder)</option>
												<option value="20190201-20190430">2018/19 Season 3 (Team Up)</option>
												<option value="20190501-20190623">2018/19 Season 4 (Unbroken Bonds)</option>
											</optgroup>
											<optgroup label="2018/19 VGC Series">
												<option value="20180904-20190107">2018/19 Sun Series</option>
												<option value="20190108-20190401">2018/19 Moon Series</option>
												<option value="20190402-20190623">2018/19 Ultra Series</option>
											</optgroup>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
						
				<div class="card my-3 border-dark">
					<div class="card-header">
						<h5 class="mb-0">
							<a data-toggle="collapse" class="collapsed" href="#collapseOptions" aria-expanded="false" aria-controls="collapseOptions">
								<i class="fas fa-feather-alt fa-1x"></i> Other Filters
								<button type="button" class="close">
									<span aria-hidden="true"><i class="fas fa-bars fa-1x"></i></span>
								</button>
							</a>
						</h5>
					</div>
					<div id="collapseOptions" class="collapse" role="tabpanel" aria-labelledby="collapseOptions">
						<div class="card-body">
							<p>
								These toggles allow you to filter your results to specific groups or show events that might
								otherwise be hidden. Essentially if it doesn't belong to one of the above categories, it'll
								be here.
							</p>
							<div class="row">
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="premierOnly" name="premierOnly">
											<label class="form-check-label" for="premierOnly">Only Show Premier Events</label>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="excludePremier" name="excludePremier">
											<label class="form-check-label" for="excludePremier">Exclude Premier Events</label>
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="form-group">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="showDeleted" name="showDeleted">
											<label class="form-check-label" for="showDeleted">Show Cancelled Events</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
<?		 echo outputFooter(); ?>
	</div>
	<div class="modal" id="locationSelect" tabindex="-1" role="dialog" aria-labelledby="locationSelectLabel" aria-hidden="true">
		<div class="modal-dialog modal-full modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="locationSelectLabel">Select your starting point</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="geocoder" class="geocoder"></div>
					<div id="map"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="acceptLocation" data-dismiss="modal" >Use This Location</button>
				</div>
			</div>
		</div>
	</div>
</body>

<script>
$(document).ready(function() {
	// Enable Select2 on all our select boxes
	var defaultOptions = {
		placeholder: '',
		allowClear: true,
		closeOnSelect: true
	};
	
    $("select#countryName").select2(defaultOptions);
    $("select#provinceState").select2(defaultOptions);
    $("select#postalZipCode").select2(defaultOptions);
    $("select#premierEvent").select2(defaultOptions);
    $("select#premierGroup").select2(defaultOptions);
    $("select#product").select2(defaultOptions);
    $("select#category").select2(defaultOptions);
    
    // Enable Bootstrap-Datepicker on the start and end date pickers
    $("#startDate").datepicker();
    $("#endDate").datepicker();
    
    // Default the start date to today's date
    $("#startDate").datepicker('update', new Date());

	// Keep a copy of the province list in memory and empty the select box
    provinceStateList = $("select#provinceState optgroup").detach();
    
    // Keep a copy of the postal/zip code list in memory and empty the select box
    postalZipCodeList = $("select#postalZipCode optgroup").detach();
    
    // When we select a single country, we'll populate the province list with the appropriate
    // values for that country, however if no country is selected, or alternatively more than
    // one is selected, we don't want the user to have the ability to select this.
    $("select#countryName").change(function() {
	    var countryNames = $("select#countryName").val();
	    
	    if ( countryNames == "" || $("select#countryName").val().length > 1 ) {
		    $("select#provinceState optgroup").detach();
		    $("select#provinceState").val(null).trigger("change");

		    $("select#postalZipCode optgroup").detach();
		    $("select#postalZipCode").val(null).trigger("change");
	    } else {
		    provinceStateList.each(function() {
			    if ( countryNames.indexOf($(this).attr("label")) > -1 ) {
				    $("select#provinceState").append($(this));
			    }
		    });
			
		    postalZipCodeList.each(function() {
			    if ( countryNames.indexOf($(this).attr("label").split("/")[0]) > -1 ) {
				    $("select#postalZipCode").append($(this));
			    }
		    });
		}
    });

    $("select#provinceState").change(function() {
	    var countryNames = $("select#countryName").val();
	    var provinceNames = $("select#provinceState").val();
	    
	    $("select#postalZipCode optgroup").detach();

	    postalZipCodeList.each(function() {
		    for ( countryName in countryNames ) {
			    if ( provinceNames.length == 0 ) {
				    if ( countryNames[countryName] == $(this).attr("label").split("/")[0] ) {
					    $("select#postalZipCode").append($(this));
				    }					    
			    } else {
				    for ( provinceName in provinceNames ) {
					    if ( $(this).attr("label") == (countryNames[countryName] + "/" + provinceNames[provinceName]) ) {
						    $("select#postalZipCode").append($(this));
					    }
					}
				}
		    }
	    });
    });
    
    // Allow predetermined date ranges to be selected.
    $("#specificDateRange").change(function() {
	    var dateRange = $("#specificDateRange").val().split("-");
	    $("#startDate").datepicker('update',
			new Date(parseInt(dateRange[0].substr(0, 4), 10), parseInt(dateRange[0].substr(4, 2)) - 1, parseInt(dateRange[0].substr(6, 2)))
		);
	    $("#endDate").datepicker('update',
			new Date(parseInt(dateRange[1].substr(0, 4), 10), parseInt(dateRange[1].substr(4, 2)) - 1, parseInt(dateRange[1].substr(6, 2)))
		);
    });
    
<?	if ( isset($filter["countryName"]) ) { ?>
	$("select#countryName").val(['<? echo implode("','", $filter["countryName"]); ?>']);
	$("select#countryName").trigger("change");
<?	} ?>
<?	if ( isset($filter["provinceState"]) ) { ?>
	$("select#provinceState").val(['<? echo implode("','", $filter["provinceState"]); ?>']);
	$("select#provinceState").trigger("change");
<?	} ?>
<?	if ( isset($filter["postalZipCode"]) ) { ?>
	$("select#postalZipCode").val(['<? echo implode("','", $filter["postalZipCode"]); ?>']);
	$("select#postalZipCode").trigger("change");
<?	} ?>
<?	if ( isset($filter["premierEvent"]) ) { ?>
	$("select#premierEvent").val(['<? echo implode("','", $filter["premierEvent"]); ?>']);
	$("select#premierEvent").trigger("change");
<?	} ?>
<?	if ( isset($filter["premierGroup"]) ) { ?>
	$("select#premierGroup").val(['<? echo implode("','", $filter["premierGroup"]); ?>']);
	$("select#premierGroup").trigger("change");
<?	} ?>
<?	if ( isset($filter["product"]) ) { ?>
	$("select#product").val(['<? echo implode("','", $filter["product"]); ?>']);
	$("select#product").trigger("change");
<?	} ?>
<?	if ( isset($filter["category"]) ) { ?>
	$("select#category").val(['<? echo implode("','", $filter["category"]); ?>']);
	$("select#category").trigger("change");
<?	} ?>
<?	if ( isset($filter["startDate"]) ) { ?>
	$("#startDate").val(['<? echo $filter["startDate"]; ?>']);
<?	} ?>
<?	if ( isset($filter["endDate"]) ) { ?>
	$("#endDate").val(['<? echo $filter["endDate"]; ?>']);
<?	} ?>
<?	if ( isset($filter["latitude"]) ) { ?>
	$("#latitude").val(['<? echo $filter["latitude"]; ?>']);
<?	} ?>
<?	if ( isset($filter["longitude"]) ) { ?>
	$("#longitude").val(['<? echo $filter["longitude"]; ?>']);
<?	} ?>
<?	if ( isset($filter["radius"]) ) { ?>
	$("#radius").val(['<? echo $filter["radius"]; ?>']);
<?	} ?>
<?	if ( isset($filter["premierOnly"]) ) { ?>
	$("#premierOnly").prop("checked", true);
<?	} ?>
<?	if ( isset($filter["excludePremier"]) ) { ?>
	$("#excludePremier").prop("checked", true);
<?	} ?>
<?	if ( isset($filter["showDeleted"]) ) { ?>
	$("#showDeleted").prop("checked", true);
<?	} ?>
<?	if ( isset($filter["useMiles"]) ) { ?>
	$("#useMiles").prop("checked", true);
<?	} ?>
});

var initMapDiv = false;

function loadLocationPicker() {
	$("#locationSelect").on('shown.bs.modal', function() {
		if ( ! initMapDiv ) {
			mapboxgl.accessToken = '<? echo MAPBOX_API_KEY; ?>';
		
			var map = new mapboxgl.Map({
				container: 'map',
				style: 'mapbox://styles/mapbox/streets-v11',
				center: [-79.4512, 43.6568],
				zoom: 13
			});
			 
			var geocoder = new MapboxGeocoder({
				accessToken: mapboxgl.accessToken,
				mapboxgl: mapboxgl
			});
			
			document.getElementById('geocoder').appendChild(geocoder.onAdd(map));
			
			$("#acceptLocation").click(function() {
				var latLng = null;
				
				if ( geocoder.mapMarker == undefined ) {
					$("#latitude").val("");
					$("#longitude").val("");
				} else {
					latLng = geocoder.mapMarker.getLngLat();
					$("#latitude").val(latLng.lat);
					$("#longitude").val(latLng.lng);
				}
			});
			
			initMapDiv = true;
		}
	});

	$("#locationSelect").modal("show");
}
</script>

</html>