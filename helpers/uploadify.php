<?php

require_once '../../../../wp-config.php';
require_once '../lib/zm-upload/MediaUpload.php';

if ( ! empty( $_FILES ) ) {

    $media = new MediaUpload;
    $uploaded_media = $media->saveUpload( $field_name='Filedata' );

    /**
     * @todo MediaUpload does NOT handle resizing of images,
     * normally its done in WordPress, but for some reason
     * wp_generate_attachment_metadata() does not work when
     * used in a plugin.
     */
    $thumb  = $media->resizeImage( $uploaded_media['file'], 'thumb' );
    $square = $media->resizeImage( $uploaded_media['file'], 'square' );
    $main   = $media->resizeImage( $uploaded_media['file'], 'main' );

    if ( $uploaded_media['file_info']['extension'] == 'jpeg' ) {
        $uploaded_media['file_info']['extension'] = 'jpg';
    }

    // Since we are updating the image meta we need to pull ALL
    // of it to update ONE section of it.
    $image_meta = wp_read_image_metadata( $uploaded_media['file'] );
    $meta['image_meta'] = $image_meta;
    $meta["zm_sizes"] = array(
        'thumb'  => $media->upload_dir['subdir'] . '/' . $uploaded_media['file_info']['filename'] . '-zm-thumb.' . $uploaded_media['file_info']['extension'],
        'square' => $media->upload_dir['subdir'] . '/' . $uploaded_media['file_info']['filename'] . '-zm-square.' . $uploaded_media['file_info']['extension'],
        'main'   => $media->upload_dir['subdir'] . '/' . $uploaded_media['file_info']['filename'] . '-zm-main.' . $uploaded_media['file_info']['extension'],
    );
    wp_update_attachment_metadata( $uploaded_media['attachment_id'], $meta );

    $stuff['attachment_id'] = $uploaded_media['attachment_id'];
    $stuff['meta'] = wp_get_attachment_metadata( $uploaded_media['attachment_id'] );

    print json_encode( $stuff );
}