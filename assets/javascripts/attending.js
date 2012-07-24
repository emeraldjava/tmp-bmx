/***
 * Handle the Ajax, DOM manipulation and other JS functionality
 * for the attending.
 *
 * @requires: jquery, script.js
 */
 jQuery(document).ready(function( $ ){

    /**
     * Call global function and load utility template
     */
    function load_attending(){
        temp_load({
            "target_div": "#bmx_rs_attending_target",
            "template": $( '#bmx_rs_attending_handle' ).attr( 'data-template' ),
            "post_id": $( '#bmx_rs_attending_handle' ).attr('data-post_id')
        });
    }


    /**
     * Highlights the count box next to the users Avatar when the
     * user adds or removes a Race Event from their Schedule.
     *
     * Yes, this is not a "live" count, but works for now.
     */
    function highlight_count_box( book ) {
console.log( book );
        current = Number( $('#schedule_target').html() );
        if ( book == 'add' ) {
            total = current + 1;
        }

        if ( book == 'remove' ) {
            total =  ( current - 1 );
        }

        $('#schedule_target').html( total );
        $('#schedule_target').parent().parent().effect("highlight");
        $('#schedule_target').effect("bounce");
    }


    /**
     * Run thigns when the page is loaded.
     */
    $( window ).load(function(){
        if ( $( '#bmx_rs_attending_handle' ).length ) {
            load_attending();
        }
    });


    /**
     * When the user adds or removes the Event we call the needed
     * method and adjus the markup as needed.
     *
     * @param data-post_id post_id (int)
     * @param data-current_user_id user_id (int)
     * @param data-action add/remove (str)
     *
     */
    $( '.yes_no_handle' ).live('change', function( event ){

        var _action = $( this ).attr( "data-action" );
        var _this = $( this );

        if ( $( this ).prop("checked") ) {
            change_to = 'remove';
            label = "Remove";

            $( this ).attr("title", label);
        } else if ( _action == 'remove' ) {
            change_to = 'add';
            label = "Add";

            $( this ).attr("title", label );
            // $( this ).closest('tr').fadeOut();

        } else {
            return;
        }

console.log( $(this) );

        $.ajax({
            data: {
                "action": "bmx_rs_add_remove_attendees",
                "post_id": $( this ).attr( "data-post_id" ),
                "current_user_id": $( this ).attr( "data-current_user_id" ),
                "event_action": _action,
                "cache_buster": ~~(Math.random() * 1000000)
            },
            dataType: "json",
            global: false,
            success: function( msg ) {
console.log( 'hi' );
console.log( msg );
                if ( msg.status == 7 ) {
                    $('#zm_register_dialog').dialog('open');
                    $('#zm_register_dialog .zm-register-status-target').html( msg.description );
                } else {
                    highlight_count_box( _action );

                    $( _this ).attr('data-action', change_to );

                    if ( typeof msg == "object" ) {
                        _user.attending = msg;
                    }
                }
            }
        });
    });
});