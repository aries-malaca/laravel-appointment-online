function initMap(lat,long){
    var latlng = new google.maps.LatLng(lat, long);

    var map = new google.maps.Map(document.getElementById("map-canvas"), {
        center: latlng,
        zoom: 15,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    var marker = new google.maps.Marker({
        position: latlng,
        draggable: true,
    });

// To add the marker to the map, call setMap();
    marker.setMap(map);
//activate bounds for multiple markers
    /*
     var defaultBounds = new google.maps.LatLngBounds(
     new google.maps.LatLng(<?php echo $latlng;?>),
     new google.maps.LatLng(<?php echo $latlng;?>));
     map.fitBounds(defaultBounds);*/
// Create the search box and link it to the UI element.
// [START region_getplaces]
// Listen for the event fired when the user selects an item from the
// pick list. Retrieve the matching places for that item.
    google.maps.event.addListener(marker, 'dragend', function(a) {
        document.getElementById("position").value = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4);
    });
    google.maps.event.addListener(marker, 'click', function(a) {
        document.getElementById("position").value = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4);
    });
// [END region_getplaces]
}


// This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

var placeSearch, autocomplete;

function initAutocomplete() {
    // Create the autocomplete object, restricting the search to geographical
    // location types.
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
        {types: ['geocode']});
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
    console.log('test');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
        });
    }
}