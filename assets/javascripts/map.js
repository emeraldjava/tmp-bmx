/**
 * Data for the markers consisting of a name, a LatLng and a zIndex for
 * the order in which these markers should display on top of each
 * other.
 */
var markers = {"map":[]};
var map;
var bounds;
var infowindow = new google.maps.InfoWindow({
    size: new google.maps.Size(50,50)
});
var user_center;

jQuery( document ).ready(function( $ ){

    /**
     * Only load the map when the map target is present. Send an ajax request, * if we get a response back we then push everything we want in from our
     * feed into our marker array. Finally we initialize our Google Map
     */
    if ( $('#map_canvas').length ) {
        $.ajax({
            url: '/races/tracks.json',
            success: function( msg ){
                for ( var i in msg ) {
                    if ( msg[i].l && msg[i].lo ){
                        markers.map.push({
                            lat: msg[i].l,
                            lon: msg[i].lo,
                            title: msg[i].t,
                            url: msg[i].u,
                            city: msg[i].c,
                            state: msg[i].s,
                            event_count: msg[i].ec,
                            website: msg[i].w
                        });
                    }
                }
                initialize();
            }
        });
    }
});


/**
 * Set the map options: zoom level, center (global user object for lat/long),
 * and map type. Create the map object based on the options set and attach it
 * to the given DOM id.
 *
 * Finally we set our markers.
 */
function initialize() {

    user_center = new google.maps.LatLng( _user.lat, _user.lon );

    var myOptions = {
        zoom: 8,
        center: user_center,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map( document.getElementById("map_canvas"), myOptions);

    // google.maps.event.addListener( map, 'idle', function( event ){
    //     setMarkers( map, markers );
    // });

    setMarkers( map, markers );

    var circleCenter = {
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 1,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
        map: map,
        center: user_center,
        radius: 50000
    };

    userCircle = new google.maps.Circle( circleCenter );

}


/**
 * For each item in our markers object we build a new marker and attach an
 * info window to it.
 */
function setMarkers( map, locations ) {

    for ( var i = 0; i < locations.map.length; i++ ) {
        curLat = locations.map[i].lat;
        curLon = locations.map[i].lon;

        var myLatLng = new google.maps.LatLng( curLat, curLon );

        /**
         * Build our marker
         */
        var marker = new google.maps.Marker({
            position: myLatLng,
            map:   map,
            title: locations.map[i].title,
            url:   locations.map[i].url,
            city:  locations.map[i].city,
            state: locations.map[i].state,
            website: locations.map[i].website,
            event_count: locations.map[i].event_count,
            animation: google.maps.Animation.DROP
        });

        myInfoWindow( marker, map, infowindow );
    }
}

// function setMarkers(map, locations) {
//     var bounds = map.getBounds();
//     console.log( bounds );

//     for ( var i = 0; i < locations.map.length; i++ ) {

//         curLat = locations.map[i].lat;
//         curLon = locations.map[i].lon;

//         x1 = bounds.ba.b;
//         x2 = bounds.ca.b;
//         y1 = bounds.ba.j;
//         y2 = bounds.ca.j;

// console.log( 'x1' );
//         var myLatLng = new google.maps.LatLng( curLat, curLon );

//         if ( curLat < x2 && curLat > x1 && curLon < y2 && curLon > y1 ) {
//             console.log( 'show marker' );
//             // marker.setMap( map );
//         } else {
//             console.log( 'remove marker' );
//             // remove markers
//             // marker.setMap( null );
//         }

//         /**
//          * Build our marker
//          */
//         var marker = new google.maps.Marker({
//             position: myLatLng,
//             map:   map,
//             title: locations.map[i].title,
//             url:   locations.map[i].url,
//             city:  locations.map[i].city,
//             state: locations.map[i].state,
//             // zIndex: ,
//             animation: google.maps.Animation.DROP
//         });

//         /**
//          * Call our function passing
//          */
//         myInfoWindow( marker, map, infowindow );
//     }
// }

function myInfoWindow( marker, map, infowindow ){
    google.maps.event.addListener(marker, 'click', function() {

        html = '<h3>'+marker.title+'</h3>';
        html += '<p>'+marker.city+', '+marker.state+'</p><br />';

        html += '<ul class="inline">';
        html += '<li><a href="'+marker.url+'">More info</a><span class="bar">|</span></li>';
        html += '<li><a href="'+marker.website+'" target="_blank">Website</a><span class="bar">|</span></li>';
        html += '<li><a href="'+marker.url+'#events">Events</a><span class="count">' + marker.event_count + '</span></li>';
        html += '</ul>';

        infowindow.setContent( html );
        infowindow.open(map,marker);
        infowindow.setPosition( new google.maps.LatLng( _user.lat, _user.lon ) );
    });
}