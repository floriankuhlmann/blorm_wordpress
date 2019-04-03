<?php
/*
Plugin Name: Blorm
Plugin URI: https://themecoder.de/
Description: My boilerplate for WordPress Plugins
Author: Thomas Weichselbaumer
Author URI: http://netzberufler.de
Version: 1.0
Text Domain: plugin-boilerplate
Domain Path: /languages/
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Plugin Boilerplate
Copyright(C) 2016, Thomas Weichselbaumer - kontakt@themecoder.de

*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Blorm Class
 *
 * @package Blorm
 */
class Blorm {

    /*private static $config  = [
                                'username' => 'floriank',
                                'password' => '123456',
                                'writeapi' => 'http://localhost:8082',
                                'readapi' => 'http://localhost:8081',
                                'blogdomain' => 'http://localhost'
                                ];*/

    //static private $config  = (require_once 'config.php');

    private $a_config;

    public function __construct()
    {
        // NON
        $this->a_config = require_once __DIR__.'/config.php';


    }

    /**
     * Call all Functions to setup the Plugin
     *
     * @uses Blorm::constants() Setup the constants needed
     * @uses Blorm::includes() Include the required files
     * @uses Blorm::setup_actions() Setup the hooks and actions
     * @return void
     */
    public function setup() {

        // Setup Constants.
        $this->constants();

        // Setup Translation.
        //add_action( 'plugins_loaded', array( __CLASS__, 'translation' ) );


        // Include Files.
        $this->includes();

        // Setup Action Hooks.
        $this->setup_actions();


    }

    /**
     * Setup plugin constants
     *
     * @return void
     */
    private function constants() {


        //echo "<h1>".self::$config['blogdomain']."</h1>";

        // Definee Configs
        define( 'CONFIG_BLORM_BLOGDOMAIN', $this->a_config['blogdomain']);

        define( 'CONFIG_BLORM_BLOGURL', get_bloginfo('wpurl'));

        define( 'CONFIG_BLORM_WRITEAPI', $this->a_config['writeapi']);

        define( 'CONFIG_BLORM_READAPI', $this->a_config['readapi']);

        define( 'CONFIG_BLORM_USERNAME', $this->a_config['username']);

        define( 'CONFIG_BLORM_USERPASS', $this->a_config['password']);


        // Define Plugin Name.
        define( 'PLUGIN_BLORM_NAME', 'Plugin Blorm' );

        // Define Version Number.
        define( 'PLUGIN_BLORM_VERSION', 1.0 );

        // Plugin Folder Path.
        define( 'PLUGIN_BLORM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin Folder URL.
        define( 'PLUGIN_BLORM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

        // Plugin Root File.
        define( 'PLUGIN_BLORM_FILE', __FILE__ );

    }

    /**
     * Load Translation File
     *
     * @return void
     */
    static function translation() {

        load_plugin_textdomain( 'plugin-blorm', false, dirname( plugin_basename( PLUGIN_BLORM_FILE ) ) . '/languages/' );

    }

    /**
     * Include required files
     *
     * @return void
     */
    private function includes() {

        // Include Admin Classes.
        require_once PLUGIN_BLORM_PLUGIN_DIR . '/classes/SetupActions.php';

    }

    /**
     * Setup Action Hooks
     *
     * @see https://codex.wordpress.org/Function_Reference/add_action WordPress Codex
     * @return void
     */
    private function setup_actions() {

        // init add the blorm post type
        add_action( 'init',  array( 'SetupActions', 'create_post_type_blorm' ) );
        add_post_type_support( 'blorm_reblog', 'post-formats' );
        add_action( 'init', array( 'SetupActions', 'ah_custom_post_type' ), 0 );

        // Enqueue Stylesheet.
        //add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( 'SetupActions', 'enqueue_blorm_admin_theme_style' ));

        // remove dashboard widgets
        add_action( 'admin_init', array( 'SetupActions', 'prepare_dashboard_meta') );
        add_action( 'wp_dashboard_setup', array( 'SetupActions', 'add_dashboard_blorm_feed_widget'));

        // add ajax methods
        add_action( 'wp_ajax_blorm', array( __CLASS__, 'blorm_ajax_api' ));

        // add modified rendering of blorm posts
        add_action( 'the_post', array( 'SetupActions', 'render_blorm_post_action'));

    }

    /**
     * Enqueue Stylesheet
     *
     * @return void
     */
    /*static function enqueue_styles() {

        // Enqueue Plugin Stylesheet.
        wp_enqueue_style( 'plugin-blorm', PLUGIN_BLORM_PLUGIN_URL . 'assets/css/blorm.css', array(), PLUGIN_BLORM_VERSION );

    }*/

    static function blorm_ajax_api() {

        //global $wpdb; // this is how you get access to the database

        $basicauth64 = base64_encode(CONFIG_BLORM_USERNAME.":".CONFIG_BLORM_USERPASS);


        //$_POST = json_encode(file_get_contents('php://input'), true);
       /*
        $_POSTjson = json_encode($_POST);
        echo "post".$_POST['headline'];
        wp_die();
                if($_SERVER['REQUEST_METHOD']==='POST' ) {
                    //$_POST = json_decode(file_get_contents('php://input'), true);
                    status_header(200);
                    header('Content-Type: application/json');
                    //echo json_encode(file_get_contents('php://input'));
                    //echo file_get_contents('php://input');
                    //$_POST = file_get_contents('php://input');

                    echo file_get_contents('php://input')." POST headline".$_POST->headline;
                    wp_die();
                }

                echo file_get_contents('php://input')." POST headline".$_POST->headline;
                wp_die();
                
      */
        switch ($_GET['todo']) {

            case "getUserFeed":

                $args = array(
                    'headers' => array('Authorization' => 'Basic ' . $basicauth64),
                    'method' => 'GET'
                );

                $response = wp_remote_request(CONFIG_BLORM_READAPI . "/content/userfeed", $args);
                break;

            case "getUserData":

                $args = array(
                    'headers' => array('Authorization' => 'Basic ' . $basicauth64),
                    'method' => 'GET'
                );

                $response = wp_remote_request(CONFIG_BLORM_READAPI . "/user/profile", $args);
                break;

            case "new_post":

                //$target_dir = get_home_path() . "wp-content/uploads/";
                //$target_file = $target_dir . basename($_FILES["file"]["name"]);
                //$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                //if (move_uploaded_file($_FILES["file"], $target_file)) {


                $uploadedfile = $_FILES['file'];

                $upload_overrides = array( 'test_form' => false );

                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

                if ( $movefile && ! isset( $movefile['error'] ) ) {
                    echo "File is valid, and was successfully uploaded to: ".$movefile['file'];
                    //var_dump( $movefile );
                } else {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */
                    $error_message = "file upload error for file: ".$movefile['error'];
                }

                $file = @fopen( $movefile['file'], 'r' );
                $file_size = filesize( $movefile['file'] );
                $file_data = fread( $file, $file_size );

                    $response = wp_remote_post(CONFIG_BLORM_WRITEAPI . "/content", array(
                            'method' => 'POST',
                            'timeout' => 45,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking' => true,
                            'headers' => array(
                                'Authorization' => 'Basic ' . $basicauth64
                                ),
                            'body' => array(
                                'headline' => $_POST['headline'],
                                'content' => $_POST['text'],
                                'url' => $_POST['url'],
                                'file' => $movefile['url']),
                            'cookies' => array()
                        )
                    );
                //} else {
                  //  $error_message = "file upload error for file: ".$target_file;
                //}

                break;

            case "post_share":


                $response = wp_remote_post(CONFIG_BLORM_WRITEAPI . "/content/share", array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => array(
                            'Authorization' => 'Basic ' . $basicauth64
                        ),
                        'body' => array(
                            'id' => $_POST['id'],
                            'headline' => $_POST['headline'],
                            'content' => $_POST['text'],
                            'url' => $_POST['url'],
                            'file' => $_POST['filepath']),
                        'cookies' => array()
                    )
                );
                //} else {
                //  $error_message = "file upload error for file: ".$target_file;
                //}

                break;

            case "post_reblog":

                $content = "<a href='".$_POST['url']."'><img src='".$_POST['filepath']."'><p>". $_POST['text']."</p></a>";


                $post_id = wp_insert_post( array(
                    'post_title'        => '<span class=\'blorm_reblog\'>'.$_POST['headline'].'</span>',
                    'post_content'      => $content,
                    'post_status'       => 'publish',
                    'post_category'     => 'Blorm',
                    'post_type'         => 'blorm_reblog'
                ) );

                if ( $post_id != 0 )
                {
                    $response = '*Post Added | ';

                    $response .= wp_remote_post( CONFIG_BLORM_WRITEAPI."/reaction/reblogPost", array(
                            'method' => 'POST',
                            'timeout' => 45,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking' => true,
                            'headers' => array('Authorization' => 'Basic '.$basicauth64),
                            'body' => array(
                                'id' => $_POST['id']
                            ),
                            'cookies' => array()
                        )
                    );


                }
                else {
                    $response = '*Error occurred while adding the post';
                }
                // Return the String

                break;

            case "delete_post":


                $contentId = "0d7415dc-c5d7-11e8-8080-80015de642d5";

                $args = array(
                    'headers' => array('Authorization' => 'Basic '.$basicauth64),
                    'method' => 'DELETE'
                );

                $response = wp_remote_request( CONFIG_BLORM_WRITEAPI."/content/".$contentId, $args );
                break;

            case "new_post_comment":

                $response = wp_remote_post( CONFIG_BLORM_WRITEAPI."/comment", array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => array('Authorization' => 'Basic '.$basicauth64),
                        'body' => array(
                            'text' => $_POST['text'],
                            'id' => $_POST['id']
                        ),
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

                $response = wp_remote_request( CONFIG_BLORM_WRITEAPI."/comment/".$contentId, $args );
                break;

            case "follow_user":

                if ($_GET['blogId'] == "") {
                    $error_message = "noid";
                    break;
                }

                $blogId = $_GET['blogId'];
                $args = array(
                    'headers' => array('Authorization' => 'Basic '.$basicauth64),
                    'method' => 'GET'
                );

                $response = wp_remote_request( CONFIG_BLORM_WRITEAPI."/blog/follow/this/".$blogId, $args );
                break;

            case "unfollow_user":

                if ($_GET['blogId'] == "") {
                    $error_message = "noid";
                    break;
                }

                $blogId = $_GET['blogId'];                $args = array(
                    'headers' => array('Authorization' => 'Basic '.$basicauth64),
                    'method' => 'GET'
                );

                $response = wp_remote_request( CONFIG_BLORM_WRITEAPI."/blog/unfollow/this/".$blogId, $args );
                break;

            case "getListOfFollowableBlogs":

                $blogId = "user_perisphere_com";
                $args = array(
                    'headers' => array('Authorization' => 'Basic '.$basicauth64),
                    'method' => 'GET'
                );

                $response = wp_remote_request( CONFIG_BLORM_READAPI."/blog/listfollowablefeeds", $args );
                break;

            case "getListOfAllBlogs":

                $blogId = "user_perisphere_com";
                $args = array(
                    'headers' => array('Authorization' => 'Basic '.$basicauth64),
                    'method' => 'GET'
                );

                $response = wp_remote_request( CONFIG_BLORM_READAPI."/blog/listallfeeds", $args );
                break;

            case "getListOfFollowingBlogs":

                $blogId = "user_perisphere_com";
                $args = array(
                    'headers' => array('Authorization' => 'Basic '.$basicauth64),
                    'method' => 'GET'
                );

                $response = wp_remote_request( CONFIG_BLORM_READAPI."/blog/listfollowedfeeds", $args );
                break;


            default:

                break;
        }


        if (is_wp_error($response)) {
            status_header(404);
            $error_message = $response->get_error_message();
            echo "{ blormerror,".$error_message."}";
        }

        if (!empty($error_message)) {
            status_header(404);
            echo "{ blormerror,".$error_message."}";
        }

        status_header(200);
        header('Content-Type: application/json');
        echo $response['body'];

        //echo "blorm_ajax_request_ok_".$_POST->headline;
        wp_die(); // this is required to terminate immediately and return a proper response

    }



}

$blormapp = new Blorm();

// Run Plugin.
$blormapp->setup();
