<?php

//https://developer.wordpress.org/plugins/post-types/working-with-custom-post-types/
add_action( 'pre_get_posts', 'blorm_add_posttype_blorm_to_loop' );
function blorm_add_posttype_blorm_to_loop( $query ) {

    if (is_admin()) {
        return $query;
    }

    $options_config = get_option( 'blorm_plugin_options_frontend' );
    $options_cat = get_option( 'blorm_plugin_options_category' );

    if (isset($options_config['display_config'])) {

        switch ($options_config['display_config']) {

            case "display_config_widget":
                return $query;
                break;

            case "display_config_category":
                // category
                if (isset($options_cat['blorm_category_show_reblogged'])) {
                    if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
                        $query->set( 'post_type', array( 'blormpost' ));
                        return $query;
                    }
                }
                break;

            case "display_config_loop":
                if ($query->is_main_query()) {
                    $query->set('post_type', array('post', 'blormpost'));
                    return $query;
                }
                break;

            case "display_config_loop_and_widget":
                if ($query->is_main_query()) {
                    $query->set('post_type', array('post', 'blormpost'));
                    return $query;
                }
                break;

            case "display_config_loop_and_category":
                // category
                if (isset($options_cat['blorm_category_show_reblogged'])) {
                    if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
                        $query->set( 'post_type', array( 'blormpost' ));
                        return $query;
                    }
                }
                // main query
                $query->set( 'post_type', array( 'post', 'blormpost' ));
                return $query;
                break;

            case "display_config_category_and_widget":
                // category
                if (isset($options_cat['blorm_category_show_reblogged'])) {
                    if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
                        $query->set( 'post_type', array( 'blormpost' ));
                        return $query;
                    }
                }
                if ($query->is_main_query()) {
                    return $query;
                }
                break;

            case "display_config_loop_and_category_and_widget":
                if (isset($options_cat['blorm_category_show_reblogged'])) {
                    if( $query->is_category($options_cat['blorm_category_show_reblogged'])) {
                        $query->set( 'post_type', array( 'blormpost' ));
                        return $query;
                    }
                }
                // main query
                if ($query->is_main_query()) {
                    $query->set('post_type', array('post', 'blormpost'));
                    return $query;
                }
                break;
        }
    }

    return $query;
}

// modify the css-classes of the posts
add_filter( 'post_class', 'blorm_created_class',10,3);
function blorm_created_class (array $classes, $class, $post_id) {

    $options = get_option("blorm_plugin_options_frontend");

    if (!isset($options['position_widget_menue']))
        return $classes;

    $a = get_post_meta($post_id);
    if (isset($a["blorm_create"])) {
        array_push($classes, 'blorm-shared');

        if ( $options['position_widget_menue'] === 'add_blorm_info_on_image' ) {
            array_push($classes, 'blormwidget-on-image-post');
        } else {
            array_push($classes, 'blormwidget-add-to-content');
        }
    }

    if (isset($a["blorm_reblog_activity_id"])) {
        array_push($classes, 'blorm-rebloged');

        if ( $options['position_widget_menue'] === 'add_blorm_info_on_image' ) {
            array_push($classes, 'blormwidget-on-image-post');
        } else {
            array_push($classes, 'blormwidget-add-to-content');
        }
    }

    // add the standard 'post' class for layout-consistence
    if (isset($a["blorm_create"]) || isset($a["blorm_reblog_activity_id"]))
        array_push($classes, 'post');

    return $classes;
}


add_action( 'the_posts', 'blorm_mod_the_posts' );
function blorm_mod_the_posts($posts) {

    if (is_admin() || is_single()) {
        return $posts;
    }

    $options = get_option("blorm_plugin_options_frontend");

    foreach ($posts as $post) {

        $a = get_post_meta($post->ID);

        $acivityId = "";
        $post_class = "blorm-post-data";
        if (isset($a["blorm_reblog_activity_id"])) {
            $post_class= "blorm-reblog-post-data";
            $acivityId = $a['blorm_reblog_activity_id'][0];
            $material_icon = "flip_to_back";
        }

        if (isset($a["blorm_create_activity_id"])) {
            $post_class= "blorm-create-post-data";
            $acivityId = $a['blorm_create_activity_id'][0];
            $material_icon = "flip_to_front";
        }

        if (isset($a["blorm_reblog_activity_id"]) || isset($a["blorm_create_activity_id"])) {

            // add the blorm icon to the title of a post?
            if ( isset( $options['blorm_icon_to_title']) ) {
                if ($options['blorm_icon_to_title'] === 'add_blorm_icon_to_title') {
                    $post->post_title = '<span class="material-icons">' . $material_icon . '</span>' . $post->post_title;
                }
            }

            // modify title and content
            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_before_title' ) {
                    $post->post_title = '<span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span>'.$post->post_title;
                }
            }

            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_after_title' ) {
                    $post->post_title = $post->post_title .'<span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span>';
                }
            }

            // modify content
            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_before_content' ) {
                    $post->post_content = '<span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span>'.$post->post_content;
                }
            }

            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_after_content' ) {
                    $post->post_content = $post->post_content.'<span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span>';

                }
            }

            // modify content to place on image
            if ( isset( $options['position_widget_menue']) ) {
                if ( $options['position_widget_menue'] === 'add_blorm_info_on_image' ) {
                    $post->post_content = $post->post_content.'<span class="blormWidget" data-postid="'.$post->ID.'" data-activityid="'.$acivityId.'"></span>';
                }
            }
        }
    }

    return $posts;
}


if ( ! function_exists( 'blorm_display_widget' ) && ! is_admin() ) :
    /**
     *
     *
     */
    function blorm_display_widget($id = 0) {

        if ($id == 0) $id = get_the_ID();
        $a = get_post_meta($id);

        $acivityId = "";
        if (isset($a["blorm_reblog_activity_id"])) $acivityId = $a['blorm_reblog_activity_id'][0];
        if (isset($a["blorm_create_activity_id"])) $acivityId = $a['blorm_create_activity_id'][0];

        if (isset($a["blorm_reblog_activity_id"]) || isset($a["blorm_create_activity_id"])) {
            echo '<span class="blormWidget blormwidget-template-tag" data-postid="'.$id.'" data-activityid="'.$acivityId.'"></span>';
        }

    }

endif;