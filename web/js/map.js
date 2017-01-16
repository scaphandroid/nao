var map, zoom, maxZoom;
var markersArray = [];
var infowindow = null;
var infowindowArray = [];
/* Le zoom dépend du type de compte*/
zoom = (window.innerWidth <= 800 && window.innerHeight <= 600) ? 5 : 6;

/* Affichage sur la page accueil*/
function initMapHome() {
    maxZoom = (($("#typeCompte").text()) > 0) ? null : 8;
    afficherCartePictos(maxZoom, false); /* On désactive le zoom lors du scroll sur la page d'accueil*/
}
/* Affichage sur la page rechercher*/
function initMapRechercher() {
    maxZoom = (($("#typeCompte").text()) > 0) ? null : 8;
    afficherCartePictos(maxZoom, true);
}
/* Affichage sur la page mes observations de Profile*/
function initMapProfile() {
    maxZoom = null;
    afficherCartePictos(maxZoom, true);
}
function afficherCartePictos(maxZoom, scrollwheel) {
/*    detectBrowser();*/
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 46.764548, lng: 1.718674999999962}, // coordonnées du centre de la F
        zoom: zoom,
        scrollwheel: scrollwheel,
        maxZoom: maxZoom //null si particulier
    });
    var observation = $("#observation").text();
    var observation_decode = $.parseJSON(observation);
    for (var i=0; i<observation_decode.length; i++) {
        var position = {lat: observation_decode[i].lat, lng: observation_decode[i].lon};
        var marker = new google.maps.Marker({
            position: position,
            map:map,
            title: observation_decode[i].espece
        });
        var class_title = observation_decode[i].valide ? "title_valide" : "title_invalide";

        var content = '<div id="iw-container">' +
            '<div class="iw-title text-center ' + class_title + '">' + observation_decode[i].espece + '</div>' + '<hr>' +
            '<div class="iw-content row">' +
                '<div class="col-xs-5">' +
                    '<img src="' + observation_decode[i].photoObs + '" alt="' + observation_decode[i].espece +'" width="100%" height="auto">'+
                '</div>'+
                '<div class="col-xs-7 police_infowindow">' +
                    '<p>Observé par ' + observation_decode[i].username + '</p>' +
                    '<p>le ' + observation_decode[i].date + '</p>'+
                '</div>'+
            '</div>';

        infowindow = new google.maps.InfoWindow({
            content: content,
            maxWidth : 300
        });

        google.maps.event.addListener(marker,'click', (function(marker,content, infowindow){
            return function() {
                closeAllInfoWindows();
                infowindow.setContent(content);
                infowindow.open(map,marker);
                infowindowArray.push(infowindow);
                google.maps.event.addListener(map, 'click', function() {
                    infowindow.close();
                });
            };
        })(marker,content,infowindow));
    }
}

function closeAllInfoWindows() {
    if (infowindowArray) {
        for (var i = 0; i < infowindowArray.length; i++) {
            infowindowArray[i].close();
        }
        infowindowArray.length = 0;
    }
}


function detectBrowser() {
    var useragent = navigator.userAgent;
    var mapdiv = document.getElementById("map");

    if (useragent.indexOf('iPhone') != -1 || useragent.indexOf('Android') != -1 ) {
        mapdiv.style.width = '100%';
        mapdiv.style.height = '100%';
    }
}

function observerMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 46.764548, lng: 1.718674999999962}, // coordonnées du centre de la F
        zoom: zoom,
        scrollwheel: false
    });
    var localise = document.getElementById('nao_platformbundle_observation_localise');
    if(localise.checked) {
        geolocation();
    }
    else {// on rend la carte cliquable pour que l'utilisateur puisse saisir son lieu d'observation
        map.addListener('click', function(event) {
            document.getElementById("nao_platformbundle_observation_localise").checked = false;
            placeMarker(event.latLng);
            document.getElementById("nao_platformbundle_observation_lat").value = event.latLng.lat();
            document.getElementById("nao_platformbundle_observation_lon").value = event.latLng.lng();
        });
    }
    $(localise).on( 'click', function() {
        if(localise.checked) { // si la case "je suis sur place" est cochée
            geolocation();
        }
        else {
            map.addListener('click', function(event) {
                document.getElementById("nao_platformbundle_observation_localise").checked = false;
                placeMarker(event.latLng);
                // Ajout des coordonnées dans les champs cachés
                document.getElementById("nao_platformbundle_observation_lat").value = event.latLng.lat();
                document.getElementById("nao_platformbundle_observation_lon").value = event.latLng.lng();
            });
        }
    });
}

function geolocation() {
    // HTML5 geolocation.
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            placeMarker(pos);
            // Ajout des coordonnées dans les champs cachés
            document.getElementById("nao_platformbundle_observation_lat").value = pos.lat;
            document.getElementById("nao_platformbundle_observation_lon").value = pos.lng;
        }, function () {
            handleLocationError(true, infoWindow, map.getCenter());
        });
    } else {
        // Browser doesn't support Geolocation
        handleLocationError(false, infoWindow, map.getCenter());
    }
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.');
}

function placeMarker(location) {
    console.log(location);
    deleteMarkers();
    var marker = new google.maps.Marker({
        position: location,
        map: map
    });
    // ajouter le marqueur dans le tableau
    markersArray.push(marker);
 /*   map.setCenter(location);*/

}

function deleteMarkers() {
    // Supprimer tous les markers
    if (markersArray) {
        for (i in markersArray) {
            markersArray[i].setMap(null);
        }
        markersArray.length = 0;
    }
}


