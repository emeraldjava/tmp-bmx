<?php
function get_event_meta_date( $post_id=null, $meta_key=null ){

    $meta_values = get_post_meta( $post_id, $meta_key );

    if ( isset( $meta_values[0] ) ) {
        $start_date = strtotime( $meta_values[0] );
    }

    if ( isset( $meta_values[1] ) ) {
        $end_date = strtotime( $meta_values[1] );
    }

    $data["s_d"] = $start_date * 1000;

    if ( empty( $end_date ) ) {
        $data["e_d"] = $start_date * 1000;
    } else {
        $data["e_d"] = $end_date * 1000;
    }
    return $data;
}

Class Feeds {

    static public function event(){
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'events',
            'post_status' => 'publish',
            'order' => 'ASC',
            'orderby' => 'meta_value',
            'meta_key' => 'bmx_re_start_date'
            );

        $query = new WP_Query( $args );

        $events = array();
        $i = 0;
        foreach( $query->posts as $posts ) {
            $this_event = array();

            // just for fucking sanity
            $event_id = $posts->ID;
            $track_id = Events::getTrackId( $event_id );

            $this_event['ID'] = $event_id;
            $this_event['t'] = $posts->post_title;
            $this_event['tr'] = Events::getTrackTitle( $event_id );
            $this_event['ta'] = Events::getTags( $event_id );

            $this_event['c'] = Venues::getMetaField( 'city', $track_id );
            $this_event['s'] = Venues::getMetaField( 'state', $track_id );
            $this_event['r'] = Venues::getRegion( $track_id );

            $this_event['u'] = '/events/'.$posts->post_name . '/';
            $this_event['s_u'] = Venues::getMapImage( $track_id, 'small', true );

            $tmp_this_event = array_merge( $this_event, get_event_meta_date( $event_id, 'bmx_re_start_date' ) );

            if ( ! empty( $tmp_this_event ) )
                $this_event = $tmp_this_event;

            $events[] = $this_event;

            // $i++;
            // if ( $i == 100 )
            //     return $events;
        }
        // print_r( $events );
        $file = file_put_contents( TMP_RACES_DIR . 'events.json', json_encode( $events ) );
        if ( $file )
            print "File created, size: {$file}\n";
    }
}


// /**
//  * Still not sure where or how to manage this file
//  */
// if ( ENV != 'development' )
//     die('no');

// // die('feed');
// /**
//  * Build a json feed of track urls, tracks ids, and state abbreviations. The
//  * point of this function is to reduce the number of web request we need to
//  * make when scrapeing data from http://usabmx.com/
//  *
//  * @note test with $state_list = array( 'MD' => 'Maryland', 'NY' => 'New York' );
//  * @uses Venues::$state_list;
//  * @uses Venues::$state_list;
//  */
// function scrape_track_urls(){

//     ini_set( 'max_execution_time', 300 ); //300 seconds = 5 minutes
//     include( VENDOR_ROOT_DIR . 'simple-html-dom/simple_html_dom.php');

//     $track_id = null;
//     $state_url = 'http://usabmx.com/site/bmx_tracks/by_state?section_id=12&state=';
//     $track_url = 'http://usabmx.com/site/bmx_tracks/'.$track_id;

//     // Test with only two states
//     $state_abbreviations = Venues::$state_list;

//     $track_ids = array();
//     $track_urls = array();

//     // build array of track ids
//     foreach( $state_abbreviations as $abbr => $full ){

//         /**
//          * Build state url and get html
//          */
//         $state_html = file_get_html( $state_url . $abbr );

//         /**
//          * Parse track_id from html and build track url
//          * i.e. http://usabmx.com/site/bmx_tracks/359?section_id=12
//          */
//         foreach( $state_html->find('h5.track_title a') as $element ) {

//             // returns /site/bmx_tracks/359?section_id=12
//             $tmp_track_id = $element->href . '<br>';

//             // split on / return array
//             $tmp_track_id = explode('/', $tmp_track_id );

//             // split on ? return array
//             $tmp_track_id = explode( '?', $tmp_track_id[3] );
//             // first index is our track_id

//             $track_id = $tmp_track_id[0];
//             $tmp_track_urls['state'] = $abbr;
//             $tmp_track_urls['id'] = $track_id;
//             $tmp_track_urls['url'] = $track_url . $track_id;
//             $track_urls[] = $tmp_track_urls;
//         }
//     }
//     $file = file_put_contents( TMP_RACES_DIR . 'usa-bmx-track-urls-and-state.json', json_encode( $track_urls ) );
//     return $file;
// }
// // scrape_track_urls();


// function scrape_track_content( $file=null ){

//     ini_set( 'max_execution_time', 300 ); //300 seconds = 5 minutes
//     include( VENDOR_ROOT_DIR . 'simple-html-dom/simple_html_dom.php');

//     $tracks_feed = json_decode( file_get_contents( TMP_RACES_DIR . $file ), true );
//     $tracks = array();

//     foreach( $tracks_feed as $tf ){

//         /**
//          * Build our tracks url
//          */
//         $bmx_tracks_url = 'http://usabmx.com/site/bmx_tracks/'. $tf['id'];

//         /**
//          * Get the contents of each page
//          */
//         $tracks_html = file_get_html( $bmx_tracks_url );

//         // Parse email
//         $tmp_email = $tracks_html->find('ul.no-style li', 0);
//         if ( $tmp_email ) {
//             $tmp_email->innertext();
//             $tmp_email = explode( ':', $tmp_email );
//             $email = strip_tags( trim( $tmp_email[1] ) );
//         } else {
//             $email = null;
//         }

//         // Parse track phone
//         $tmp_track_phone = $tracks_html->find('ul.no-style li', 1);
//         if ( $tmp_track_phone ) {
//             $tmp_track_phone->innertext();
//             $tmp_track_phone = explode( ':', $tmp_track_phone );
//             $track_phone = strip_tags( trim( $tmp_track_phone[1] ) );
//         } else {
//             $track_phone = null;
//         }

//         // Parse primary contact
//         $tmp_primary_contact = $tracks_html->find('ul.no-style li', 1);
//         if ( $tmp_primary_contact ) {
//             $tmp_primary_contact->innertext();
//             if ( is_array( $tmp_primary_contact ) )
//                 $primary_contact = strip_tags( trim( $tmp_primary_contact[1] ) );
//             else
//                 $primary_contact = null;
//         } else {
//             $primary_contact = null;
//         }

//         // Parse secondary contact
//         // $tmp_primary_contact_phone = $tracks_html->find('ul.no-style li', 1)->innertext();
//         // $tmp_primary_contact_phone = explode( ':', $tmp_primary_contact_phone );
//         // $primary_contact_phone = trim( $tmp_primary_contact_phone[1] );

//         // Parse lat/long
//         // This returns the full url i.e. http://maps.google.com/maps?q=33.29944,-111.825837+(Chandler%20BMX)&amp;iwloc=A&amp;iwd=1"
//         // From here we explod this numerous times, quick and dirty
//         $tmp_lat_long = $tracks_html->find('#track_location p', 1)->find('a',0)->href;
//         $tmp_lat_long = explode( '?q=', $tmp_lat_long );
//         $tmp_lat_long = explode( '+', $tmp_lat_long[1] );
//         $tmp_lat_long = explode( ',', $tmp_lat_long[0] );
//         $lat = $tmp_lat_long[0];
//         $long = $tmp_lat_long[1];

//         // Parse district
//         $tmp_district = $tracks_html->find('#track_district', 0);
//         if ( $tmp_district ) {
//             $tmp_district->innertext();
//             $tmp_district = explode( ':', $tmp_district );
//             $district = strip_tags( trim( $tmp_district[1] ) );
//         } else {
//             $district = null;
//         }

//         // Parse website
//         $tmp_website = $tracks_html->find('#track_location p', 2);
//         if ( $tmp_website ) {
//             $website = strip_tags( trim( $tmp_website->find('a', 0)->href ) );
//         } else {
//             $website = null;
//         }

//         // Parse name
//         // Yes, apparently they have more than 1 h1
//         $tmp_name  = $tracks_html->find('h1', 1);
//         if ( $tmp_name ){
//             $tmp_name = $tmp_name->innertext();
//             $tmp_name = explode( '<', $tmp_name );
//             $tmp_name = $tmp_name[0];
//             $name = strip_tags( trim( $tmp_name ) );
//         } else {
//             $name = null;
//         }

//         // Build tmp array
//         $tmp_tracks['usabmx_id']       = $tf['id'];
//         $tmp_tracks['name']            = $name;
//         $tmp_tracks['website']         = $website;
//         $tmp_tracks['email']           = $email;
//         $tmp_tracks['track_phone']     = $track_phone;
//         $tmp_tracks['primary_contact'] = $primary_contact;
//         $tmp_tracks['lat']             = $lat;
//         $tmp_tracks['long']            = $long;
//         $tmp_tracks['District']        = $district;

//         // push tmp array onto the end of our final array
//         $tracks[] = $tmp_tracks;
//     }
//     $file = file_put_contents( TMP_RACES_DIR . 'usa-bmx-tracks-full.json', json_encode( $tracks ) );
//     return $file;
// }
// // print scrape_track_content( 'usa-bmx-track-urls-and-state.json' );


// /**
//  * Update WP DB with json feed
//  */
// function update_tracks_from_feed(){

//     /**
//      * We will be running a query, and will need our global laters.
//      */
//     global $wpdb;


//     /**
//      * This allows us to use a dynamic list of meta fields from our
//      * controller. Note, this list MUST match what is in the db!
//      *
//      * json feed fields == meta_fields == db meta fields!
//      */
//     $meta_fields = Venues::$meta_fields;


//     /**
//      * Get our json file, decode it and return it an as associative array.
//      * Loop over that for each item matching the json feed titles and our
//      * post_titles.
//      */
//     $tracks = json_decode( file_get_contents( TMP_RACES_DIR . 'usa-bmx-tracks-full.json'), true );
//     foreach( $tracks as $track ){
//         /**
//          * We run a custom query to compare whats in the $tracks array
//          * with what is in our post_table. Return results as an numerically
//          * indexed array.
//          */
//         $query = "SELECT ID, post_title
//             FROM {$wpdb->prefix}posts
//             WHERE post_title
//             LIKE '{$track['name']}'
//             AND post_type LIKE 'tracks'
//             AND post_title NOT LIKE 'Auto Draft'";
//         $match = $wpdb->get_results( $query, ARRAY_N );


//         /**
//          * If we have a match we update our post meta. If not we need
//          * to insert a new post (venue) and then upate the post meta
//          */
//         if ( $match ) {

//             /**
//              * First index from our match is the ID, second index
//              * from the match is our title.
//              */
//             $post_id = $match[0][0];
//             $post_title = $match[0][1];


//             /**
//              * We loop over our dynamic list of meta fields
//              */
//             foreach( $meta_fields as $meta ){

//                 /**
//                  * If we have a meta field in our venue array that matches
//                  * our list of meta fields we update the meta field.
//                  */
//                 if ( $track[ $meta ] ){

//                     /**
//                      * We need to trim white space and lower case due to a
//                      * malformated date in our feed.
//                      */
//                     $value = trim( $track[$meta] );
//                     $field = strtolower( $meta );


//                     /**
//                      * We display the output as a confirmation, first line is the command that
//                      * we executed, returns meta_id on success, false on failure.
//                      */
//                     print "update_post_meta( {$post_id}, '{$field}', '{$value}' )\n";
//                     var_dump( update_post_meta( $post_id, $field, $value ) );
//                     print "\n";
//                 }
//             }
//             print "\n";
//         } else {
//             print $track['name'] . "\n";

//             /**
//              * Insert our new tracks into our db
//              */
//             $post = array(
//                 'post_author' => 1,
//                 'post_date' => date('Y-m-d'),
//                 'post_name' => sanitize_title( $track['name'] ),
//                 'post_status' => 'publish',
//                 'post_title' => $track['name'],
//                 'post_type' => 'tracks'
//             );


//             /**
//              * @todo update region as well?
//              */
//             $post_id = wp_insert_post( $post );


//             /**
//              * If we have a post ID proceed to update our post meta
//              */
//             if ( $post_id ) {

//                 foreach( $meta_fields as $meta ){

//                     /**
//                      * If we have a meta field in our venue array that matches
//                      * our list of meta fields we update the meta field.
//                      */
//                     if ( $track[ $meta ] ){

//                         /**
//                          * We need to trim white space and lower case due to a
//                          * malformated date in our feed.
//                          */
//                         $value = trim( $track[$meta] );
//                         $field = strtolower( $meta );

//                         /**
//                          * We display the output as a confirmation, first line is the command that
//                          * we executed, returns meta_id on success, false on failure.
//                          */
//                         print "update_post_meta( {$post_id}, '{$field}', '{$value}' )\n";
//                         var_dump( update_post_meta( $post_id, $field, $value ) );
//                         print "\n";
//                     }
//                 }
//             }
//             print "\n";
//         }
//     }
//     die();
// }
// // update_tracks_from_feed();


// /**
//  * Retrive the small and medium image from Google Map Image API
//  * based on the lat/long from the track.
//  *
//  * @param $track_id, will default to all.
//  */
// function get_static_map( $track_ids=null ){

//     if ( is_null( $track_ids ) ) {
//         global $wpdb;
//         $query = "SELECT ID
//             FROM {$wpdb->prefix}posts
//             WHERE post_type LIKE 'tracks'
//             AND post_title NOT LIKE 'Auto Draft'";
//         $track_ids = $wpdb->get_results( $query, ARRAY_N );
//     }


//     $tracks = new Venues;

//     /**
//      * Google API Static URL
//      * $lat_long = '38.932707,-75.427762';
//      */
//     $g_map_image = 'http://maps.googleapis.com/maps/api/staticmap?center=';
//     $sep = '&';

//     /**
//      * Zoom and site are directly related.
//      */
//     $small = 'zoom=17'.$sep.'size=125x82';
//     $medium = 'zoom=18'.$sep.'size=460x300';

//     $type = 'maptype=satellite';
//     $sensor = 'sensor=false';


//     foreach( $track_ids as $track_id ){

//         if ( is_array( $track_id ) ){
//             $track_id = $track_id[0];
//         }

//         $lat_long = $tracks->getLatLon( $track_id );



//             /**
//              * Build our URLs
//              */
//             $small_url = $g_map_image . $lat_long . $sep . $small . $sep . $type . $sep . $sensor . $sep . 'key='.MAP_KEY;
//             $medium_url = $g_map_image . $lat_long . $sep . $medium . $sep . $type . $sep . $sensor . $sep . 'key='.MAP_KEY;

//             print $track_id . "\n";
//             print $small_url . "\n";
//             print $medium_url . "\n";

//             /**
//              * Save the contents of the url to our server
//              */
//             $tracks->saveMapImage( $track_id, $small_url, 'small' );
//             $tracks->saveMapImage( $track_id, $medium_url, 'medium' );


//             /**
//              * Retrive the new location
//              */
//             $s_url = $tracks->getMapImage( $track_id, 'small' );
//             $m_url = $tracks->getMapImage( $track_id, 'medium' );


//             /**
//              * Update the meta fields
//              */
//             $tracks->updateMapImageMeta( $track_id, $s_url, 'small' );
//             $tracks->updateMapImageMeta( $track_id, $m_url, 'medium' );

//     }
// }
// $track_ids = array(
// // 1802,
// // 1803,
// // 1804,
// // 1805,
// // 1806,
// // 1807,
// // 1808,
// // 1809,
// // 1810,
// // 1811,
// // 1812,
// // 1813,
// // 1814,
// // 1815,
// // 1816,
// // 1817,
// // 1818,
// // 1819,
// // 1820
// // 1821,
// // 1822,
// // 1823,
// // 1824,
// // 1825,
// // 1826,
// // 1827,
// // 1828,
// // 1829,
// // 1830,
// // 1831,
// // 1832,
// // 1833,
// // 1834,
// // 1835,
// // 1836,
// // 1837,
// // 1838,
// // 1839,
// // 1840
// // 1841,
// // 1842,
// // 1843,
// // 1844,
// // 1845,
// // 1846,
// // 1847,
// // 1848,
// // 1849,
// // 1850,
// // 1851,
// // 1852,
// // 1853,
// // 1854,
// // 1855,
// // 1856,
// // 1857,
// // 1858,
// // 1859,
// // 1860,
// // 1861,
// // 1862,
// // 1863,
// // 1864,
// // 1865,
// // 1866,
// // 1867,
// // 1868,
// // 1869,
// // 1870
// // 1871,
// // 1872,
// // 1873,
// // 1874,
// // 1875,
// // 1876,
// // 1877,
// // 1878,
// // 1879,
// // 1880,
// // 1881,
// // 1882,
// // 1883,
// // 1884,
// // 1885,
// // 1886,
// // 1887,
// // 1888,
// // 1889,
// // 1890,
// // 1891,

// // 1892,
// // 1893,
// // 1894,
// // 1895,
// // 1896,
// // 1897,
// // 1898,
// // 1899

// // 1900,
// // 1901,
// // 1902,
// // 1903,
// // 1904,
// // 1905,
// // 1906,
// // 1907,
// // 1908,
// // 1909,
// // 1910,
// // 1911,
// // 1912,
// // 1913,
// // 1914,
// // 1915,
// // 1916,
// // 1917,
// // 1918,
// // 1919,
// // 1920

// // 1921,
// // 1922,
// // 1923,
// // 1924,
// // 1925,
// // 1926,
// // 1927,
// // 1928,
// // 1929,
// // 1930,
// // 1931,
// // 1932,
// // 1933,
// // 1934,
// // 1935,
// // 1936,
// // 1937,
// // 1938,
// // 1939,
// // 1940,
// // 1941,
// // 1942,
// // 1943,
// // 1944,
// // 1945,
// // 1946,
// // 1947,
// // 1948,
// // 1949,
// // 1950

// // 1951,
// // 1952,
// // 1953,
// // 1954,
// // 1955,
// // 1956,
// // 1957,
// // 1958,
// // 1959,
// // 1960,
// // 1961,
// // 1962,
// // 1963,
// // 1964,
// // 1965,
// // 1966,
// // 1967,
// // 1968,
// // 1969,
// // 1970

// // 1971,
// // 1972,
// // 1973,
// // 1974,
// // 1975,
// // 1976,
// // 1977,
// // 1978,
// // 1979,
// // 1980,
// // 1981,
// // 1982,
// // 1983,
// // 1984,
// // 1985,
// // 1986,
// // 1987,
// // 1988,
// // 1989,
// // 1990,
// // 1991,
// // 1992,
// // 1993,
// // 1994,
// // 1995,
// // 1996,
// // 1997,
// // 1998,
// // 1999,
// // 2000

// // 2001,
// // 2002,
// // 2003,
// // 2004,
// // 2005,
// // 2006,
// // 2007,
// // 2008,
// // 2009,
// // 2010,
// // 2011,
// // 2012,
// // 2013,
// // 2014,
// // 2015,
// // 2016,
// // 2017,
// // 2018,
// // 2019,
// // 2020,
// // 2021,
// // 2022,
// // 2023,
// // 2024,
// // 2025,
// // 2026,
// // 2027,
// // 2028,
// // 2029,
// // 2030,
// // 2031,


// // 2032,
// // 2033,
// // 2034,
// // 2035,
// // 2036,
// // 2037,
// // 2038,
// // 2039,
// // 2040,
// // 2078,
// // 2079
// // 2081,

// // 2082,
// // 2084,
// // 2085,
// // 2086,
// // 2087,
// // 2089,
// // 2090,
// // 2091,
// // 2101,
// // 2102,
// // 2103,
// // 2104,
// // 2105,
// // 2106,

// // 2107,
// // 2108,
// // 2109,
// // 2110,
// // 2111,
// // 2112,
// // 2113,
// // 2114,
// // 2115,
// // 2116,
// // 2117,
// // 2118,
// // 2119,

// // 2120,
// // 2121,
// // 2122,
// // 2123,
// // 2124,
// // 2125,
// // 2126,
// // 2127,
// // 2128,
// // 2129,
// // 2130


// // 2131,
// // 2132,
// // 2133,
// // 2134,
// // 2135,
// // 2136,
// // 2137,

// // 2138,
// // 2139,
// // 2140,
// // 2141,
// // 2142,
// // 2143,
// // 2144,
// // 2145,
// // 2146,
// // 2147

// // 2148,
// // 2149,
// // 2150,
// // 2151,
// // 2152,
// // 2153,
// // 2154,
// // 2155,
// // 2156,
// // 2157,
// // 2158,


// // 2159,
// // 2160,
// // 2161,
// // 2162,
// // 2163,
// // 2164,
// // 2165,
// // 2166,
// // 2167,
// // 2168,
// // 2169,
// // 2170,
// // 2171,
// // 2172,
// // 2173,
// // 2174

// // 2175,
// // 2176,
// // 2177,
// // 2178,
// // 2179,
// // 2180,
// // 2181,
// // 2182,
// // 2183,
// // 2184,
// // 2185,
// // 2186,

// // from here down
// // 2187,
// // 2188,
// // 2189,
// // 2190,
// // 2191,
// // 2192,
// // 2193,
// // 2194,
// // 2195,
// // 2196,
// // 2197,
// // 2198,
// // 2199,
// // 2200,
// // 2201,
// // 2202,
// // 2203,
// // 2204,
// // 2205,
// // 2206,
// // 2207,
// // 2208,
// // 2209,
// // 2210,
// // 2211,
// // 2212,
// // 2213,
// // 2214,
// );
// print '<pre>';
// // get_static_map($track_ids);


// /**
//  * Converts a date format given the meta_key to the one
//  * desired.
//  *
//  * @note Y-m-d is standard mysql format, this way string
//  * sort works
//  * @param $meta_key the meta key for which date is stored
//  * @param new date format, see php.net/date
//  */
// function update_date_format( $meta_key=null, $to=null ){
//     global $wpdb;
//     $query = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_key LIKE '".$meta_key."'";
//     $results = $wpdb->get_results( $query, ARRAY_A );

//     foreach( $results as $result ){
//         $meta_value = get_post_meta( $result['post_id'], $result['meta_key'], true );
//         if ( $meta_value ) {
//             $timestamp = strtotime( $result['meta_value'] );
//             $date = date( $to, $timestamp );
//             $tmp = update_post_meta( $result['post_id'], $result['meta_key'], $date, $result['meta_value'] );
//             // var_dump( $tmp );
//             print "<br />";
//         }
//     }
// }
// // update_date_format( 'bmx_rs_date', 'Y-m-d' );


// function update_meta_key( $old_key=null, $new_key=null ){
//     global $wpdb;
//     $query = "UPDATE ".$wpdb->prefix."postmeta SET meta_key = '".$new_key."' WHERE meta_key = '".$old_key."'";
//     $results = $wpdb->get_results( $query, ARRAY_A );
//     return $results;
// }
// // var_dump( update_meta_key( 'bmx_rs_date', 'bmx_re_start_date') );
// // var_dump( update_meta_key( 'bmx_rs_pre_race_date', 'bmx_re_start_date') );
// // var_dump( update_meta_key( 'bmx_rs_national_day_one_date', 'bmx_re_start_date') );
// // var_dump( update_meta_key( 'bmx_rs_national_day_two_date', 'bmx_re_end_date') );


// function tracks_feed(){
//     /**
//      * Retrive ALL Tracks and order by Track Title ASC
//      */
//     $args = array(
//         'posts_per_page' => -1,
//         'post_type' => 'tracks',
//         'post_status' => 'publish',
//         'order' => 'ASC',
//         'orderby' => 'title'
//         );

//     $query = new WP_Query( $args );
//     $tracks_obj = new Venues;
//     $tracks = array();

//     foreach( $query->posts as $post ) {
//         $this_event = array();

//         $this_event['ID'] = $post->ID;
//         $this_event['t'] = $post->post_title;
//         $this_event['u'] = '/tracks/'.$post->post_name . '/';

//         $tmp_city = get_post_meta( $post->ID, 'tracks_city', true );
//         $tmp_state = get_post_meta( $post->ID, 'tracks_state', true );
//         $tmp_lat = get_post_meta( $post->ID, 'lat', true );
//         $tmp_long = get_post_meta( $post->ID, 'long', true );
//         $tmp_street = get_post_meta( $post->ID, 'tracks_street', true );
//         $tmp_region = $tracks_obj->getRegion( $post->ID );
//         $tmp_tags = $tracks_obj->getTags( $post->ID );
//         $tmp_schedule = $tracks_obj->getTrackSchedule( $post->ID );
//         $tmp_website = get_post_meta( $post->ID, 'tracks_website', true ) ? get_post_meta( $post->ID, 'tracks_website', true ) : get_post_meta( $post->ID, 'website', true );

//         if ( $tmp_city )
//             $this_event['c'] = $tmp_city;

//         if ( $tmp_state )
//             $this_event['s'] = $tmp_state;

//         if ( $tmp_lat )
//             $this_event['l'] = $tmp_lat;

//         if ( $tmp_long )
//             $this_event['lo'] = $tmp_long;

//         if ( $tmp_street )
//             $this_event['st'] = $tmp_street;

//         if ( $tmp_website )
//             $this_event['w'] = $tmp_website;

//         if ( $tmp_region )
//             $this_event['r'] = $tmp_region;

//         if ( $tmp_tags )
//             $this_event['ta'] = $tmp_tags;

//         if ( $tmp_schedule )
//             $event_count = $tmp_schedule->post_count;

//         $this_event['ec'] = $event_count;
//         $this_event['s_u'] = '/images/maps/'. $tracks_obj->getMapImage( $post->ID, 'small' );
//         $this_event['m_u'] = '/images/maps/'. $tracks_obj->getMapImage( $post->ID, 'medium' );

//         $tracks[] = $this_event;
//     }
//     // print_r( $tracks );
//     // $file = file_put_contents( TMP_RACES_DIR . 'tracks.json', json_encode( $tracks ) );
//     // var_dump( $file );
// }
// // tracks_feed();
// // die();

// function get_event_meta_date( $post_id=null, $meta_key=null ){

//     $meta_values = get_post_meta( $post_id, $meta_key );

//     if ( isset( $meta_values[0] ) ) {
//         $start_date = strtotime( $meta_values[0] );
//     }

//     if ( isset( $meta_values[1] ) ) {
//         $end_date = strtotime( $meta_values[1] );
//     }

//     $data["s_d"] = $start_date * 1000;

//     if ( empty( $end_date ) ) {
//         $data["e_d"] = $start_date * 1000;
//     } else {
//         $data["e_d"] = $end_date * 1000;
//     }
//     return $data;
// }


// function events_feed(){

//     $args = array(
//         'posts_per_page' => -1,
//         'post_type' => 'events',
//         'post_status' => 'publish',
//         'order' => 'ASC',
//         'orderby' => 'meta_value',
//         'meta_key' => 'bmx_re_start_date'
//         );

//     $query = new WP_Query( $args );

//     $events = array();
//     $i = 0;
//     foreach( $query->posts as $posts ) {
//         $this_event = array();

//         // just for fucking sanity
//         $event_id = $posts->ID;
//         $track_id = Events::getTrackId( $event_id );

//         $this_event['ID'] = $event_id;
//         $this_event['t'] = $posts->post_title;
//         $this_event['tr'] = Events::getTrackTitle( $event_id );
//         $this_event['ta'] = Events::getTags( $event_id );

//         $this_event['c'] = Venues::getMetaField( 'city', $track_id );
//         $this_event['s'] = Venues::getMetaField( 'state', $track_id );
//         $this_event['r'] = Venues::getRegion( $track_id );

//         $this_event['u'] = '/events/'.$posts->post_name . '/';
//         $this_event['s_u'] = '/images/maps/'. Venues::getMapImage( $track_id, 'small' );

//         $tmp_this_event = array_merge( $this_event, get_event_meta_date( $event_id, 'bmx_re_start_date' ) );

//         if ( ! empty( $tmp_this_event ) )
//             $this_event = $tmp_this_event;

//         $events[] = $this_event;

//         // $i++;
//         // if ( $i == 100 )
//         //     return $events;
//     }
//     // print_r( $events );
//     // $file = file_put_contents( TMP_RACES_DIR . 'events.json', json_encode( $events ) );
//     // var_dump( $file );
// }
// // events_feed();
// die();

// // function post_type_converter( $to=null, $from=null, $post_id=null ){

// // }
// // post_type_converter( 'tracks', 'venues');

// 1. add the following terms, east, west, central
// 2. get the term_id
// 3. uncomment the state array, term and function call for each term

// // East
// $states = array(
//     'Alabama',
//     'Connecticut',
//     'Delaware',
//     'Indiana',
//     'Florida',
//     'Georgia',
//     'Maryland',
//     'Massachusetts',
//     'Michigan',
//     'North Carolina',
//     'New York',
//     'New Jersey',
//     'Ohio',
//     'Ontario',
//     'Pennsylvania',
//     'Virginia',
//     'Tennessee'
//     );
// $term_id = 570; // East
// bulk_update_track_region_by_state( $states, $term_id );


// // Central
// $states = array(
//     'Arizona',
//     'Colorado',
//     'Illinois',
//     'Iowa',
//     'Kansas',
//     'Louisiana',
//     'maryland',
//     'Missouri',
//     'michigan',
//     'Minnesota',
//     'Nebraska',
//     'new mexico',
//     'North Dakota',
//     'Oklahoma',
//     'texas',
//     'Wisconsin'
//     );
// $term_id = 572;
// bulk_update_track_region_by_state( $states, $term_id );


// West
// $states = array(
//     'British Columbia',
//     'California',
//     'Colorado',
//     'California',
//     'Louisiana',
//     'Hawaii',
//     'Idaho',
//     'Montana',
//     'Nevada',
//     'New Mexico',
//     'Oklahoma',
//     'Oregon',
//     'Washington',
//     'Wyoming'
//     );
// $term_id = 571; // West
// bulk_update_track_region_by_state( $states, $term_id );
