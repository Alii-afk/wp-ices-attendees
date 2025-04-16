<?php

/* 
Plugin Name: ICES Attendees
Plugin URI: https://alihaiderhamadani.com/ices-attendees-wp-plugin
Description: A Wordpress plugin to manage attendees for ICES events 
Version: 1.0
Author: Syed Ali Haider
Author URI: https://alihaiderhamadani.com
License: GPL2
*/


// create menu option in wordpress
add_action('admin_menu', 'ices_admin_menu');
function ices_admin_menu()
{
    add_menu_page(
        'Manage Attendees',
        'Attendees',
        'manage_options',
        'ices_attendees',
        'display_ices_attendees_page',
        'dashicons-groups',
        6
    );
}

$base_dir = __DIR__; // Adjust this path if your files are in a subdirectory

// Require the necessary files
require_once $base_dir . '/admin/view-attendees-page.php';
require_once $base_dir . '/includes/class-attendee-csv.php';
require_once $base_dir . '/includes/class-attendee-emails.php';

register_activation_hook(__FILE__, 'create_attendees_table');
function create_attendees_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ices_attendees';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        order_id bigint(20) NOT NULL,
        product_id bigint(20) NOT NULL,
        attendee_first_name varchar(100) NOT NULL,
        attendee_last_name varchar(100) NOT NULL,
        attendee_email varchar(100) NOT NULL,
        attendee_organization varchar(255),
        attendee_dietary varchar(255),
        attendee_country varchar(100),
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// create a metabox with manage attendees checkbox
add_action('add_meta_boxes', 'ices_add_metabox');
function ices_add_metabox()
{
    add_meta_box(
        'ices-attendees-metabox',
        'Manage Attendees',
        'ices_metabox_callback',
        'product',
        'side',
        'default'
    );
}

// save the metabox data
function ices_metabox_callback($post)
{
    $value = get_post_meta($post->ID, 'manage_attendees', true);
    wp_nonce_field(basename(__FILE__), 'ices_attendees_nonce');
    $is_checked = "";
    if ($value == "yes") {
        $is_checked = "checked";
    } ?>
    <p>
        <label for="manage_attendees">Manage Attendees</label>
        <input type="checkbox" name="manage_attendees" id="manage_attendees" value="yes" <?php echo $is_checked; ?> />
    </p>
<?php
}

// save the metabox data
add_action('save_post', 'ices_save_metabox');
function ices_save_metabox($post_id)
{
    if (!isset($_POST['ices_attendees_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['ices_attendees_nonce'], basename(__FILE__))) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $value = isset($_POST['manage_attendees']) ? 'yes' : 'no';
    update_post_meta($post_id, 'manage_attendees', $value);
}

// add inline script in the footer on product page
add_action('wp_footer', 'inline_script_for_product_page');
function inline_script_for_product_page()
{
    if (! is_product()) return;
    // check if product has meta - Manage Attendees
    global $product;
    if (! $product->get_meta('manage_attendees')) return; ?>
    <script>
        jQuery(document).ready(function($) {
            // Target the form or a specific container on your product page
            let formContainer = $('form.cart'),
                quantityInput = formContainer.find('input.qty');
            // Create and append the attendee fields container if not already present
            let attendeeFieldsContainer = $('<div id="attendee-fields-container" style="clear:both"></div>');
            formContainer.append(attendeeFieldsContainer);
            const countries = `
                <option value=""><?php _e('Select Country', 'textdomain'); ?></option>
                <option value="Afghanistan"><?php _e('Afghanistan', 'textdomain'); ?></option>
                <option value="Åland Islands"><?php _e('Åland Islands', 'textdomain'); ?></option>
                <option value="Albania"><?php _e('Albania', 'textdomain'); ?></option>
                <option value="Algeria"><?php _e('Algeria', 'textdomain'); ?></option>
                <option value="American Samoa"><?php _e('American Samoa', 'textdomain'); ?></option>
                <option value="Andorra"><?php _e('Andorra', 'textdomain'); ?></option>
                <option value="Angola"><?php _e('Angola', 'textdomain'); ?></option>
                <option value="Anguilla"><?php _e('Anguilla', 'textdomain'); ?></option>
                <option value="Antarctica"><?php _e('Antarctica', 'textdomain'); ?></option>
                <option value="Antigua and Barbuda"><?php _e('Antigua and Barbuda', 'textdomain'); ?></option>
                <option value="Argentina"><?php _e('Argentina', 'textdomain'); ?></option>
                <option value="Armenia"><?php _e('Armenia', 'textdomain'); ?></option>
                <option value="Aruba"><?php _e('Aruba', 'textdomain'); ?></option>
                <option value="Australia"><?php _e('Australia', 'textdomain'); ?></option>
                <option value="Austria"><?php _e('Austria', 'textdomain'); ?></option>
                <option value="Azerbaijan"><?php _e('Azerbaijan', 'textdomain'); ?></option>
                <option value="Bahamas"><?php _e('Bahamas', 'textdomain'); ?></option>
                <option value="Bahrain"><?php _e('Bahrain', 'textdomain'); ?></option>
                <option value="Bangladesh"><?php _e('Bangladesh', 'textdomain'); ?></option>
                <option value="Barbados"><?php _e('Barbados', 'textdomain'); ?></option>
                <option value="Belarus"><?php _e('Belarus', 'textdomain'); ?></option>
                <option value="Belgium"><?php _e('Belgium', 'textdomain'); ?></option>
                <option value="Belize"><?php _e('Belize', 'textdomain'); ?></option>
                <option value="Benin"><?php _e('Benin', 'textdomain'); ?></option>
                <option value="Bermuda"><?php _e('Bermuda', 'textdomain'); ?></option>
                <option value="Bhutan"><?php _e('Bhutan', 'textdomain'); ?></option>
                <option value="Bolivia"><?php _e('Bolivia', 'textdomain'); ?></option>
                <option value="Bosnia and Herzegovina"><?php _e('Bosnia and Herzegovina', 'textdomain'); ?></option>
                <option value="Botswana"><?php _e('Botswana', 'textdomain'); ?></option>
                <option value="Bouvet Island"><?php _e('Bouvet Island', 'textdomain'); ?></option>
                <option value="Brazil"><?php _e('Brazil', 'textdomain'); ?></option>
                <option value="British Indian Ocean Territory"><?php _e('British Indian Ocean Territory', 'textdomain'); ?></option>
                <option value="Brunei Darussalam"><?php _e('Brunei Darussalam', 'textdomain'); ?></option>
                <option value="Bulgaria"><?php _e('Bulgaria', 'textdomain'); ?></option>
                <option value="Burkina Faso"><?php _e('Burkina Faso', 'textdomain'); ?></option>
                <option value="Burundi"><?php _e('Burundi', 'textdomain'); ?></option>
                <option value="Cambodia"><?php _e('Cambodia', 'textdomain'); ?></option>
                <option value="Cameroon"><?php _e('Cameroon', 'textdomain'); ?></option>
                <option value="Canada"><?php _e('Canada', 'textdomain'); ?></option>
                <option value="Cape Verde"><?php _e('Cape Verde', 'textdomain'); ?></option>
                <option value="Cayman Islands"><?php _e('Cayman Islands', 'textdomain'); ?></option>
                <option value="Central African Republic"><?php _e('Central African Republic', 'textdomain'); ?></option>
                <option value="Chad"><?php _e('Chad', 'textdomain'); ?></option>
                <option value="Chile"><?php _e('Chile', 'textdomain'); ?></option>
                <option value="China"><?php _e('China', 'textdomain'); ?></option>
                <option value="Christmas Island"><?php _e('Christmas Island', 'textdomain'); ?></option>
                <option value="Cocos (Keeling) Islands"><?php _e('Cocos (Keeling) Islands', 'textdomain'); ?></option>
                <option value="Colombia"><?php _e('Colombia', 'textdomain'); ?></option>
                <option value="Comoros"><?php _e('Comoros', 'textdomain'); ?></option>
                <option value="Congo"><?php _e('Congo', 'textdomain'); ?></option>
                <option value="Congo, The Democratic Republic of The"><?php _e('Congo, The Democratic Republic of The', 'textdomain'); ?></option>
                <option value="Cook Islands"><?php _e('Cook Islands', 'textdomain'); ?></option>
                <option value="Costa Rica"><?php _e('Costa Rica', 'textdomain'); ?></option>
                <option value="Cote D'ivoire"><?php _e("Cote D'ivoire", 'textdomain'); ?></option>
                <option value="Croatia"><?php _e('Croatia', 'textdomain'); ?></option>
                <option value="Cuba"><?php _e('Cuba', 'textdomain'); ?></option>
                <option value="Cyprus"><?php _e('Cyprus', 'textdomain'); ?></option>
                <option value="Czech Republic"><?php _e('Czech Republic', 'textdomain'); ?></option>
                <option value="Denmark"><?php _e('Denmark', 'textdomain'); ?></option>
                <option value="Djibouti"><?php _e('Djibouti', 'textdomain'); ?></option>
                <option value="Dominica"><?php _e('Dominica', 'textdomain'); ?></option>
                <option value="Dominican Republic"><?php _e('Dominican Republic', 'textdomain'); ?></option>
                <option value="Ecuador"><?php _e('Ecuador', 'textdomain'); ?></option>
                <option value="Egypt"><?php _e('Egypt', 'textdomain'); ?></option>
                <option value="El Salvador"><?php _e('El Salvador', 'textdomain'); ?></option>
                <option value="Equatorial Guinea"><?php _e('Equatorial Guinea', 'textdomain'); ?></option>
                <option value="Eritrea"><?php _e('Eritrea', 'textdomain'); ?></option>
                <option value="Estonia"><?php _e('Estonia', 'textdomain'); ?></option>
                <option value="Ethiopia"><?php _e('Ethiopia', 'textdomain'); ?></option>
                <option value="Falkland Islands (Malvinas)"><?php _e('Falkland Islands (Malvinas)', 'textdomain'); ?></option>
                <option value="Faroe Islands"><?php _e('Faroe Islands', 'textdomain'); ?></option>
                <option value="Fiji"><?php _e('Fiji', 'textdomain'); ?></option>
                <option value="Finland"><?php _e('Finland', 'textdomain'); ?></option>
                <option value="France"><?php _e('France', 'textdomain'); ?></option>
                <option value="French Guiana"><?php _e('French Guiana', 'textdomain'); ?></option>
                <option value="French Polynesia"><?php _e('French Polynesia', 'textdomain'); ?></option>
                <option value="French Southern Territories"><?php _e('French Southern Territories', 'textdomain'); ?></option>
                <option value="Gabon"><?php _e('Gabon', 'textdomain'); ?></option>
                <option value="Gambia"><?php _e('Gambia', 'textdomain'); ?></option>
                <option value="Georgia"><?php _e('Georgia', 'textdomain'); ?></option>
                <option value="Germany"><?php _e('Germany', 'textdomain'); ?></option>
                <option value="Ghana"><?php _e('Ghana', 'textdomain'); ?></option>
                <option value="Gibraltar"><?php _e('Gibraltar', 'textdomain'); ?></option>
                <option value="Greece"><?php _e('Greece', 'textdomain'); ?></option>
                <option value="Greenland"><?php _e('Greenland', 'textdomain'); ?></option>
                <option value="Grenada"><?php _e('Grenada', 'textdomain'); ?></option>
                <option value="Guadeloupe"><?php _e('Guadeloupe', 'textdomain'); ?></option>
                <option value="Guam"><?php _e('Guam', 'textdomain'); ?></option>
                <option value="Guatemala"><?php _e('Guatemala', 'textdomain'); ?></option>
                <option value="Guernsey"><?php _e('Guernsey', 'textdomain'); ?></option>
                <option value="Guinea"><?php _e('Guinea', 'textdomain'); ?></option>
                <option value="Guinea-bissau"><?php _e('Guinea-bissau', 'textdomain'); ?></option>
                <option value="Guyana"><?php _e('Guyana', 'textdomain'); ?></option>
                <option value="Haiti"><?php _e('Haiti', 'textdomain'); ?></option>
                <option value="Heard Island and Mcdonald Islands"><?php _e('Heard Island and Mcdonald Islands', 'textdomain'); ?></option>
                <option value="Holy See (Vatican City State)"><?php _e('Holy See (Vatican City State)', 'textdomain'); ?></option>
                <option value="Honduras"><?php _e('Honduras', 'textdomain'); ?></option>
                <option value="Hong Kong"><?php _e('Hong Kong', 'textdomain'); ?></option>
                <option value="Hungary"><?php _e('Hungary', 'textdomain'); ?></option>
                <option value="Iceland"><?php _e('Iceland', 'textdomain'); ?></option>
                <option value="India"><?php _e('India', 'textdomain'); ?></option>
                <option value="Indonesia"><?php _e('Indonesia', 'textdomain'); ?></option>
                <option value="Iran, Islamic Republic of"><?php _e('Iran, Islamic Republic of', 'textdomain'); ?></option>
                <option value="Iraq"><?php _e('Iraq', 'textdomain'); ?></option>
                <option value="Ireland"><?php _e('Ireland', 'textdomain'); ?></option>
                <option value="Isle of Man"><?php _e('Isle of Man', 'textdomain'); ?></option>
                <option value="Israel"><?php _e('Israel', 'textdomain'); ?></option>
                <option value="Italy"><?php _e('Italy', 'textdomain'); ?></option>
                <option value="Jamaica"><?php _e('Jamaica', 'textdomain'); ?></option>
                <option value="Japan"><?php _e('Japan', 'textdomain'); ?></option>
                <option value="Jersey"><?php _e('Jersey', 'textdomain'); ?></option>
                <option value="Jordan"><?php _e('Jordan', 'textdomain'); ?></option>
                <option value="Kazakhstan"><?php _e('Kazakhstan', 'textdomain'); ?></option>
                <option value="Kenya"><?php _e('Kenya', 'textdomain'); ?></option>
                <option value="Kiribati"><?php _e('Kiribati', 'textdomain'); ?></option>
                <option value="Korea, Democratic People's Republic of"><?php _e("Korea, Democratic People's Republic of", 'textdomain'); ?></option>
                <option value="Korea, Republic of"><?php _e('Korea, Republic of', 'textdomain'); ?></option>
                <option value="Kuwait"><?php _e('Kuwait', 'textdomain'); ?></option>
                <option value="Kyrgyzstan"><?php _e('Kyrgyzstan', 'textdomain'); ?></option>
                <option value="Lao People's Democratic Republic"><?php _e("Lao People's Democratic Republic", 'textdomain'); ?></option>
                <option value="Latvia"><?php _e('Latvia', 'textdomain'); ?></option>
                <option value="Lebanon"><?php _e('Lebanon', 'textdomain'); ?></option>
                <option value="Lesotho"><?php _e('Lesotho', 'textdomain'); ?></option>
                <option value="Liberia"><?php _e('Liberia', 'textdomain'); ?></option>
                <option value="Libyan Arab Jamahiriya"><?php _e('Libyan Arab Jamahiriya', 'textdomain'); ?></option>
                <option value="Liechtenstein"><?php _e('Liechtenstein', 'textdomain'); ?></option>
                <option value="Lithuania"><?php _e('Lithuania', 'textdomain'); ?></option>
                <option value="Luxembourg"><?php _e('Luxembourg', 'textdomain'); ?></option>
                <option value="Macao"><?php _e('Macao', 'textdomain'); ?></option>
                <option value="Macedonia, The Former Yugoslav Republic of"><?php _e('Macedonia, The Former Yugoslav Republic of', 'textdomain'); ?></option>
                <option value="Madagascar"><?php _e('Madagascar', 'textdomain'); ?></option>
                <option value="Malawi"><?php _e('Malawi', 'textdomain'); ?></option>
                <option value="Malaysia"><?php _e('Malaysia', 'textdomain'); ?></option>
                <option value="Maldives"><?php _e('Maldives', 'textdomain'); ?></option>
                <option value="Mali"><?php _e('Mali', 'textdomain'); ?></option>
                <option value="Malta"><?php _e('Malta', 'textdomain'); ?></option>
                <option value="Marshall Islands"><?php _e('Marshall Islands', 'textdomain'); ?></option>
                <option value="Martinique"><?php _e('Martinique', 'textdomain'); ?></option>
                <option value="Mauritania"><?php _e('Mauritania', 'textdomain'); ?></option>
                <option value="Mauritius"><?php _e('Mauritius', 'textdomain'); ?></option>
                <option value="Mayotte"><?php _e('Mayotte', 'textdomain'); ?></option>
                <option value="Mexico"><?php _e('Mexico', 'textdomain'); ?></option>
                <option value="Micronesia, Federated States of"><?php _e('Micronesia, Federated States of', 'textdomain'); ?></option>
                <option value="Moldova, Republic of"><?php _e('Moldova, Republic of', 'textdomain'); ?></option>
                <option value="Monaco"><?php _e('Monaco', 'textdomain'); ?></option>
                <option value="Mongolia"><?php _e('Mongolia', 'textdomain'); ?></option>
                <option value="Montenegro"><?php _e('Montenegro', 'textdomain'); ?></option>
                <option value="Montserrat"><?php _e('Montserrat', 'textdomain'); ?></option>
                <option value="Morocco"><?php _e('Morocco', 'textdomain'); ?></option>
                <option value="Mozambique"><?php _e('Mozambique', 'textdomain'); ?></option>
                <option value="Myanmar"><?php _e('Myanmar', 'textdomain'); ?></option>
                <option value="Namibia"><?php _e('Namibia', 'textdomain'); ?></option>
                <option value="Nauru"><?php _e('Nauru', 'textdomain'); ?></option>
                <option value="Nepal"><?php _e('Nepal', 'textdomain'); ?></option>
                <option value="Netherlands"><?php _e('Netherlands', 'textdomain'); ?></option>
                <option value="Netherlands Antilles"><?php _e('Netherlands Antilles', 'textdomain'); ?></option>
                <option value="New Caledonia"><?php _e('New Caledonia', 'textdomain'); ?></option>
                <option value="New Zealand"><?php _e('New Zealand', 'textdomain'); ?></option>
                <option value="Nicaragua"><?php _e('Nicaragua', 'textdomain'); ?></option>
                <option value="Niger"><?php _e('Niger', 'textdomain'); ?></option>
                <option value="Nigeria"><?php _e('Nigeria', 'textdomain'); ?></option>
                <option value="Niue"><?php _e('Niue', 'textdomain'); ?></option>
                <option value="Norfolk Island"><?php _e('Norfolk Island', 'textdomain'); ?></option>
                <option value="Northern Mariana Islands"><?php _e('Northern Mariana Islands', 'textdomain'); ?></option>
                <option value="Norway"><?php _e('Norway', 'textdomain'); ?></option>
                <option value="Oman"><?php _e('Oman', 'textdomain'); ?></option>
                <option value="Pakistan"><?php _e('Pakistan', 'textdomain'); ?></option>
                <option value="Palau"><?php _e('Palau', 'textdomain'); ?></option>
                <option value="Palestinian Territory, Occupied"><?php _e('Palestinian Territory, Occupied', 'textdomain'); ?></option>
                <option value="Panama"><?php _e('Panama', 'textdomain'); ?></option>
                <option value="Papua New Guinea"><?php _e('Papua New Guinea', 'textdomain'); ?></option>
                <option value="Paraguay"><?php _e('Paraguay', 'textdomain'); ?></option>
                <option value="Peru"><?php _e('Peru', 'textdomain'); ?></option>
                <option value="Philippines"><?php _e('Philippines', 'textdomain'); ?></option>
                <option value="Pitcairn"><?php _e('Pitcairn', 'textdomain'); ?></option>
                <option value="Poland"><?php _e('Poland', 'textdomain'); ?></option>
                <option value="Portugal"><?php _e('Portugal', 'textdomain'); ?></option>
                <option value="Puerto Rico"><?php _e('Puerto Rico', 'textdomain'); ?></option>
                <option value="Qatar"><?php _e('Qatar', 'textdomain'); ?></option>
                <option value="Reunion"><?php _e('Reunion', 'textdomain'); ?></option>
                <option value="Romania"><?php _e('Romania', 'textdomain'); ?></option>
                <option value="Russian Federation"><?php _e('Russian Federation', 'textdomain'); ?></option>
                <option value="Rwanda"><?php _e('Rwanda', 'textdomain'); ?></option>
                <option value="Saint Helena"><?php _e('Saint Helena', 'textdomain'); ?></option>
                <option value="Saint Kitts and Nevis"><?php _e('Saint Kitts and Nevis', 'textdomain'); ?></option>
                <option value="Saint Lucia"><?php _e('Saint Lucia', 'textdomain'); ?></option>
                <option value="Saint Pierre and Miquelon"><?php _e('Saint Pierre and Miquelon', 'textdomain'); ?></option>
                <option value="Saint Vincent and The Grenadines"><?php _e('Saint Vincent and The Grenadines', 'textdomain'); ?></option>
                <option value="Samoa"><?php _e('Samoa', 'textdomain'); ?></option>
                <option value="San Marino"><?php _e('San Marino', 'textdomain'); ?></option>
                <option value="Sao Tome and Principe"><?php _e('Sao Tome and Principe', 'textdomain'); ?></option>
                <option value="Saudi Arabia"><?php _e('Saudi Arabia', 'textdomain'); ?></option>
                <option value="Senegal"><?php _e('Senegal', 'textdomain'); ?></option>
                <option value="Serbia"><?php _e('Serbia', 'textdomain'); ?></option>
                <option value="Seychelles"><?php _e('Seychelles', 'textdomain'); ?></option>
                <option value="Sierra Leone"><?php _e('Sierra Leone', 'textdomain'); ?></option>
                <option value="Singapore"><?php _e('Singapore', 'textdomain'); ?></option>
                <option value="Slovakia"><?php _e('Slovakia', 'textdomain'); ?></option>
                <option value="Slovenia"><?php _e('Slovenia', 'textdomain'); ?></option>
                <option value="Solomon Islands"><?php _e('Solomon Islands', 'textdomain'); ?></option>
                <option value="Somalia"><?php _e('Somalia', 'textdomain'); ?></option>
                <option value="South Africa"><?php _e('South Africa', 'textdomain'); ?></option>
                <option value="South Georgia and The South Sandwich Islands"><?php _e('South Georgia and The South Sandwich Islands', 'textdomain'); ?></option>
                <option value="Spain"><?php _e('Spain', 'textdomain'); ?></option>
                <option value="Sri Lanka"><?php _e('Sri Lanka', 'textdomain'); ?></option>
                <option value="Sudan"><?php _e('Sudan', 'textdomain'); ?></option>
                <option value="Suriname"><?php _e('Suriname', 'textdomain'); ?></option>
                <option value="Svalbard and Jan Mayen"><?php _e('Svalbard and Jan Mayen', 'textdomain'); ?></option>
                <option value="Swaziland"><?php _e('Swaziland', 'textdomain'); ?></option>
                <option value="Sweden"><?php _e('Sweden', 'textdomain'); ?></option>
                <option value="Switzerland"><?php _e('Switzerland', 'textdomain'); ?></option>
                <option value="Syrian Arab Republic"><?php _e('Syrian Arab Republic', 'textdomain'); ?></option>
                <option value="Taiwan"><?php _e('Taiwan', 'textdomain'); ?></option>
                <option value="Tajikistan"><?php _e('Tajikistan', 'textdomain'); ?></option>
                <option value="Tanzania, United Republic of"><?php _e('Tanzania, United Republic of', 'textdomain'); ?></option>
                <option value="Thailand"><?php _e('Thailand', 'textdomain'); ?></option>
                <option value="Timor-leste"><?php _e('Timor-leste', 'textdomain'); ?></option>
                <option value="Togo"><?php _e('Togo', 'textdomain'); ?></option>
                <option value="Tokelau"><?php _e('Tokelau', 'textdomain'); ?></option>
                <option value="Tonga"><?php _e('Tonga', 'textdomain'); ?></option>
                <option value="Trinidad and Tobago"><?php _e('Trinidad and Tobago', 'textdomain'); ?></option>
                <option value="Tunisia"><?php _e('Tunisia', 'textdomain'); ?></option>
                <option value="Turkey"><?php _e('Turkey', 'textdomain'); ?></option>
                <option value="Turkmenistan"><?php _e('Turkmenistan', 'textdomain'); ?></option>
                <option value="Turks and Caicos Islands"><?php _e('Turks and Caicos Islands', 'textdomain'); ?></option>
                <option value="Tuvalu"><?php _e('Tuvalu', 'textdomain'); ?></option>
                <option value="Uganda"><?php _e('Uganda', 'textdomain'); ?></option>
                <option value="Ukraine"><?php _e('Ukraine', 'textdomain'); ?></option>
                <option value="United Arab Emirates"><?php _e('United Arab Emirates', 'textdomain'); ?></option>
                <option value="United Kingdom"><?php _e('United Kingdom', 'textdomain'); ?></option>
                <option value="United States"><?php _e('United States', 'textdomain'); ?></option>
                <option value="United States Minor Outlying Islands"><?php _e('United States Minor Outlying Islands', 'textdomain'); ?></option>
                <option value="Uruguay"><?php _e('Uruguay', 'textdomain'); ?></option>
                <option value="Uzbekistan"><?php _e('Uzbekistan', 'textdomain'); ?></option>
                <option value="Vanuatu"><?php _e('Vanuatu', 'textdomain'); ?></option>
                <option value="Venezuela"><?php _e('Venezuela', 'textdomain'); ?></option>
                <option value="Viet Nam"><?php _e('Viet Nam', 'textdomain'); ?></option>
                <option value="Virgin Islands, British"><?php _e('Virgin Islands, British', 'textdomain'); ?></option>
                <option value="Virgin Islands, U.S."><?php _e('Virgin Islands, U.S.', 'textdomain'); ?></option>
                <option value="Wallis and Futuna"><?php _e('Wallis and Futuna', 'textdomain'); ?></option>
                <option value="Western Sahara"><?php _e('Western Sahara', 'textdomain'); ?></option>
                <option value="Yemen"><?php _e('Yemen', 'textdomain'); ?></option>
                <option value="Zambia"><?php _e('Zambia', 'textdomain'); ?></option>
                <option value="Zimbabwe"><?php _e('Zimbabwe', 'textdomain'); ?></option>
            `;

            // Function to update fields based on quantity
            function updateAttendeeFields(quantity) {
                let currentFields = attendeeFieldsContainer.find('.attendee-field-group').length;
                // Add fields if needed
                if (quantity > currentFields) {
                    for (let i = currentFields + 1; i <= quantity; i++) {
                        attendeeFieldsContainer.append(`<div class="attendee-field-group" style="margin-top:2em;">
                    <h3>Attendee ${i}</h3>
                    <input type="text" name="attendee_${i}_first_name" placeholder="First Name" required style="margin-top:1px;" />
                    <input type="text" name="attendee_${i}_last_name" placeholder="Last Name" required style="margin-top:1px;" />
                    <input type="email" name="attendee_${i}_email" placeholder="Email Address" required  style="margin-top:1px;"/>
                    <input type="text" name="attendee_${i}_organization" placeholder="Organization" required style="margin-top:1px;"/>
					<input type="text" name="attendee_${i}_dietary" placeholder="Dietary Restrictions?"  style="margin-top:1px;"/>
                    <select name="attendee_${i}_country" required style="margin-top:1px;">${countries}</select>
                    </div>`);
                    }
                }
                // Remove excess fields if needed
                else if (quantity < currentFields) {
                    for (let i = currentFields; i > quantity; i--) {
                        attendeeFieldsContainer.find('.attendee-field-group').last().remove();
                    }
                }
            }
            // Initialize fields based on the current quantity and update on change
            updateAttendeeFields(quantityInput.val());
            quantityInput.change(function() {
                updateAttendeeFields($(this).val());
            });
        });
    </script>
<?php
}

// on add to cart save the attendee data
add_filter('woocommerce_add_cart_item_data', 'ices_save_attendee_data', 10, 2);
function ices_save_attendee_data($cart_item_data, $product_id)
{
    // as above but dynamic
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
    for ($i = 1; $i <= $quantity; $i++) {
        $cart_item_data['attendee_' . $i . '_first_name'] = sanitize_text_field($_POST['attendee_' . $i . '_first_name']);
        $cart_item_data['attendee_' . $i . '_last_name'] = sanitize_text_field($_POST['attendee_' . $i . '_last_name']);
        $cart_item_data['attendee_' . $i . '_email'] = sanitize_email($_POST['attendee_' . $i . '_email']);
        $cart_item_data['attendee_' . $i . '_organization'] = sanitize_text_field($_POST['attendee_' . $i . '_organization']);
        $cart_item_data['attendee_' . $i . '_dietary'] = sanitize_text_field($_POST['attendee_' . $i . '_dietary']);
        $cart_item_data['attendee_' . $i . '_country'] = sanitize_text_field($_POST['attendee_' . $i . '_country']);
    }
    return $cart_item_data;
}


if (!function_exists('set_default_value_n')) {
    function set_default_value_n($value) {
        return empty($value) ? 'N/A' : $value;
    }
}
add_filter('woocommerce_get_item_data', 'ices_display_attendee_data', 10, 2);
function ices_display_attendee_data($item_data, $cart_item)
{
    $product = $cart_item['data'];
    $quantity = $cart_item['quantity'];

    for ($i = 1; $i <= $quantity; $i++) {
        if (!empty($cart_item['attendee_' . $i . '_first_name'])) {
            $item_data[] = array(
                'key' => 'Attendee ' . $i,
                'value' => '<div style="clear:both"> 
                    <dl class="variation">
                        <dt>First Name:</dt>
                        <dd>' . set_default_value_n($cart_item['attendee_' . $i . '_first_name']) . '</dd>
                        <dt>Last Name:</dt>
                        <dd>' . set_default_value_n($cart_item['attendee_' . $i . '_last_name']) . '</dd>
                        <dt>Email:</dt>
                        <dd>' . set_default_value_n($cart_item['attendee_' . $i . '_email']) . '</dd>
                        <dt>Organization:</dt>
                        <dd>' . set_default_value_n($cart_item['attendee_' . $i . '_organization']) . '</dd>
                        <dt>Dietary:</dt>
                        <dd>' . set_default_value_n($cart_item['attendee_' . $i . '_dietary']) . '</dd>
                        <dt>Country:</dt>
                        <dd>' . set_default_value_n($cart_item['attendee_' . $i . '_country']) . '</dd>
                    </dl>
                </div>',
            );
        }
    }

    return $item_data;
}


add_action('woocommerce_checkout_create_order_line_item', 'ices_transfer_attendee_data_to_order_items', 10, 4);
function ices_transfer_attendee_data_to_order_items($item, $cart_item_key, $values, $order)
{
    // Check if the product manages attendees
    if (!$item->get_product()->get_meta('manage_attendees')) {
        return;
    }
    $quantity = $item->get_quantity();
    for ($i = 1; $i <= $quantity; $i++) {
        // Define the fields that need to be transferred from the cart to the order
        $fields = ['first_name', 'last_name', 'email', 'organization', 'dietary', 'country'];
        foreach ($fields as $field) {
            // Construct the meta key used in the cart
            $cart_meta_key = 'attendee_' . $i . '_' . $field;
            // Check if this data exists in the cart item
            if (isset($values[$cart_meta_key])) {
                // Construct the nicely formatted key for the order item meta
                switch ($field) {
                    case "first_name":
                        $nicename = 'First Name';
                        break;
                    case "last_name":
                        $nicename = 'Last Name';
                        break;
                    case "email":
                        $nicename = "Email";
                        break;
                    case "organization":
                        $nicename = "Organization";
                        break;
                    case "dietary":
                        $nicename = "Dietary";
                        break;
                    case "country":
                        $nicename = "Country";
                        break;
                }
                // Add the data as order item meta
                $item->add_meta_data('Attendee ' . $i . ' ' . $nicename, $values[$cart_meta_key]);
            }
        }
    }
}

add_action('woocommerce_thankyou', 'store_attendees_in_custom_table');
function store_attendees_in_custom_table($order_id)
{
    global $wpdb;
error_log('Hook triggered for Order ID: ' . $order_id);

    // Helper function to set default value
    function set_default_value($value) {
        return empty($value) ? 'N/A' : $value;
    }

    // Get the order object
    $order = wc_get_order($order_id);

    // Loop through each order item
    foreach ($order->get_items() as $item_id => $item) {
        // Check if the product manages attendees
        $product = $item->get_product();
        if (!$product || !$product->get_meta('manage_attendees')) {
            continue;
        }

        $product_id = $product->get_id();
        $quantity = $item->get_quantity();

        // Loop through attendees
        for ($i = 1; $i <= $quantity; $i++) {
            $attendee_data = [
                'first_name' => set_default_value($item->get_meta('Attendee ' . $i . ' First Name')),
                'last_name'  => set_default_value($item->get_meta('Attendee ' . $i . ' Last Name')),
                'email'      => set_default_value($item->get_meta('Attendee ' . $i . ' Email')),
                'organization' => set_default_value($item->get_meta('Attendee ' . $i . ' Organization')),
                'dietary'    => set_default_value($item->get_meta('Attendee ' . $i . ' Dietary')),
                'country'    => set_default_value($item->get_meta('Attendee ' . $i . ' Country')),
            ];

            // Insert attendee data into custom table
            $wpdb->insert(
                $wpdb->prefix . 'ices_attendees',
                [
                    'order_id'             => $order_id,
                    'product_id'           => $product_id,
                    'attendee_first_name'  => $attendee_data['first_name'],
                    'attendee_last_name'   => $attendee_data['last_name'],
                    'attendee_email'       => $attendee_data['email'],
                    'attendee_organization'=> $attendee_data['organization'],
                    'attendee_dietary'     => $attendee_data['dietary'],
                    'attendee_country'     => $attendee_data['country'],
                ],
                [
                    '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s'
                ]
            );
        }
    }
}


// Show attendee data to the order admin view
add_action('woocommerce_admin_order_data_after_order_details', 'ices_add_attendee_data_to_order_admin');
function ices_add_attendee_data_to_order_admin($order)
{
    $output = '';
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (!$product) continue;
        if ($product->get_meta('manage_attendees')) {
            $output .= '<div class="form-field form-field-wide wc-order-item-meta">';
            $output .= "<h3 style='font-size: 1.35em;margin-bottom: 1em;border-top: 1px solid #eee;padding-top: .5em;'>Attendees</h3>";
            for ($i = 1; $i <= $item->get_quantity(); $i++) {
                if ($item->get_meta('Attendee ' . $i . ' Active') == "") {
                    $is_checked = "checked='checked'";
                } else {
                    $is_checked = checked($item->get_meta('Attendee ' . $i . ' Active'), 'yes', false);
                }
                if ($item->get_meta('Attendee ' . $i . ' Active') == "no") {
                    $grayout = "class='grayout'";
                } else {
                    $grayout = "";
                }
                $output .= '<fieldset ' . $grayout . '><legend><b>Attendee ' . $i . ':</b></legend>
                    <p><label>First Name:</label><input type="text" name="attendee_' . $item_id . '-' . $i . '_first_name" value="' . esc_attr($item->get_meta('Attendee ' . $i . ' First Name')) . '"></p>
                    <p><label>Last Name:</label><input type="text" name="attendee_' . $item_id . '-' . $i . '_last_name" value="' . esc_attr($item->get_meta('Attendee ' . $i . ' Last Name')) . '"></p>
                    <p><label>Email:</label><input type="email" name="attendee_' . $item_id . '-' . $i . '_email" value="' . esc_attr($item->get_meta('Attendee ' . $i . ' Email')) . '"></p>
                    <p><label>Organization:</label><input type="text" name="attendee_' . $item_id . '-' . $i . '_organization" value="' . esc_attr($item->get_meta('Attendee ' . $i . ' Organization')) . '"></p>
                    <p><label>Dietary Restrictions:</label><input type="text" name="attendee_' . $item_id . '-' . $i . '_dietary" value="' . esc_attr($item->get_meta('Attendee ' . $i . ' Dietary')) . '"></p>
                    <p><label>Active:</label><input class="is_active_attendee" type="checkbox" name="attendee_' . $item_id . '-' . $i . '_active" ' . $is_checked . '></p>
                    <p><label>Country:</label><input type="text" name="attendee_' . $item_id . '-' . $i . '_country" value="' . esc_attr($item->get_meta('Attendee ' . $i . ' Country')) . '"></p>
                  </fieldset>';
            }
            $output .= '</div><style>.is_active_attendee{width:auto!important}.grayout{opacity:.5}</style>';
        }
    }
    echo $output;
}

add_action('woocommerce_process_shop_order_meta', 'ices_save_attendee_data_from_admin', 999, 2);
function ices_save_attendee_data_from_admin($post_id, $post)
{
    $order = wc_get_order($post_id);
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        if (!$product) continue;
        if ($product->get_meta('manage_attendees')) {
            for ($i = 1; $i <= $item->get_quantity(); $i++) {
                $fields = ['first_name', 'last_name', 'email', 'organization', 'dietary', 'country', 'active'];
                foreach ($fields as $field) {
                    if (isset($_POST['attendee_' . $item_id . '-' . $i . '_' . $field])) {
                        switch ($field) {
                            case "first_name":
                                $nicename = 'First Name';
                                break;
                            case "last_name":
                                $nicename = 'Last Name';
                                break;
                            case "email":
                                $nicename = "Email";
                                break;
                            case "organization":
                                $nicename = "Organization";
                                break;
                            case "dietary":
                                $nicename = "Dietary";
                                break;
                            case "country":
                                $nicename = "Country";
                                break;
                            case "active":
                                $nicename = "Active";
                                $value = $_POST['attendee_' . $item_id . '-' . $i . '_' . $field] == 'on' ? 'yes' : 'no';
                                $item->update_meta_data('Attendee ' . $i . ' ' . $nicename, $value);
                                continue 2;
                                break;
                        }
                        $item->update_meta_data('Attendee ' . $i . ' ' . $nicename, wc_clean($_POST['attendee_' . $item_id . '-' . $i . '_' . $field]));
                    } else {
                        if ($field == 'active') {
                            $item->update_meta_data('Attendee ' . $i . ' Active', 'no');
                        }
                    }
                }
            }
            $item->save_meta_data();
        }
    }
}

// add a custom note in the order email
add_action('woocommerce_email_before_order_table', 'ices_add_custom_note_to_order_email', 10, 4);
function ices_add_custom_note_to_order_email($order, $sent_to_admin, $plain_text, $email)
{
    if ($email->id == 'customer_completed_order') {
        echo '<p><strong>Please note:</strong> Attendees will receive emails with their ticket information in the following weeks.</p>';
    }
}







