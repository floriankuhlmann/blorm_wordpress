<?php

/*
 * setup js and css for the frontend rendering
 *
 */


function getBlormFrontendConfigJs() {
	$jsdata =  "var blormAssets = '".plugins_url()."/blorm/assets/';\n";
	return $jsdata;
}

function enqueue_blorm_frontend_theme_style() {
	if (is_home()) {
		wp_enqueue_style ('blorm-theme-style', plugins_url('blorm/assets/css/blorm_frontend.css'));
	}
}

function enqueue_blorm_frontend_js() {
	if (is_home()) {
		wp_enqueue_script( 'blorm-theme-js', plugins_url( 'blorm/assets/js/blorm.js' ) );
		wp_add_inline_script( 'blorm-theme-js', getBlormFrontendConfigJs(), 'before' );
	}
}

// Enqueue Stylesheet and Js for frontend rendering.
add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_theme_style');
add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_js');
add_action( 'wp_head', 'add_getstream_data_to_head');



function add_getstream_data_to_head() {

	// POSTS ARE CREATED ON THIS PLATFORM AND SHARED ON BLORM
	// we need the information about created post on frontend rendering the posts and will collect them here
    $aBlormCreatePosts = array();

    // get all posts from this plattformed that are shared on blorm
    $aRecentPostsCreate = wp_get_recent_posts(array('meta_key' => 'blorm_create', 'meta_value' => '1'));

    // the activity_id is important to connect the posts with the blorm-data
    foreach ( $aRecentPostsCreate as $aRecentPostCreate) {
        $meta = get_post_meta($aRecentPostCreate["ID"]);
        if (!empty($meta)) {
            $aBlormCreatePosts[] = array(
	            "post_id" => $aRecentPostCreate["ID"],
                "activity_id" => $meta["blorm_create_activity_id"][0]
            );
        }
    }


    // POSTS ARE CREATED ON REMOTE PLATFORM AND REBLOGGED ON THIS PLATFORM
	// we need the information about reblogged post on frontend rendering the posts and will collect them here
	$aBlormRebloggedPosts = array();

	// get all posts from this plattformed that are shared on blorm
	$aRecentPostsReblogged = wp_get_recent_posts(array('meta_key' => 'blorm_reblog_activity_id'));
	//var_dump($aRecentPostsReblogged);
	// the activity_id is important to connect the posts with the blorm-data
	foreach ( $aRecentPostsReblogged as $aRecentPostReblogged) {
		$meta = get_post_meta($aRecentPostReblogged["ID"]);
		if (!empty($meta)) {
			$aBlormRebloggedPosts[] = array(
				"post_id" => $aRecentPostReblogged["ID"],
				"activity_id" => $meta["blorm_reblog_activity_id"][0],
				"teaser_image" => $meta["blorm_reblog_teaser_image"][0],
				"teaser_url" => $meta["blorm_reblog_teaser_url"][0],
				"teaser_iri" => $meta["blorm_reblog_object_iri"][0],
			);
		}
	}

	// ALL POSTS FROM THE GETSTREM TIMELINE
    // we need the blorm-data like comments, shares, retweets to enrich the posts on the local plattform
    // prepare the request
    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );
    $response = wp_remote_request(CONFIG_BLORM_APIURL ."/feed/timeline", $args);
    $bodyObjects = json_decode($response['body']);

    // blorm data for local usage
    $aGetStreamCreatedData = array();
	$aGetStreamRebloggedData = array();

    foreach ($bodyObjects as $bodyObject) {

	    $getStreamData = new stdClass();
    	// CREATED POSTS
    	// search for the data of the created posts
        if (array_search($bodyObject->id, array_column($aBlormCreatePosts, "activity_id")) !== false) {

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

	        $aGetStreamCreatedData[] = $getStreamData;
        }


        // REBLOGGED POSTS
	    if (array_search($bodyObject->id, array_column($aBlormRebloggedPosts, "activity_id")) !== false) {

	    	//var_dump($bodyObject->actor->data);

		    $id = array_search($bodyObject->id, array_column($aBlormRebloggedPosts, "activity_id"));
		    $getStreamData->PostId = $aBlormRebloggedPosts[$id]["post_id"];
		    $getStreamData->ActivityId = $bodyObject->id;
		    $getStreamData->TeaserImage = $aBlormRebloggedPosts[$id]["teaser_image"];
		    $getStreamData->TeaserUrl = $aBlormRebloggedPosts[$id]["teaser_url"];
		    $getStreamData->TeaserIri = $aBlormRebloggedPosts[$id]["teaser_iri"];
		    $getStreamData->OriginWebsiteName = $bodyObject->actor->data->data->website_name;
		    $getStreamData->OriginWebsiteUrl = $bodyObject->actor->data->data->website_url;

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

		    $aGetStreamRebloggedData[] = $getStreamData;
	    }


    }

    echo "<script type=\"text/javascript\">\n\n";
    echo "var blormapp = {
            blormPosts: ".json_encode($aGetStreamCreatedData, JSON_PRETTY_PRINT).",
            rebloggedPosts: ".json_encode($aGetStreamRebloggedData, JSON_PRETTY_PRINT)."\n";
    echo "}\n</script>";
}

add_filter( 'post_class', 'blorm_created_class',10,3);
function blorm_created_class (array $classes, $class, $post_id) {

    $a = get_post_meta($post_id);
    if (isset($a["blorm_create"])) {
        $classes[] = 'blorm-shared';
    }

    if (isset($a["blorm_reblog_activity_id"])) {
        $classes[] = 'blorm-reblogged';
    }

    return $classes;
}


add_action( 'the_posts', 'blorm_add_the_posts' );
function blorm_add_the_posts($posts) {

    $options = get_option("blorm_plugin_options_frontend");

    //var_dump($posts);
    foreach ($posts as $post) {

	    $a = get_post_meta($post->ID);
	    //var_dump($a);

	    if (isset($a["blorm_reblog_activity_id"])) {

		    $post->post_content = '<span class="blorm-reblog-post-data" data-post-id="'.$a['blorm_reblog_activity_id'][0].'">'.$post->post_content.'</span>';

		    if (isset($options['add_blorm_info_before_title'])) {
		    	// modify title
			    if (checked("show", $options['add_blorm_info_before_title'], false)) {
			    	$post->post_title = "<span class=\"blormWidget\" id=\"blormWidget\" data-post-id=\"".$a['blorm_reblog_activity_id'][0]."\">b</span>".$post->post_title;
			    }
		    }

		    if (isset($options['add_blorm_info_after_title'])) {
		    	if ( checked( "show", $options['add_blorm_info_after_title'], false ) ) {
				        $post->post_title = $post->post_title . "<span class=\"blormWidget\" id=\"blormWidget\" data-post-id=\"".$a['blorm_reblog_activity_id'][0]."\">b</span>";
		    	}
		    	// modify content
		    }

		    if (isset($options['add_blorm_info_before_content'])) {
		    	if ( checked( "show", $options['add_blorm_info_before_content'], false ) ) {
		    		$post->post_content = "<span class=\"blormWidget\" id=\"blormWidget\" data-post-id=\"".$a['blorm_reblog_activity_id'][0]."\">b</span>" . $post->post_content;
		    	}
		    }

		    if (isset($options['add_blorm_info_after_content'])) {
		    	if ( checked( "show", $options['add_blorm_info_after_content'], false ) ) {
		    		$post->post_content = $post->post_content . "<div class=\"blormWidget\" id=\"blormWidget\" data-post-id=\"".$a['blorm_reblog_activity_id'][0]."\">b</div>";
		    	}
		    }

	    }

	    if (isset($a["blorm_create_activity_id"])) {

		    $post->post_content = '<span class="blorm-create-post-data" data-post-id="'.$a['blorm_create_activity_id'][0].'">'.$post->post_content.'</span>';

		    /*
					// modify title
					if (checked("show", $options['add_blorm_info_before_title'], false)) {

					}

					if (checked("show", $options['add_blorm_info_after_title'], false)) {

					}
					// modify content

					if (checked("show", $options['add_blorm_info_before_content'], false)) {

					}

					if (checked("show", $options['add_blorm_info_after_content'], false)) {

					}
			*/

	    }

    }


    //var_dump($post);
    return $posts;
}
