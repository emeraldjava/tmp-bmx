jQuery( document ).ready(function( $ ){

    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;


    /**
     * Gets the google map and determines directions from the
     * global _user object.
     */
    function get_google_map( map_target ){

        directionsDisplay = new google.maps.DirectionsRenderer();

        var myOptions = {
            center: new google.maps.LatLng( _user.lat, _user.lon ),
            disableDefaultUI: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoom: 5
        };

        var map = new google.maps.Map( map_target, myOptions );
        directionsDisplay.setMap(map);
        directionsDisplay.setPanel(document.getElementById("directionsPanel"));
    }


    /**
     * Calucaltes the route based on the global _user object
     * along with the track city and state from the DOM.
     *
     * Ref: https://developers.google.com/maps/documentation/javascript/directions
     */
    function calcRoute() {

        city = $('#track_city').html();
        region = $('#track_region').html();

        var start = '"'+_user.city +','+ _user.region+'"';
        var end = '"'+city+','+region+'"';

        var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING
        };

        directionsService.route(request, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
            }
        });
    }


    /**
     * Run some stuff when the page is loaded.
     */
    $( window ).load(function(){

        /**
         * If we have the #bmx_rs_map_target on this page
         * run the below code on page load.
         */
        if ( $( '#bmx_rs_map_target' ).length != 0 ) {
            var params = {
                "action" : "zm_load_template",
                "target_div": "#bmx_rs_map_target",
                "template": $( '#bmx_rs_map_handle' ).attr( 'data-template' ),
                "post_id": $( '#bmx_rs_map_handle' ).attr('data-post_id')
            };

            $.ajax({
                data: params,
                success: function( msg ){
                    $( params.target_div ).fadeIn().html( msg );
                    $('#_user_city_target').html( _user.city );
                    $('#_user_region_target').html( _user.region );

                    get_google_map( document.getElementById('mini_map_target') );
                    calcRoute();

                    $('#directionsPanel').on({
                        mouseenter: function(){
                            $( this ).stop().animate({
                                width: "400px",
                                height: ($('.adp-directions').height() + 180) + "px"
                            });
                        },
                        mouseleave: function(){
                            $( this ).stop().animate({
                                width: "215px",
                                height: "240px"
                            });
                        }
                    });
                    $('body').click(function(){
                        $('#directionsPanel').stop().animate({
                            width: "215px",
                            height: "240px"
                        });
                    });
                }
            });
        } // End 'if lenght'
    });
});