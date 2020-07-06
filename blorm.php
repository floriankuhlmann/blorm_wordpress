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

$a_config = require_once __DIR__.'/config.php';
$options_r = get_option("blorm_plugin_options");

// Definee Configs
define( 'CONFIG_BLORM_BLOGDOMAIN', get_bloginfo('wpurl'));
define( 'CONFIG_BLORM_BLOGURL', get_bloginfo('wpurl'));
define( 'CONFIG_BLORM_APIURL', $a_config['api']);
define( 'CONFIG_BLORM_APIKEY', $options_r['api_key']);

// Define Plugin Name.
define( 'PLUGIN_BLORM_NAME', 'Plugin Blorm' );

// Define Version Number.
define( 'PLUGIN_BLORM_VERSION', $a_config['version'] );

// Plugin Folder Path.
define( 'PLUGIN_BLORM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Plugin Folder URL.
define( 'PLUGIN_BLORM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Plugin Root File.
define( 'PLUGIN_BLORM_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . '/classes/SetupActions.php';
require_once plugin_dir_path( __FILE__ ) . '/lib/settings.php';
require_once plugin_dir_path( __FILE__ ) . '/lib/blorm_post.php';
require_once plugin_dir_path( __FILE__ ) . '/lib/blorm_api.php';
require_once plugin_dir_path( __FILE__ ) . '/lib/frontend.php';
require_once plugin_dir_path( __FILE__ ) . '/lib/admin.php';


load_plugin_textdomain( 'plugin-blorm', false, dirname( plugin_basename( PLUGIN_BLORM_FILE ) ) . '/languages/' );

function blorm_multi_diff($arr1,$arr2){
    $result = array();
    foreach ($arr1 as $k=>$v){
        if(!isset($arr2[$k])){
            $result[$k] = $v;
        } else {
            if(is_array($v) && is_array($arr2[$k])){
                $diff = blorm_multi_diff($v, $arr2[$k]);
                if(!empty($diff))
                    $result[$k] = $diff;
            }
        }
    }
    return $result;
}