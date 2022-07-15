<?php

/**
 * Plugin Name: WC sync with third party API 
 * Version: 1.0.0
 * Description: This plugin synchronise woocommerce with a third party API data
 * Author: Sina Ghiasi
 * Text Domain: wc-sync-with-API
 *
 * @author Sina Ghiasi
 */

if (!defined('ABSPATH')) {
    exit;
}


//NOTE adding adming menu
add_action('admin_menu', 'wcswa_admin_menu');
function wcswa_admin_menu()
{

    add_menu_page(

        __('Products updating page', 'wc-sync-with-API'),

        __('Update products', 'wc-sync-with-API'),

        'manage_options',

        'wcswa-settings',

        'wcswa_page_contents',

        'dashicons-update',

        3
    );
}

add_action('admin_init',  'wcswa_register_setting');
function wcswa_register_setting()
{

    register_setting(
        'wcswa_settings', // settings group name
        'wcswa_api_key', // option name
        'sanitize_text_field' // sanitization function
    );

    add_settings_section(
        'wcswa_general_settings_section', // section ID
        '', // title 
        '', // callback function
        'wcswa-settings' // page slug
    );

    add_settings_field(
        'wcswa_api_key',
        'API key', //lable
        'wcswa_api_field_html', // function which prints the field
        'wcswa-settings', // page slug
        'wcswa_general_settings_section', // section ID
        array(
            'wcswa_api_key',
            'label_for' => 'wcswa_api_key',
            'class' => 'wcswa-class', // for <tr> element
        )
    );
}

function wcswa_api_field_html($args)
{
    $option_status = get_option($args[0]) ? __('Already set and saved', 'wc-sync-with-API') : __('Not set', 'wc-sync-with-API');
    echo '<input style="width:70%;" id="' . $args[0] . '" name="' . $args[0] . '" placeholder="' . $option_status . '" />';
}
//NOTE plugin database table
global $wcswa_db_version;
$wcswa_db_version = '1.0';
register_activation_hook(__FILE__, 'wcswa_install_db');

function wcswa_install_db()
{
    global $wpdb;
    global $wcswa_db_version;

    $table_name = $wpdb->prefix . 'wcswa_db';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		lastUpdate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        userTimezone TINYTEXT NOT NULL,
		updatedProducts SMALLINT(255) UNSIGNED NOT NULL,
		allProducts SMALLINT(255) UNSIGNED NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    add_option('wcswa_db_version', $wcswa_db_version);
}

function wcswa_replace_data($currentTime, $userTimezone, $updatedProducts, $allProducts)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'wcswa_db';

    $wpdb->replace(
        $table_name,
        array(
            'id' => 1,
            'lastUpdate' => $currentTime,
            'userTimezone' => $userTimezone,
            'updatedProducts' => $updatedProducts,
            'allProducts' => $allProducts,
        )
    );
}

function wcswa_page_contents()
{
    //NOTE API section

?>

    <div class="wrap">
        <h2><?php _e('Updating products informations', 'wc-sync-with-API') ?></h2>
        <p><?php _e('In the following section, after setting the key, you can manually update the information by pressing the update button (the plugin automatically updates products daily)', 'wc-sync-with-API') ?></p>
        <form method="post" action="options.php">
            <?php
            settings_fields('wcswa_settings'); // settings group name
            do_settings_sections('wcswa-settings'); // just a page slug
            submit_button(__('Save key', 'wc-sync-with-API'));
            ?>
        </form>
    </div>
    <?php
    //NOTE update section

    //using nonce to secure request
    $nonce = wp_create_nonce("wcswa_update_products_nonce");
    $link = admin_url('admin-ajax.php?action=wcswa_update_products' . '&nonce=' . $nonce);
    ?>
    <p><?php _e('Date of last update :', 'wc-sync-with-API') ?> <span id="wcswa-update-date"></span></p>
    <p><?php _e('Number of products last updated :', 'wc-sync-with-API') ?> <span id="wcswa-product-count"></span></p>
    <a id="wcswa-update-btn" class="button button-primary" data-nonce="<?php echo $nonce ?>" href="<?php echo $link ?>">
        <?php _e('Update products', 'wc-sync-with-API') ?>
    </a>
    <div style="max-width:75%; margin:30px 0;" id="wcswa-update-result">
        <p id="wcswa-update-result-message"></p>
    </div>
<?php
}


// define the actions for logged in users
add_action("wp_ajax_wcswa_update_products", "wcswa_update_products");
function wcswa_update_products()
{
    // nonce check for an extra layer of security, the function will exit if it fails
    if (!wp_verify_nonce($_REQUEST['nonce'], "wcswa_update_products_nonce")) {
        exit("Permission denied !");
    }

    //NOTE update process section

    //sample data
    $update_process = true;
    $new_product_count = 10;

    date_default_timezone_set($_REQUEST['userTimezone']);
    $new_date = date("Y-m-d h:i:s");

    wcswa_replace_data($new_date, $_REQUEST['userTimezone'], 50, 100);

    //NOTE update result section
    if ($update_process === false) {
        $result['type'] = "error";
    } else {
        $result['type'] = "success";
        $result['product_count'] = $new_product_count;
        $result['date'] =  $new_date;
    }

    // check if action was fired via Ajax call. If yes, JS code will be triggered, else the user is redirected to the post page
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
    } else {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }

    // using die() function to end script - very important
    die();
}

// define the actions for logged out users
add_action("wp_ajax_nopriv_wcswa_update_products", "please_login");
function please_login()
{
    echo "You must log in to update products !";
    die();
}

//NOTE enqueue Ajax script file
add_action('init', 'script_enqueuer');
function script_enqueuer()
{
    // Register the JS file with a unique handle, file location, and an array of dependencies
    wp_register_script("wcswa_ajax_script", plugin_dir_url(__FILE__) . 'ajax_script.js', array('jquery'));

    // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
    wp_localize_script('wcswa_ajax_script', 'wcswaAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

    wp_enqueue_script('jquery');
    wp_enqueue_script('wcswa_ajax_script');
}
