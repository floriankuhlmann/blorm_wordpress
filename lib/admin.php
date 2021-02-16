<?php

//var_dump(admin_url());
//var_dump(get_dashboard_url());die();

// Enqueue Stylesheet and Js for admin area.
add_action( 'admin_enqueue_scripts', 'enqueue_blorm_admin_theme_style');

// remove dashboard widgets
add_action( 'admin_init', 'prepare_dashboard_meta');
add_action( 'wp_dashboard_setup', 'add_dashboard_blorm_feed_widget');

/**
 * Enqueue Stylesheet
 *
 * @return void
 */
function enqueue_blorm_admin_theme_style() {

    wp_register_style('blorm-admin-theme-style', plugins_url('../assets/css/blorm.css', __FILE__),false, '1.0.0' );
    /* CSS */
    wp_enqueue_style('blorm-admin-theme-style');

    /* JS */
    global $pagenow;
    if (is_admin() && $pagenow == 'index.php') {

        wp_enqueue_script('blorm-admin-theme-timeago', plugins_url('../assets/js/moment.min.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-jquery', plugins_url('../assets/js/jquery-3.3.1.min.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-axios', plugins_url('../assets/js/axios.min.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-vue', plugins_url('../assets/js/vue.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-materialize', plugins_url('../assets/js/jquery-ui-1.12.1/jquery-ui.min.js', __FILE__));

        wp_enqueue_script('blorm-admin-theme-index', plugins_url('../assets/js/blorm/blorm_app.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-index-feed', plugins_url('../assets/js/blorm/feed.js', __FILE__));

        /* Wordpress API backbone.js */
        wp_enqueue_script('wp-api');

        // Register custom variables for the AJAX script.
        wp_localize_script( 'blorm-admin-theme-index', 'restapiVars', [
            'root'  => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ] );

        wp_add_inline_script('blorm-admin-theme-index', getConfigJs() ,'before');

    }

}

//add_action( 'vue_templates', 'add_vue_templates');
/*function add_vue_templates() {
    // echo the vue js stuff
    require_once PLUGIN_BLORM_PLUGIN_DIR  .'/templates/blorm_vue_templates.php';
} */



function prepare_dashboard_meta() {
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
	$newPostTitle = "<img src=\"".plugins_url( 'blorm/assets/images/blorm_icon_white_3.png' )."\" class=\"blormImage\"> share here and now:";
	add_meta_box( 'BlormDashboardWidgetNewPost', $newPostTitle, 'dashboard_widget_blorm_newpost', 'dashboard', 'side', 'high' );

	$searchUserTitle = "<img src=\"".plugins_url( 'blorm/assets/images/blorm_icon_world.png' )."\" class=\"blormImage\"> who do you want to follow? ";
    add_meta_box( 'BlormDashboardWidgetSearchUser', $searchUserTitle, 'dashboard_widget_blorm_usermodule' , 'dashboard', 'side', 'high' );

	$followingTitle = "<img src=\"".plugins_url( 'blorm/assets/images/blorm_icon_world.png' )."\" class=\"blormImage\"> you are following:";
	add_meta_box( 'BlormDashboardWidgetFollowing', $followingTitle, 'dashboard_widget_blorm_followinglist' , 'dashboard', 'side', 'high' );

	$followerTitle = "<img src=\"".plugins_url( 'blorm/assets/images/blorm_icon_world.png' )."\" class=\"blormImage\"> your followers:";
	add_meta_box( 'BlormDashboardWidgetFollowers', $followerTitle, 'dashboard_widget_blorm_followerlist' , 'dashboard', 'side', 'high' );

}


function dashboard_widget_blorm_usermodule() {
    // echo get list of blogusers
    require_once PLUGIN_BLORM_PLUGIN_DIR  . '/templates/blorm_user_search.php';
}

function dashboard_widget_blorm_followinglist() {
    // echo get list of blogusers
    require_once PLUGIN_BLORM_PLUGIN_DIR  . '/templates/blorm_followinglist.php';
}

function dashboard_widget_blorm_followerlist() {
    // echo get list of blogusers
    require_once PLUGIN_BLORM_PLUGIN_DIR  . '/templates/blorm_followerlist.php';
}

function dashboard_widget_blorm_newpost() {
    // echo form for new post
    require_once PLUGIN_BLORM_PLUGIN_DIR  . '/templates/blorm_newpost.php';
}

function blorm_dashboard_widget_feed_function() {
    // echo the blorm feed
    require_once PLUGIN_BLORM_PLUGIN_DIR  . '/templates/blorm_feed.php';
}


function add_dashboard_blorm_feed_widget() {

    $blormUserName = "*";

    global $blormUserData;
    if ($blormUserData->error == null) {
        $blormUserName = $blormUserData->user->name;
    }
	$title = "<img src=\"".plugins_url( 'blorm/assets/images/blorm_logo_world.png' )."\" class=\"blormImage\"> - ". $blormUserName;
    wp_add_dashboard_widget(
        'BlormDashboardWidgetFeed', // Widget slug.
        $title, // Title.
        'blorm_dashboard_widget_feed_function' // Display function.
    );
}

function getConfigJs() {

    $jsdata =   "var blogurl = '".CONFIG_BLORM_BLOGURL."';\n";
    $jsdata .=  "var blogdomain = '".CONFIG_BLORM_BLOGDOMAIN."';\n";
    $jsdata .=  "var ajaxapi = blogdomain+ajaxurl;\n";
    $jsdata .=  "var templateUrl = '".plugins_url()."';\n";

    // user data

    $userdata =  "var blormapp = {
                user : {
                    \"name\": \"*\",
                    \"blormhandle\": \"*\",
                    \"id\": \"*\",
                    \"website\": \"*\",
                    \"photo_url\": \"*\",
                },
        };\n";

    global $blormUserData;
    if ($blormUserData->error == null) {
        $userdata =  "var blormapp = {
                user : {
                    \"name\": \"".$blormUserData->user->name."\",
                    \"blormhandle\": \"".$blormUserData->user->blormhandle."\",
                    \"id\": \"".$blormUserData->user->id."\",
                    \"photo_url\": \"".$blormUserData->user->photo_url."\",
                },
        };\n";
    }

    $jsdata .= $userdata;

    return $jsdata;
}