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

       // wp_enqueue_script('blorm-admin-theme-timeago', plugins_url('../assets/js/moment.min.js', __FILE__));
        wp_enqueue_script('blorm-admin-theme-app', plugins_url('../assets/js/blorm_app.js', __FILE__), '','',true);

        /* Wordpress API backbone.js */
        wp_enqueue_script('wp-api');

        // Register custom variables for the AJAX script.
        wp_localize_script( 'blorm-admin-theme-app', 'restapiVars', [
            'root'  => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ]);

        wp_add_inline_script('blorm-admin-theme-app', getConfigJs() ,'before');

    }
}

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
    add_meta_box(
        'BlormDashboardWidgetUserProfile',
        '<img src='.plugins_url("blorm/assets/images/blorm_icon_white_3.png").' class="blormImage"> <blorm-username fill-word-second-person="r" ></blorm-username> user profile:',
        'dashboard_widget_blorm_userprofile',
        'dashboard',
        'side',
        'high');

	add_meta_box(
	    'BlormDashboardWidgetNewPost',
        '<img src='.plugins_url("blorm/assets/images/blorm_icon_white_3.png").' class="blormImage"> share here and now:',
        'dashboard_widget_blorm_newpost',
        'dashboard',
        'side',
        'high');

    add_meta_box(
        'BlormDashboardWidgetSearchUser',
        '<img src='.plugins_url( "blorm/assets/images/blorm_icon_world.png" ).' class="blormImage"> who do you want to follow? ',
        'dashboard_widget_blorm_usermodule',
        'dashboard',
        'side',
        'high');

	add_meta_box(
	    'BlormDashboardWidgetFollowing',
        '<img src='.plugins_url( "blorm/assets/images/blorm_icon_world.png" ).' class="blormImage"> <blorm-username fill-word-second-person=" are" fill-word-third-person=" is"></blorm-username> following:',
        'dashboard_widget_blorm_followinglist',
        'dashboard',
        'side',
        'high');

	add_meta_box(
	    'BlormDashboardWidgetFollowers',
        '<img src='.plugins_url( "blorm/assets/images/blorm_icon_world.png" ).' class="blormImage"> <blorm-username fill-word-second-person="r" fill-word-third-person="s"></blorm-username> followers:',
        'dashboard_widget_blorm_followerlist',
        'dashboard',
        'side',
        'high');
}

function dashboard_widget_blorm_userprofile() {
    // echo get list of blogusers
    require_once PLUGIN_BLORM_PLUGIN_DIR  . 'templates/blorm-userprofile_component.php';
}

function dashboard_widget_blorm_usermodule() {
    // echo get list of blogusers
    require_once PLUGIN_BLORM_PLUGIN_DIR  . 'templates/blorm-usersearch_component.php';
}

function dashboard_widget_blorm_followinglist() {
    // echo get list of blogusers
    require_once PLUGIN_BLORM_PLUGIN_DIR  . 'templates/blorm-followinglist_component.php';
}

function dashboard_widget_blorm_followerlist() {
    // echo get list of blogusers
    require_once PLUGIN_BLORM_PLUGIN_DIR  . 'templates/blorm-followerlist_component.php';
}

function dashboard_widget_blorm_newpost() {
    // echo form for new post
    require_once PLUGIN_BLORM_PLUGIN_DIR  . 'templates/blorm-newpost_component.php';
}

function blorm_dashboard_widget_feed_function() {
    // echo the blorm feed
    require_once PLUGIN_BLORM_PLUGIN_DIR  . 'templates/blorm-feed_component.php';
}

function add_dashboard_blorm_feed_widget() {

    $blormUserName = "*";
    global $blormUserAccountData;
    if ($blormUserAccountData->error == null) {
        $blormUserName = $blormUserAccountData->user->name;
    }
    wp_add_dashboard_widget(
        'BlormDashboardWidgetFeed', // Widget slug.
        '<a href="/wp-admin/index.php"><img src="'.plugins_url( 'blorm/assets/images/blorm_logo_world.png' ).'" class="blormImage"></a> <blorm-username fill-word-second-person="r" fill-word-third-person="s"></blorm-username> Feed',
        'blorm_dashboard_widget_feed_function' // Display function.
    );
}

function getConfigJs() {

    global $blormUserAccountData;

    $jsdata =   "var blogurl = '".CONFIG_BLORM_BLOGURL."';\n";
    $jsdata .=  "var blogdomain = '".CONFIG_BLORM_BLOGDOMAIN."';\n";
    $jsdata .=  "var ajaxapi = blogdomain+ajaxurl;\n";
    $jsdata .=  "var templateUrl = '".plugins_url()."';\n";
    $jsdata .=  "var blormPluginUrl = '".plugins_url()."/blorm';\n";
    // user data fallback definiton
    $userdata =  "var blormapp = {
                    account : {
                        \"name\": \"*\",
                        \"blormhandle\": \"*\",
                        \"id\": \"*\",
                        \"photo_url\": \"*\",
                        \"website_name\": \"*\",
                        \"website_href\": \"*\",
                        \"website_category\": \"*\",
                        \"website_type\": \"*\",
                        \"website_id\": \"*\",
                    }
                };\n
                blormapp.user = blormapp.account;
                ";

    if ($blormUserAccountData->error != null) {
        $jsdata .= $userdata;
        return $jsdata;
    }

    // user data setup
    $userdata =  "var blormapp = {
                    account : {
                        \"name\": \"".$blormUserAccountData->user->name."\",
                        \"blormhandle\": \"".$blormUserAccountData->user->blormhandle."\",
                        \"id\": \"".$blormUserAccountData->user->id."\",
                        \"photo_url\": \"".$blormUserAccountData->user->photo_url."\",
                        \"website_name\": \"".$blormUserAccountData->user->website_name."\",
                        \"website_href\": \"".$blormUserAccountData->user->website_href."\",
                        \"website_category\": \"".$blormUserAccountData->user->website_category."\",
                        \"website_type\": \"".$blormUserAccountData->user->website_type."\",
                        \"website_id\": \"".$blormUserAccountData->user->website_id."\",
                    },
                    recentPosts: [\n";
                    $recent_posts = wp_get_recent_posts();
                    foreach ($recent_posts as $recent_post) {
                        $blocks = parse_blocks( $recent_post["post_content"] );
                        if (isset($blocks[0])){
                            $teasertext = str_replace("\n","",filter_var($blocks[0]['innerHTML'], FILTER_SANITIZE_STRING));
                        }
                        $userdata .= '{id:"'.$recent_post["ID"].'", headline:"'.$recent_post["post_title"].'", teasertext:"'.$teasertext.'"},';
                    }
                    $userdata .=  "]\n
                };\n
                blormapp.user = blormapp.account;\n";

    $jsdata .= $userdata;

    return $jsdata;
}