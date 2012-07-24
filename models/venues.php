<?php

$tracks = new Venues();
$tracks->post_type = array(
    array(
        'name' => 'Race Track',
        'type' => 'tracks',
        'rewrite' => array(
            'slug' => 'tracks'
            ),
        'supports' => array(
            'title',
            'editor',
            'comments'
        ),
        'taxonomies' => array(
            'track_tags',
            'region'
            )
    )
);

$tracks->taxonomy = array(
    array(
         'name' => 'region',
         'post_type' => 'tracks',
         'menu_name' => 'Region'
         ),
    array(
         'name' => 'tracks_tags',
         'post_type' => 'tracks',
         'menu_name' => 'Tags'
         )
    );


// $tracks->meta_section = array(
//     array(
//         'name' => 'address',
//         'label' => __('Address','myplugin_textdomain'),
//         'post_type' => 'tracks',
//         'fields' => array(
//             array(
//                 'label' => 'Full Address',
//                 'type' => 'input'
//             ),
//             array(
//                 'label' => 'City',
//                 'type' => 'input'
//             ),
//             array(
//                 'label' => 'State',
//                 'type' => 'input'
//             ),
//             array(
//                 'label' => 'Website',
//                 'type' => 'input'
//             ),
//             array(
//                 'label' => 'Lat',
//                 'type' => 'input'
//             ),
//             array(
//                 'label' => 'Long',
//                 'type' => 'input'
//             )
//         )
//     ),
//     array(
//         'name' => 'images_two',
//         'label' => __( 'Images', 'myplugin_textdomain' ),
//         'post_type' => 'tracks',
//         'fields' => array(
//             'label' => 'Thumb',
//             'type' => 'input'
//             )
//         )
//     );