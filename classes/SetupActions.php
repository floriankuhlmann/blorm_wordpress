<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 03.11.18
 * Time: 09:02
 */

class SetupActions
{

    /*
     *
     * post type
     *
     */

    function create_post_type_blorm() {
        register_post_type( 'blorm_reblog',
            array(
                'labels' => array(
                    'name' => __( 'Blorm_Reblogs' ),
                    'singular_name' => __( 'Blorm_Reblog' )
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'editor', 'post-formats')
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




    /*
     *
     * ADMIN
     *
     */

    /**
     * Enqueue Stylesheet
     *
     * @return void
     */

    static function enqueue_blorm_admin_theme_style() {
        /* CSS */
        wp_enqueue_style('blorm-admin-theme-blorm', plugins_url('../assets/css/blorm.css', __FILE__));
        wp_enqueue_style('blorm-admin-theme-materialize', plugins_url('../assets/js/jquery-ui-1.12.1/jquery-ui.structure.min.css', __FILE__));

        /* JS */
        wp_enqueue_script('blorm-admin-theme-jquery', plugins_url('../assets/js/jquery-3.3.1.min.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-axios', plugins_url('../assets/js/axios.min.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-vue', plugins_url('../assets/js/vue.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-materialize', plugins_url('../assets/js/jquery-ui-1.12.1/jquery-ui.min.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-index', plugins_url('../assets/js/app.js', __FILE__));

        /* Wordpress API backbone.js */
        wp_enqueue_script('wp-api');

        // Register custom variables for the AJAX script.
        wp_localize_script( 'blorm-admin-theme-index', 'restapiVars', [
            'root'  => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ] );

        wp_add_inline_script('blorm-admin-theme-index',self::getconfigjs(),'before');
    }

    static function prepare_dashboard_meta() {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_browser_nag','dashboard','normal');
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
        remove_action('welcome_panel', 'wp_welcome_panel');

        //https://codex.wordpress.org/Dashboard_Widgets_API
        add_meta_box( 'id1', 'BLORM - New Post', array( __CLASS__, 'dashboard_widget_blorm_newpost' ), 'dashboard', 'side', 'high' );
        /*
        add_meta_box( 'id2', 'BLORM - Blogs to follow', array( __CLASS__, 'dashboard_widget_blorm_bloglist' ), 'dashboard', 'side', 'high' );
        add_meta_box( 'id3', 'BLORM - i am following', array( __CLASS__, 'dashboard_widget_blorm_followingbloglist' ), 'dashboard', 'side', 'high' );*/
        add_meta_box( 'id3', 'BLORM - User and blogs', array( __CLASS__, 'dashboard_widget_blorm_usermodule' ), 'dashboard', 'side', 'high' );

    }

    static function getconfigjs() {

        $jsdata =   "var blogurl = '".CONFIG_BLORM_BLOGURL."';";
        $jsdata .=  "var blogdomain = '".CONFIG_BLORM_BLOGDOMAIN."';";
        $jsdata .=  "var ajaxapi = blogdomain+ajaxurl;";
        $jsdata .=  "var blormapp = {};";
        $jsdata .=  "var templateUrl = '".plugins_url()."';";

        return $jsdata;
    }

    static function dashboard_widget_blorm_usermodule() {
        // echo get list of blogusers
        require_once plugin_dir_path( __FILE__ )  . '../templates/blorm_usermodule.php';
    }

    static function dashboard_widget_blorm_newpost() {
        // echo form for new post
        require_once plugin_dir_path( __FILE__ )  . '../templates/blorm_newpost.php';
    }

    static function dashboard_widget_blorm_feed() {
        // echo the blorm feed
        require_once plugin_dir_path( __FILE__ )  . '../templates/blorm_feed.php';
    }

    static function add_vue_templates() {
        // echo the vue js stuff
        require_once plugin_dir_path( __FILE__ )  .'../templates/blorm_vue_templates.php';
    }

    static function add_dashboard_blorm_feed_widget() {
        wp_add_dashboard_widget(
            'wpexplorer_dashboard_widget_feed', // Widget slug.
            'Blorm - Newsfeed from '.CONFIG_BLORM_USERNAME, // Title.
            array( 'SetupActions', 'dashboard_widget_blorm_feed' ) // Display function.
        );
    }

}