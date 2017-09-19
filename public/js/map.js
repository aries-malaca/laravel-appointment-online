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