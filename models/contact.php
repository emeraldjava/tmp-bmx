<?php

$contact = New Contact();
$contact->post_type = array(
    array(
        'name' => 'Contact',
        'type' => 'contact',
        'menu_name' => 'yo',
        'rewrite' => array(
            'slug' => 'contact'
            ),
        'supports' => array(
            'title',
            'editor',
        ),
        'taxonomies' => array(
            'bmx_re_contact_category'
        )
    )
);

$contact->taxonomy = array(
     array(
         'name' => 'bmx_re_contact_category',
         'post_type' => 'contact',
         'menu_name' => 'Category'
         )
    );

