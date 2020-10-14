<?php


// init add the blorm post type
// https://codex.wordpress.org/Function_Reference/register_post_type
//add_action( 'init',  'create_post_type_blorm');
/*function create_post_type_blorm() {
    register_post_type( 'blormpost',
    array(
        'labels' => array(
            'name' => __( 'Blormposts' ),
            'singular_name' => __( 'Blormpost' )
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'capability_type' => array('blormpost','blormposts'),
        'rewrite'     => array( 'slug' => 'blormpost' ), // my custom slug
        'supports' => array('title', 'editor', 'post-formats'),
        'menu_position' => 1,
        'capabilities' => array(
            // https://stackoverflow.com/questions/3235257/wordpress-disable-add-new-on-custom-post-type
            'create_posts' => true, // false < WP 4.5, credit @Ewout
            'edit_posts' => true,
            'delete_posts' => true,
            'publish_posts ' => true,
            'read_private_posts' => true,
            'read' => true,
            'delete_private_posts' => true,
            'delete_published_posts' => true,
            'delete_others_posts' => true,
            'edit_private_posts' => true,
            'edit_published_posts' => true
        ),
        'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
        'publicly_queryable'  => true,
        //'capability_type'     => 'post',

    )
    );
}*/

/*function blorm_post_add_meta_irl( $post_id ) {

    // If this is just a revision, don't send the email.
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    if ( get_post_type() == 'blormpost' ) {
        add_post_meta( $post_id, "blorm_irl", "the-unique-blorm-irl");
    }

    return $post_id;
}*/
//add_action( 'save_post', 'blorm_post_add_meta_irl' );


//https://developer.wordpress.org/plugins/post-types/working-with-custom-post-types/
add_action( 'pre_get_posts', 'blorm_add_custom_post_types_to_query');
function blorm_add_custom_post_types_to_query($query) {

    // https://developer.wordpress.org/plugins/post-types/working-with-custom-post-types/
    // https://wordpress.stackexchange.com/questions/160814/adding-custom-post-type-to-loop

    $options = get_option("blorm_plugin_options");


    /*    if (checked("show", $options['add_blorm_to_home_loop'], false)) {
            if ( is_home() && $query->is_main_query() ) {
                //if ( is_home() ) {
                $query->set( 'post_type', array(  'post', 'blormpost' ) );
            }
            return $query;
        }

        /*if ( is_home() && $query->is_main_query() ) {
            //if ( is_home() ) {
            $query->set( 'post_type', array(  'post','page', 'blormpost' ) );
        }*/
        return $query;
}




/*
// register custom post type 'my_custom_post_type' with 'supports' parameter
add_action( 'init', 'create_my_post_type' );
function create_my_post_type() {
    register_post_type( 'my_custom_post_type',
        array(
            'labels' => array( 'name' => __( 'Products' ) ),
            'public' => true,
            'supports' => array('title', 'editor', 'post-formats')
        )
    );
}*/

/*apply_filters('found_posts ','blorm_found_posts');
function blorm_found_posts($posts) {

    //var_dump($posts);

    return $posts;
}*/
/*
add_filter( 'post_class', 'new_class', 10,3);
function new_class (array $classes, $class, $id) {
    $newclass = 'blorm-reblogged';

    if ($id == 21) {
        $classes[] = esc_attr( $newclass );
    }

    return $classes;
}*/