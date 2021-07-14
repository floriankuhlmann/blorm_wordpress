<?php
// remove dashboard widgets
add_action( 'admin_init', 'prepare_dashboard_meta');

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


add_action( 'wp_dashboard_setup', 'add_dashboard_blorm_feed_widget');

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
