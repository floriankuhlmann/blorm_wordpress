<?php

function getUserDataFromBlorm($a_config) {

    // prepare the request
    $args = array(
        'headers' => array('Authorization' => 'Bearer '.$a_config['apikey'], 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );

    // @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
    $response = wp_remote_request(CONFIG_BLORM_APIURL ."/user/data", $args);

    //var_dump($response["body"]);
    $jsonObjResponse = json_decode($response["body"]);

    $user = new stdClass;
    $user->name = $jsonObjResponse->name;
    $user->blormhandle = $jsonObjResponse->blormhandle;
    $user->id = $jsonObjResponse->id;
    $user->website = $jsonObjResponse->website;
    $user->photo_url = $jsonObjResponse->photo_url;

    return $user;

}

$blormUser = getUserDataFromBlorm($a_config);