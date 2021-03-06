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
require_once plugin_dir_path( __FILE__ ) . '/lib/helper.php';
require_once __DIR__.'/config/config.php';

// Definee Configs
define( 'CONFIG_BLORM_BLOGDOMAIN', get_bloginfo('wpurl'));
define( 'CONFIG_BLORM_BLOGURL', get_bloginfo('wpurl'));
define( 'CONFIG_BLORM_APIURL', get_blorm_config_param('api'));
define( 'CONFIG_BLORM_APIKEY', get_blorm_config_param('api_key'));

// Define Plugin Name.
define( 'PLUGIN_BLORM_NAME', 'Plugin Blorm' );

// Define Version Number.
define( 'PLUGIN_BLORM_VERSION', get_blorm_config_param('version') );

// Plugin Folder Path.
define( 'PLUGIN_BLORM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Plugin Folder URL.
define( 'PLUGIN_BLORM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Plugin Root File.
define( 'PLUGIN_BLORM_FILE', __FILE__ );

// configuration and code for the frontend rendering
require_once plugin_dir_path( __FILE__ ) . 'lib/frontend_styles_and_scripts.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/frontend_cache_and_cron.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/frontend_loop_and_post.php';

// blormpost, widget and ajax api
require_once plugin_dir_path( __FILE__ ) . 'lib/blorm_post.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/blorm_api.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/blorm_widget.php';

// the admin area
if (is_admin()) {
    require_once plugin_dir_path( __FILE__ ) . 'lib/user.php';
    require_once plugin_dir_path( __FILE__ ) . 'lib/admin_settings.php';

    $blormUserAccountData = getUserAccountDataFromBlorm();
    if ($blormUserAccountData->error == null) {
        require_once plugin_dir_path( __FILE__ ) . 'lib/admin_styles_and_scripts.php';
        require_once plugin_dir_path( __FILE__ ) . 'lib/admin_init_dashboard.php';

    } else {
        require_once plugin_dir_path( __FILE__ ) . 'lib/admin_error.php';
    }
}
