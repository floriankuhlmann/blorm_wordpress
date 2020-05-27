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


//$config = (require_once 'config.php');

//$basicauth64 = base64_encode($config['username'].":".$config['password']);


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


        // Definee Configs
        define( 'CONFIG_BLORM_BLOGDOMAIN', $this->a_config['blogdomain']);

        define( 'CONFIG_BLORM_BLOGURL', get_bloginfo('wpurl'));

        define( 'CONFIG_BLORM_APIURL', $this->a_config['api']);

        define( 'CONFIG_BLORM_USERNAME', $this->a_config['username']);

        define( 'CONFIG_BLORM_USERPASS', $this->a_config['password']);

        define( 'CONFIG_BLORM_APIKEY', $this->a_config['apikey']);

        define( 'CONFIG_BLORM_APIURL', $this->a_config['readapi']);

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
        add_action( 'init',  array( 'SetupActions', 'ah_custom_post_type' ), 0 );
        add_post_type_support( 'blorm_reblog', 'post-formats' );

        // Enqueue Stylesheet and Js.
        add_action( 'admin_enqueue_scripts', array( 'SetupActions', 'enqueue_blorm_admin_theme_style' ));
        add_action( 'vue_templates', array( 'SetupActions', 'add_vue_templates' ));


        // remove dashboard widgets
        add_action( 'admin_init', array( 'SetupActions', 'prepare_dashboard_meta') );
        add_action( 'wp_dashboard_setup', array( 'SetupActions', 'add_dashboard_blorm_feed_widget'));

        // add ajax methods

        // add modified rendering of blorm posts
        // add_action( 'the_post', array( 'SetupActions', 'render_blorm_post_action'));

        add_action( 'rest_api_init', array($this, 'rest_blorm_api_endpoint' ));

    }

    function rest_blorm_api_endpoint() {

        // http://blog1.blorm/wp-json/blormapi/v1/

        // Register the GET route
        register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_blormapi_handler'),
        ));

        // Register the POST route
        /*register_rest_route( 'blormapi/v1', '/file/upload', array(
            'methods' => 'POST',
            'callback' => array($this, 'rest_blormapi_handler_file_upload'),
        ));*/

        // Register the POST route
        register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'rest_blormapi_handler'),
        ));

        // Register the PUT route
        register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'rest_blormapi_handler'),
        ));

        // Register the DELETE route
        register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'rest_blormapi_handler'),
        ));

    }

    function rest_blormapi_handler(WP_REST_Request $request) {

        if ( !is_user_logged_in() ) {
            return new WP_REST_Response(array("message" =>"user not logged in"),200 ,array('Content-Type' => 'application/json'));
        }

        if(!empty($_FILES['uploadfile'])) {
            //error_log("_FILES 'file': ".$_FILES['file']);

            /*if(!empty($_FILES['file'])) {
                return new WP_REST_Response(array("message" => "no_file_data"),200 ,array('Content-Type' => 'application/json'));
            }*/

            if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );


            $uploadFile = $_FILES['uploadfile'];
            $upload_overrides = array( 'test_form' => false );
            $movedFile = wp_handle_upload( $uploadFile, $upload_overrides );

            error_log("movefile: ".$movedFile['url']);


            if ( $movedFile && ! isset( $movdFile['error'] ) ) {
                return new WP_REST_Response(array("message" => "success", "url" => $movedFile['url']),200 ,array('Content-Type' => 'application/json'));

            }
            return new WP_REST_Response(array("message" => "upload_error"),200 ,array('Content-Type' => 'application/json'));
        }



        $params = $request->get_params();
        error_log("api url: ".CONFIG_BLORM_APIURL ."/".$params['restparameter']);
        // error_log("body : ".$request->get_body());

        $args = array(
            'headers' => array('Authorization' => 'Bearer '.$this->a_config['apikey'], 'Content-type' => 'application/json'),
            'method' => $request->get_method(),
            'body' => $request->get_body(),
            'data_format' => 'body',
        );

        $response = wp_remote_request(CONFIG_BLORM_APIURL ."/". $params['restparameter'], $args);
        //error_log("response: ".wp_remote_retrieve_body($response));

        return new WP_REST_Response(json_decode(wp_remote_retrieve_body($response)),200 ,array('Content-Type' => 'application/json'));

    }

    function rest_blormapi_file_upload(WP_REST_Request $request) {

        if ( !is_user_logged_in() ) {
            return new WP_REST_Response(array("message" =>"user not logged in"),200 ,array('Content-Type' => 'application/json'));
        }

        error_log("_FILES 'file': ".$_FILES['file']);

        if(!empty($_FILES['file'])) {
            return new WP_REST_Response(array("message" => "no_file_data"),200 ,array('Content-Type' => 'application/json'));
        }

        $uploadedfile = $_FILES['file'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

        error_log("movefile: ".$movefile);


        if (isset( $movefile['error'])) {
            return new WP_REST_Response(array("message" => "upload_error"),200 ,array('Content-Type' => 'application/json'));
        }

        return new WP_REST_Response(array("message" => "success", "url" => $movefile['file']),200 ,array('Content-Type' => 'application/json'));
        /*$file = @fopen( $movefile['file'], 'r' );
        $file_size = filesize( $movefile['file'] );
        $file_data = fread( $file, $file_size );*/
    }

}

$blormapp = new Blorm();

// Run Plugin.
$blormapp->setup();


/*add_action( 'wp_enqueue_scripts', 'rest_site_scripts', 999 );
function rest_site_scripts() {
    // Enqueue our JS file
    wp_enqueue_script( 'rest_appjs',
        get_template_directory_uri() . '/assets/js/app.js',
        array( 'jquery' ),  get_template_directory() . '/assets/js/app.js', true
    );

    // Provide a global object to our JS file contaning our REST API endpoint, and API nonce
    // Nonce must be 'wp_rest' !
    wp_localize_script( 'rest_appjs', 'rest_object',
        array(
            'api_nonce' => wp_create_nonce( 'wp_rest' ),
            'api_url'   => site_url('/wp-json/rest/v1/')
        )
    );
}


add_action( 'rest_api_init', 'rest_validate_email_endpoint' );
function rest_validate_email_endpoint() {
    // Declare our namespace
    $namespace = 'rest/v1';

    // Register the route
    register_rest_route( $namespace, '/email/', array(
        'methods'   => 'POST',
        'callback'  => 'rest_validate_email_handler',
        'args'      => array(
            'email'  => array( 'required' => true ), // This is where we could do the validation callback
        )
    ) );
}
*/
// The callback handler for the endpoint
/*function rest_validate_email_handler( $request ) {
    // We don't need to specifically check the nonce like with admin-ajax. It is handled by the API.
    $params = $request->get_params();

    // Check if email is valid
    if ( is_email( $params['email']) ) {
        return new WP_REST_Response( array('message' => 'Valid email.'), 200 );
    }

    // Previous check didn't pass, email is invalid.
    return new WP_REST_Response( array('message' => 'Not a valid email.'), 200 );
}*/

/*
add_action( 'wp_ajax_nopriv_blormAjaxApi', 'blormAjaxApi');


function blormAjaxApi() {



    /*$args = array(
             'headers' => array('Authorization' => 'Bearer thWO90Lbfw1M19MbjkmEIhkgwm0URKkwlJgenolCwxNs9HCnjo9Amwohm5zb', 'Content-type' => 'application/json'),
             'method' => 'GET',
             'body' => json_encode(
                 array(
                  '@context' => 'https://www.w3.org/ns/activitystreams',
                  'summary' => 'user gets his timeline',
                     'type' => 'read')
             ),
    );

    $response = wp_remote_request(CONFIG_BLORM_APIURL . "/user/timeline", $args);

    status_header(200);

    echo json_encode("hello blorm");

    //$result = file_get_contents(CONFIG_BLORM_APIURL, 0, $ctx);

    //echo $result;
    wp_die(); // this is required to terminate immediately and return a proper response
}


add_action( 'wp_ajax_my_action', 'my_action' );

function my_action() {
    global $wpdb; // this is how you get access to the database

    $whatever = intval( $_POST['whatever'] );

    $whatever += 10;

    echo $whatever;

    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'admin_footer', 'my_action_javascript' ); // Write our JS below here

function my_action_javascript() { ?>
    <script type="text/javascript" >
        jQuery(document).ready(function($) {

            var data = {
                'action': 'my_action',
                'whatever': 1234
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                alert('Got this from the server: ' + response);
            });
        });
    </script> <?php
}*/

