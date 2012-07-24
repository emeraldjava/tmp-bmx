/* This automatically created for you.
 It is your js file for the venues model, controller
 Created On: July 4, 2012, 8:27 pm */
jQuery(document).ready(function( $ ){

    /**
     * Wait for feed to be fully loaded
     * Since ALL js files are loaded ALL the time in our dev/stage env
     * we check the length of the target element
     */
    if ( $('.tracks_archive_target').length ) {
        $( document ).bind( 'feedLoaded-tracks' , function() {

            var state = _user.region_full;

            /**
             * Query the feed for the ALL local Venues in users current state
             */
             console.log(_feed_name);
            var result_id_local = feeds.doSearch( _feed_name, '+"'+state +'"',{showCurrent: true, showPast: false});

            /**
             * Define our data object
             */
            var results_data_object = {
                "local_tracks":[],
                "count_all_locals": function(){ return this.local_tracks.length; }
            };

            var item;
            /**
             * Local Match
             * IDs with results
             */
            for ( var i in result_id_local.results ) {

                item = feeds.data['tracks'][result_id_local.results[i][0]];

                results_data_object.local_tracks.push({
                    id: item.ID,
                    url: item.u,
                    title: item.t,
                    city: item.c,
                    state: item.s,
                    map_small: item.s_u
                });
            }

            /**
             * Call template
             */
            $( '.tracks_archive_target' ).html( ich.tracks_archive( results_data_object ) );
            $( ".tabs-handle" ).tabs();
        }); // End 'feedLoaded'
    }
});