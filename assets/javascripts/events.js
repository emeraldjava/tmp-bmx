/**
 * Functionality for submitting a new BMX Race Eevent.
 *
 * This file will set up the delete and create dialogs,
 * provide Clear and Exit functions, make sure Title is
 * not empty, and submit the form via Ajax.
 *
 * @requires script.js
 */
jQuery( document ).ready(function( $ ){
    function dateTimePicker(){
        $('.datetime-picker-start').datetimepicker({
            hourMin: 7,
            hourMax: 24,
            dateFormat: "yy-mm-dd",
            stepMinute: 10,
            ampm: true,
            onClose: function(dateText, inst) {
                var endDateTextBox = $('.datetime-picker-end');
                if (endDateTextBox.val() != '') {
                    var testStartDate = new Date(dateText);
                    var testEndDate = new Date(endDateTextBox.val());
                    if (testStartDate > testEndDate)
                        endDateTextBox.val(dateText);
                }
                else {
                    endDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime){
                var start = $(this).datetimepicker('getDate');
                $('.datetime-picker-end').datetimepicker('option', 'minDate', new Date(start.getTime()));
            }
        });

        $('.datetime-picker-end').datetimepicker({
            hourMin: 7,
            hourMax: 24,
            dateFormat: "yy-mm-dd",
            stepMinute: 10,
            ampm: true,
            onClose: function(dateText, inst) {
                var startDateTextBox = $('.datetime-picker-start');
                if (startDateTextBox.val() != '') {
                    var testStartDate = new Date(startDateTextBox.val());
                    var testEndDate = new Date(dateText);
                    if (testStartDate > testEndDate)
                        startDateTextBox.val(dateText);
                }
                else {
                    startDateTextBox.val(dateText);
                }
            },
            onSelect: function (selectedDateTime){
                var end = $(this).datetimepicker('getDate');
                $('.datetime-picker-start').datetimepicker('option', 'maxDate', new Date(end.getTime()) );
            }
        });
    }


    dialogs = {
        "create_ticket_dialog":  {
            autoOpen: false,
            minWidth: 600,
            maxWidth: 600,
            minHeight: 450,
            modal: true,
            resizable: false
        },
        "delete_dialog": {
            resizable: false,
            autoOpen: false,
            title: 'Delete',
            minWidth: 266,
            modal: true,
            dialogClass: "confirmation-container",
            buttons: {
                "Yes": function() {
                    data = {
                        action: "postTypeDelete",
                        post_id: $( this ).attr( 'data-post_id' ),
                        security: $( this ).attr( 'data-security' )
                    };
                    var post_id = $( this ).attr( 'data-post_id');
                    $.ajax({
                        data: data,
                        success: function( msg ){
                            $( '.post-' + post_id ).fadeOut();
                        }
                    });
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        }
    };

    $( '#create_ticket_dialog, #delete_dialog' ).each(function() {
        $(this).dialog( dialogs[this.id] );
    });

    $( '.default_delete_handle' ).live( "click", function(){
        var post_id = $( this ).attr( 'data-post_id');
        $( "#delete_dialog" )
            .attr("data-post_id", $(this).attr("data-post_id"))
            .attr("data-security", $(this).attr("data-security"))
            .dialog('open');
    });

    $('#post_title').live('blur', function(){
        if ( $.trim( $(this).val() ) != '' ) {
            $( 'input[type="submit"], input[type="button"]' ).animate({ opacity: 1 }).removeAttr('disabled');
        } else {
            $( 'input[type="submit"], input[type="button"]').animate({ opacity: 0.5 }).attr('disabled','disabled');
        }
     });

    $( '#clear' ).live('click', function(){
        $('#default_message_target').empty();
        $( 'input[type="submit"], input[type="button"]').animate({ opacity: 0.5 }).attr('disabled','disabled');
        $(':input','#new_events')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');
    });

    $( '#create_ticket' ).click(function(){
        $('#create_ticket_dialog').dialog( 'open' );
        $.ajax({
            data: {
                "action": "zm_load_template",
                "target_div": "#bmx_rs_create_event_target",
                "template": $( this ).attr("data-template"),
                "post_type": $( this ).attr("data-post_type")
                },
            success: function( msg ){

                /**
                 * Since the form is loaded via ajax the js needed, needs
                 * to be init'd
                 */
                $( "#bmx_rs_create_event_target" ).fadeIn().html( msg );

                $(".chzn-select").chosen();

                $(".chzn-select-deselect").chosen({
                    allow_single_deselect: false
                });

                dateTimePicker();
                load_uploadify();
            },
            error: function( xhr ){
                console.log( params );
                console.log( 'XHR Error: ' + xhr );
            }
        });
    });

    /**
     * Exit our dialog box on click and reload our archive view
     * if it is present.
     */
    $( '#exit' ).live('click', function(){
        $('#create_ticket_dialog').dialog('close');
    });

    /**
     * Call global function and load utility template
     */
    function load_utility(){
        temp_load({
            "target_div": "#bmx_rs_utility_target",
            "template": $( '#bmx_rs_utility_handle' ).attr( 'data-template' ),
            "post_id": $( '#bmx_rs_utility_handle' ).attr( 'data-post_id' ),
            "post_type": $( '#bmx_rs_utility_handle' ).attr( 'data-post_type' )
        });
    }

    // Start directions
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

        load_utility();

        if ( $( '#new_events' ).length )
            dateTimePicker();

        if ( $( '#new_events' ).length && _user.ID == 0 ) {
            $('#bmx_rs_dialog').dialog('open');
            $.ajax({
                data: {
                    "action": "zm_load_template",
                    "target_div": "#bmx_rs_login_target",
                    "template": $( '.login-handle' ).attr("data-template")
                    },
                global: false,
                success: function( msg ){
                    $( "#bmx_rs_login_target" ).fadeIn().html( msg ); // Give a smooth fade in effect
                }
            });
        }

        /**
         * If we have the #bmx_rs_map_target on this page
         * run the below code on page load.
         */
        if ( $( '#bmx_rs_map_target' ).length != 0 ) {
            var params = {
                "action" : "zm_load_template",
                "target_div": "#bmx_rs_map_target",
                "template": $( '#bmx_rs_map_handle' ).attr( 'data-template' ),
                "post_id": $( '#bmx_rs_map_handle' ).attr('data-post_id'),
                "post_type": $( '#bmx_rs_map_handle' ).attr('data-post_type')
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
        } // End 'if length'
    });

    /**
     * Populate content for default /events page
     */
    if ( $('.event_archive_target').length ) {
        $( document ).bind( 'feedLoaded-events', function() {

            var state = _user.region_full;

            /**
             * Query the feed for the ALL events in users current state
             */
            var result_id_local = feeds.doSearch( 'events', '+"'+state +'"',{showCurrent: true, showPast: false});
            var result_id_national = feeds.doSearch( 'events', 'national',{showCurrent: true, showPast: false});

            /**
             * Define our data object
             */
            var results_data_object = {
                locals:[],
                nationals:[],
                count_all_locals: function(){ return this.locals.length; },
                count_nationals: function(){ return this.nationals.length; }
            };

            var item;

            /**
             * Local Match
             * IDs with results
             */
            for ( var i in result_id_local.results ) {

                item = feeds.data['events'][result_id_local.results[i][0]];

                results_data_object.locals.push({
                    id: item.ID,
                    url: item.u,
                    title: item.t,
                    city: item.c,
                    state: item.s,
                    track: item.tr,
                    date: item.dateHTML,
                    map_small: item.s_u
                });
            }

            /**
             * National Match
             * IDs with results
             */
            for ( var i in result_id_national.results ) {

                item = feeds.data['events'][result_id_national.results[i][0]];

                results_data_object.nationals.push({
                    id: item.ID,
                    title: item.t,
                    url: item.u,
                    city: item.c,
                    state: item.s,
                    track: item.tr,
                    date: item.dateHTML
                });
            }

            /**
             * Call template
             */
            $( '.event_archive_target' ).fadeIn().html( ich.events_archive( results_data_object ) );
            $( ".tabs-handle" ).tabs();
        }); // End 'feedLoaded'
    }


    /**
     * Attach the tinymce to our textarea
     *
     * Only if the jQuery plugin is loaded and a user is logged in.
     */
    if ( jQuery().tinymce && _user.ID != 0 ) {
        $('#tinymce_textarea').tinymce({

            // Location of TinyMCE script
            script_url : _vendor_url + '/tinymce/tiny_mce.js',

            // General options
            theme: "advanced",

            // Buttons
            theme_advanced_buttons1 : "bold, italic, strikethrough, |, bullist, numlist,|, link, |,code",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align: "center",
            theme_advanced_layout_manager: "SimpleLayout",
            theme_advanced_resizing : true,
            theme_advanced_resize_horizontal : false,
            height: "300",
            template_replace_values : {
                username : _user.name,
                staffid : _user.ID
            }
        });
    }


}); // End 'doc ready'