<?php
/**
 * Helper Funciton Definition -- We define our helper function by the following:
 * "Any funciton that maybe of use when building a WordPress theme." They should have the following
 * characteristics:
 * 1. Are NOT used in hooks, filters or actions.
 * 2. Should NOT be dependent on each other i.e. can be pulled out and dropped into a functions.php
 *
 * @version 0.0.1
 * @authoer Zane M. Kolnik
 */

/**
 * Retrives the following: 'Posted xxx days ago', no not like that..
 *
 * @package helper
 */
if ( ! function_exists( 'zm_base_posted_on' ) ) :
function zm_base_posted_on() {
    printf( __( ' Posted <span class="%1$s">%2$s</span> ago', 'zm_base' ),
        'zm-base-meta-prep-author',
        sprintf( '<span class="zm-base-date">%1$s</span>',
            esc_attr( human_time_diff( get_the_time('U'), current_time('timestamp') ) )
        )
    );
}
endif;


/**
 * Print the human time diff, i.e. '1 minute agao' of when the site was last updated.
 *
 * @todo this should return recent post by post modfied NOT post created
 */
if ( ! function_exists( 'zm_site_last_updated' ) ) :
function zm_site_last_updated( $post_type=null ) {

    $updated = wp_get_recent_posts( array( 'numberposts' => 1, 'post_type' => $post_type ) );
    $updated = strtotime($updated[0]['post_modified']);

    print esc_attr( human_time_diff( $updated, current_time('timestamp') ) );
}
add_action( 'zm_site_last_updated', 'zm_site_last_updated', 15, 1 );
endif;

/**
 * Prints Posted by with author avatar and link to author archive page
 *
 * @package helper
 */
if ( ! function_exists( 'zm_base_posted_by' ) ) :
function zm_base_posted_by() {
    printf( __( '%1$s <span class="%2$s">%3$s</span> ', 'zm_base' ),
        sprintf( '<span class="zm-base-author-image zm-base-vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a> </span>',
            get_author_posts_url( get_the_author_meta( 'ID' ) ),
            sprintf( esc_attr__( 'View all posts by %s', 'zm_base' ), get_the_author() ),
            get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 40 ) )
        ),
        'zm-meta-prep-author',
        sprintf( '<span class="zm-base-author-nickname zm-base-vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a> </span>',
            get_author_posts_url( get_the_author_meta( 'ID' ) ),
            sprintf( esc_attr__( 'View all posts by %s', 'collection' ), get_the_author() ),
            get_the_author_meta('nickname')
        )
    );
}
endif;

/**
 * Prints ONLY the author image with link to author archive
 *
 * @package helper
 */
if ( ! function_exists( 'zm_base_author_avatar' ) ) :
function zm_base_author_avatar() {

    $content = sprintf( '<span class="zm-base-author-image zm-base-vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a> </span>',
            get_author_posts_url( get_the_author_meta( 'ID' ) ),
            sprintf( esc_attr__( 'View all posts by %s', 'zm_base' ), get_the_author() ),
            get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 40 ) )
        );
    $css = 'zm-meta-prep-author';

    printf( __( '%1$s <span class="%2$s"></span> ', 'zm_base' ), $content, $css );
}
endif;

/**
 * Prints Posted in Category and Tags
 *
 * @package helper
 */
if ( ! function_exists( 'zm_base_posted_in' ) ) :
function zm_base_posted_in() {

    // Retrieves tag list of current post, separated by commas.
    $tag_list = get_the_tag_list( '', ', ' );

    if ($tag_list) {
        $posted_in = __('<span class="zm-base-posted-in"> &nbsp;Posted in %1$s </span> Tags %2$s', 'collection');
    } elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
        $posted_in = __('<span class="zm-base-posted-in"> Posted in %1$s </span> ', 'collection');
    } else {
        $posted_in = null;
    }

    // Prints the string, replacing the placeholders.
    printf(
        $posted_in,
        get_the_category_list( ', ' ), // 1
        $tag_list,
        get_the_category_list( ', ' ) // 1
    );
}
endif;

/**
 * Retrive user's information based on a param
 *
 * @package helper
 * @param int $user_id
 * @param string $param
 */
if ( ! function_exists( 'zm_base_userdata' ) ) :
function zm_base_userdata($user_id=1, $param=null){

    if ( $param == null )
        exit('need param');

    /** @todo check list: http://codex.wordpress.org/Function_Reference/get_userdata */
    $user_info = get_userdata( $user_id );
    echo $user_info->$param ;
}
endif;

/**
 * Print the src to an image given a size. Must be used in the_loop!
 *
 * @package helper
 * @param $size
 */
if ( ! function_exists( 'zm_base_image_src' ) ) :
function zm_base_image_src( $size=null ) {
    /** @todo check for post->ID */
    /* @todo check against global image sizes */
    if ( $size == null )
        $size = 'large';

    $src = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );
    print $src[0];
}
endif;

/**
 * Prints semantically structured term list for a given POST.
 *
 * @package helper
 * @param int $id=0,
 * @param string $taxonomy, $before, $sep, $after
 */
if ( ! function_exists( 'zm_base_get_the_term_list' ) ) :
function zm_base_get_the_term_list( $post_id=null, $taxonomy=null, $before = '', $sep = ', ', $after = '' ) {


    if ( is_array( $post_id ) )
        extract( $post_id );

    if ( ! $post_id ) {
        global $post;
        $post_type = $post->post_type;
        $post_id = $post->ID;
    }


    $taxonomy = strtolower( trim( str_replace( " ", "-", $taxonomy ) ) );
    $terms = get_the_terms( $post_id, $taxonomy );
    $my_link = null;

    if ( empty( $extra_class ) ) {
        $extra_class = null;
    }

    if ( $terms && !is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {

                if ( isset( $link ) && $link == 'javascript://' ) {
                    $my_link = 'javascript://';
                } elseif ( isset( $link ) && $link == 'anchor' ) {

                    if ( !$post_type )
                        die( 'I need a post type for anchor' );

                    $my_link = home_url() . '/' . get_post_type_object( $post_type )->rewrite['slug'] .  '/#/' . $term->taxonomy . '_'. $term->slug;
                } elseif ( isset( $link ) && $link == 's' ) {
                    $my_link = home_url() . '/?s=' . $term->name;
                } else {
                    $my_link = get_term_link( $term, $taxonomy );
                }

                if ( is_wp_error( $my_link ) )
                    return $my_link;

                $title = sprintf( '%1$s', sprintf( __("%s"), $term->name) );
                $count = sprintf( '%d',  sprintf( __("%d"), $term->count ) );
                $description = sprintf( '%s', $term->description );

                if ( isset( $link ) && $link == 'none' ) {
                    $term_links[] = '<span class="zm-base-'. $taxonomy.'-'.$term->slug .' ' . $extra_class. '">' . $term->name . '</span>';
                } else {
                    $term_links[] = '<a href="' . $my_link . '" title="View all ' . $count . " in " . $title . '" rel="'.$term->taxonomy . '_' . $term->slug.'" class="zm-base-'. $taxonomy.'-'.$term->slug .' ' . $extra_class. '" data-content="'. $description .'">' . $term->name . '</a>';
                }
        }
        $term_links = apply_filters( "term_links-$taxonomy", $term_links );
        return $before . join( $sep, $term_links ) . $after;
    }
}
endif;

/**
 * This funtction will return a 'well' structured list of links for a given taxonomy
 *
 * @package helper
 * @param string $taxonomy
 */
if ( ! function_exists( 'zm_base_list_terms' ) ) :
function zm_base_list_terms( $taxonomy ) {

    global $post;

    if ( is_array( $taxonomy ) )
        extract( $taxonomy );

    $taxonomy = strtolower( trim( str_replace( " ", "-", $taxonomy ) ) );

    $terms = get_terms( $taxonomy, array('hide_empty' => 1 ) );

    if ( !isset( $label ) )
        $label = $taxonomy;

    if ( ! isset( $auto_expando ) )
        $auto_expando = false;

    if ( !$terms )
        return;

    $i = 0;
    $len = count( $terms );
    $html = $first = $last = $my_link = null;
    $trigger = 6; // Number to trigger "auto expando"

    if ( empty( $extra_class ) ) {
        $extra_class = null;
    }

    /** @todo -- add support for rss link */
    // very fucking usefull http://php.net/manual/en/types.comparisons.php
    if ( is_wp_error( $terms ) )
        return;

    foreach( $terms as $term ) {

        if ( isset( $link ) && $link == 'javascript://' )
            $my_link = 'javascript://';
        elseif ( isset( $link ) && $link == 'anchor' )
            $my_link = home_url() . '/' . get_post_type_object( $post_type )->rewrite['slug'] . '/#/' . $term->taxonomy . '_'. $term->slug;
        else
            $my_link = get_term_link( $term->slug, $term->taxonomy );

        // First
        if ( $i == 0 )
            $html .= '<div class="zm-base-title ' . $term->taxonomy . '">' . $label .'</div>';

        // Open our wrapper on the number from auto expando
        if ( $len >= $trigger ) {
            $tmp = $auto_expando - 1;

            if ( $i == $tmp ) {
                $html .= '<div class="auto-expando-container">';
                $html .= '<div class="auto-expando-target" style="display: none;">';
            }
        }

        $title = sprintf( '%1$s', sprintf( __("View all %s"), $term->name ) );
        $description = sprintf( '%1$s', $term->description );

        $html .= '<div class="zm-base-item ' . $term->taxonomy . '-container">';
        $html .= '<a href="' . $my_link . '" title="'.$title.'" data-original-title="'.$title.'" data-content="' . $description . '" rel="' . $term->taxonomy . '_' . $term->slug . '" class="zm-base-' . $term->taxonomy .'-'.$term->slug . ' ' . $extra_class . '">' . $term->name . '</a>';
        $html .= '<span class="zm-base-count">' . $term->count . '</span>';
        $html .= '</div>';

        // auto expando and last add closing div
        if ( $len >= $trigger ) {
            if ( $i == $len - 1 ) {
                $html .= '</div>';
                $html .= '<a href="#" class="auto-expando-handle">More</a>';
                $html .= '<span class="arrow"></span>';
                $html .= '</div>';
            }
        }

        $i++;
    }

    /** @todo make sure term used as class name is 'clean', i.e. no spaces! all lower case. */
    print '<div class="zm-base-list-terms-container">'.$html.'</div>';
}
endif;

/**
 * Determine the current term, idk fucking no what I'm doing.
 *
 * @package helper
 * @param string $taxonomy
 */
if ( ! function_exists( 'zm_base_current_term' ) ) :
function zm_base_current_term( $taxonomy ) {

    global $post, $wp_query;
    $current_term = null;

// print_r( $wp_query->posts[0]->ID );

    /** @todo better way to combine conditional */
    if ( $post ) {
        $my_terms = get_the_terms( $post->ID, $taxonomy );
        if ( $my_terms ) {
            if ( is_wp_error( $my_terms ) ) {
                return;
            }
            foreach( $my_terms as $my_term ) {
                $current_term = $my_term->name;
            }
        }
    }

    return $current_term;
}
endif;

/**
 * This mimics get_terms, but has shows error messages if we have one.
 *
 * @package helper
 * @param string $taxonomy
 * @todo $args should be a params, or look into using add_filter
 */
if ( ! function_exists( 'zm_base_get_terms' ) ) :
function zm_base_get_terms( $taxonomy ) {

    /** All Terms */
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
//        exit( "Opps..." . $terms->get_error_message() . "..dog, cmon, fix it!" );
    }

    return $terms;
}
endif;

/**
 * Build an option list of Terms based on a given Taxonomy.
 *
 * @package helper
 * @uses zm_base_get_terms to return the terms with error checking
 * @param string $taxonomy
 * @param mixed $value, the value to be used in the form field, can be term_id or term_slug
 */
if ( ! function_exists( 'zm_base_build_options' ) ) :
function zm_base_build_options( $taxonomy=null, $value=null ) {

    if ( is_null ( $value ) )
        $value = 'term_id';

    if ( is_array( $taxonomy ) )
        extract( $taxonomy );

    // white list
    if ( empty( $prepend ) )
        $prepend = null;

    if ( empty( $extra_data ) )
        $extra_data = null;

    if ( empty( $extra_class ) )
        $extra_class = null;

    if ( ! empty( $multiple ) ) {
        $multiple = 'multiple="multiple"';
    } else {
        $multiple = false;
    }

    if ( !isset( $label ) )
        $label = $taxonomy;

    if ( empty( $post_id ) )
        $post_id = null;

    /** All Terms */
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
//        exit( "Opps..." . $terms->get_error_message() . "..dog, cmon, fix it!" );
        $terms = false;
    }

    // This hackiness is coming from...
    // we might be on a single page or our id is
    // being passed in explictiyly
    if ( is_single() ) {
        global $post;
        $current_terms = get_the_terms( $post->ID, $taxonomy );
        $index = null;
    } else {
        if ( ! empty( $post_id ) ) {
            $current_terms = get_the_terms( $post_id, $taxonomy );
            $index = 0;
        }
    }

    $temp = null;
    ?>
    <?php if ( $terms ) : ?>
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container <?php echo $taxonomy; ?>-container">
    <label class="zm-base-title"><?php echo $label; ?></label>
    <select name="<?php echo $taxonomy; ?><?php if ( $multiple=='multiple="multiple"') print '[]'; ?>" <?php echo $multiple; ?> <?php echo $extra_data; ?> class="<?php echo $extra_class; ?>" id="" <?php echo $multiple; ?>>
        <?php // Support for placeholder ?>
        <option></option>
        <?php foreach( $terms as $term ) : ?>
            <?php if ( ! empty( $current_terms )) : ?>
            <?php for ( $i=0, $count=count($current_terms); $i <= $count; $i++ ) : ?>
                <?php

                // Check if we have an index, if we do start our loop
                // using the term id because our current_terms array
                // will be index based on the term id.

                // This is because we are on the single post page
                // if not it might be an ajax request or the id is
                // being passed in explictiyly
                if ( is_null( $index ) )
                    $tmp_index = $term->term_id;
                else
                    $tmp_index = 0;

                if ( $current_terms[ $tmp_index ]->name ) {
                    $temp = $current_terms[ $tmp_index ]->name;
                } else {
                    $temp = null;
                }
                ?>
            <?php endfor; ?>
            <?php endif; ?>
            <?php $term->name == $temp ? $selected = 'selected="selected"' : $selected = null; ?>
            <option
            value="<?php echo $prepend; ?><?php echo $term->$value; ?>"
            data-value="<?php echo $term->slug; ?>"
            class="taxonomy-<?php echo $taxonomy; ?> term-<?php echo $term->slug; ?> <?php echo $taxonomy; ?>-<?php echo $term->term_id; ?>"
            <?php echo $selected; ?>>
            <?php echo $term->name; ?>
            </option>
        <?php endforeach; ?>
    </select>
    </fieldset>
    <?php endif; ?>
<?php }
endif;

/**
 * Build radio buttons or checkboxes of Terms based on a given Taxonomy.
 *
 * @package helper
 * @uses zm_base_get_terms to return the terms with error checking
 * @uses zm_base_current_term() to get the current term for post type currently being viewed
 * @param string $taxonomy
 * @param string $value, The value to be used in the 'name' field of the form
 */

/* Usage

// Array based args, Display a list of terms as radio buttons for a single taxonomy
$args = array(
    'taxonomy'=> 'type',
    'type'=> 'radio', // input|radio|checkbox
    'default' => 'Personal', // slug of term to be the default
    'label' => 'Category', // $taxonomy(default)
    'value' => 'name', // $prepend[name|slug|term_id(default)]
    'show_count' => true, // Show count posts in each term, default(false)
    'prepend' => 'entry-fee-' // prepended to the value, null(default)
    );
zm_base_build_input( $args );

// Machine gun them out, non-array params
foreach ( $your_array_of_taxonomies as $tax ) {
    <?php zm_base_build_input( $tax->name );
}
*/
if ( ! function_exists( 'zm_base_build_input' ) ) :
function zm_base_build_input( $taxonomy=null ) {

    /**
     * If your passing in an array as the param, I'll assume those are arguments.
     */
    if ( is_array( $taxonomy ) )
        extract( $taxonomy );

    if ( !isset( $label ) )
        $label = $taxonomy;

    // @todo need to merge
    $defaults = array(
        'value' => 'term_id'
    );

    if ( empty( $prepend ) )
        $prepend = null;

    if ( empty( $current_term ) )
        $current_term = null;

    if ( empty( $show_count ))
        $show_count = null;

    extract( $defaults );

    /** All Terms */
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
       wp_die( "Opps..." . $terms->get_error_message() );
    }

    /**
     * @todo add support for array of defaults
     */
    if ( ! empty( $default ) )
        $current_term = $default;
    else
        $current_term = zm_base_current_term( $taxonomy );

    // @fix –- add loop for current state
    if ( ! empty( $post ) ) {
        $current_terms = get_the_terms( $post->ID, $taxonomy );
    }

    ?>
    <?php if ( $terms ) : ?>
    <fieldset class="<?php echo $taxonomy; ?>-container">
    <legend class="zm-base-title"><?php echo $label; ?></legend>
    <?php foreach( $terms as $term ) : ?>
        <?php /** Some cryptic short hand true:false */ ?>
        <?php $current_term == $term->name ? $selected = 'checked=checked' : $selected = null; ?>
        <?php
        /**
         * Make sure label and id match, so when user clicks on word it checks the box
         */
        ?>
        <label
            for="<?php echo $taxonomy; ?>-<?php echo $term->slug; ?>"
            class="zm-base-<?php print $taxonomy; ?>-<?php print $term->slug; ?>">
        <input
            type="<?php echo $type; ?>"
            value="<?php echo $prepend; ?><?php echo $term->$value; ?>"
            class="taxonomy-<?php echo $taxonomy; ?> term-<?php echo $term->slug; ?> <?php echo $taxonomy; ?>-<?php echo $term->term_id; ?>"
            id="<?php echo $taxonomy; ?>-<?php echo $term->slug; ?>"
            name="<?php echo $taxonomy; ?>[]"
            <?php
            /**
             * @todo add loop to dynamicall add new data-attributes
             */
            ?>
            data-value="<?php echo $term->slug; ?>"
            data-taxonomy="<?php echo $taxonomy; ?>"
            data-name="<?php echo $term->name; ?>"
            <?php echo $selected; ?> />
        <?php echo $term->name; ?>
        <?php if ( $term->count > 0 && $show_count ) : ?><span class="zm-base-count">Count<?php echo $term->count; ?></span><?php endif; ?>
        </label>
    <?php endforeach; ?>
    </fieldset>
    <?php endif; ?>
<?php }
endif;

/**
 * Build checkbox of Terms based on a given Taxonomy.
 *
 * @package helper
 * @deprecated use zm_base_build_input()
 * @uses zm_base_get_terms to return the terms with error checking
 * @uses zm_base_current_term() to get the current term for post type currently being viewed
 * @param string $taxonomy
 * @param string $value, The value to be used in the 'name' field of the form
 */
if ( ! function_exists( 'zm_base_build_checkbox' ) ) :
function zm_base_build_checkbox( $taxonomy=null, $value='term_id' ) {

    /** All Terms */
    $args = array(
        'orderby' => 'name',
        'hide_empty' => false
         );

    $terms = get_terms( $taxonomy, $args );

    if ( is_wp_error( $terms ) ) {
//        exit( "Opps..." . $terms->get_error_message() . "..dog, cmon, fix it!" );
        $terms = false;
    }

    $current_term = zm_base_current_term( $taxonomy );

    /** @todo the below markup should be pulled out into a 'view' */
    ?>
    <fieldset class="zm-base-<?php echo $taxonomy; ?>-container"><legend class="zm-base-title"><?php echo $taxonomy; ?></legend>
    <?php foreach( $terms as $term ) : ?>
        <?php /** Some cryptic short hand true:false */ ?>
        <?php $current_term == $term->name ? $selected = 'checked=checked' : $selected = null; ?>
        <label for="<?php echo $term->$value; ?>">
        <input type="checkbox" value="<?php echo $term->$value; ?>" id="<?php echo $term->term_id; ?>" my_term_id="<?php echo $term->term_id; ?>" name="<?php echo $taxonomy; ?>[]" <?php echo $selected; ?> />
        <?php echo $term->name; ?></label>
    <?php endforeach; ?>
    </fieldset>
<?php }
endif;

/**
 * Retrive the next post type modified from TwentyTen
 *
 * @package helper
 */
if ( ! function_exists( 'zm_next_post' ) ) :
function zm_next_post() {
    global $post;

    // Retrieve next post link that is adjacent to current post.
    $nextPost = get_next_post( false );

    // Check to make sure we have a previous post
    if ( !empty( $nextPost ) ) {
        if ( function_exists( 'get_the_post_thumbnail' ) ) {
            $nextThumbnail = get_the_post_thumbnail( $nextPost->ID, 'thumbnail' );
        }

        /** @todo markup should be 'cleaner' */
        if ( isset( $nextThumbnail) && !empty( $nextThumbnail ) ) {
            echo '<div class="image">';
            next_post_link( '%link', "$nextThumbnail", false );
            echo '</div>';
        }

        print '<div class="next-container">';
        print '<span class="title">';
        next_post_link('%link', '%title');
        print '</span>';

        // Get our list of catgeories
        if (get_the_category($nextPost->ID)) {

            // Returns an array of objects
            $categories = get_the_category( $nextPost->ID );

            $catTotal = count($categories);
            $i = 0;

            print 'Category ';
            for ($i; $i < $catTotal; $i++) {
                print  '<a href="'.get_category_link($categories[$i]->cat_ID).'">'.$categories[$i]->cat_name.'</a> ';
            }
            print '<span class="posted-on">Posted on ';
            the_modified_time('m/d/ Y');
            print '</span>';
        } else {
            $category = 'no';
        }
        print '</div>';
    }
}
endif;

/**
 * Retrive the previous post type modified from TwentyTen
 *
 * @package helper
 */
if ( ! function_exists( 'zm_previous_post' ) ) :
function zm_previous_post() {
    global $post;

    // Retrieve next post link that is adjacent to current post.
    $prevPost = get_previous_post(false);

    // Check to make sure we have a previous post
    if (!empty($prevPost)) {
        if (function_exists('get_the_post_thumbnail')) {
            $prevthumbnail = get_the_post_thumbnail($prevPost->ID, 'thumbnail');
        }

        if (isset($prevthumbnail) && !empty($prevthumbnail)) {
            echo '<div class="image">';
            previous_post_link('%link',"$prevthumbnail", false);
            echo '</div>';
        }

        // Probally a better way to do this, but f-it, it works
        print '<div class="previous-container">';
        print '<span class="title">';
        previous_post_link('%link', '%title');
        print '</span>';

        // Get our list of catgeories
        if (get_the_category($prevPost->ID)) {

            // Returns an array of objects
            $categories = get_the_category($prevPost->ID);

            $catTotal = count($categories);
            $i = 0;

            print 'Category ';

            for ($i; $i < $catTotal; $i++) {
                print  '<a href="'.get_category_link($categories[$i]->cat_ID).'">'.$categories[$i]->cat_name.'</a> ';
            }
            print '<span class="posted-on">Posted on ';

            the_modified_time('m/d/ Y');
            print '</span>';
        } else {
            $category = 'no';
        }
            print '</div>';
    }
}
endif;

/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own collection_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
if ( ! function_exists( 'zm_comment' ) ) :
function zm_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case '' :
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <div id="comment-<?php comment_ID(); ?>">
        <div class="comment-author vcard">
            <?php echo get_avatar( $comment, 40 ); ?>
            <?php printf( __( '%s', 'collection' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
        </div> <!-- .comment-author .vcard -->
        <?php if ( $comment->comment_approved == '0' ) : ?>
            <em><?php _e( 'Your comment is awaiting moderation.', 'collection' ); ?></em>
            <br />
        <?php endif; ?>

        <div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
            <?php
                /* translators: 1: date, 2: time */
                printf( __( '%1$s at %2$s', 'collection' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'collection' ), ' ' );
            ?>
        </div><!-- .comment-meta .commentmetadata -->

        <div class="comment-body"><?php comment_text(); ?></div>

        <div class="reply">
            <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        </div><!-- .reply -->
    </div><!-- #comment-##  -->

    <?php
            break;
        case 'pingback'  :
        case 'trackback' :
    ?>
    <li class="post pingback">
        <p><?php _e( 'Pingback:', 'collection' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'collection'), ' ' ); ?></p>
    <?php
            break;
    endswitch;
}
endif;

/**
 * Provide a list of links to the image sizes.
 *
 * @package helper
 */
if ( ! function_exists( 'zm_image_download' ) ) :
function zm_image_download() {

    global $post;

    $meta_array = wp_get_attachment_metadata( $post->ID );
    $i = 0;
    $len = count( $meta_array['image_meta'] );

    foreach ( $meta_array['sizes'] as $key => $value ) {

        if ($i == 0) {
            print '<ul class="zm-meta">';
            $class = 'zm-item zm-item-first';
            the_title( '<li><h3 class="title">Download sizes for the <em>','</em> image.</h3></li>' );
        }  else {
            $class = 'zm-item';
        }

        $link = wp_get_attachment_image_src( get_post_thumbnail_id(), $key );

        /** @todo maybe printf this? */
        print "<li class='{$class}'><a href='{$link[0]}' target='_blank' title='Download the {$key} size'>{$key}</a> {$value['height']} x {$value['width']}</li>";

        if ( $i == $len - 1 ) {
            print '</ul>';
        }

        $i++;
    }
}
endif;

/**
 * Provide a list of exif information for images
 *
 * @package helper
 */
if ( ! function_exists( 'zm_image_exif' ) ) :
function zm_image_exif() {
    global $post;

    $meta_array = wp_get_attachment_metadata( $post->ID );
    $i = 0;
    $len = count( $meta_array['image_meta'] );

    foreach ( $meta_array['image_meta'] as $key => $value ) {

        if ($i == 0) {
            print '<ul class="zm-meta">';
            $class = 'zm-item zm-item-first';
            the_title('<li><h3 class="title">Exif data for the <em>','</em> image.</h3></li>');
        }  else {
            $class = 'item';
        }

        if ( $key == 'created_timestamp' ) {
            $key = 'created';
            $value = date('F, j, Y', $value);
        }

        if ( $key == 'focal_length' ) {
            $key = 'focal length';
        }

        if ( $key == 'shutter_speed' ) {
            $key = 'shutter speed';
        }

        print "<li class='{$class}'><span class='key'>{$key}</span> <span class='value'>{$value}</span></li>";

        if ( $i == $len - 1 ) {
            print '</ul>';
        }

        $i++;
    }
}
endif;

/**
 * Creats a 'Back to Post' link
 *
 * @package helper
 */
if ( ! function_exists( 'zm_back_to_post_link' ) ) :
function zm_back_to_post_link() {
    global $post; ?>
    <a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php esc_attr( printf( __( 'Return to %s', 'zm_base' ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery">
    <?php printf( __( '<span class="meta-nav">&larr; </span> Return to: %s', 'collection' ), get_the_title( $post->post_parent ) ); ?></a>
<?php }
endif;

/**
 * Truncate a content.
 *
 * @package helper
 * @param string $content, The content we want to truncate
 * @param int $size, The size we want to truncate to
 */
if ( ! function_exists( 'zm_base_truncate' ) ) :
function zm_base_truncate( $content, $size=35 ) {
    global $post;
    $content = get_post(get_post_thumbnail_id())->post_content;
    $length = strlen( $content );
    echo substr( $content, 0, $size);
    if ( $length > $size ) echo "...";
}
endif;

/**
 * Returns a linked list of terms for a given taxonomy
 *
 * @package helper
 * @prama string $zm_term
 */
if ( ! function_exists( 'zm_term_links' ) ) :
function zm_term_links( $zm_term=null) {

    // Set our global, we'll use this to check the "current" state
    global $wp_query;

    // Check if we have a term
    if ( !isset( $zm_term ) )
       die('no term, gtfo');

    // Does our term exists
    if ( !taxonomy_exists( $zm_term ) )
        die('taxo no exsito, gtfo');

    // Our object of terms
    $terms = get_terms( $zm_term );
    $x = 1;
    $count = count( $terms );
    $html = null;
    $class = '';

    foreach ($terms as $term) {

        // First
        if ( $x == 1 ) {
            $html .= '<li class="zm-title">'. $zm_term . '</li>';
            $bar = '<span class="zm-bar">|</span>';
        // "Middle"
        } elseif ( $x == $count ) {
            $bar = '';
        // Last
        } else {
            $bar = '<span class="zm-bar">|</span>';
        }

        // Determine if the user is currently viewing our term
        if ( $wp_query->query_vars['term'] == $term->slug ) {
            // Set a class for styling
            $class = 'zm-current';
            $term_html = $term->name;
        } else {
            // If this is NOT the current term the we wrap the term in an "a" tag
            $term_html = '<a href="'.get_term_link( $term->slug , $zm_term ).'">'.$term->name.'</a>';
        }

        $html .= '<li class="'.$class. ' zm-'. $term->slug.'">' . $term_html . $bar . '</li>';
        $x++;
    }

    print '<ul>' . $html . '</ul>';
}
endif;

/**
 * Procedural code designed to be used at the "template" level.
 *
 * This file should only contain procedural code that is NOT part of any Hook/Action.
 * Please place Hook/Action funcitons in the apropiate file.
 */

/**
 * Prints the "age" of a Task from the current date to when it was posted
 */
if ( ! function_exists( 'tt_task_age' ) ) :
function tt_task_age() {
    printf( __( '<span class="meta"><span class="%1$s">%2$s</span></span>', 'Task' ),
        'meta-prep-author',
        sprintf( '<span class="date">%1$s</span>',
            esc_attr( human_time_diff( get_the_time('U'), current_time('timestamp') ) )
        )
    );
}
endif;

/**
 * Prints a json dataset of Tasks
 *
 * @param $post_type = string
 * @param $taxonomies = array()
 */
if ( ! function_exists( 'zm_cpt_json_feed' ) ) :
function zm_cpt_json_feed( $post_type, $taxonomies=array(), $status=null ) {

    if ( empty( $post_type ) || empty( $taxonomies ) )
        die( 'I need a post type and a array of taxonomies' );

    global $wp_query, $post;
    $my_query = null;
    $types = array();

    if ( empty( $status ) ) {
        $status = 'publish';
    }

    $args = array(
       'post_type' => $post_type,
       'post_status' => $status
    );

    $my_query = new WP_Query( $args );

    while ( $my_query->have_posts() ) : $my_query->the_post();

        $types[$post->ID] = array(
            "id" => $post->ID,
            "title" => $post->post_title
            );

        foreach ( $taxonomies as $taxonomy ) {
            $term = wp_get_object_terms( $post->ID, $taxonomy );

            if ( !is_wp_error( $term ) || empty( $term )) {
                $term = ( $term ) ? $term[0]->slug : 'none' ;
                $types[$post->ID][$taxonomy] = $term;
            }
        }

    endwhile;
    print '<script type="text/javascript">var _data = ' . json_encode( $types ) . '</script>';
}
endif;


if ( ! function_exists( 'zm_twitter_bootstrap' ) ) :
function zm_twitter_bootstrap(){

    // Load twitter bootstrap
    wp_enqueue_style( 'twitter-bootstrap',  plugin_dir_url( __FILE__ ) . 'twitter-bootstrap/bootstrap.css', '', 'all' );

    // Register some twitter bootstrap
    wp_enqueue_script( 'bootstrap-twipsy',  plugin_dir_url( __FILE__ ) . 'twitter-bootstrap/js/bootstrap-twipsy.js',  array('jquery'), '1.4.0' );
    wp_enqueue_script( 'bootstrap-popover', plugin_dir_url( __FILE__ ) . 'twitter-bootstrap/js/bootstrap-popover.js', '', '1.4.0' );
    wp_enqueue_script( 'bootstrap-modal',   plugin_dir_url( __FILE__ ) . 'twitter-bootstrap/js/bootstrap-modal.js',   array('jquery'), '1.4.0' );
    wp_enqueue_script( 'bootstrap-alerts',  plugin_dir_url( __FILE__ ) . 'twitter-bootstrap/js/bootstrap-alerts.js',  array('jquery'), '1.4.0' );
}
add_action( 'load_twitter_bootstrap', 'zm_twitter_bootstrap' );
endif;


/**
 * Builds a select box with options representing the Month.
 * List all 12 months in Long format, i.e. December. The default
 * value is the current Month, or the default passed in.
 *
 * @param $params mapped array.
 * @param $form = name of form this should be tied to, its used to
 *  derive the elementIDs
 */
if ( ! function_exists( 'zm_month_option') ) :
function zm_month_option( $default=array() ) {

    extract( $default );

    if ( ! empty( $default[0] ) )
        $default = $default[0];
    else
        $default = date("F");

?><select name="my_month" id="<?php print $form; ?>_post_month_create" style="width: 100px;" class="chzn-select">
    <?php for ( $i = 1; $i <= 12; $i++ ) : ?>
        <?php $date = date("F", mktime(0, 0, 0, $i+1, 0, 0)); ?>
        <?php if ( $date == $default ) : ?>
            <?php $checked="selected='selected'"; ?>
        <?php else : ?>
            <?php $checked = null; ?>
        <?php endif; ?>
        <option <?php print $checked; ?> value="<?php print $date; ?>">
        <?php print $date; ?>
        </option>
    <?php endfor; ?>
</select><?php }
add_action( 'zm_month_option', 'zm_month_option', 10, 1);
endif;


/**
 * Builds a select box with options representing the Date.
 * Prints upto 31 no matter what. Default value is the current
 * Date, i.e. 1 or 31 or the default passed in.
 *
 * @param $params mapped array.
 * @param $form = name of form this should be tied to, its used to
 *  derive the elementIDs
 */
if ( ! function_exists( 'zm_day_option' ) ) :
function zm_day_option( $default=array()) {

    extract( $default );

    if ( ! empty( $default[0] ) )
        $default = $default[0];
    else
        $default = date("d");

?><select name="my_day" id="<?php print $form; ?>_post_day" style="width: 100px;" class="chzn-select">
    <?php for ( $date = 1; $date <= 31; $date++ ) : ?>
    <?php if ( $date == $default ) : ?>
        <?php $checked="selected='selected'"; ?>
    <?php else : ?>
        <?php $checked = null; ?>
    <?php endif; ?>
    <option <?php print $checked; ?> value="<?php print $date; ?>">
    <?php print $date; ?></option>
    <?php endfor; ?>
</select><?php }
add_action( 'zm_day_option', 'zm_day_option', 10, 1 );
endif;


/**
 * Builds a select box with options representing the year.
 * It only goes 1+- year from the current year. The default
 * value is the current Year, or the default passed in.
 *
 * @param $params mapped array.
 * @param $form = name of form this should be tied to, its used to
 *  derive the elementIDs
 */
if ( ! function_exists( 'zm_year_option' ) ) :
function zm_year_option( $default=array()) {

    extract( $default );

    if ( ! empty( $default[0] ) )
        $default = $default[0];
    else
        $default = date("Y");

?><select name="my_year" id="<?php print $form; ?>_post_year" style="width: 100px;" class="chzn-select">
    <?php for ( $i = date("Y"); $i <= 2014; $i++ ) : ?>
        <?php $date = date("Y", mktime(0, 0, 0, 0, 0, $i)); ?>
        <?php if ( $date == $default ) : ?><?php $checked="selected='selected'"; ?><?php else : ?><?php $checked = null; ?><?php endif; ?>
        <option <?php print $checked; ?> value="<?php print $date; ?>"><?php print $date; ?></option>
    <?php endfor; ?>
</select><?php }
add_action( 'zm_year_option', 'zm_year_option', 10, 1 );
endif;


/**
 * loads a template from a specificed path
 *
 * @package Ajax
 *
 * @uses load_template()
 * @todo move to zm-ajax or something?
 */
function zm_load_template() {

    if ( ! isset( $_POST['template'] ) )
        return;

    $file = $_POST['template'];
    $path = dirname( dirname( plugin_dir_path( __FILE__ ) ) );

    $template = $path . '/' . $file;

    if ( $template == null )
        wp_die( 'Yo, you need a template!');

    load_template( $template );
    die();
} // zm_load_template
add_action( 'wp_ajax_nopriv_zm_load_template', 'zm_load_template' );
add_action( 'wp_ajax_zm_load_template', 'zm_load_template' );