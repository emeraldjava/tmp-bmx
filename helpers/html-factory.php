<?php

/**
 * Some of our mark-up patterns have enough logic
 * between generating class names, ids, and are shared
 * across templates that it justifies to be placed in
 * its own file/class.
 */
Class HtmlFactory {

    public function entryTitle(){

        // leave a space so we can concatinate css class names
        $classes = 'post-title ';
        $dom_id = null;

        if ( is_user_logged_in() ) {
            $dom_id .= 'edit_title';
        }

        if ( get_the_title() ) {
            $title = get_the_title();
        } else {
            $title = '<em>Click to add a Title</em>';
        }

        return '<h1 class="' . $classes . '" id="'.$dom_id.'">'.$title.'</h1>';
    }


    public function entryContent(){

        // leave a space so we can concatinate css class names
        $classes = "post-content ";
        $dom_id = null;
        $content = '<em>No description available.</em>';

        if ( is_user_logged_in() ) {
            $dom_id = "edit_content";
        } else {
            $dom_id = null;
        }

        if ( get_the_content() ) {
            $content = get_the_content();
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
        }

        return '<div class="'.$classes.'" id="'.$dom_id.'">'.$content.'</div>';
    }
}