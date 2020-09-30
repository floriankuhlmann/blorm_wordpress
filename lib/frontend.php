<?php

// Enqueue Stylesheet and Js for frontend rendering.
add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_theme_style');
add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_js');
add_action( 'wp_head', 'add_getstream_data_to_head');


function enqueue_blorm_frontend_theme_style() {

    wp_enqueue_style ('blorm-theme-style', plugins_url('blorm/assets/css/blorm_frontend.css'));

}

function enqueue_blorm_frontend_js() {

    wp_enqueue_script ('blorm-theme-js', plugins_url('blorm/assets/js/blorm.js'));


    wp_add_inline_script('blorm-theme-js', getBlormFrontendConfigJs() ,'before');

}

function getBlormFrontendConfigJs() {

    $jsdata =  "var blormAssets = '".plugins_url()."/blorm/assets/';\n";

    return $jsdata;
}



function add_getstream_data_to_head() {

    $aBlormCreatePosts = array();
    $aRecentPosts = wp_get_recent_posts(array('meta_key' => 'blorm_create', 'meta_value' => '1',));

    foreach ( $aRecentPosts as $aRecentPost) {
        $meta = get_post_meta($aRecentPost["ID"]);
        if (!empty($meta)) {
            $aBlormCreatePosts[] = array(
                "activity_id" => $meta["blorm_create_activity_id"][0],
                "post_id" => $aRecentPost["ID"]
            );
        }
    }

    // prepare the request
    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );
    $response = wp_remote_request(CONFIG_BLORM_APIURL ."/feed/timeline", $args);
    $bodyObjects = json_decode($response['body']);

    $aGetStreamData = array();
    foreach ($bodyObjects as $bodyObject) {

        if (array_search($bodyObject->id, array_column($aBlormCreatePosts, "activity_id")) !== false) {

            $getStreamData = new stdClass();

            $id = array_search($bodyObject->id, array_column($aBlormCreatePosts, "activity_id"));
            $getStreamData->PostId = $aBlormCreatePosts[$id]["post_id"];

            $getStreamData->ActivityId = $bodyObject->id;

            if (isset($bodyObject->reaction_counts->reblog)) {
                $getStreamData->RebloggedCount = $bodyObject->reaction_counts->reblog;
                $getStreamData->Reblogged = $bodyObject->latest_reactions->reblog;
            }

            if (isset($bodyObject->reaction_counts->comment)) {
                $getStreamData->CommentsCount = $bodyObject->reaction_counts->comment;
                $getStreamData->Comments = $bodyObject->latest_reactions->comment;
            }

            if (isset($bodyObject->reaction_counts->shared)) {
                $getStreamData->SharedCount = $bodyObject->reaction_counts->shared;
                $getStreamData->Shared = $bodyObject->latest_reactions->shared;
            }

            $aGetStreamData[] = $getStreamData;
        }
    }
    //var_dump($aGetStreamData);
    //echo "<pre>";
    echo "<script type=\"text/javascript\">";
    echo "var blormapp = {
        sharedPosts: ".json_encode($aGetStreamData, JSON_PRETTY_PRINT);
    echo "}</script>";
    //echo "</pre>";
    //die();
    /*echo "<pre>";
    var_dump();

    echo json_encode($response['body'], JSON_PRETTY_PRINT);

    echo "</pre>";
    die();*/
}

add_filter( 'post_class', 'blorm_created_class',10,3);
function blorm_created_class (array $classes, $class, $post_id) {

    $a = get_post_meta($post_id);
    if (isset($a["blorm_create"])) {
        $classes[] = 'blorm-shared';
    }
    return $classes;
}