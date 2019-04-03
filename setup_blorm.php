<?php
/**
 * @package Hello_Dolly
 * @version 1.7
 */
/*
Plugin Name: Blorm
Plugin URI: http://wordpress.org/plugins/blorm/
Description: This is not just a blorm plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Florian Kuhlmann
Version: 1.7
Author URI: http://ma.tt/

https://codex.wordpress.org/Dashboard_Widgets_API

*/

$config = (require_once 'config.php');
$basicauth64 = base64_encode($config['username'].":".$config['password']);

// plugin folder url
if(!defined('FK_BLORM_PLUGIN_URL')) {
    define('FK_BLORM_PLUGIN_URL', plugin_dir_url( __FILE__ ));
}

function getconfigjs() {

    global $config;

    $jsdata = "var wpurl = '".get_bloginfo('wpurl')."';";
    $jsdata .= "var blogurl = '".$config['blogurl']."';";
    $jsdata .= "var blormapp = {};";
    return $jsdata;
}

function my_admin_theme_style() {
    wp_enqueue_style('blorm-admin-theme-blorm', plugins_url('assets/css/blorm.css', __FILE__));
    wp_enqueue_style('blorm-admin-theme-materialize', plugins_url('assets/js/jquery-ui-1.12.1/jquery-ui.structure.min.css', __FILE__));

    wp_enqueue_script('blorm-admin-theme-jquery', plugins_url('assets/js/jquery-3.3.1.min.js', __FILE__));
    wp_enqueue_script('blorm-admin-theme-axios', plugins_url('assets/js/axios.min.js', __FILE__));
    wp_enqueue_script('blorm-admin-theme-vue', plugins_url('assets/js/vue.js', __FILE__));
    wp_enqueue_script('blorm-admin-theme-materialize', plugins_url('assets/js/jquery-ui-1.12.1/jquery-ui.min.js', __FILE__));
    wp_enqueue_script('blorm-admin-theme-index', plugins_url('assets/js/index.js', __FILE__));
    wp_enqueue_script('wp-api');

    wp_add_inline_script('blorm-admin-theme-index',getconfigjs(),'before');

}
add_action('admin_enqueue_scripts', 'my_admin_theme_style');

function dashboard_widget_blorm_bloglist() {
    // echo get list of blogusers

    require_once 'templates/blormuserlist.php';
}

function dashboard_blorm_newpost_widget() {
    // Display whatever it is you want to show.
    require_once 'templates/blormnewpost.php';
}

function add_dashboard_blorm_newpost_widget() {
    /*wp_add_dashboard_widget(
        'wpexplorer_dashboard_widget', // Widget slug.
        'New Post', // Title.
        'dashboard_blorm_newpost_widget' // Display function.
    );*/
    require_once 'templates/blormnewpost.php';
}


function remove_dashboard_meta() {
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
    add_meta_box( 'id1', 'BLORM - New Post', 'add_dashboard_blorm_newpost_widget', 'dashboard', 'side', 'high' );

    add_meta_box( 'id2', 'BLORM - Blogs to follow', 'dashboard_widget_blorm_bloglist', 'dashboard', 'side', 'high' );

}
add_action( 'admin_init', 'remove_dashboard_meta' );

/**
 * Create the function to output the contents of your Dashboard Widget.
 */


//add_action( 'wp_dashboard_setup', 'add_dashboard_blorm_newpost_widget' );


function dashboard_blorm_feed_widget() {
    // Display whatever it is you want to show.
    require_once 'templates/blormfeed.php';
}

function add_dashboard_blorm_feed_widget() {
    wp_add_dashboard_widget(
        'wpexplorer_dashboard_widget_feed', // Widget slug.
        'Blorm - Newsfeed ', // Title.
        'dashboard_blorm_feed_widget' // Display function.
    );
}
add_action( 'wp_dashboard_setup', 'add_dashboard_blorm_feed_widget' );



function blorm_request_api() {
    // Handle request then generate response using echo or leaving PHP and using HTML
    //echo "blorm_request_api";

    if($_SERVER['REQUEST_METHOD']==='POST' && empty($_POST)) {
        $_POST = json_decode(file_get_contents('php://input'));
    }

    status_header(200);
    die("Server received request from your browser.".$_POST->headline);
    //echo "blorm_request_ok";
    //wp_die();
}

function blorm_ajax_api() {

    global $wpdb; // this is how you get access to the database
    global $config;
    global $basicauth64;


    if($_SERVER['REQUEST_METHOD']==='POST' && empty($_POST)) {
        $_POST = json_decode(file_get_contents('php://input'));
    }


    switch ($_GET['todo']) {

        case "new_post":
            $response = wp_remote_post( $config['writeapi']."/content", array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array('Authorization' => 'Basic '.$basicauth64),
                    'body' => array( 'headline' => $_POST->headline, 'content' => $_POST->text, 'url' => $_POST->url ),
                    'cookies' => array()
                )
            );

            break;

        case "delete_post":


            $contentId = "0d7415dc-c5d7-11e8-8080-80015de642d5";

            $args = array(
                'headers' => array('Authorization' => 'Basic '.$basicauth64),
                'method' => 'DELETE'
            );

            $response = wp_remote_request( $config['writeapi']."/content/".$contentId, $args );
            break;

        case "new_comment":

            $response = wp_remote_post( $config['writeapi']."/comment", array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array('Authorization' => 'Basic '.$basicauth64),
                    'body' => array( 'headline' => $_POST->headline, 'content' => $_POST->comment, 'url' => $_POST->url ),
                    'cookies' => array()
                )
            );
            break;

        case "delete_comment":

            $contentId = "0d7415dc-c5d7-11e8-8080-80015de642d5";
            $args = array(
                'headers' => array('Authorization' => 'Basic '.$basicauth64),
                'method' => 'DELETE'
            );

            $response = wp_remote_request( $config['writeapi']."/comment/".$contentId, $args );
            break;

        case "follow_user":

            $blogId = "user_perisphere_com";
            $args = array(
                'headers' => array('Authorization' => 'Basic '.$basicauth64),
                'method' => 'GET'
            );

            $response = wp_remote_request( $config['writeapi']."/blog/follow/this/".$blogId, $args );
            break;

        case "unfollow_user":

            $blogId = "user_perisphere_com";
            $args = array(
                'headers' => array('Authorization' => 'Basic '.$basicauth64),
                'method' => 'GET'
            );

            $response = wp_remote_request( $config['writeapi']."/blog/unfollow/this/".$blogId, $args );
            break;

        case "getbloglist":

            $blogId = "user_perisphere_com";
            $args = array(
                'headers' => array('Authorization' => 'Basic '.$basicauth64),
                'method' => 'GET'
            );

            $response = wp_remote_request( "http://localhost:8081/users/listallblogs", $args );
            break;

        default:

            break;
    }


    if ( is_wp_error( $response ) ) {
        status_header(404);
        $error_message = $response->get_error_message();
        echo "{ blormerror,".$error_message."}";
    } else {
        status_header(200);
        header('Content-Type: application/json');
        echo $response['body'];
    }

    //echo "blorm_ajax_request_ok_".$_POST->headline;
    wp_die(); // this is required to terminate immediately and return a proper response

}

add_action( 'admin_post_blorm', 'blorm_request_api' );
add_action( 'wp_ajax_blorm', 'blorm_ajax_api' );


function add_vue_templates() {
    echo require_once 'templates/blorm_vue_templates.php';
}

add_action('vue_templates','add_vue_templates');

