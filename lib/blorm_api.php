<?php

// add ajax methods

add_action( 'rest_api_init','rest_blorm_api_endpoint' );

function rest_blorm_api_endpoint() {

    // http://blog1.blorm/wp-json/blormapi/v1/

    // Register the GET route
    register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
        'methods' => 'GET',
        'callback' =>'rest_blormapi_handler',
    ));

    // Register the POST route
    register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
        'methods' => 'POST',
        'callback' =>'rest_blormapi_handler',
    ));

    // Register the PUT route
    register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
        'methods' => 'PUT',
        'callback' =>'rest_blormapi_handler',
    ));

    // Register the DELETE route
    register_rest_route( 'blormapi/v1', '/(?P<restparameter>[\S]+)', array(
        'methods' => 'DELETE',
        'callback' =>'rest_blormapi_handler',
    ));

}

function rest_blormapi_handler(WP_REST_Request $request) {

    global $a_config;

    if ( !is_user_logged_in() ) {
        return new WP_REST_Response(array("message" =>"user not logged in"),200 ,array('Content-Type' => 'application/json'));
    }

    if(!empty($_FILES['uploadfile'])) {

        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $uploadFile = $_FILES['uploadfile'];
        $upload_overrides = array( 'test_form' => false );
        $movedFile = wp_handle_upload( $uploadFile, $upload_overrides );

        if ( $movedFile && !isset( $movedFile['error'] ) ) {
            return new WP_REST_Response(array("message" => "success", "url" => $movedFile['url']),200 ,array('Content-Type' => 'application/json'));

        }
        error_log("movefile: ".$movedFile['url']);
        return new WP_REST_Response(array("message" => "upload_error"),200 ,array('Content-Type' => 'application/json'));
    }

    // check before wp_remote_request (create, reblog)
    preRequestLocalPostsUpdate($request);

    // prepare the request
    $args = array(
        'headers' => array('Authorization' => 'Bearer '.$a_config['apikey'], 'Content-type' => 'application/json'),
        'method' => $request->get_method(),
        'body' => $request->get_body(),
        'data_format' => 'body',
    );
    $params = $request->get_params();
    $response = wp_remote_request(CONFIG_BLORM_APIURL ."/". $params['restparameter'], $args);

    // check after wp_remote_request (delete)
    postRequestLocalPostsUpdate($request,$response);

    return new WP_REST_Response(json_decode(wp_remote_retrieve_body($response)),200 ,array('Content-Type' => 'application/json'));

}

function preRequestLocalPostsUpdate(&$request) {

    $parameter = $request->get_params();
    $body = $request->get_body();

    switch($parameter["restparameter"]) {
        // CREATE
        case (preg_match('/^(blogpost\/create)\/?$/', $parameter["restparameter"]) ? true : false) :

            error_log("preRequestLocalPostsUpdate CREATE body:");
            error_log(json_encode($body));

            // we need to modify the body, add the irl to the json-object in the body
            $bodyObj = json_decode($body);

            error_log("postid ".$bodyObj->teaser->postid);
            error_log("get_permalink ". get_permalink($bodyObj->teaser->postid));
            $bodyObj->teaser->url = get_permalink($bodyObj->teaser->postid);
            $request->set_body(json_encode($bodyObj));

            error_log("preRequestLocalPostsUpdate CREATE body update url:");
            error_log(json_encode($request->get_body()));

            //error_log(json_encode($bodyObj));

            break;
    }
}


function postRequestLocalPostsUpdate($request, $response) {

    $parameter = $request->get_params();
    $body = $request->get_body();

    error_log("postRequestLocalPostsUpdate restparameter: ".$parameter["restparameter"]);

    switch($parameter["restparameter"]) {
        // CREATE
        case (preg_match('/^(blogpost\/create)\/?$/', $parameter["restparameter"]) ? true : false) :

            error_log("postRequestLocalPostsUpdate CREATE");

            $bodyObj = json_decode($body);

            error_log(json_encode($response));
            if ($response["response"]["code"] == "200") {
                // we want to save the state of the post tp prevent reposting it later again (the content-object in getstream-database is unique)
                add_post_meta($bodyObj->{'teaser'}->{'postid'},"blorm_create",true);

                $rBody = json_decode($response["body"]);
                add_post_meta($bodyObj->{'teaser'}->{'postid'},"blorm_create_activity_id",$rBody->data->activity_id);
                error_log("activity_id: ".$rBody->data->activity_id);

            }

            break;

        // REBLOG
        case (preg_match('/^(blogpost\/share)\/?$/', $parameter["restparameter"]) ? true : false) :
            error_log("postRequestLocalPostsUpdate restparameter REBLOG");

            $bodyObj = json_decode($body);
            // save custom post
            $post_id = wp_insert_post( array(
                "post_title"        => "<span class=\'blorm_reblog\'>".$bodyObj->{'origin_post_data'}->{'headline'}."</span>",
                "post_content"      => $bodyObj->{'origin_post_data'}->{'text'},
                "post_status"       => "publish",
                "post_category"     => array("Blorm"),
                "post_type"         => "blormpost"
            ));

            add_post_meta($post_id,"blorm_reblog_teaser_image",$bodyObj->{'origin_post_data'}->{'image'});
            add_post_meta($post_id,"blorm_reblog_teaser_url",$bodyObj->{'origin_post_data'}->{'url'});
            add_post_meta($post_id,"blorm_reblog_object_iri",$bodyObj->{'origin_post'}->{'object_iri'});
            add_post_meta($post_id,"blorm_reblog_activity_id",$bodyObj->{'origin_post'}->{'activity_id'});

            error_log("postRequestLocalPostsUpdate restparameter CREATED: ".$post_id);

            break;

        // DELETE
        case (preg_match('/^(blogpost\/delete\/)[a-z0-9-]+$/', $parameter["restparameter"]) ? true : false) :
            $delparameter = explode('/', $parameter["restparameter"]);

            if ($response) {
                //error_log("localFeeedUpdate restparameter DELETE: ".end($delparameter));
                $recent_posts_with_meta = wp_get_recent_posts(array('meta_key' => 'blorm_activity_id', 'meta_value' => $delparameter));
                //error_log(json_encode($recent_posts_with_meta));
                //error_log("id".$recent_posts_with_meta[0]["ID"]);
                delete_post_meta($recent_posts_with_meta[0]["ID"],"blorm_create_activity_id");
                delete_post_meta($recent_posts_with_meta[0]["ID"],"blorm_create");
            }
            break;

        default:
            error_log("localFeeedUpdate restparameter: no matches");
    }
}