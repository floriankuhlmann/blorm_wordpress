<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 03.11.18
 * Time: 09:02
 */

class SetupActions
{

    private static $blorm_cpt = "blormpost";
    private static $single_template =	"blormpost_single";			//name der single template datei ohne .php endung
    private static $archive_template =	"blormpost_archive";

    private static $add_blorm_to_home = true;
    /*
     *
     * post type
     * https://codex.wordpress.org/Function_Reference/register_post_type
     */

    /*static function create_post_type_blorm() {
        register_post_type( 'blormpost',
            array(
                'labels' => array(
                    'name' => __( 'Blorm' ),
                    'singular_name' => __( 'Blorm_Reblog x' )
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite'     => array( 'slug' => 'blormpost' ), // my custom slug
                'supports' => array('title', 'editor', 'post-formats'),
                'menu_position'         => 5,
                'capabilities' => array(
                    // https://stackoverflow.com/questions/3235257/wordpress-disable-add-new-on-custom-post-type
                    //'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
                    //'edit_posts' => 'do_not_allow',
                ),
                'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'show_in_rest' => true,
            )
        );
    }

    static function blorm_add_custom_post_types_to_query($query) {

        // https://developer.wordpress.org/plugins/post-types/working-with-custom-post-types/
        // https://wordpress.stackexchange.com/questions/160814/adding-custom-post-type-to-loop

        if (!SetupActions::$add_blorm_to_home) {
            return $query;
        }

        if ( is_home() && $query->is_main_query() ) {
        //if ( is_home() ) {
            $query->set( 'post_type', array(  'post','page', 'blormpost' ) );
        }
        return $query;
    }

*/

    /* The filter function */
    static function blorm_template_controller( $template ) {

        // https://pixelbar.be/blog/wordpress-how-to-eigene-templates/

        // Post ID
        $post_id = get_the_ID();

        // For all other CPT
        if ( get_post_type( $post_id ) == SetupActions::$blorm_cpt && is_archive() ) {
            //echo "1";
            $template = SetupActions::blorm_get_template_hierarchy( SetupActions::$archive_template );
        } else if ( get_post_type( $post_id ) == SetupActions::$blorm_cpt ) {
            echo "2";
            $template = SetupActions::blorm_get_template_hierarchy( SetupActions::$single_template );
        }
        echo $template;
        return $template;
    }

    /* The function that checks if the file exists in the theme folder. */
    static function blorm_get_template_hierarchy( $template ) {
        //var_dump($template);

        // Get the template slug
        $template_slug = rtrim( $template, '.php' );
        $template = $template_slug . '.php';

        //var_dump($template);

        // Check if a custom template exists in the theme folder, if not, load the plugin template file
        if ( $theme_file = locate_template( $template ) ) {
            $file = $theme_file;
        } else {
            $file = PLUGIN_BLORM_PLUGIN_DIR  . 'templates/' . $template;
        }

        /*if (file_exists($file)) {
         echo "yes";
        }*/

        //var_dump($file);die();


        return $file;
    }

    /*
    function create_post_type_blormsettings() {
        register_post_type( 'blormsettings',
            array(
                'labels' => array(
                    'name' => __( 'Blorm_Settings' ),
                    'singular_name' => __( 'Blorm_Setting' ),
                    'view_item'             => __( 'Wissenschafts­größe ansehen', 'mintcommunity' ),
                    'view_items'            => __( 'Alle Wissenschafts­größen ansehen', 'mintcommunity' ),

                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'editor', 'post-formats'),
                'taxonomies' => array( 'category', 'post_tag' ),
                'show_in_menu'          => true,
                'menu_position'         => 80,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
            )
        );
    }


    function ah_custom_post_type() {

        $labels = array(
            'name' => 'Portfolio Einträge',
            'singular_name' => 'Portfolio',
            'menu_name' => 'Portfolio',
            'parent_item_colon' => '',
            'all_items' => 'Alle Einträge',
            'view_item' => 'Eintrag ansehen',
            'add_new_item' => 'Neuer Eintrag',
            'add_new' => 'Hinzufügen',
            'edit_item' => 'Eintrag bearbeiten',
            'update_item' => 'Update Eintrag',
            'search_items' => '',
            'not_found' => '',
            'not_found_in_trash' => '',
        );
        $rewrite = array(
            'slug' => 'portfolio',
            'with_front' => true,
            'pages' => true,
            'feeds' => true,
        );
        $args = array(
            'labels' => $labels,
            'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'trackbacks', ),
            'taxonomies' => array( 'category', 'post_tag' ),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'can_export' => false,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'rewrite' => $rewrite,
            'capability_type' => 'page',
        );
        register_post_type( 'portfolio', $args );

    }
    */

    /*
     * FRONTEND
     *
     */

/*static function enqueue_blorm_frontend_theme_style() {

        wp_enqueue_style ('blorm-theme-style', plugins_url('blorm/assets/css/blorm_frontend.css'), array('blorm-theme-style'));

    }

    static function enqueue_blorm_frontend_js() {

        wp_enqueue_script ('blorm-theme-js', plugins_url('blorm/assets/js/blorm.js'), array('blorm-theme-js'));

    }
*/

    /*
     *
     * ADMIN
     *
     */




}