function initMap() {
    var mapOptions = {
        zoom: 7, // Zoom du 0 à 21
        center: new google.maps.LatLng(33, -7), // centre de la carte
        mapTypeId: google.maps.MapTypeId.ROADMAP // ROADMAP, SATELLITE, HYBRID, TERRAIN
    };
    var map = new google.maps.Map(document.getElementById("map"), mapOptions);

    var pointRelaiId = $('input[id="point_relai_id"]').val();
    var url = admin_url + "points_relais/get";
    if ($.isNumeric(pointRelaiId)) {
        url += '/' + pointRelaiId;
    }
    var imgMarker = site_url + 'assets/images/defaults/marker.png';
    var locations = [];
    setTimeout(function(){ 
        $.post(url, function (response) {
            var data = $.parseJSON(response);
            if (data !== null && typeof (data) !== 'undefined' && data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    locations.push(['Point Relai : <b>' + data[i].nom + '</b>', 'Adresse : <b>' + data[i].adresse + '</b>', data[i].latitude, data[i].longitude, imgMarker]);
                }
                setMarkers(map, locations);
            }
        });
    }, 5000);
}

function setMarkers(map, locations) {
    for (var i = 0; i < locations.length; i++) {
        var location = locations[i];
        var myLatLng = new google.maps.LatLng(location[2], location[3]);
        var infoWindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: location[4],
            animation: google.maps.Animation.DROP
        });
        (function (i) {
            google.maps.event.addListener(marker, "click", function () {
                var location = locations[i];
                infoWindow.close();
                infoWindow.setContent("<div>" + location[0] + "<br />" + location[1] + "</div>");
                infoWindow.open(map, this);
            });
        })(i);
    }
}