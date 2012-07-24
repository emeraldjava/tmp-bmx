<?php

Class Home {

    public function randomImage(){
        $images = array(
            IMAGES_DIR . 'home/one.jpg',
            IMAGES_DIR . 'home/two.jpg',
            IMAGES_DIR . 'home/three.jpg'
            );
        return Helpers::makeRandom( $images );
    }

    public function randomBanner(){

        $obj_local_venues = new Venues;
        $local_venues = $obj_local_venues->getLocalVenues();

        $random_venue_id = $obj_local_venues->randomId();

        $args = array(
            'post_type' => 'events',
            'posts_per_page' => 1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'bmx_re_start_date',
                    'value' => array( date('Y-m-d'), Helpers::plusMonth(6)),
                    'type' => 'DATE',
                    'compare' => 'BETWEEN'
                ),
                array(
                    'key' => 'tracks_id',
                    'value' => $random_venue_id,
                    'compare' => '='
                    )
                )
            );

        $query = new WP_Query( $args );
        $event = null;

        if ( $query->post_count == 0 ) {
            $event = false;
        } else {
            foreach( $query->posts as $post ){
                setup_postdata( $post );
                $event['ID'] = $post->ID;
                $event['title'] = $obj_local_venues->getMetaField( 'state', $random_venue_id ) . ' ' . $post->post_title;
                $event['link'] = "/events/" . $post->post_name;
                $event['date'] = Helpers::formatDate( $post->ID );
            }
        }
        return $event;
    }
}