<?php


function getUserDataFromBlorm() {

    $returnObj = new stdClass();
    $returnObj->error = null;
    $returnObj->user = null;

    // prepare the request
    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );

    // @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
    $ApiResponse = wp_remote_request(CONFIG_BLORM_APIURL ."/user/data", $args);

    if (is_a($ApiResponse,"WP_ERROR")) {

        if (isset($ApiResponse->errors['http_request_failed'])) {
            error_log($ApiResponse->errors['http_request_failed'][0]);
        }

        $returnObj->error = $ApiResponse;
        return $returnObj;
    }

    if ($ApiResponse["response"]["message"] == "Unauthorized" ) {
        $returnObj->error = $ApiResponse["response"]["message"];
        return $returnObj;
    }

    $jsonObjResponse = json_decode($ApiResponse["body"]);

    //var_dump($jsonObjResponse);die();

	if ($jsonObjResponse == null) {
		$returnObj->error = "Apiresponse is empty";
		return $returnObj;
	}

    $user = new stdClass();
    $user->name = $jsonObjResponse->name;
    $user->blormhandle = $jsonObjResponse->blormhandle;
    $user->id = $jsonObjResponse->id;
    $user->photo_url = $jsonObjResponse->photo_url;
    $returnObj->user = $user;

    //var_dump(json_decode($ApiResponse["body"]));die();

    return $returnObj;

}