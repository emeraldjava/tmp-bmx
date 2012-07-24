<?php

Class Contact extends zMCustomPostTypeBase {

    private $my_cpt;
    private static $instance;

    public function __construct(){

        self::$instance = $this;
        $this->my_cpt = strtolower( get_class( self::$instance ) );

        /**
         * Our parent construct has the init's for register_post_type
         * register_taxonomy and many other usefullness.
         */
        parent::__construct();

        add_action( 'add_meta_boxes', array( &$this, 'metaField' ) );
        add_action( 'save_post', array( &$this, 'saveContactMeta' ) );

        add_action( 'wp_ajax_nopriv_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );
        add_action( 'wp_ajax_postTypeSubmit', array( &$this, 'postTypeSubmit' ) );
    }

    public function registerActivation(){}

    public function metaField(){
        add_meta_box(
            'meta_field',
            __( 'Address', 'myplugin_textdomain' ),
            array( &$this, 'metaFieldRender'),
            $this->my_cpt
        );
    }

    public function metaFieldRender( $post ){
        wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

        $date = get_post_custom_values( "{$this->my_cpt}_first_name", $post->ID);
        print '<p>Fist Name<input type="text" name="'.$this->my_cpt . '_first_name" value="'.$date[0].'" /></p>';

        $date = get_post_custom_values("{$this->my_cpt}_last_name", $post->ID);
        print '<p>Last Name <input type="text" name="'.$this->my_cpt.'_last_name" value="'.$date[0].'" /></p>';

        $pre_race = get_post_custom_values("{$this->my_cpt}_email", $post->ID);
        print '<p>Email <input type="text" name="'.$this->my_cpt.'_email" value="'.$pre_race[0].'" /></p>';
    }

    /**
     * @todo really needs to be part of zm-cpt abstract! and derived via
     * models/contact.php
     */
    public function saveContactMeta( $post_id ){

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        if ( empty( $_POST ) || $_POST['post_type'] != $this->my_cpt )
            return;

        if ( !empty($_POST['contact_first_name']))
            update_post_meta( $post_id, 'contact_first_name', $_POST['contact_first_name'] );

        if ( !empty($_POST['contact_last_name']))
            update_post_meta( $post_id, 'contact_last_name', $_POST['contact_last_name'] );

        if ( !empty($_POST['contact_email']))
            update_post_meta( $post_id, 'contact_email', $_POST['contact_email'] );

        return;
    }

    public function postTypeSubmit(){
        // @smells This doesn't smell right, we have this conditional
        // to prevent this method from submmitting posts that are
        // not of its type.
        if ( $_POST['post_type'] != $this->my_cpt )
            return;

        // Verify nonce
        $nonce = $_POST['_new_'.$this->my_cpt];
        Security::verifyPostSubmission( $nonce );

        if ( get_current_user_id() )
            $author_ID = get_current_user_id();
        else
            $author_ID = null;

        $post = array(
            'post_title' => $_POST['post_title'],
            'post_content' => $_POST['content'],
            'post_author' => $author_ID,
            'post_type' => $_POST['post_type'],
            'post_date' => date( 'Y-m-d H:i:s' ),
            'post_status' => 'publish' // So terms show up in the admin
        );

        $post_id = wp_insert_post( $post, true );

        if ( ! $post_id )
            die('ooops');

        $term_id = $_POST['bmx_re_contact_category'];
        wp_set_post_terms( $post_id, $term_id, 'bmx_re_contact_category' );

        $html = null;
        $html .= '<div class="success-container zm-fade-out">';
        $html .= '<div class="message">';
        $html .= '<p>Thanks for contacting us!</p>';
        $html .= '</div>';
        $html .= '</div>';

        print $html;
        die();
    }
}