<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 08.10.18
 * Time: 23:54
 */

/*return [

    'api' => 'http://localhost:8000',
    'apikey' => 'CXYJGdrKTUzwSodnBLY5hdNe1CeJ3fH7WElvFORJBtZgYYhHEyeSgGPcs5qF',
    'version' => '0.9'

];*/

function get_blorm_config() {


    $options = get_option("blorm_plugin_options_api");

    if ($options == false) {
        $options = array();
    }

    $returnArray = array_merge(
        $options,
        Array(
        'api' => 'http://blorm-api.blormdev:8000',
        'version' => '0.9'
        )
    );

    return $returnArray;
};

function get_blorm_config_param($key) {

    $config = get_blorm_config();

    if (isset($config[$key]) ) {
        return $config[$key];
    }

    return "no value for key avilable";
};