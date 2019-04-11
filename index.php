<?php

include_once("resources/php/helpers.php");

$countryNames = getDistinctList("countryName");
$countryProvinceStates = getProvinceList();
$premierEvents = getDistinctList("premierEvent");
$premierGroups = getDistinctList("premierGroup");
$products = getDistinctList("product");
$categories = getDistinctList("category");

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
	
	<!-- Select2 CDN -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	
	<!-- Bootstrap-Datepicker CDN -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
</head>

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
				</div>
			</div>
		</div>
		
		<form action="/display.php">
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
							</div>
						</div>
					</div>
				</div>
			
				<div class="card-footer text-center">
					<input type="submit" value="Get My Calendar" />
				</div>
			</div>
		</form>
		
		<div class="text-center text-light small">
			Developed by <a href="https://www.codearoundcorners.com/">Tim Crockford</a><span class="d-none d-sm-inline"> - </span>
			<br class="d-block d-sm-none" />
			Source Code available on <a href="https://github.com/timcrockford/pokemon-event-locator">GitHub</a>
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
    
});
</script>

</html>