<html>
<head>
<title>Pokémon Event Locator/Subscription Tool</title>
	<meta name="viewport" content="width=device-width, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
	<link href="resources/css/pokecal.css" rel="stylesheet" />

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
</head>

<body>
<?php

include_once("resources/php/helpers.php");

$countryNames = getDistinctList("countryName");
$countryProvinceStates = getProvinceList();
$premierEvents = getDistinctList("premierEvent");
$products = getDistinctList("product");
$categories = getDistinctList("category");

?>
<div class="container p-3">

<div class="card border-dark">
<div class="card-header text-light bg-danger">
<h1 class="d-none d-sm-none d-md-block">Pokémon Event Locator/Subscription Tool</h1>
<h4 class="d-block d-sm-block d-md-none"><center>Pokémon Event Locator/Subscription Tool</center></h4>
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
<?php

foreach($countryNames as $countryName) {
	echo "<option value='" . $countryName . "'>" . $countryName . "</option>";
}

?>
</select>
</div>
</div>

<div class="col-12 col-md-6 col-xl-4">
<div class="form-group">
<label for="provinceState"><b>Select Province/States:</b></label>
<select class="select2 form-control" id="provinceState" name="provinceState[]" multiple="multiple" width="100%">
<?php

foreach($countryProvinceStates as $countryName => $provinceStates) {
	echo "<optgroup label='" . $countryName . "'>";
	foreach($provinceStates as $provinceState) {
		echo "<option value='" . $provinceState . "'>" . $provinceState . "</option>";
	}
	echo "</optgroup>";
}

?>
</select>
</div>
</div>

<div class="col-12 col-md-6 col-xl-4">
<div class="form-group">
<label for="premierEvent"><b>Select Premier Event Type:</b></label>
<select class="select2 form-control" id="premierEvent" name="premierEvent[]" multiple="multiple" width="100%">
<?php

foreach($premierEvents as $premierEvent) {
	echo "<option value='" . $premierEvent . "'>" . $premierEvent . "</option>";
}

?>
</select>
</div>
</div>

<div class="col-12 col-md-6 col-xl-4">
<div class="form-group">
<label for="product"><b>Select Products:</b></label>
<select class="select2 form-control" id="product" name="product[]" multiple="multiple" width="100%">
<?php

foreach($products as $product) {
	echo "<option value='" . $product . "'>" . $product . "</option>";
}

?>
</select>
</div>
</div>

<div class="col-12 col-md-6 col-xl-4">
<div class="form-group">
<label for="category"><b>Select Categories:</b></label>
<select class="select2 form-control" id="category" name="category[]" multiple="multiple" width="100%">
<?php

foreach($categories as $category) {
	echo "<option value='" . $category . "'>" . $category . "</option>";
}

?>
</select>
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
</div>
</div>

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

</div>
</div>

<div class="card-footer">
<center>
<input type="submit" value="Get My Calendar" />
</center>
</div>

</form>

</div>
</body>

<script>
$(document).ready(function() {
    $("select#countryName").select2();
    $("select#provinceState").select2();
    $("select#premierEvent").select2();
    $("select#product").select2();
    $("select#category").select2();
    
    provinceStateList = $("select#provinceState optgroup").detach();
    
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
    
    $("#display").click(function() {
	    $("form").attr("action", "/display.php");
    });
    
    $("#startDate").datepicker({});

    $("#endDate").datepicker({});
});
</script>

</html>