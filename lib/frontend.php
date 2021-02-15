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

	$catId = '';
	$options = get_option( 'blorm_plugin_options_category' );
	if (isset( $options['blorm_category_show_reblogged'] )) {
		$catId = $options['blorm_category_show_reblogged'];
	}

	if (is_home() || is_single() || is_category($catId)) {
		wp_enqueue_style ('blorm-theme-style', plugins_url('blorm/assets/css/blorm_frontend.css'));
	}
}

function enqueue_blorm_frontend_js() {

	$catId = '';
	$options = get_option( 'blorm_plugin_options_category' );
	if (isset( $options['blorm_category_show_reblogged'] )) {
		$catId = $options['blorm_category_show_reblogged'];
	}

	if (is_home() || is_single() || is_category($catId)) {
		wp_enqueue_script( 'blorm-mobile-detect', plugins_url( 'blorm/assets/js/mobile-detect.min.js' ) );
		wp_enqueue_script( 'blorm-theme-js', plugins_url( 'blorm/assets/js/blorm/blorm_web_widget.js' ) );
		wp_add_inline_script( 'blorm-theme-js', getBlormFrontendConfigJs(), 'before' );
	}
}


// Enqueue Stylesheet and Js for frontend rendering.
add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_theme_style');
add_action( 'wp_enqueue_scripts', 'enqueue_blorm_frontend_js');
add_action( 'wp_head', 'add_getstream_data_to_head');



/*
 *
 * adding cron schedule for loading the getstream data
 * the getstream data is loaded every 180 seconds to cache the data
 * and prevent the blorm-api from high traffic webpages
 *
*/
add_filter( 'cron_schedules', 'blorm_add_cron_getstream_interval' );
function blorm_add_cron_getstream_interval( $schedules ) {
	$schedules['onehundredeighty_seconds'] = array(
		'interval' => 180,
		'display'  => esc_html__( 'Every sixty Seconds' ), );
	return $schedules;
}

add_action( 'blorm_cron_getstream_hook', 'blorm_cron_getstream_exec' );

if ( ! wp_next_scheduled( 'blorm_cron_getstream_hook' ) ) {
	wp_schedule_event( time(), 'onehundredeighty_seconds', 'blorm_cron_getstream_hook' );
}

function blorm_cron_getstream_exec() {

	$getstreamPostObjects = "{}";

	$args = array(
		'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
		'method' => 'GET',
		'body' => '',
		'data_format' => 'body',
	);
	$response = wp_remote_request(CONFIG_BLORM_APIURL ."/feed/timeline", $args);

	if ($response['body'] != "") {
		$getstreamPostObjects = $response['body'];

	}

	update_option( 'blorm_getstream_cached_post_data', $getstreamPostObjects, true );

}


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
	$aBlormReblogedPosts = array();

	// get all posts from this plattformed that are shared on blorm
	$aRecentPostsRebloged = wp_get_recent_posts(array('meta_key' => 'blorm_reblog_activity_id'));
	//var_dump($aRecentPostsReblogged);
	// the activity_id is important to connect the posts with the blorm-data
	foreach ( $aRecentPostsRebloged as $aRecentPostRebloged) {
		$meta = get_post_meta($aRecentPostRebloged["ID"]);
		if (!empty($meta)) {
			$aBlormReblogedPosts[] = array(
				"post_id" => $aRecentPostRebloged["ID"],
				"activity_id" => $meta["blorm_reblog_activity_id"][0],
				"teaser_image" => $meta["blorm_reblog_teaser_image"][0],
				"teaser_url" => $meta["blorm_reblog_teaser_url"][0],
				"teaser_iri" => $meta["blorm_reblog_object_iri"][0],
			);
		}
	}

	// ALL POSTS FROM THE GETSTREAM TIMELINE
    // we need the blorm-data like comments, shares, retweets to enrich the posts on the local plattform
	// the data is loaded every 180 seconds via cron schedule 'blorm_cron_getstream_hook' and stored to wp_options

	$bodyObjects = json_decode(get_option( 'blorm_getstream_cached_post_data' ));

    // blorm data for local usage
    $aGetStreamCreatedData = array();
	$aGetStreamReblogedData = array();

    foreach ($bodyObjects as $bodyObject) {

	    $getStreamData = new stdClass();
	    if (isset($bodyObject->id)) {

	        // CREATED POSTS
	        // search for the data of the created posts
	        if (array_search($bodyObject->id, array_column($aBlormCreatePosts, "activity_id")) !== false) {

	            $id = array_search($bodyObject->id, array_column($aBlormCreatePosts, "activity_id"));
	            $getStreamData->PostId = $aBlormCreatePosts[$id]["post_id"];

	            $getStreamData->ActivityId = $bodyObject->id;

		        $getStreamData->ReblogedCount = 0;
		        $getStreamData->CommentsCount = 0;
		        $getStreamData->SharedCount = 0;

		        if (isset($bodyObject->reaction_counts->reblog)) {
			        $getStreamData->ReblogedCount = $bodyObject->reaction_counts->reblog;
		        }

		        if (isset($bodyObject->latest_reactions->reblog)) {
			        $getStreamData->Rebloged = $bodyObject->latest_reactions->reblog;
		        }

		        if (isset($bodyObject->reaction_counts->comment)) {
			        $getStreamData->CommentsCount = $bodyObject->reaction_counts->comment;
		        }

		        if (isset($bodyObject->latest_reactions->comment)) {
			        $getStreamData->Comments = $bodyObject->latest_reactions->comment;
		        }

		        if (isset($bodyObject->reaction_counts->shared)) {
			        $getStreamData->SharedCount = $bodyObject->reaction_counts->shared;
		        }

		        if (isset($bodyObject->latest_reactions->shared)) {
			        $getStreamData->Shared = $bodyObject->latest_reactions->shared;
		        }

		        $aGetStreamCreatedData[$getStreamData->PostId] = $getStreamData;
	        }


	        // REBLOGED POSTS
		    if (array_search($bodyObject->id, array_column($aBlormReblogedPosts, "activity_id")) !== false) {

		        //var_dump($bodyObject->actor->data);

			    $id = array_search($bodyObject->id, array_column($aBlormReblogedPosts, "activity_id"));
			    $getStreamData->PostId = $aBlormReblogedPosts[$id]["post_id"];
			    $getStreamData->ActivityId = $bodyObject->id;
			    $getStreamData->TeaserImage = $aBlormReblogedPosts[$id]["teaser_image"];
			    $getStreamData->TeaserUrl = $aBlormReblogedPosts[$id]["teaser_url"];
			    $getStreamData->TeaserIri = $aBlormReblogedPosts[$id]["teaser_iri"];
			    if (isset($bodyObject->object->data->data->published_on_website_name)) {
				    $getStreamData->OriginWebsiteName = $bodyObject->object->data->data->published_on_website_name;
			    }
			    if (isset($bodyObject->object->data->data->published_on_website_url)) {
				    $getStreamData->OriginWebsiteUrl = $bodyObject->object->data->data->published_on_website_url;
			    }
			    $getStreamData->ReblogedCount = 0;
			    $getStreamData->CommentsCount = 0;
			    $getStreamData->SharedCount = 0;

			    if (isset($bodyObject->reaction_counts->reblog)) {
				    $getStreamData->ReblogedCount = $bodyObject->reaction_counts->reblog;
			    }

			    if (isset($bodyObject->latest_reactions->reblog)) {
				    $getStreamData->Rebloged = $bodyObject->latest_reactions->reblog;
			    }

			    if (isset($bodyObject->reaction_counts->comment)) {
				    $getStreamData->CommentsCount = $bodyObject->reaction_counts->comment;
			    }

			    if (isset($bodyObject->latest_reactions->comment)) {
				    $getStreamData->Comments = $bodyObject->latest_reactions->comment;
			    }

			    if (isset($bodyObject->reaction_counts->shared)) {
				    $getStreamData->SharedCount = $bodyObject->reaction_counts->shared;
			    }

			    if (isset($bodyObject->latest_reactions->shared)) {
				    $getStreamData->Shared = $bodyObject->latest_reactions->shared;
			    }

			    $aGetStreamReblogedData[$getStreamData->PostId] = $getStreamData;
		    }
	    }
    }

	$blormPostConfig = new stdClass();
	$blormPostConfig->blormAssets = plugins_url()."/blorm/assets/";

	$options = get_option( 'blorm_plugin_options_frontend' );

	$blormPostConfig->float = "left";
	if (isset( $options['position_widget_menue_adjust_float'] )) {
		$blormPostConfig->float = $options['position_widget_menue_adjust_float'];
	}

	$blormPostConfig->classForWidgetPlacement = "";
	if (isset( $options['position_widget_menue_adjust_classForWidgetPlacement'] )) {
		$blormPostConfig->classForWidgetPlacement = $options['position_widget_menue_adjust_classForWidgetPlacement'];
	}

	$blormPostConfig->positionTop = 0;
	if (isset( $options['position_widget_menue_adjust_positionTop'] )) {
		$blormPostConfig->positionTop = $options['position_widget_menue_adjust_positionTop'];
	}

	$blormPostConfig->positionRight = 0;
	if (isset( $options['position_widget_menue_adjust_positionRight'] )) {
		$blormPostConfig->positionRight = $options['position_widget_menue_adjust_positionRight'];
	}

	$blormPostConfig->positionBottom = 0;
	if (isset( $options['position_widget_menue_adjust_positionBottom'] )) {
		$blormPostConfig->positionBottom = $options['position_widget_menue_adjust_positionBottom'];
	}

	$blormPostConfig->positionLeft = 0;
	if (isset( $options['position_widget_menue_adjust_positionLeft'] )) {
		$blormPostConfig->positionLeft = $options['position_widget_menue_adjust_positionLeft'];
	}

	$blormPostConfig->positionUnit = "px";
	if (isset( $options['position_widget_menue_adjust_positionUnit'] )) {
		$blormPostConfig->positionUnit = $options['position_widget_menue_adjust_positionUnit'];
	}


    echo "<script type=\"text/javascript\">\n\n";
    echo "var blormapp = {
			postConfig: ".json_encode($blormPostConfig, JSON_PRETTY_PRINT).",\n
            blormPosts: ".json_encode($aGetStreamCreatedData, JSON_PRETTY_PRINT).",\n
            reblogedPosts: ".json_encode($aGetStreamReblogedData, JSON_PRETTY_PRINT)."\n";
    echo "}\n</script>";
}

add_filter( 'post_class', 'blorm_created_class',10,3);
function blorm_created_class (array $classes, $class, $post_id) {

    $a = get_post_meta($post_id);
    if (isset($a["blorm_create"])) {
        $classes[] = 'blorm-shared';
    }

    if (isset($a["blorm_reblog_activity_id"])) {
        $classes[] = 'blorm-rebloged';
    }

    return $classes;
}


/*
 *
 * if there is a category for showing the blorm posts lets remove the rebloged post from the mainloop
 */

add_action( 'pre_get_posts', 'wpsites_remove_posts_from_home_page' );
function wpsites_remove_posts_from_home_page( $query ) {

	$options = get_option( 'blorm_plugin_options_category' );

	if (isset( $options['blorm_category_show_reblogged'] )) {
		if( $query->is_main_query() && $query->is_home() ) {
			$query->set( 'cat', '-'.$options['blorm_category_show_reblogged'] );
		}
	}
}
add_action( 'the_posts', 'blorm_mod_the_posts' );



function blorm_mod_the_posts($posts) {

    $options = get_option("blorm_plugin_options_frontend");

    foreach ($posts as $post) {

    	$a = get_post_meta($post->ID);

	    $acivityId = "";
	    $post_class = "blorm-post-data";
	    if (isset($a["blorm_reblog_activity_id"])) {
		    $post_class= "blorm-reblog-post-data";
		    $acivityId = $a['blorm_reblog_activity_id'][0];
	    }

	    if (isset($a["blorm_create_activity_id"])) {
		    $post_class= "blorm-create-post-data";
		    $acivityId = $a['blorm_create_activity_id'][0];
	    }

	    if (isset($a["blorm_reblog_activity_id"])  || isset($a["blorm_create_activity_id"])) {

	    	// modify title
		    if ( isset( $options['position_widget_menue']) ) {
			    if ( $options['position_widget_menue'] === 'add_blorm_info_before_title' ) {
				    $post->post_title = '<div class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></div>' . $post->post_title;
			    }
		    }

		    if ( isset( $options['position_widget_menue']) ) {
			    if ( $options['position_widget_menue'] === 'add_blorm_info_after_title' ) {
				    $post->post_title = $post->post_title . '<div class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></div>';
			    }
		    }

		    // modify content
		    if ( isset( $options['position_widget_menue']) ) {
			    if ( $options['position_widget_menue'] === 'add_blorm_info_before_content' ) {
				    $post->post_content = '<div class="blorm-post-content-container '.$post_class.'" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"><span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span>'.$post->post_content.'</div>';
				    $post->post_excerpt = $post->post_content;
			    }
		    }

		    if ( isset( $options['position_widget_menue']) ) {
			    if ( $options['position_widget_menue'] === 'add_blorm_info_after_content' ) {
				    $post->post_content = '<div class="blorm-post-content-container '.$post_class.'" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'">'.$post->post_content.'<span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span></div>';
				    $post->post_excerpt = $post->post_content;
			    }
		    }

		    // modify content to place on image
		    if ( isset( $options['position_widget_menue']) ) {
			    if ( $options['position_widget_menue'] === 'add_blorm_info_on_image' ) {
				    $post->post_title = '<span class="blorm-icon" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"><img src="'.plugins_url().'/blorm/assets/images/blorm_icon.png"></span>' . $post->post_title;

				    $post->post_content = '<div class="blorm-post-content-container blormWidget-on-image '.$post_class.'" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'">'.$post->post_content.'</div>';
				    $post->post_excerpt = $post->post_content;
			    }
		    }

	    }
    }



    //var_dump($post);
    return $posts;
}
