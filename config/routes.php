<?php
/**
 * This file handles redirecting of our templates to our given views
 * dir and anything else.
 *
 * Check if the themer has made a theme file in their
 * theme dir, if not load our default.
 *
 * @uses template_redirect http://codex.wordpress.org/Plugin_API/Action_Reference/template_redirect
 */
add_action('template_redirect', function( $params=array() ) {

    global $post_type;

    /**
     * If we cant determine a post type just leave
     */
    // if ( empty( $post_type ) )
    //     return;

    /**
     * We use this later for our site.com/$post_type/new/ form
     * along with our checking for "real files".
     */
    $url = explode( '/', $_SERVER['REQUEST_URI'] );

    /**
     * Support for "real files", we treat the first index
     * as a $post_type and redirect to
     * site.com/phone/
     * views/phone/index.html.php
     */
    if ( empty( $post_type ) ) {
        if ( isset( $url[1] ) ) {
            $post_type = $url[1];
        }
    }

    /**
     * Start routing to the needed admin page
     *
     * This is done for each post_type, i.e. model/page as needed.
     * Example: site.com/events/new
     * Loads the form to add a new event.
     *
     * @note Permissions are NOT done here!!!!
     * @smells, These type of condiditonals always smell bad
     */
    if ( isset( $url[2] ) && $url[2] == 'new' && $url[1] == $post_type ) {

        $admin_file = 'new.html.php';
        $admin_form = '_new.html.php';
        $admin_dir = VIEWS_DIR . $post_type . DS;

        if ( file_exists( $admin_dir . $admin_file ) ) {
            load_template( $admin_dir . $admin_file );
            exit();
        } else {
            $content = "This is your Form to add a NEW {$post_type}.";
            $content .= "\n";
            $content .= "\nPlease ONLY add your FORM in this file as it should be easily used in a page, dialog or where ever.";
            $content .= "\nNOTE PERMISSIONS are NOT handled for you!!! You should handle them on your own in this file!";

            file_put_contents( $admin_dir . $admin_form, $content );
            load_template( $admin_dir . DS . $admin_form );
            exit();
        }
    }


    /**
     * I forgot why I had to add this
     */
    if ( $post_type == 'any' ) {
        $post_type = 'shared';
    }

    $taxonomy = get_query_var('taxonomy');

    /**
     * @note currently we are pushing ALL Archive and Search redirects to shared
     *
     * we could of done this:
     * 'search'  => VIEWS_DIR . $post_type . DS . 'search.html.php',
     * 'archive' => VIEWS_DIR . $post_type . DS . 'search.html.php',
     *
     * @todo add support for true 404, currently we 404 to default
     */
    $template = array(
        'post_type' => $post_type,
        'single'    => VIEWS_DIR . $post_type . DS . 'index.html.php',
        'archive'   => VIEWS_DIR . $post_type . DS . 'archive.html.php',
        'search'    => VIEWS_DIR . 'shared'   . DS . 'search.html.php',
        'taxonomy'  => VIEWS_DIR . $taxonomy  . DS . 'index.html.php',
        'default'   => VIEWS_DIR . 'home'     . DS . 'index.html.php',
        'feeds'     => FEEDS_DIR . 'feed.php'
        );

    /**
     * Support for "themeing" from the
     * wp-content/my-theme/ directory
     */
    $theme_dir = get_stylesheet_directory() . DS;
    $theme_files = array(
        'single' => $theme_dir . 'single-' . $post_type . '.php',
        'archive' => $theme_dir . 'archive-' . $post_type . '.php',
        'taxonomy' => $theme_dir . 'taxonomy-'
        );

    /**
     * If this is a single template, and the post type is
     * our custom post type.
     */
    if ( is_single() && get_query_var('post_type') == $post_type ) {
        if ( file_exists( $theme_files['single'] ) ) {
            load_template( $theme_files['single']  );
            exit();
        } else {
            load_template( $template['single'] );
            exit();
        }
    }

    /**
     * Check if the post type archive for our custom
     * post type is being displayed, i.e.
     * site.com/$post_type/
     */
    elseif ( is_post_type_archive( $post_type ) ) {
        if ( file_exists( $theme_files['archive'] ) ) {
            load_template( $theme_files['archive'] );
            exit();
        } else {
            load_template( $template['archive'] );
            exit();
        }
    }

    elseif ( is_search() ) {
        if ( file_exists( $template['search'] ) ) {
            load_template( $template['search'] );
            exit;
        }
    }

    /**
     * Check if this is a taxonomy, if so try loading
     * a template for EACH term in that taxonomy.
     * i.e., taxonomy-$term.php
     */
    elseif ( is_tax() ) {

        global $wp_query;
        if ( isset( $wp_query->query_vars['taxonomy'] ) ) {
            $queried_tax = $wp_query->query_vars['taxonomy'];
        } else {
            $queried_tax = null;
        }

        /**
         * Since we are now on a taxonomy page the global $post_type
         * variable isn't set. Thus we derive our $post_type from
         * our global $taxonomy
         */
        $tax_obj = get_taxonomy( $taxonomy );
        $post_type = $tax_obj->object_type[0]; // Yes, we TRUST the first index

        /**
         * Derive the post type object, from here we derive the
         * list of taxonoimes.
         */
        $cpt_obj = get_post_types( array( 'name' => $post_type ), 'objects' );
        if ( isset( $cpt_obj[ $post_type ] ) ){
            $taxonomies = $cpt_obj[ $post_type ]->taxonomies;
        }

        foreach( $taxonomies as $tax ){
            if ( $queried_tax == $tax ) {
                if ( file_exists( $theme_files['taxonomy'] . $tax . '.php' ) ) {
                    load_template( $theme_files['taxonomy'] . $tax . '.php' );
                    exit;
                } else {
                    load_template( $template['taxonomy'] );
                    exit;
                }
            }
        }
    }

    else {
        if ( file_exists( $template['single'] ) ) {
            load_template( $template['single'] );
            exit;
        } else {
            load_template( $template['default'] );
            exit;
        }
    }

}, 6);