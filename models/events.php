<?php
/**
 * Define parameters for our Custom Post Type.
 *
 * BMXRaceSchedulePostType extends zMCustomPostTypeBase
 */

$tmp_cpt = 'events';

$event = new Events();
$event->post_type = array(
    array(
        'name' => 'Race Event',
        'type' => $tmp_cpt,
        'has_one' => 'tracks', // add support 'has_many' => 'other_cpt'
        'rewrite' => array(
            'slug' => 'events'
            ),
        'supports' => array(
            'title',
            'editor',
            'comments'
        ),
        'taxonomies' => array(
            'type',
            'bmx_rs_tag',
            'attendees'
        )
    )
);

$event->taxonomy = array(
     array(
         'name' => 'type',
         'post_type' => $tmp_cpt,
         'menu_name' => 'Type'
         ),
    array(
        'name' => 'bmx_rs_tag',
        'post_type' => $tmp_cpt,
        'menu_name' => 'BMX Tags',
        'slug' => 'bmx-tags'
        ),
    array(
        'name' => 'attendees',
        'post_type' => $tmp_cpt
        )
);