/**
 * Run jQuery in no-conflict mode but still have access to $
 */
var _plugindir = "theme/";

jQuery('.my-popover').live("mouseover mouseout", function(){
    jQuery('.my-popover').popover({
        html: true,
        placement: 'above'
    });
});

jQuery(document).ready(function( $ ){

    function showCurrentLocation(){
        $('._user-city-target').fadeIn().html( _user.city + ', ');
        $('._user-region-target').fadeIn().html( _user.region );
    }

    function showMessage( msg ) {

        if ( ! msg )
            return;

        jQuery('.zm-msg-target').toggleClass( msg.cssClass );
        jQuery('.zm-msg-target').fadeIn().html( msg.description ).delay(2000).fadeOut();
        // $('.zm-register-status-target').fadeIn().html( msg.description ).delay(1000).fadeOut();
    }

    /**
     * Server side email validation.
     * @uses lib/zm-wordpress-helpers/ validEmail()
     * @users showMessage()
     * @todo showMessage, validate username and this should be part
     * of an "js" object.
     */
    function zm_validate_email( myObj ){
        $this = myObj;

        if ( $.trim( $this.val() ) == '' ) return;

        $.ajax({
            data: "action=validEmail&email=" + $this.val(),
            dataType: 'json',
            global: false,
            success: function( msg ){
                showMessage( msg );
            }
        });
    }

    window.load_uploadify = function(){
        $('#file_upload').uploadify({
            'swf': _vendor_url + '/uploadify-v3.1/uploadify.swf',
            'uploader': '/wp-content/plugins/bmx-race-schedules/helpers/uploadify.php',
            'buttonClass': 'button',
            'buttonText': 'Browse...',
            'onUploadSuccess': function(file, data, response) {
                var obj = $.parseJSON( data );

                $( '#file_upload_action' ).val( obj.attachment_id );

                var thumb_image = '<img src="' + _site_url + '/wp-content/uploads' + obj.meta.zm_sizes.thumb + '" />';
                $('.tmp-image-target').fadeIn().html( thumb_image );
            }
        });
    }

    $(".tablesorter").tablesorter();

    $('a, button, .tool-tip').twipsy({
        html: true,
        live: true
    });

    if ( jQuery().chosen )
        $( "select" ).chosen();

    /**
     * Default ajax setup
     */
    $.ajaxSetup({
        type: "POST",
        url: ajaxurl
    });

    $('#zm_register_dialog').dialog({
        autoOpen: false,
        width: 265, // needed default is 300px
        resizable: false,
        modal: true,
        title: 'Register'
    });

    /**
     * @todo current using as a jQuery plugin, make up your mind
     * either use as jQuery plugin or wp API do_action()
     */
    if ( typeof _post_id !== "undefined" && $("#edit_title").length ) {
        $("#edit_title").inPlaceEdit({
                postId: _post_id,
                field: "title"
        });

        $("#edit_content").inPlaceEdit({
                postId: _post_id,
                field: "content"
        });

        $("#edit_excerpt").inPlaceEdit({
                postId: _post_id,
                field: "excerpt"
        });
    }

    /**
     * Load our plugins for DOM elements that are
     * loaded via ajax.
     */
    $('body').ajaxSuccess(function(){

        // $( "table" ).tablesorter();

        $('.modal-close').click(function(){
            $('.modal').modal('hide');
        });

        $('.zm-loading-icon').hide();

    }); // End 'ajaxSuccess'

    $('.zm-loading-icon').ajaxStart(function(){
        $(this).show();
    });

    // @todo templating still handled via php, consider js templating?
    window.temp_load = function( params, my_global ) {

        var my_global;
        var request_in_process = false;

        params.action = "zm_load_template";

        $.ajax({
            data: params,
            global: my_global,
            success: function( msg ){
                $( params.target_div ).fadeIn().html( msg );
                request_in_process = false;
                if (typeof params.callback === "function") {
                    params.callback();
                }
            },
            error: function( xhr ){
                console.log( params );
                console.log( 'XHR Error: ' + xhr );
            }
        });
    }

    $( window ).load(function(){

        $('#logo_target').fadeIn(1000);

        if ( $( '#bmx_rs_comment_handle' ).length ) {
            $( '#bmx_rs_comment_target .zm-loading-icon').show();
            temp_load({
                "target_div": "#bmx_rs_comment_target",
                "template": $( '#bmx_rs_comment_handle' ).attr( 'data-template' ),
                "post_id": $( '#bmx_rs_comment_handle' ).attr( 'data-post_id' )
            });
        }

        showCurrentLocation();

        /**
         * Handle file uploads using Uploadify plugin. On succesful
         * upload a post type of attachment is created and the attachment_id
         * is returned. Note that we update the 'file_upload_action' with
         * the attachment_id so we can relate the new "event" (post) with
         * the attachment.
         *
         * @todo use proxy or allow ajax requested using htaccess stuffy
         */
        if ( $('#file_upload').length ){
            load_uploadify();
        }
    }); // End 'window.load'

    /* @todo this needs to be tied down via a class? */
    $( '.zm-show-hide tr' ).live( "mouseover mouseout", function( event ){
        if ( event.type == "mouseover" ) {
            $(this).find('.utility-container').addClass( 'zm-base-visible').removeClass( 'zm-base-hidden');
        } else {
            $(this).find('.utility-container').addClass( 'zm-base-hidden wtf').removeClass( 'zm-base-visible');
        }
    });

    $( '#bmx_rs_filter_form input[type=checkbox], #bmx_rs_filter_form select' ).live( 'change', function(){
        build_filters( _form_selector='#bmx_rs_filter_form' );
    });

    /**
     * Slide toggle the items
     */
    $( '.auto-expando-handle').on( 'click', function( event ){
        event.preventDefault();
        $( this ).siblings('.auto-expando-target').slideToggle(100);
        $( this ).siblings( ".arrow" ).toggleClass("arrow-up");
    });

    $('.user-panel-handle').on('click', function( event ){
        event.preventDefault();
        $( '.mini-panel-container' ).show();
        $( this ).addClass('active');
        event.stopPropagation();
    });

    $('body').click(function(){
        // @todo needs 'top-bar-container' needs to be dynamic
        $('.top-bar-container .user-panel-handle').removeClass('active');
        $('.top-bar-container .mini-panel-container').hide();
    });

    /**
     * Submit new comment, note comments are loaded via ajax
     */
     $( '#default_add_comment_form' ).live( 'submit', function(){
        data = {
            action: "zm_ajax_add_comment",
            post_id: _post_id,
            comment: $( '#comment' ).val()
        };

        $.ajax({
            data: data,
            global: false,
            success: function( msg ){
                temp_load({
                    "target_div": "#bmx_rs_comment_target",
                    "template": $( '#bmx_rs_comment_handle' ).attr( 'data-template' ),
                    "post_id": $( '#bmx_rs_comment_handle' ).attr( 'data-post_id' )
                }, false );
            },
            error: function( xhr ) {
                console.log( 'XHR Error: ' + xhr );
            }
        });
    });

    /**
     * Allow Comment form to be submitted when the user
     * presses the "enter" key.
     */
    $('#default_add_comment_form textarea').live('keypress', function( event ){
        if ( event.keyCode == '13' ) {
            event.preventDefault();
            $('#default_add_comment_form').submit();
        }
    });

    $('.register-handle').live('click', function( event ){
        event.preventDefault();
        $('#zm_register_dialog').dialog('open');
        $('#zm_register_dialog .zm-register-status-target').html( $(this).attr('data-message'));
    });

    $('#zm_register_close').on('click', function(){
       $('#zm_register_dialog').dialog('close');
    });


    /**
     * @todo this needs to be part of the core zm-cpt
     */
    $( '#register_form' ).live('submit', function(){
        $.ajax({
            global: false,
            data: "action=zm_regsiter_submit&" + $( this ).serialize(),
            dataType: 'json',
            success: function( msg ) {
                showMessage( msg );
                if ( msg.status == 6 )
                    window.location.replace( window.location.pathname );
            }
        });
    });

    /**
     * @todo this needs to be part of the core zm-cpt
     * @todo general error repsone
     * success: function( msg ){ show_resposne( msg ); }
     */
    $( '#user_login' ).live('blur', function(){
        if ( $.trim( $(this).val() ) == '' ) return;

        $.ajax({
            data: "action=zm_valid_username&login=" + $( this ).val(),
            dataType: 'json',
            global: false,
            success: function( msg ){
                showMessage( msg );
            }
        });
    });

    $( '.zm-validate-email' ).live('blur', function(){
        zm_validate_email( $(this) );
    });

    /**
     * field one, field two
     */
    $('#user_confirm_password').live('blur', function(){
        if ( $.trim( $(this).val() ) == '' ) return;

        match_id = $( this ).attr( 'data-match_id' );
        match_value = $( match_id ).val();

        value = $( this ).val();
        register_button_id = $( this ).attr( 'data-register_button_id' );

        if ( value == match_value ) {
            $( register_button_id ).removeAttr('disabled');
            $( register_button_id ).animate({ opacity: 1 });
        } else {
            $( register_button_id ).attr('disabled','disabled');
            showMessage({
                "cssClass": "error",
                "description": "<div class='error-container'>Passwords do not match.</div>"
            });
        }
     });

    /**
     * Our element we are attaching the 'click' event to is loaded via ajax.
     */
    $('.fb-login').live( 'click', function( event ){

        /**
         * Doc code from FB, shows fb pop-up box
         *
         * @url https://developers.facebook.com/docs/reference/javascript/FB.login/
         */
        FB.login( function( response ) {

            /**
             * If we get a succesful authorization response we handle it
             * note the "scope" parameter.
             */
            if ( response.authResponse ) {

                /**
                 * "me" referes to the current FB user, console.log( response )
                 * for a full list.
                 */
                FB.api('/me', function(response) {
                    var fb_response = response;

                    /**
                     * Yes, bad, very bad!
                     */
                    email = response.email;
                    var user_name = email.split("@");
                    user_name = user_name[0];

                    /**
                     * Make an Ajax request to the "create_facebook_user" function
                     * passing the params: username, fb_id and email.
                     *
                     * @note Not all users have user names, but all have email
                     * @note Must set global to false to prevent gloabl ajax methods
                     */
                    $.ajax({
                        data: {
                            action: "create_facebook_user",
                            username: user_name,
                            fb_id: fb_response.id,
                            email: fb_response.email
                        },
                        global: false,
                        success: function( msg ){
                            window.location.replace( window.location.pathname );
                        }
                    });
                });
            } else {
                console.log('User cancelled login or did not fully authorize.');
            }
        },{
            /**
             * See the following for full list:
             * @url https://developers.facebook.com/docs/authentication/permissions/
             */
            scope: 'email'
        });
    });

    $('.inner').on('scroll', function(){
        tmp = 50;
        $( '.scroll-bar-y' ).animate({
            top: tmp + 'px'
        });
    });

    /**
     * JavaScript file for loading the custom login form.
     * Note we are using jQuery in "no conflict mode".
     */
    /**
     * Set-up our default dialog box with the following
     * parameters.
     */
    $('#bmx_rs_dialog').dialog({
        autoOpen: false,
        width: 265, // needed since default is 300px
        resizable: false,
        modal: true,
        title: 'Login'
    });

    /**
     * Close our dialog box when the user clicks anything
     * inside of the div "bmx_rs_dialog" with the class
     * of "cancel".
     */
    $( '#bmx_rs_dialog .cancel' ).live('click', function(){
        $( '#bmx_rs_dialog' ).dialog( 'close' );
    });

    /**
     * We hook into the form submission and submit it via ajax.
     * the action maps to our php function, which is added as
     * an action, and we serialize the entire contents of the form.
     * note we set global false to prevent other global ajax functions
     * from firing.
     */
    $( '#bmx_rs_dialog form, .login-form' ).live('submit', function(){
        $.ajax({
            data: "action=zm_register_login_submit&" + $(this).serialize(),
            global: false,
            success: function( msg ){
                window.location.replace( window.location.pathname );
            }
        });
    });

    /**
     * When ever a DOM element with a class of "login-handle" is clicked.
     * We open the dialog box, and send an ajax request to load our form.
     */
    $('.login-handle').live('click', function( event ){
        event.preventDefault();
        $('#bmx_rs_dialog').dialog('open');
        $.ajax({
            data: {
                "action": "zm_load_template",
                "target_div": "#bmx_rs_login_target",
                "template": $( this ).attr("data-template")
                },
            global: false,
            success: function( msg ){
                $( "#bmx_rs_login_target" ).fadeIn().html( msg ); // Give a smooth fade in effect
            }
        });
    });

    // $('.primary-navigation a').on('click', function( event ){
    //     // event.preventDefault();

    //     var $this = $(this);
    //     post_type = $this.attr('data-post_type');

    //     $('#post_type_target').attr('value', post_type );

    //     $('.primary-navigation a').each(function(){
    //         if ( $( this).hasClass('current') ) {
    //             $( this ).removeClass('current');
    //         } else {
    //             $this.addClass('current');
    //         }
    //     });
    // });


    /**
     * Load tabs
     */
    $(function(){
        $( ".tabs-handle" ).tabs();
    });

    var post_type = getParameterByName( 'type' ).toLowerCase();
    var locationPathName = location.pathname.toLowerCase();

    if ( post_type === 'events' || locationPathName === '/events/' ) {
        _feed_name = 'events';
    } else if ( post_type === 'tracks' || locationPathName === '/tracks/' || locationPathName === '/tracks' ) {
        _feed_name = 'tracks';
    // default for now is events
    } else {
        _feed_name = 'events';
    }

    window.displayResults = function(searchObject) {

        var new_header = '';
        var results_message_html = '';
        var results_data_object = {"result":[]};

        var results = searchObject.results;
        var term_count = searchObject.term_count;
        var attending;
        var top_percent = 100, this_percent;
        var tmp_plural, this_result;
        var tpl;
        var maxResults = 25;

        if ( results.length ) {

            for ( var i = 0; i<=maxResults; i++ ) {

                if ( typeof results[i] === 'object') {
                    this_result = feeds.data[_feed_name][ results[i][0] ];
                    this_percent = ~~( ( results[i][1] / term_count ) * 100 );
                    if(!i) {
                        top_percent = this_percent;
                    }
                    if(this_percent < top_percent >> 1) {
                        break;
                    }

                    attending = inArray( this_result.ID, _user.attending );

                    if ( attending ) {
                        action = 'remove';
                        css_class = 'event-added';
                    } else {
                        action = 'add';
                        css_class = 'not-attending';
                    }

                    if ( this_result.ec > 1 ) {
                        event_message = this_result.ec + ' events at';
                        event_count = this_result.ec;
                    } else {
                        event_message = null;
                        event_count = null;
                    }

                    tmp_expired_class = '';

                    // Start
                    // @todo same obj for events and tracks, no no no!
                    results_data_object.result.push({
                        id: this_result.ID,
                        title: this_result.t,
                        track: this_result.tr,
                        date: this_result.dateHTML,
                        city: this_result.c,
                        current_user: _user.ID,
                        css_class: css_class,
                        action: action,
                        state: this_result.s,
                        percent: this_percent,
                        url: this_result.u,
                        event_msg: event_message,
                        event_count: event_count,
                        expired_class: tmp_expired_class,
                        map_small: this_result.s_u,
                        map_medium: this_result.m_u
                    });
                    // End

                    if ( i === maxResults && results.length > maxResults ) {
                        results_message_html = '<span class="meta">Showing top ' + maxResults + ' of ' + results.length + ' total results</span>';
                        break;
                    }
                }
            }
            if(results_message_html === '') {
                results_message_html = '<span class="meta">All '+maxResults+' results match</span>';
            }
        } else {
            // results_message_html = '<span class="meta">No results were found for your search. Try broadening your terms</span>';
            results_message_html = '';
        }
        if ( $('#search_target').length ) {
            $( '#results_message_target' ).html( results_message_html );
        }
        $( '#search_target thead' ).html( new_header );

        if ( _feed_name === 'events' ) {
            new_header = '<tr><th class="attending">Add</th><th class="date">Date</th><th class="title">Event</th><th class="track">Track</th><th class="state">State</th></tr>';
            tpl = ich.result_event_tpl( results_data_object );
        } else {
            new_header = '';
            tpl = ich.result_track_tpl( results_data_object );
        }

        //$( '#search_target thead' ).html( new_header );
        $( '#search_target tbody' ).html( tpl );

        $("#search_target .title a, #search_target span.meta").each(function() {
            var th = this.innerHTML;
            for(var i in searchObject.terms) {
                th = th.replace(new RegExp('(' + searchObject.terms[i] + ')', "gi"), "<b>$1</b>");
            }
            this.innerHTML = th;
        });
    }

        /**
     * Populate content for default /events page
     */
    $( document ).bind( 'feedLoaded-' + _feed_name, function() {

        $('#s').one('keydown', function(){
            $('.logo-search-area').animate({
                "marginTop": "-=242px"
            }, "fast");
            $('.logo-search-area').addClass('header-container');
            $('#logo_target').toggleClass("hidden");
            $('.boo-container, .sidebar-wide-container').fadeIn('slow');
        });

        /**
         * On each key up start filtering results
         */
        $('#s').on( 'keyup', function() {
            var thisVal = $(this).val();
            var searchObject = feeds.doSearch( _feed_name, thisVal, { showCurrent: true } );
            displayResults(searchObject);
        });

        $('#s').trigger( 'keyup' );

        var month_num = (new Date()).getUTCMonth();
        var month = monthFull[ month_num ];
        var next_month = monthFull[ month_num + 1 ];
        var state = _user.region_full;

        /**
         * Query the feed for the ALL events in users current state
         */
        var result_id_local = feeds.doSearch( _feed_name, '+"'+state +'"',{showCurrent: true, showPast: false});
        var result_id_local_current = feeds.doSearch( _feed_name, '+"'+state + '" +' + month );
        var result_id_local_next = feeds.doSearch( _feed_name, '+"'+state + '" +' + next_month );
        var result_id_national = feeds.doSearch( _feed_name, 'national',{showCurrent: true, showPast: false});

        /**
         * Define our data object
         */
        var results_data_object = {
            locals_next_month:[],
            locals_current_month:[],
            locals:[],
            nationals:[],
            count_current_locals: function(){ return this.locals_current_month.length; },
            count_next_locals: function(){ return this.locals_next_month.length; },
            count_all_locals: function(){ return this.locals.length; },
            count_nationals: function(){ return this.nationals.length; }
        };

        /**
         * Local Match
         * IDs with results
         */
        for ( var i in result_id_local.results ) {

            var item = feeds.data[_feed_name][result_id_local.results[i][0]];

            results_data_object.locals.push({
                count: '10',
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
         * This Month Match
         * IDs with results
         */
        for ( var i in result_id_local_current.results ) {

            item = feeds.data[_feed_name][result_id_local_current.results[i][0]];

            results_data_object.locals_current_month.push({
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
         * Next Month Match
         * IDs with results
         */
        for ( var i in result_id_local_next.results ) {

            item = feeds.data[_feed_name][result_id_local_next.results[i][0]];

            results_data_object.locals_next_month.push({
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

            item = feeds.data[_feed_name][result_id_national.results[i][0]];

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
        $( '.temp_target' ).fadeIn('slow').html( ich.tabs_tpl( results_data_object ) );
        $( ".tabs-handle" ).tabs();
    }); // End 'feedLoaded'


    /**
     * Generic AJAX form handler.
     *
     * ALL post submissions are pushed to this function, from here the
     * desired method "postTypeSubmit" is called, given the post type.
     */
    $( '#postTypeSubmit' ).live( 'click', function(){

        if ( $('#default_message_target').length ) {
            $('#default_message_target').empty();
        }

        var $form = $( this ).closest('form');

        $.ajax({
            data: "action=postTypeSubmit&" + $form.serialize(),
            success: function( msg ) {

                if ( msg.length ) {

                    // @todo slide down, fade in smoother!
                    $( '#default_message_target' ).html( msg );

                    // @chrome, safari bug to prevent select/deselect bug
                    $('.share-link').mouseup(function(e){
                        e.preventDefault();
                    });

                    $('.share-link').on('focus', function(){
                        $(this).select();
                    });

                    // @todo slide down, fade in smoother!
                    $('.zm-fade-out').delay(2000).slideUp();
                }
            }
        });
    });

}); // End 'document ready'

// Load the SDK Asynchronously
(function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));

window.fbAsyncInit = function() {
    FB.init({
        // appId      : '401033839924979', // App ID
        appId      : _app_id, // App ID
        channelUrl : '//'+location.origin+'/channel.html', // Channel File
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        xfbml      : true  // parse XFBML
    });
};