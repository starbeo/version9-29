function initMap() {
    var mapOptions = {
        zoom: 7, // Zoom du 0 à 21
        center: new google.maps.LatLng(33, -7), // centre de la carte
        mapTypeId: google.maps.MapTypeId.ROADMAP // ROADMAP, SATELLITE, HYBRID, TERRAIN
    };
    var map = new google.maps.Map(document.getElementById("map"), mapOptions);

    var livreurId = $('input[id="livreurid"]').val();
    var url = admin_url + "staff/get_livreurs";
    if ($.isNumeric(livreurId)) {
        url += '/' + livreurId;
    }
    var imgMarker = site_url + 'assets/images/defaults/marker.png';
    var livreurs = [];
    setTimeout(function(){ 
        $.post(url, function (response) {
            var data = $.parseJSON(response);
            if (data !== null && typeof (data) !== 'undefined' && data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    livreurs.push(['Livreur : <b>' + data[i].firstname + ' ' + data[i].lastname + '</b>', 'Téléphone : <b>' + data[i].phonenumber + '</b>', data[i].latitude, data[i].longitude, imgMarker]);
                }
                setMarkers(map, livreurs);
            }
        });
    }, 5000);
}

function setMarkers(map, locations) {
    for (var i = 0; i < locations.length; i++) {
        var livreurs = locations[i];
        var myLatLng = new google.maps.LatLng(livreurs[2], livreurs[3]);
        var infoWindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: livreurs[4],
            animation: google.maps.Animation.DROP
        });
        (function (i) {
            google.maps.event.addListener(marker, "click", function () {
                var livreurs = locations[i];
                infoWindow.close();
                infoWindow.setContent("<div>" + livreurs[0] + "<br />" + livreurs[1] + "</div>");
                infoWindow.open(map, this);
            });
        })(i);
    }
}