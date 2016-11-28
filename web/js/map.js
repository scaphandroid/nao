var map;
/* Affichage sur la page d'accueil*/
function initMapIndex() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 46.764548, lng: 1.718674999999962}, // coordonnées du centre de la F
        zoom: 6
    });
    var observation = $("#observation").text();
    var observation_decode = $.parseJSON(observation);
    for (var i=0; i<observation_decode.length; i++) {
        var position = {lat: observation_decode[i].lat, lng: observation_decode[i].lon};
        var marker = new google.maps.Marker({
            position: position,
            map:map,
            title: observation_decode[i].espece,
        });
        var content = '<div id="iw-container">' +
            '<div class="iw-title">' + observation_decode[i].espece + '</div>' + '<hr>' +
            '<div class="iw-content row">' +
                '<div class="col-xs-5">' +
                    '<img src="../images/' + observation_decode[i].photoObs + '" alt="' + observation_decode[i].espece +'" height="auto" width="100">'+
                '</div>'+
                '<div class="col-xs-7">' +
                    '<p>Observé par ' + observation_decode[i].username + '</p>' +
                    '<p>le ' + observation_decode[i].date + '</p>'+
                '</div>'+
            '</div>';


        var infowindow = new google.maps.InfoWindow({
            content: content,
            maxWidth : 300
        });

        google.maps.event.addListener(marker,'click', (function(marker,content, infowindow){
            return function() {
                if (infowindow) {
                    infowindow.close();
                }
                infowindow.setContent(content);
                infowindow.open(map,marker);
                map.setCenter(marker.getPosition());
                google.maps.event.addListener(map, 'click', function() {
                    infowindow.close();
                });
            };
        })(marker,content,infowindow));

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


