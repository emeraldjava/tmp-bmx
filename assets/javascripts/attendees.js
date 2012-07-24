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
     * method and adjust the markup as needed.
     *
     * @param data-post_id post_id (int)
     * @param data-current_user_id user_id (int)
     * @param data-action add/remove (str)
     *
     */
    function attendingEvent( ){

        var _this = $( this );
        var _action = _this.attr( "data-action" );

        if ( _action == 'add' ) {
             change_to = 'remove';
            label = "Remove";

            _this.attr("title", label);
        } else if ( _action == 'remove' ) {
            change_to = 'add';
            label = "Add";

            _this.attr("title", label );

            if ( $('.attendee-dashboard-container').attr('data-owner_id') == _user.fb_id ) {
                _this.closest('tr').fadeOut();
            }

        } else {
            return;
        }

        $.ajax({
            data: {
                "action": "bmx_rs_add_remove_attendees",
                "post_id": _this.attr( "data-post_id" ),
                "current_user_id": _user.ID,
                "event_action": _action,
                "cache_buster": ~~(Math.random() * 1000000)
            },
            dataType: "json",
            global: false,
            success: function( msg ) {
                if ( msg.status == 7 ) {
                    $('#zm_register_dialog').dialog('open');
                    $('#zm_register_dialog .zm-register-status-target').html( msg.description );
                } else {
                    highlight_count_box( _action );
                    _this.attr('data-action', change_to );
                    if(change_to == 'add') {
                        _this.removeClass('event-added');
                    } else {
                        _this.addClass('event-added');
                    }

                    if ( typeof msg == "object" ) {
                        _user.attending = msg;
                    }
                }
            }
        }); // End attendingEvent()
    }
    $( document ).delegate('.button-attend', 'click', attendingEvent);
});