<?php

include_once("resources/php/helpers.php");

$countryNames = getDistinctList("countryName");
$countryProvinceStates = getProvinceList();
$premierEvents = getDistinctList("premierEvent");
$premierGroups = getDistinctList("premierGroup");
$products = getDistinctList("product");
$categories = getDistinctList("category");

$filter = buildSearchFilter();

echo outputHtmlHeader(true, true, false, true);
?>
<body>
	<div class="container p-3">
		<div class="card border-dark">
			<div class="card-header text-light bg-danger">
				<h4 class="text-center text-md-left">Pokémon Event Locator/Subscription Tool</h4>
			</div>
			<div class="card-body">
				<div class="card-text">
					Find using the official Pokemon.com locator too difficult to use? Try this one instead! Not only can you search
					easily for events but you can also subscribe to the calendar, making it really easy to keep up to date with events
					in your area. Please not that local leagues are not currently supported. You can only filter on a state or province
					when you have selected a single country.
					<br /><br />
					If you want to filter locations to within a certain radius of a location, you can enter the GPS coordinated into
					the text boxes below, or click the "Search For Coordinates" option to look up an address.
				</div>
			</div>
		</div>
		
		<form action="/display.php" method="post">
			<div class="card my-3 border-dark">
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label for="countryName"><b>Select Countries:</b></label>
								<select class="select2 form-control" id="countryName" name="countryName[]" multiple="multiple" width="100%">
<?								foreach($countryNames as $countryName) { ?>
									<option value="<? echo $countryName; ?>"><? echo $countryName; ?></option>
<?								} ?>
								</select>
							</div>
						</div>

						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label for="provinceState"><b>Select Province/States:</b></label>
								<select class="select2 form-control" id="provinceState" name="provinceState[]" multiple="multiple" width="100%">
<?								foreach($countryProvinceStates as $countryName => $provinceStates) { ?>
									<optgroup label="<? echo $countryName; ?>">
<?									foreach($provinceStates as $provinceState) { ?>
										<option value="<? echo $provinceState; ?>"><? echo $provinceState; ?></option>
<?									} ?>
									</optgroup>
<?								} ?>
								</select>
							</div>
						</div>
				
						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label for="premierGroup"><b>Select Premier Event Group:</b></label>
								<select class="select2 form-control" id="premierGroup" name="premierGroup[]" multiple="multiple" width="100%">
<?								foreach($premierGroups as $premierGroup) { ?>
									<option value="<? echo $premierGroup; ?>"><? echo $premierGroup; ?></option>
<?								} ?>
								</select>
							</div>
						</div>
				
						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label for="premierEvent"><b>Select Premier Event Type:</b></label>
								<select class="select2 form-control" id="premierEvent" name="premierEvent[]" multiple="multiple" width="100%">
<?								foreach($premierEvents as $premierEvent) { ?>
									<option value="<? echo $premierEvent; ?>"><? echo $premierEvent; ?></option>
<?								} ?>
								</select>
							</div>
						</div>
				
						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label for="product"><b>Select Game Type:</b></label>
								<select class="select2 form-control" id="product" name="product[]" multiple="multiple" width="100%">
<?								foreach($products as $product) { ?>
									<option value="<? echo $product; ?>"><? echo $product; ?></option>
<?								} ?>
								</select>
							</div>
						</div>
				
						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label for="category"><b>Select Game Formats:</b></label>
								<select class="select2 form-control" id="category" name="category[]" multiple="multiple" width="100%">
<?								foreach($categories as $category) { ?>
									<option value="<? echo $category; ?>"><? echo $category; ?></option>;
<?								} ?>
								</select>
							</div>
						</div>
					</div>
					
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
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label for="startDate"><b>Select Start Date:</b></label>
								<input class="datepicker form-control" data-date-format="yyyy/mm/dd" id="startDate" name="startDate">
							</div>
						</div>
						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label for="endDate"><b>Select End Date:</b></label>
								<input class="datepicker form-control" data-date-format="yyyy/mm/dd" id="endDate" name="endDate">
							</div>
						</div>
						<div class="col-12 col-md-6 col-xl-4">
							<div class="form-group">
								<label><b>Select Options:</b></label>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="premierOnly" name="premierOnly">
									<label class="form-check-label" for="premierOnly">Only Show Premier Events</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="excludePremier" name="excludePremier">
									<label class="form-check-label" for="excludePremier">Exclude Premier Events</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="showDeleted" name="showDeleted">
									<label class="form-check-label" for="showDeleted">Show Cancelled Events</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="useMiles" name="useMiles">
									<label class="form-check-label" for="useMiles">Use Miles for Distance</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			
				<div class="card-footer text-center">
					<div class="row">
						<div class="col-12 col-md-4">
							<input type="submit" value="Get My Calendar" />
						</div>
						<div class="col-12 col-md-4">
							<a href="javascript:loadLocationPicker();">Search For Coordinates</a>
						</div>
						<div class="col-12 col-md-4">
							<a href="/index.php">Reset My Selections</a>
						</div>
					</div>
				</div>
			</div>
		</form>
		<? echo outputFooter(); ?>
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
    $("select#countryName").select2();
    $("select#provinceState").select2();
    $("select#premierEvent").select2();
    $("select#premierGroup").select2();
    $("select#product").select2();
    $("select#category").select2();
    
    // Enable Bootstrap-Datepicker on the start and end date pickers
    $("#startDate").datepicker();
    $("#endDate").datepicker();
    
    // Default the start date to today's date
    $("#startDate").datepicker('update', new Date());

	// Keep a copy of the province list in memory and empty the select box
    provinceStateList = $("select#provinceState optgroup").detach();
    
    // When we select a single country, we'll populate the province list with the appropriate
    // values for that country, however if no country is selected, or alternatively more than
    // one is selected, we don't want the user to have the ability to select this.
    $("select#countryName").change(function() {
	    var countryNames = $("select#countryName").val();
	    
	    provinceStateList.each(function() {
		    if ( countryNames == "" || $("select#countryName").val().length > 1 ) {
			    $("select#provinceState optgroup").detach();
		    } else {
			    if ( countryNames.indexOf($(this).attr("label")) > -1 ) {
				    $("select#provinceState").append($(this));
			    }
		    }
	    });
    });
    
<?	if ( isset($filter["countryName"]) ) { ?>
	$("select#countryName").val(['<? echo implode("','", $filter["countryName"]); ?>']);
	$("select#countryName").trigger("change");
<?	} ?>
<?	if ( isset($filter["provinceState"]) ) { ?>
	$("select#provinceState").val(['<? echo implode("','", $filter["provinceState"]); ?>']);
	$("select#provinceState").trigger("change");
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