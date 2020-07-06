<?php


function blorm_add_settings_page() {
    add_options_page(
        'Blorm plugin page',    // title for browser window
        'Blorm Settings',       // menue name
        'manage_options',       //
        'blorm-example-plugin',
        'blorm_render_plugin_settings_page' // name of the rendering function
    );
}
add_action( 'admin_menu', 'blorm_add_settings_page' );

function blorm_render_plugin_settings_page() {
    ?>
    <h2>Blorm Plugin Settings</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'blorm_plugin_options' );
        do_settings_sections( 'blorm_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function blorm_register_settings() {
    register_setting( 'blorm_plugin_options', 'blorm_plugin_options', 'blorm_plugin_options_validate' );
    add_settings_section( 'api_settings', 'API Settings', 'blorm_plugin_section_text', 'blorm_plugin' );

    add_settings_field( 'blorm_plugin_setting_api_key', 'API Key', 'blorm_plugin_setting_api_key', 'blorm_plugin', 'api_settings' );
    add_settings_field( 'blorm_plugin_setting_add_blorm_to_home_loop', 'Show Blorm Reblog on Start page', 'blorm_plugin_setting_add_blorm_to_home_loop', 'blorm_plugin', 'api_settings' );
    //add_settings_field( 'blorm_plugin_setting_start_date', 'Start Date', 'blorm_plugin_setting_start_date', 'blorm_plugin', 'api_settings' );
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

    return $newinput;
}

function blorm_plugin_section_text() {
    echo '<p>Here you can set all the options for using the API</p>';
}

function blorm_plugin_setting_api_key() {
    $options = get_option( 'blorm_plugin_options' );
    echo "<input id='blorm_plugin_setting_api_key' name='blorm_plugin_options[api_key]' type='password' size='60' value='".esc_attr( $options['api_key'] )."' />";
}

function blorm_plugin_setting_add_blorm_to_home_loop() {
    $options = get_option( 'blorm_plugin_options' );
    if (checked("show", $options['add_blorm_to_home_loop'], false)) {
        echo "<input id='blorm_plugin_setting_results_limit' name='blorm_plugin_options[add_blorm_to_home_loop]' type='checkbox' value='show' checked />";
    } else {
        echo "<input id='blorm_plugin_setting_results_limit' name='blorm_plugin_options[add_blorm_to_home_loop]' type='checkbox' value='show' />";

    }
}
/*
function blorm_plugin_setting_start_date() {
    $options = get_option( 'blorm_plugin_options' );
    echo "<input id='blorm_plugin_setting_start_date' name='blorm_plugin_options[start_date]' type='text' value='".esc_attr( $options['start_date'] )."' />";
}*/
