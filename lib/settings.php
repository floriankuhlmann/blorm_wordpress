<?php

$menuSlug= 'blorm-plugin';


function blorm_options_page() {
	$hookname = add_menu_page(
        'Blorm plugin page',    // title for browser window
        'Blorm Settings',       // menue name
        'manage_options',       //
        'blorm-plugin',
        'blorm_render_options_page' // name of the rendering function
    );
	add_action( 'load-' . $hookname, 'blorm_plugin_options_page_submit' );
}
add_action( 'admin_menu', 'blorm_options_page' );

function blorm_plugin_options_page_submit() {

	$blorm_plugin_options_api = array();
	$blorm_plugin_options_frontend = array();

    if ( isset( $_POST['_wpnonce'] ) && $_GET['page'] == 'blorm-plugin') {
		if( wp_verify_nonce( $_POST['_wpnonce'], 'blorm-plugin-section-options' )) {

			$blorm_plugin_options_api['api_key'] = trim($_POST['blorm_plugin_options_api']['api_key']);
			if (preg_match('/^[a-z0-9]{60}$/i', $blorm_plugin_options_api['api_key'])) {
				update_option('blorm_plugin_options_api', $blorm_plugin_options_api);
			}

			if (sizeof($_POST['blorm_plugin_options_frontend']) != 0) {
				update_option('blorm_plugin_options_frontend', $_POST['blorm_plugin_options_frontend']);
            }

			if (sizeof($_POST['blorm_plugin_options_category']) != 0) {
				update_option('blorm_plugin_options_category', $_POST['blorm_plugin_options_category']);
			}
        }
	}
}


function blorm_render_options_page() {
    ?>
    <h2>Blorm Plugin Settings</h2>
    <form action="<?php menu_page_url( 'blorm-plugin' ) ?>" method="post">
        <?php
        settings_fields( 'blorm-plugin-section' );
        do_settings_sections( 'blorm-plugin-api-section' );
		do_settings_sections( 'blorm-plugin-frontend-section' );
        do_settings_sections( 'blorm-plugin-category-section' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

/*
 * https://developer.wordpress.org/reference/functions/add_settings_field/
 */

function blorm_register_settings() {

    //register_setting( 'blorm-plugin-api-section', 'blorm_plugin_options_api', 'blorm_plugin_options_api_validate' );

    // api key

    add_settings_section(
            'blorm-plugin-api-section',
            'API Settings',
            'blorm_plugin_api_section_text',
            'blorm-plugin-api-section' );

    add_settings_field(
            'blorm_plugin_setting_api_key',
            'API Key',
            'blorm_plugin_setting_api_key',
            'blorm-plugin-api-section',
            'blorm-plugin-api-section' );

    add_option("blorm_plugin_options_api", array(), "", "yes");

    // frontend rendering options

    add_settings_section(
        'blorm-plugin-frontend-section',
        'Website display settings',
        'blorm_plugin_frontend_section_text',
        'blorm-plugin-frontend-section' );

    add_settings_field(
        'blorm_plugin_setting_add_blorm_info_origin',
        'Origin of post',
        'blorm_plugin_setting_add_blorm_info_origin',
        'blorm-plugin-frontend-section',
        'blorm-plugin-frontend-section' );

	add_settings_field(
		'blorm_plugin_setting_add_blorm_info_shared_content',
		'Shared content widget',
		'blorm_plugin_setting_add_blorm_info_shared_content',
		'blorm-plugin-frontend-section',
		'blorm-plugin-frontend-section' );

	add_settings_field(
		'blorm_plugin_setting_add_blorm_info_reblogged_content',
		'Reblogged content widget',
		'blorm_plugin_setting_add_blorm_info_reblogged_content',
		'blorm-plugin-frontend-section',
		'blorm-plugin-frontend-section' );

	add_option("blorm_plugin_options_frontend", array(), "", "yes");


	// display categories

	add_settings_section(
		'blorm-plugin-category-section',
		'Category settings',
		'blorm_plugin_category_section_text',
		'blorm-plugin-category-section' );

	add_settings_field(
		'blorm_plugin_setting_category_automatic_post',
		'Automatic post',
		'blorm_plugin_setting_category_automatic_post',
		'blorm-plugin-category-section',
		'blorm-plugin-category-section' );

	add_settings_field(
		'blorm_plugin_setting_category_display_reblog',
		'Show reblogged posts',
		'blorm_plugin_setting_category_display_reblog',
		'blorm-plugin-category-section',
		'blorm-plugin-category-section' );


	add_option("blorm_plugin_options_category", array(), "", "yes");


}
add_action( 'admin_init', 'blorm_register_settings' );


function blorm_plugin_api_section_text() {
    echo '<p>Here you can set all the options for using the API</p>';
}

function blorm_plugin_frontend_section_text() {
    echo '<p>Here you can set all the options for using the Plugin on the web</p>';
}

function blorm_plugin_category_section_text() {
	echo '<p>Select categories for showing posts on your page or automatic pushing to blorm</p>';
}

function blorm_plugin_setting_api_key() {
    $options = get_option( 'blorm_plugin_options_api' );

    $value = "";
    if (isset( $options['api_key'] )) {
        $value = $options['api_key'];
    }
    echo "<input id='blorm_plugin_options_api_key' name='blorm_plugin_options_api[api_key]' type='password' size='60' value='".esc_attr( $value )."' />";
}



function blorm_plugin_setting_add_blorm_info_origin() {

    $options = get_option( 'blorm_plugin_options_frontend' );

	$value = "";
	if (isset( $options['position_info_origin'] )) {
		$value = $options['position_info_origin'];
	}

	$isSelected = function($option_value) use ($value){
		if ($value == $option_value) {
			return "selected";
		}
	};

	//var_dump($isSelected('add_blorm_info_before_post'));die();
	echo "<p>Select the position of the post-origin-info.<br>Should be NOT identical with the widgets.<br><br></p>";
	echo "<select id='blorm_plugin_setting_blorm_category_automatic' name='blorm_plugin_options_frontend[position_info_origin]'>\n
            <option value='-'>Do not render</option>\n
            <option value='add_blorm_info_before_post' ".$isSelected('add_blorm_info_before_post').">before post</option>\n
            <option value='add_blorm_info_after_post' ".$isSelected('add_blorm_info_after_post').">after post</option>\n
            <option value='add_blorm_info_before_content' ".$isSelected('add_blorm_info_before_content').">before content</option>\n
            <option value='add_blorm_info_after_content' ".$isSelected('add_blorm_info_after_content').">after content</option>\n
            <option value='add_blorm_info_before_content_container' ".$isSelected('add_blorm_info_before_content_container').">before content container</option>\n
            <option value='add_blorm_info_after_content_container' ".$isSelected('add_blorm_info_after_content_container').">after content container</option>\n
            <option value='add_blorm_info_before_header_container' ".$isSelected('add_blorm_info_before_header_container').">before post header container</option>\n
            <option value='add_blorm_info_after_header_container' ".$isSelected('add_blorm_info_after_header_container').">after post header container</option>\n
            <option value='add_blorm_info_before_footer_container' ".$isSelected('add_blorm_info_before_footer_container').">before post footer container</option>\n
            <option value='add_blorm_info_after_footer_container' ".$isSelected('add_blorm_info_after_footer_container').">after post footer container</option>\n
           </select>";

}

function blorm_plugin_setting_add_blorm_info_shared_content() {

	$options = get_option( 'blorm_plugin_options_frontend' );

	$value = "";
	if (isset( $options['position_info_created_post'] )) {
		$value = $options['position_info_created_post'];
	}

	$isSelected = function($option_value) use ($value){
		if ($value == $option_value) {
			return "selected";
		}
	};

	//var_dump($isSelected('add_blorm_info_before_post'));die();
	echo "<p>Select the position of the info widget for your shared posts.<br>Shared and reblogged widget may have same position.<br><br></p>";
	echo "<select id='blorm_plugin_setting_blorm_category_automatic' name='blorm_plugin_options_frontend[position_info_created_post]'>\n
            <option value='-'>Do not render</option>\n
            <option value='add_blorm_info_before_post' ".$isSelected('add_blorm_info_before_post').">before post</option>\n
            <option value='add_blorm_info_after_post' ".$isSelected('add_blorm_info_after_post').">after post</option>\n
            <option value='add_blorm_info_before_content' ".$isSelected('add_blorm_info_before_content').">before content</option>\n
            <option value='add_blorm_info_after_content' ".$isSelected('add_blorm_info_after_content').">after content</option>\n
            <option value='add_blorm_info_before_content_container' ".$isSelected('add_blorm_info_before_content_container').">before content container</option>\n
            <option value='add_blorm_info_after_content_container' ".$isSelected('add_blorm_info_after_content_container').">after content container</option>\n
            <option value='add_blorm_info_before_header_container' ".$isSelected('add_blorm_info_before_header_container').">before post header container</option>\n
            <option value='add_blorm_info_after_header_container' ".$isSelected('add_blorm_info_after_header_container').">after post header container</option>\n
            <option value='add_blorm_info_before_footer_container' ".$isSelected('add_blorm_info_before_footer_container').">before post footer container</option>\n
            <option value='add_blorm_info_after_footer_container' ".$isSelected('add_blorm_info_after_footer_container').">after post footer container</option>\n
           </select>";

}


function blorm_plugin_setting_add_blorm_info_reblogged_content() {

	$options = get_option( 'blorm_plugin_options_frontend' );

	$value = "";
	if (isset( $options['position_info_reblogged_post'] )) {
		$value = $options['position_info_reblogged_post'];
	}

	$isSelected = function($option_value) use ($value){
		if ($value == $option_value) {
			return "selected";
		}
	};

	//var_dump($isSelected('add_blorm_info_before_post'));die();
	echo "<p>Select the position of the info widget for your reblogged posts.<br><br></p>";
	echo "<select id='blorm_plugin_setting_blorm_category_automatic' name='blorm_plugin_options_frontend[position_info_reblogged_post]'>\n
            <option value='-'>Do not render</option>\n
            <option value='add_blorm_info_before_post' ".$isSelected('add_blorm_info_before_post').">before post</option>\n
            <option value='add_blorm_info_after_post' ".$isSelected('add_blorm_info_after_post').">after post</option>\n
            <option value='add_blorm_info_before_content' ".$isSelected('add_blorm_info_before_content').">before content</option>\n
            <option value='add_blorm_info_after_content' ".$isSelected('add_blorm_info_after_content').">after content</option>\n
            <option value='add_blorm_info_before_content_container' ".$isSelected('add_blorm_info_before_content_container').">before content container</option>\n
            <option value='add_blorm_info_after_content_container' ".$isSelected('add_blorm_info_after_content_container').">after content container</option>\n
            <option value='add_blorm_info_before_header_container' ".$isSelected('add_blorm_info_before_header_container').">before post header container</option>\n
            <option value='add_blorm_info_after_header_container' ".$isSelected('add_blorm_info_after_header_container').">after post header container</option>\n
            <option value='add_blorm_info_before_footer_container' ".$isSelected('add_blorm_info_before_footer_container').">before post footer container</option>\n
            <option value='add_blorm_info_after_footer_container' ".$isSelected('add_blorm_info_after_footer_container').">after post footer container</option>\n
           </select>";

}



function blorm_plugin_setting_category_automatic_post() {
	$options = get_option( 'blorm_plugin_options_category' );

	$value = "";
	if (isset( $options['blorm_category_automatic'] )) {
		$value = $options['blorm_category_automatic'];
	}

	$categories = get_categories( array(
		'orderby' => 'name',
		'order'   => 'ASC',
		'hide_empty'      => false,
	) );

	echo "<p>Posts from this category will be pushed to blorm automatic.<br><br></p>";
	echo "<select id='blorm_plugin_setting_blorm_category_automatic' name='blorm_plugin_options_category[blorm_category_automatic]'>\n
            <option value='-'>---</option>";
	foreach( $categories as $category ) {
		if ($value == $category->cat_ID) {
			echo "<option value=\"".$category->cat_ID."\" selected>".$category->name."</option>";
		} else {
			echo "<option value=\"".$category->cat_ID."\">".$category->name."</option>";
		}
	}
	echo "</select>";

}

function  blorm_plugin_setting_category_display_reblog() {
	$options = get_option( 'blorm_plugin_options_category' );

	$value = "";
	if (isset( $options['blorm_category_show_reblogged'] )) {
		$value = $options['blorm_category_show_reblogged'];
	}

	$categories = get_categories( array(
		'orderby' => 'name',
		'order'   => 'ASC',
		'hide_empty'      => false,
	) );

	echo "<p>Select a category to display your reblogged posts.<br>If nothing is selected it will be put in the standard loop wich is shown on home in most cases.<br><br></p>";
	echo "<select id='blorm_plugin_setting_blorm_category_show_reblogged' name='blorm_plugin_options_category[blorm_category_show_reblogged]'>\n
            <option value='-'>---</option>";
	foreach( $categories as $category ) {
		if ($value == $category->cat_ID) {
			echo "<option value=\"".$category->cat_ID."\" selected>".$category->name."</option>";
		} else {
			echo "<option value=\"".$category->cat_ID."\">".$category->name."</option>";
		}
	}
	echo "</select>";

}


function  blorm_plugin_setting_blorm_blormbar_position() {
	$options = get_option( 'blorm_plugin_options' );

	$value = "";
	if (isset( $options['blorm_blormbar_position'] )) {
		$value = $options['blorm_blormbar_position'];
	}

	echo "<select id='blorm_plugin_setting_blorm_blormbar_position' name='blorm_plugin_options[blorm_blormbar_position]'>\n
            <option value='-'>---</option>\n
            <option value='header' selected>header</option>\n
            <option value='content' >content</option>\n
            <option value='footer' >footer</option>\n
            </select>";
	/*foreach( $categories as $category ) {
		if ($value == $category->cat_ID) {
			echo "<option value=\"".$category->cat_ID."\" selected>".$category->name."</option>";
		} else {
			echo "<option value=\"".$category->cat_ID."\">".$category->name."</option>";
		}
	}
	echo "</select>";*/

}


function blorm_plugin_setting_add_blorm_info() {

	echo "<p>The blorm info bar shows interactions of the post with other pages (retweet, share and comments) to the users of yout side. 
            <br>The best place for the menue bar depends on the design of your theme.</p>
             <p>We suggest to select just one of the possible positions.</p>";

	$options = get_option( 'blorm_plugin_options_frontend' );

	$add_blorm_info_before_title = "";
	if (isset( $options['add_blorm_info_before_title'] )) {
		$add_blorm_info_before_title = $options['add_blorm_info_before_title'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_title ), false)) {
		echo "<input id='blorm_plugin_setting_info_before_title' name='blorm_plugin_options_frontend[add_blorm_info_before_title]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_info_before_title' name='blorm_plugin_options_frontend[add_blorm_info_before_title]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before title</p>";

	$add_blorm_info_after_title = "";
	if (isset( $options['add_blorm_info_after_title'] )) {
		$add_blorm_info_after_title = $options['add_blorm_info_after_title'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_title ), false)) {
		echo "<input id='blorm_plugin_setting_info_after_title' name='blorm_plugin_options_frontend[add_blorm_info_after_title]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_info_after_title' name='blorm_plugin_options_frontend[add_blorm_info_after_title]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after title</p>";

	$add_blorm_info_before_content = "";
	if (isset( $options['add_blorm_info_before_content'] )) {
		$add_blorm_info_before_content = $options['add_blorm_info_before_content'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_content ), false)) {
		echo "<input id='blorm_plugin_setting_info_before_content' name='blorm_plugin_options_frontend[add_blorm_info_before_content]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_info_before_content' name='blorm_plugin_options_frontend[add_blorm_info_before_content]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before content</p>";

	$add_blorm_info_after_content = "";
	if (isset( $options['add_blorm_info_after_content'] )) {
		$add_blorm_info_after_content = $options['add_blorm_info_after_content'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_content ), false)) {
		echo "<input id='blorm_plugin_setting_info_after_content' name='blorm_plugin_options_frontend[add_blorm_info_after_content]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_info_after_content' name='blorm_plugin_options_frontend[add_blorm_info_after_content]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after content</p>";

	echo "<br><hr><br>";
	echo "<p>If your theme uses the standard class names for the posts you may also try to use on of these positions</p>";

	$add_blorm_info_before_post_header = "";
	if (isset( $options['add_blorm_info_before_post_header'] )) {
		$add_blorm_info_before_post_header = $options['add_blorm_info_before_post_header'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_post_header ), false)) {
		echo "<input id='blorm_plugin_setting_before_post_header' name='blorm_plugin_options_frontend[add_blorm_info_before_post_header]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_before_post_header' name='blorm_plugin_options_frontend[add_blorm_info_before_post_header]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before html-block 'post header'</p>";


	$add_blorm_info_after_post_header = "";
	if (isset( $options['add_blorm_info_after_post_header'] )) {
		$add_blorm_info_after_post_header = $options['add_blorm_info_after_post_header'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_post_header ), false)) {
		echo "<input id='blorm_plugin_setting_after_post_header' name='blorm_plugin_options_frontend[add_blorm_info_after_post_header]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_after_post_header' name='blorm_plugin_options_frontend[add_blorm_info_after_post_header]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after html-block 'post header'</p>";


	$add_blorm_info_before_post_content = "";
	if (isset( $options['add_blorm_info_before_post_content'] )) {
		$add_blorm_info_before_post_content = $options['add_blorm_info_before_post_content'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_post_content ), false)) {
		echo "<input id='blorm_plugin_setting_before_post_content' name='blorm_plugin_options_frontend[add_blorm_info_before_post_content]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_before_post_content' name='blorm_plugin_options_frontend[add_blorm_info_before_post_content]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before html-block 'post content'</p>";


	$add_blorm_info_after_post_content = "";
	if (isset( $options['add_blorm_info_after_post_content'] )) {
		$add_blorm_info_after_post_content = $options['add_blorm_info_after_post_content'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_post_content ), false)) {
		echo "<input id='blorm_plugin_setting_after_post_content' name='blorm_plugin_options_frontend[add_blorm_info_after_post_content]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_after_post_content' name='blorm_plugin_options_frontend[add_blorm_info_after_post_content]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after html-block 'post content'</p>";


	$add_blorm_info_before_post_footer = "";
	if (isset( $options['add_blorm_info_before_post_footer'] )) {
		$add_blorm_info_before_post_footer = $options['add_blorm_info_before_post_footer'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_before_post_footer ), false)) {
		echo "<input id='blorm_plugin_setting_before_post_footer' name='blorm_plugin_options_frontend[add_blorm_info_before_post_footer]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_before_post_footer' name='blorm_plugin_options_frontend[add_blorm_info_before_post_footer]' type='checkbox' value='show' />";
	}
	echo "&nbsp;before html-block 'post footer'</p>";


	$add_blorm_info_after_post_footer = "";
	if (isset( $options['add_blorm_info_after_post_footer'] )) {
		$add_blorm_info_after_post_footer = $options['add_blorm_info_after_post_footer'];
	}
	echo "<p>";
	if (checked("show", esc_attr( $add_blorm_info_after_post_footer ), false)) {
		echo "<input id='blorm_plugin_setting_after_post_footer' name='blorm_plugin_options_frontend[add_blorm_info_after_post_footer]' type='checkbox' value='show' checked />";
	} else {
		echo "<input id='blorm_plugin_setting_after_post_footer' name='blorm_plugin_options_frontend[add_blorm_info_after_post_footer]' type='checkbox' value='show' />";
	}
	echo "&nbsp;after html-block 'post footer'</p>";

}
