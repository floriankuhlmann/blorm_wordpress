<?php


function blorm_add_settings_page() {
    add_options_page(
        'Blorm plugin page',    // title for browser window
        'Blorm Settings',       // menue name
        'manage_options',       //
        'blorm-plugin',
        'blorm_render_plugin_settings_page' // name of the rendering function
    );
}
add_action( 'admin_menu', 'blorm_add_settings_page' );

function blorm_render_plugin_settings_page() {
    ?>
    <h2>Blorm Plugin Settings</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'blorm-plugin' );
        do_settings_sections( 'blorm-plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

/*
 * https://developer.wordpress.org/reference/functions/add_settings_field/
 */

function blorm_register_settings() {
    register_setting( 'blorm-plugin', 'blorm_plugin_options', 'blorm_plugin_options_validate' );
    add_settings_section( 'blorm-plugin', 'API Settings', 'blorm_plugin_section_text', 'blorm-plugin' );

    add_settings_field(
            'blorm_plugin_setting_api_key',
            'API Key',
            'blorm_plugin_setting_api_key',
            'blorm-plugin',
            'blorm-plugin' );

    add_settings_field(
            'blorm_plugin_setting_add_blorm_to_home_loop',
            'Show Blorm Reblog on Start page',
            'blorm_plugin_setting_add_blorm_to_home_loop',
            'blorm-plugin',
            'blorm-plugin' );

    /*add_settings_field(
            'blorm_plugin_setting_start_date',
            'Start Date',
            'blorm_plugin_setting_start_date',
            'blorm-plugin',
            'blorm-plugin' );*/

    add_settings_field(
        'blorm_plugin_setting_blorm_category_automatic',
        'Automatic share this category to blorm',
        'blorm_plugin_setting_blorm_category_automatic',
        'blorm-plugin',
        'blorm-plugin' );

    add_settings_field(
        'blorm_plugin_setting_blorm_category_show_reblogged',
        'Show reblogged posts in this category',
        'blorm_plugin_setting_blorm_category_show_reblogged',
        'blorm-plugin',
        'blorm-plugin' );

}
add_action( 'admin_init', 'blorm_register_settings' );


function blorm_plugin_options_validate($input)
{
    $newinput['api_key'] = trim($input['api_key']);
    if (!preg_match('/^[a-z0-9]{60}$/i', $newinput['api_key'])) {
        $newinput['api_key'] = '';
    }

    $newinput['add_blorm_to_home_loop'] = $input['add_blorm_to_home_loop'];

    /*$newinput['results_limit'] = trim($input['add_blorm_to_home_loop']);
    if (!preg_match('/^[a-zA-Z0-9]{0,10}$/', $newinput['add_blorm_to_home_loop'])) {
        $newinput['results_limit'] = '';
    }

    $newinput['start_date'] = trim($input['start_date']);
    if (!preg_match('/^[a-zA-Z0-9]{0,10}$/', $newinput['start_date'])) {
        $newinput['start_date'] = '';
    }*/

    $newinput['blorm_category_automatic'] = trim($input['blorm_category_automatic']);

    $newinput['blorm_category_show_reblogged'] = trim($input['blorm_category_show_reblogged']);


    return $newinput;
}

function blorm_plugin_section_text() {
    echo '<p>Here you can set all the options for using the API</p>';
}

function blorm_plugin_setting_api_key() {
    $options = get_option( 'blorm_plugin_options' );

    $value = "";
    if (isset( $options['api_key'] )) {
        $value = $options['api_key'];
    }
    echo "<input id='blorm_plugin_setting_api_key' name='blorm_plugin_options[api_key]' type='password' size='60' value='".esc_attr( $value )."' />";
}

function blorm_plugin_setting_add_blorm_to_home_loop() {
    $options = get_option( 'blorm_plugin_options' );

    $value = "";
    if (isset( $options['add_blorm_to_home_loop'] )) {
        $value = $options['add_blorm_to_home_loop'];
    }
    if (checked("show", esc_attr( $value ), false)) {
        echo "<input id='blorm_plugin_setting_results_limit' name='blorm_plugin_options[add_blorm_to_home_loop]' type='checkbox' value='show' checked />";
    } else {
        echo "<input id='blorm_plugin_setting_results_limit' name='blorm_plugin_options[add_blorm_to_home_loop]' type='checkbox' value='show' />";

    }
}

function blorm_plugin_setting_blorm_category_automatic() {
    $options = get_option( 'blorm_plugin_options' );

    $value = "";
    if (isset( $options['blorm_category_automatic'] )) {
        $value = $options['blorm_category_automatic'];
    }

    $categories = get_categories( array(
        'orderby' => 'name',
        'order'   => 'ASC',
        'hide_empty'      => false,
    ) );

    echo "<select id='blorm_plugin_setting_blorm_category_automatic' name='blorm_plugin_options[blorm_category_automatic]'>\n
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

function  blorm_plugin_setting_blorm_category_show_reblogged() {
    $options = get_option( 'blorm_plugin_options' );

    $value = "";
    if (isset( $options['blorm_category_show_reblogged'] )) {
        $value = $options['blorm_category_show_reblogged'];
    }

    $categories = get_categories( array(
        'orderby' => 'name',
        'order'   => 'ASC',
        'hide_empty'      => false,
    ) );

    echo "<select id='blorm_plugin_setting_blorm_category_show_reblogged' name='blorm_plugin_options[blorm_category_show_reblogged]'>\n
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