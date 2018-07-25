<?php

/**
 * Child Theme
 * 
 */
 
function grve_blade_child_theme_setup() {
	
}
add_action( 'after_setup_theme', 'grve_blade_child_theme_setup' );

add_filter( 'wpcf7_validate_configuration', '__return_false' );

//Omit closing PHP tag to avoid accidental whitespace output errors.


/**
 * @snippet       Hide Price & Add to Cart for Logged Out Users
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=299
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.1.1
 */
add_action('init', 'crosby_hide_price_add_cart_not_logged_in');
 
function crosby_hide_price_add_cart_not_logged_in() { 
if ( !is_user_logged_in() ) {       
 remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
 remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
 remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
 remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );  
 add_action( 'woocommerce_single_product_summary', 'crosby_print_login_to_see', 31 );
 add_action( 'woocommerce_after_shop_loop_item', 'crosby_print_login_to_see', 11 );
}
}
 
function crosby_print_login_to_see() {
echo '<a href="/my-account/" class="grve-btn grve-btn-medium grve-square grve-bg-black grve-bg-hover-black grve-btn-line">' . __('Click for pricing', 'theme_name') . '</a>';
}


/**
 * Add new register fields for WooCommerce registration.
 */
function wooc_extra_register_fields() {
    ?>

    <p class="form-row form-row-wide">
    <label for="reg_npi_id"><?php _e( 'NPI #', 'woocommerce' ); ?> <abbr class="required" title="required">*</abbr></label>
    <input type="text" class="input-text" name="npi_id" id="reg_npi_id" value="<?php if ( ! empty( $_POST['npi_id'] ) ) esc_attr_e( $_POST['npi_id'] ); ?>" />
    </p>

    <?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

/**
 * Validate the extra register fields.
 *
 * @param WP_Error $validation_errors Errors.
 * @param string   $username          Current username.
 * @param string   $email             Current email.
 *
 * @return WP_Error
 */
function wooc_validate_extra_register_fields( $errors, $username, $email ) {
    if ( isset( $_POST['npi_id'] ) && empty( $_POST['npi_id'] ) ) {
        $errors->add( 'npi_id_error', __( 'NPI # is required!', 'woocommerce' ) );
    } else if (strlen($_POST['npi_id']) !== 10) {
        $errors->add( 'npi_id_error', __( 'NPI # is invalid!', 'woocommerce' ) );
    } else {
        $ch = curl_init('https://npiregistry.cms.hhs.gov/api/?number=' . $_POST['npi_id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $npi_response = curl_exec($ch);
        curl_close($ch);
        $npi_response = json_decode($npi_response);
        if (!isset($npi_response->result_count) || ($npi_response->result_count === 0)) {
            $errors->add( 'npi_id_error', __( 'This NPI # (' . $_POST['npi_id'] . ') does not exist!', 'woocommerce' ) );
        }
    }
    
    return $errors;
}
add_filter( 'woocommerce_registration_errors', 'wooc_validate_extra_register_fields', 10, 3 );


/**
 * Save the extra register fields.
 *
 * @param int $customer_id Current customer ID.
 */
function wooc_save_extra_register_fields( $customer_id ) {
    if ( isset( $_POST['npi_id'] ) ) {
        // WooCommerce billing first name.
        update_user_meta( $customer_id, 'npi_id', sanitize_text_field( $_POST['npi_id'] ) );
    }
  
}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );



/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */
function wporg_usermeta_form_field_birthday($user)
{
    ?>
    <table class="form-table">
        <tr>
            <th>
                <label for="npi_id">NPI #</label>
            </th>
            <td>
                <input type="text"
                       class="regular-text ltr"
                       id="npi_id"
                       name="birthday"
                       value="<?= esc_attr(get_user_meta($user->ID, 'npi_id', true)); ?>"
                       required>
                <p class="description">
                    Please enter your NPI #.
                </p>
            </td>
        </tr>
    </table>
    <?php
}
 

 
 
/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function wporg_usermeta_form_field_birthday_update($user_id)
{
    // check that the current user have the capability to edit the $user_id
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
 
    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'birthday',
        $_POST['birthday']
    );
}
 
// add the field to user's own profile editing screen
add_action(
    'edit_user_profile',
    'wporg_usermeta_form_field_birthday'
);
 
// add the field to user profile editing screen
add_action(
    'show_user_profile',
    'wporg_usermeta_form_field_birthday'
);
 
// add the save action to user's own profile editing screen update
add_action(
    'personal_options_update',
    'wporg_usermeta_form_field_birthday_update'
);
 
// add the save action to user profile editing screen update
add_action(
    'edit_user_profile_update',
    'wporg_usermeta_form_field_birthday_update'
);



// Alter Shipping and Billing labels for Town/City
// add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields', 10);

// Our hooked in function - $fields is passed via the filter!
// function custom_override_checkout_fields( $fields ) {
//      $fields['billing']['billing_city']['label'] = 'City / Town';
//      $fields['shipping']['shipping_city']['label'] = 'City / Town';
//      return $fields;
// }


// Hook in
// add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

// Our hooked in function - $address_fields is passed via the filter!
// function custom_override_default_address_fields( $address_fields ) {
//      $address_fields['city']['label'] = 'City / Town';

//      return $address_fields;
// }

add_filter( 'wp_nav_menu_items', 'add_membership_only_links', 0, 2 );

function add_membership_only_links( $items, $args ) {

    if ( $args->theme_location != 'grve_header_nav' ) {
        return $items;
    }
    
    if (  wc_memberships_is_user_active_member( get_current_user_id(), 'pharmacist') ) {
        // $items .= '<li><a href="/product/reorder-bundle-product/">' . __( 'Re-Order' ) . '</a></li>';
    } 

    if ( wc_memberships_is_user_active_member( get_current_user_id(), 'sales-rep') ) {
        // $items .= '<li><a href="/sales-rep-samples/">' . __( 'Sales Rep Samples' ) . '</a></li>';
    }

    return $items;
}

add_action( 'woocommerce_review_order_before_cart_contents', 'show_checkout_notice', 12 );
  
function show_checkout_notice() {
    global $woocommerce;
    $msg_states = array( 'OK', 'MS', 'KS' );

    $items = $woocommerce->cart->get_cart();

    // 10226 - THC Free POS
    // 10251 - THC Free Ticture
    $product_notice = false;
    foreach($items as $item => $values) { 
        $product_id = $values['product_id'];
        if ($product_id != 10251 && $product_id != 10226) {
            $product_notice = true;
            break;
        }
    }

    if( $product_notice && in_array( WC()->customer->get_shipping_state(), $msg_states ) ) { 
?>
    <p class="checkout_notice" style="color:red">Thank you for your interest in Ananda Professional.  Although Ananda Professional CBD products are federally legal in all states, due to regulations which exist in your state concerning hemp-derived CBD, we have chosen not to sell our products in your state at this time.  Legislation regarding hemp-derived CBD constantly evolves and we welcome the opportunity to follow up with you once the laws are favorable in your state.</p>
<?php
    }
}

add_filter( 'wc_product_sku_enabled', 'filter_wc_product_sku_enabled', 10, 1 ); 
function filter_wc_product_sku_enabled($true) {
    return $true;
}

function ananda_get_coa_attachments() {

	$files = [];
	
	$query = new WP_Query( array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'oderby' => 'meta_value_num',
        'order' => 'ASC',
        'post_status' => 'any',
        'post_parent' => null,
        'tax_query' => array(
            array(
                'taxonomy' => 'post_tag',
                'field' => 'slug',
                'terms' => 'coa'
            )
        )
    ));
    
    foreach ( $query->posts as $post ) {
    	$files[] =  [
    					"attachment_url"  => $post->guid,
    					"batch"           => $post->post_title,
                        "attachment_page" => get_attachment_link($post->ID)
    				];
    }

   	return $files;

}

function wptp_add_tags_to_attachments() {
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}

add_action( 'init' , 'wptp_add_tags_to_attachments' );


show_admin_bar(false);



function wpb_woo_my_account_order() {
    $myorder = array(
        'dashboard'          => __( 'Dashboard', 'woocommerce' ),
        'orders'             => __( 'Orders', 'woocommerce' ),
        'edit-address'       => __( 'Addresses', 'woocommerce' ),
        'edit-account'       => __( 'Manage account', 'woocommerce' ),
        // 'my-custom-endpoint' => __( 'My Stuff', 'woocommerce' ),
        // 'downloads'          => __( 'Download MP4s', 'woocommerce' ),
        // 'payment-methods'    => __( 'Payment Methods', 'woocommerce' ),
        'customer-logout'    => __( 'Logout', 'woocommerce' ),
    );
    return $myorder;
}
add_filter ( 'woocommerce_account_menu_items', 'wpb_woo_my_account_order' );

add_filter('woocommerce_save_account_details_required_fields', 'wc_save_account_details_required_fields' );
function wc_save_account_details_required_fields( $required_fields ){
    unset( $required_fields['account_display_name'] );
    return $required_fields;
}

add_filter( 'wp_nav_menu_items', 'add_loginout_link', 10, 2 );
function add_loginout_link( $items, $args ) {
    if ($args->theme_location == 'grve_header_nav') {
        if (is_user_logged_in()) {
            $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="/my-account"><span class="grve-item">My Account</span></a><ul class="sub-menu">
                    <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account/orders/"><span class="grve-item">Orders</span></a></li>
                    <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account/edit-address/"><span class="grve-item">Addresses</span></a></li>
                    <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account/edit-account/"><span class="grve-item">Manage Account</span></a></li>
                    <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="' . wp_logout_url( home_url() ) . '"><span class="grve-item">Logout</span></a></li>
                </ul></li>';
        } else {
            $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account"><span class="grve-item">Register</span></a></li>';
        }
        $items .= '<li id="menu-item-8499" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8499"><a href="https://anandaprofessional.com/contact/"><span class="grve-item">Contact</span></a></li>';
    }
    return $items;
}

if( !is_admin() )
{
    // Function to check starting char of a string
    function startsWith($haystack, $needle)
    { 
        return $needle === '' || strpos($haystack, $needle) === 0;
    }

    // Custom function to display the Billing Address form to registration page
    function my_custom_function()
    {
        global $woocommerce;
        $checkout = $woocommerce->checkout();

        foreach ($checkout->checkout_fields['billing'] as $key => $field) :
            if ($key === 'billing_email' || $key === 'rep_name' || $key === 'tax_cert') continue;
            woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
        endforeach;
    }
    add_action('woocommerce_register_form_start','my_custom_function');


    // Custom function to save Usermeta or Billing Address of registered user
    function save_address($user_id)
    {
        global $woocommerce;
        $address = $_POST;

        foreach ($address as $key => $field) :
            if(startsWith($key,'billing_'))
            {
                // Condition to add firstname and last name to user meta table
                if($key == 'billing_first_name' || $key == 'billing_last_name')
                {
                    $new_key = explode('billing_',$key);
                    update_user_meta( $user_id, $new_key[1], $_POST[$key] );
                }
                update_user_meta( $user_id, $key, $_POST[$key] );
            }
        endforeach;
    }
    add_action('woocommerce_created_customer','save_address');


    // Registration page billing address form Validation
    function wooc_validate_billing_address_register_fields( $errors, $username, $email ) {

        $address = $_POST;

        foreach ($address as $key => $field) :

            // Validation: Required fields
            if(startsWith($key,'billing_'))
            {

                if($key == 'billing_country' && $field == '')
                {
                    $errors->add( __( 'ERROR', 'woocommerce' ), __( 'Please select a country.', 'woocommerce' ) );
                }

                if($key == 'billing_first_name' && $field == '')
                {
                    $errors->add( __( 'ERROR', 'woocommerce' ), __( 'Please enter first name.', 'woocommerce' ) );
                }

                if($key == 'billing_last_name' && $field == '')
                {
                    $errors->add( __( 'ERROR', 'woocommerce' ), __( 'Please enter last name.', 'woocommerce' ) );
                }

                if($key == 'billing_address_1' && $field == '')
                {
                    $errors->add( __( 'ERROR', 'woocommerce' ), __( 'Please enter address.', 'woocommerce' ) );
                }

                if($key == 'billing_city' && $field == '')
                {
                    $errors->add( __( 'ERROR', 'woocommerce' ), __( 'Please enter city.', 'woocommerce' ) );
                }

                if($key == 'billing_state' && $field == '')
                {
                    $errors->add( __( 'ERROR', 'woocommerce' ), __( 'Please enter state.', 'woocommerce' ) );
                }

                if($key == 'billing_postcode' && $field == '')
                {
                    $errors->add( __( 'ERROR', 'woocommerce' ), __( 'Please enter a postcode.', 'woocommerce' ) );
                }

                if($key == 'billing_email' && $field == '')
                {
                    // $errors->add( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter billing email address.', 'woocommerce' ) );
                }

                if($key == 'billing_phone' && $field == '')
                {
                    $errors->add( __( 'ERROR', 'woocommerce' ), __( 'Please enter phone number.', 'woocommerce' ) );
                }
            }

        endforeach;

        return $errors;
    }
    add_filter( 'woocommerce_registration_errors', 'wooc_validate_billing_address_register_fields', 10, 3 );

    // add_action('woocommerce_register_post','custom_validation');

}


add_filter( 'cron_schedules', 'schedule_salesforce_interval' ); 
function schedule_salesforce_interval( $schedules ) {
    $schedules['salesforce_interval'] = array(
        'interval' => 60 * 15, // seconds
        'display'  => esc_html__( 'Every 15 Minutes' ),
    );
 
    return $schedules;
}

if (! wp_next_scheduled ( 'salesforce_retain_customers_hook' )) {
    wp_schedule_event(time(), 'salesforce_interval', 'salesforce_retain_customers_hook');
}

add_action('salesforce_retain_customers_hook', 'salesforce_retain_customers_exec');
function salesforce_retain_customers_exec() {

    // get customers
    $customer_args = array(
        'role' => 'customer',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'has_salesforce_checked',
                'value' => '1',
                'compare' => '!='
            ),
            array(
                'key' => 'has_salesforce_checked',
                'value' => '1',
                'compare' => 'NOT EXISTS'
            )
        )
    );
    $customers = get_users($customer_args);

    $cnt = 0;

    foreach($customers as $customer) {

        $args = array(
            'customer_id' => $customer->ID
        );

        $orders = wc_get_orders($args);

        if (count($orders) > 0) {
            update_user_meta($customer->ID, 'has_salesforce_checked', '1');
        } else {
            if (strtotime($customer->user_registered) < strtotime('-1 hour')) {

                if ($customer->user_email) {
                    $data = [
                        // 'captcha_settings' => '{"keyname":"AnandaProfessional","fallback":"true","orgId":"00D6A000002zNXn","ts":""}',
                        'oid' => '00D6A000002zNXn',
                        'retURL' => 'https://anandaprofessional.com/products/',
                        'company' => $customer->billing_company,
                        'first_name' => $customer->billing_first_name,
                        'last_name' => $customer->billing_last_name,
                        'street' => $customer->billing_address_1,
                        'city' => $customer->billing_city,
                        'state' => $customer->billing_state,
                        'zip' => $customer->billing_postcode,
                        '00N6A00000NXP1d' => 'Ananda Professional', // brand
                        'phone' => $customer->billing_phone,
                        'email' => $customer->user_email,
                        '00N6A00000NXfPA' => $customer->npi_id, // NPI Number
                    ];


                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL,"https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $server_output = curl_exec ($ch);
                    curl_close ($ch);

                    $cnt ++;
                }

                update_user_meta($customer->ID, 'has_salesforce_checked', '1');
            }
        }
    }

    if ($cnt > 0) {
        update_option('cron_status', date(DATE_RFC2822) . '------' . count($customers) . '------' . $cnt . ' ------- ' . $server_output);
    }
}


function restrictly_get_current_user_role() {
    if( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $role = ( array ) $user->roles;
        return $role[0];
    } else {
        return false;
    }
}


add_action('admin_head', 'role_based_menu_list');

function role_based_menu_list() {
    $user = wp_get_current_user();
    if ( !wp_doing_ajax() && in_array( 'pharmacy_manager', (array) $user->roles ) ) {
        ?>
            <style type="text/css">
                #adminmenu>li {
                    display: none;
                }
                #adminmenu>.toplevel_page_asl-plugin, #adminmenu>.toplevel_page_woocommerce {
                    display: block;
                }
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li, #adminmenu>.toplevel_page_woocommerce>ul.wp-submenu>li {
                    display: none;
                }
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li:nth-child(1),
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li:nth-child(2),
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li:nth-child(3),
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li:nth-child(6),
                #adminmenu>.toplevel_page_woocommerce>ul.wp-submenu>li:nth-child(1),
                #adminmenu>.toplevel_page_woocommerce>ul.wp-submenu>li:nth-child(2),
                #adminmenu>.toplevel_page_woocommerce>ul.wp-submenu>li:nth-child(4) {
                    display: block;
                }
                #wp-admin-bar-comments, #wp-admin-bar-new-content, #wp-admin-bar-kinsta-cache, #wp-admin-bar-purge-cdn, #wp-admin-bar-edit-profile, #wp-admin-bar-user-info {
                    display: none;
                }
            </style>
        <?php
    }
}


add_filter('woocommerce_login_redirect', 'wc_login_redirect');
 
function wc_login_redirect( $redirect_to ) {
     $redirect_to = 'https://anandaprofessional.com/products';
     return $redirect_to;
}


// Hook in
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields_rep_name', 10 );

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields_rep_name( $fields ) {

    // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => 'wc-completed', // array_keys( wc_get_order_statuses() ),
    ) );

    $loyal_count = 1;
    $user_already_bought = get_user_meta(get_current_user_id(), 'already_bought', true);
    if ( count( $customer_orders ) >= $loyal_count || $user_already_bought=='1') {
        unset($fields['billing']['rep_name']);
    }

    return $fields;
}

add_filter( 'woocommerce_checkout_fields' , 'custom_override_additional_fields', 10 );
function custom_override_additional_fields( $fields ) {
    unset($fields['order']['inservice_name']);
    unset($fields['order']['inservice_phonenumber']);
    unset($fields['order']['inservice_email']);
    return $fields;
}

add_action('woocommerce_checkout_after_customer_details','checkout_additional_sections');
function checkout_additional_sections() {
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => 'wc-completed', // array_keys( wc_get_order_statuses() ),
    ) );

    $loyal_count = 1;
    $user_already_bought = get_user_meta(get_current_user_id(), 'already_bought', true);

    if ( count( $customer_orders ) >= $loyal_count || $user_already_bought=='1') return;

    echo '<div class="woocommerce-inservice-fields">';
        echo '<h3 id="order_review_heading">'. __( 'In Service', 'woocommerce' ).'</h3>';
        echo '<div>';
        echo '<p>As part of commitment to your success, we would like to organise an in-service with your team.</p>';
        echo '<span>This is 20-30 minute in-service will cover topics including;</span>';
        echo '<ul>';
        echo '<li>What is the Endocannabinoid System (ECS) and what is its role in maintaining health?</li>';
        echo '<li>What is hemp-derived cannabidiol (CBD) and how does it regulate the ECS?</li>';
        echo '<li>What types of patients are candidates for CBD?</li>';
        echo '<li>How to get patients started on CBD?</li>';
        echo '<li>How to gain new patients for your store?</li>';
        echo '</ul>';
        echo '<p>Please provide the best point of contact to arrange this in-service.</p>';
        echo '<p></p>';
        echo '</div>';

        global $woocommerce;
        $checkout = $woocommerce->checkout();

        foreach ($checkout->checkout_fields['order'] as $key => $field) :
            if (!($key === 'inservice_name' || $key === 'inservice_phonenumber' || $key === 'inservice_email')) continue;
            woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
        endforeach;

    echo '</div>';
}



add_action ( 'woocommerce_checkout_after_customer_details', 'woocommerce_update_cart_ajax_by_tax_cert');
function woocommerce_update_cart_ajax_by_tax_cert() {

?>

    <script src="https://app.certcapture.com/gencert2/js?cid=82587&key=GcJMnfB0CnYMoG5R"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#tax_cert').change(function() {
                jQuery('body').trigger('update_checkout');
                if (jQuery(this).val() === 'YES') {
                    jQuery('#cert_capture_form').show();
                } else {
                    jQuery('#cert_capture_form').hide();
                }
            });
            jQuery('body').on('update_checkout', function() {
                jQuery('.checkout_notice').remove();
            });
        });
    </script>
    <style type="text/css">
        .woocommerce-SavedPaymentMethods-saveNew {
            display: none !important;
        }
    </style>

    <div id="cert_capture_form" style="display: none;">
    </div>
<?php
}

add_action( 'woocommerce_review_order_after_submit', 'custom_review_order_after_submit' );
function custom_review_order_after_submit() {
    if (is_ajax() && !empty( $_POST['post_data'] ) ) {
        parse_str( $_POST['post_data'], $post_data );
    }else {
        $post_data = $_POST;
    }
    if(!empty($post_data['tax_cert'])) {
        if ($post_data['tax_cert']!='NO') {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api.certcapture.com/v2/auth/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            curl_setopt($ch, CURLOPT_POST, TRUE);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              "x-client-id: 82587",
              "Authorization: Basic " . base64_encode('anandap_test:AnandaProfessional2018'),
              "x-customer-primary-key: customer_number",
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            $token_response = json_decode($response);

            $token = $token_response->response->token;

            $npi_id = get_user_meta(get_current_user_id(), 'npi_id', true); // cert capture - customer number

            ?>
                <script type="text/javascript">jQuery('#place_order').attr('disabled', 'disabled');</script>

                <script type="text/javascript">

                    GenCert.init(document.getElementById("cert_capture_form"), {               

                      // The token and zone must set to start the process!

                        token: '<?php echo $token; ?>',
                        // debug: true,
                        edit_purchaser: true,
                        hide_sig: true,
                        upload_only: true,
                        submit_to_stack: true,

                        onCertSuccess: function() {

                          alert('Certificate successfully generated with id:' + GenCert.certificateIds);

                          // window.location = '/home';

                        },

                        onInit: function() {
                            console.log('certcapture form inited');
                        },

                        onCancel: function() {

                            console.log('cert capture form cancelled');

                          // window.location = '/home';

                        }
                    }, 82587, 'GcJMnfB0CnYMoG5R');

         

                    GenCert.setCustomerNumber('<?php echo $npi_id; ?>'); // create customer

                    var customer = new Object();

                    customer.name = '<?php echo $post_data['shipping_first_name'] . ' ' . $post_data['shipping_last_name']; ?>';

                    customer.address1 = '<?php echo $post_data['shipping_address_1']; ?>';

                    customer.city = '<?php echo $post_data['shipping_city']; ?>';

                    // customer.state = 'California';
                    // customer.state = '<?php echo $post_data['shipping_state']; ?>';

                    // customer.country = 'United States';
                    // customer.country = '<?php echo $post_data['shipping_country']; ?>';

                    customer.phone = '<?php echo $post_data['shipping_phone'] !== '' ? $post_data['shipping_phone'] : $post_data['billing_phone']; ?>';

                    customer.zip = '<?php echo $post_data['shipping_postcode']; ?>';

                    GenCert.setCustomerData(customer);

                    GenCert.setShipZone('<?php echo $post_data['shipping_state']; ?>');

                    GenCert.show();
                </script>
            <?php
        }
    }
}


add_action( 'woocommerce_after_calculate_totals', 'custom_wc_after_calculate_totals' );
function custom_wc_after_calculate_totals() {
    if (is_ajax() && !empty( $_POST['post_data'] ) ) {
        parse_str( $_POST['post_data'], $post_data );
    }else {
        $post_data = $_POST;
    }
    if(!empty($post_data['tax_cert'])) {
        if ($post_data['tax_cert']!='NO') {
            // $totals = WC()->cart->get_totals();
            // $totals['total'] -= $totals['total_tax'];
            // $total['total_tax'] = 0;
            // WC()->cart->set_totals($totals);
            // WC()->cart->set_shipping_tax(0);
            // WC()->cart->set_shipping_taxes([]);
            // WC()->cart->set_cart_contents_tax(0);
            // WC()->cart->set_cart_contents_taxes([]);
            
        } else {
            // do_action( 'woocommerce_cart_reset', WC()->cart, false );
        }
    }
}

add_filter( 'woocommerce_product_tax_class', 'custom_wc_zero_tax_for_certificate' );
function custom_wc_zero_tax_for_certificate( $tax_class, $product) {
    if (is_ajax() && !empty( $_POST['post_data'] ) ) {
        parse_str( $_POST['post_data'], $post_data );
    }else {
        $post_data = $_POST;
    }
    if(!empty($post_data['tax_cert'])) {
        if ($post_data['tax_cert']!='NO') {
            $tax_class = 'Zero Rate';
        } else {
            // do_action( 'woocommerce_cart_reset', WC()->cart, false );
        }
    }
    return $tax_class;
}

function exclude_orders_filter_recipient( $recipient, $order ){

    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => 'wc-completed', // array_keys( wc_get_order_statuses() ),
    ) );

    $loyal_count = 1;
    $user_already_bought = get_user_meta(get_current_user_id(), 'already_bought', true);

    if ( count( $customer_orders ) >= $loyal_count || $user_already_bought=='1') {

        $recipient = explode(',', $recipient);
        $new_recipient = [];
        foreach($recipient as $val) {
            if ($val != 'orders@anandaprofessional.com') {
                $new_recipient[] = $val;
            }
        }
        $recipient = implode(',', $new_recipient);
    }

    return $recipient;
}
add_filter( 'woocommerce_email_recipient_new_order', 'exclude_orders_filter_recipient', 10, 2 );


// https://t.yctin.com/en/excel/to-php-array/
// $customers_array = array(
//     0 => array('address_1' => '212 MILLWELL DR', 'address_2' => 'SUITE A', 'city' => 'MARYLAND HEIGHTS', 'state' => 'MO', 'zip' => '63043-2512', 'phone' => '314-727-8787', 'npi' => '1790061596', 'email' => 'Mgraumenz@Legacydrug.com'),
// );
/*
$customers_array = array(
    0 => array('address_1' => '2628 County Rd. 516', 'address_2' => '', 'city' => 'Old Bridge', 'state' => 'NJ', 'zip' => '08857', 'phone' => '732-952-2244', 'npi' => '1013364645', 'email' => 'Achilles@acerxpharmacy.com'),
    1 => array('address_1' => '1695 S Lumpkin St', 'address_2' => '', 'city' => 'Athens', 'state' => 'GA', 'zip' => '30606', 'phone' => '706 548-2239', 'npi' => '1598707028', 'email' => 'add.drug@gmail.com'),
    2 => array('address_1' => '7916 Oakland Dr', 'address_2' => '', 'city' => 'Portage', 'state' => 'MI', 'zip' => '49024', 'phone' => '269 3241100', 'npi' => '1386957207', 'email' => 'acarepharmacy@gmail.com'),
    3 => array('address_1' => '3139 Lebanon Pike', 'address_2' => '', 'city' => 'Nashville', 'state' => 'TN', 'zip' => '37214', 'phone' => '615 622-2700', 'npi' => '1891102794', 'email' => 'affordablepharmacyservices@gmail.com'),
    4 => array('address_1' => '15412 Patrick Henry Hwy', 'address_2' => '', 'city' => 'Amelia Court House', 'state' => 'VA', 'zip' => '23002', 'phone' => 'usa 804 822-0245', 'npi' => '1053316166', 'email' => 'kim@cumberlandrx.com'),
    5 => array('address_1' => '87 4th Ave', 'address_2' => '', 'city' => 'Brooklyn', 'state' => 'NY', 'zip' => '11217', 'phone' => '347 850-4550', 'npi' => '1255761318', 'email' => 'musiceye@aol.com'),
    6 => array('address_1' => '3782 Old US 41 N, Ste B', 'address_2' => '', 'city' => 'Valdosta', 'state' => 'GA', 'zip' => '31602', 'phone' => '229 253-0067', 'npi' => '1760884233', 'email' => 'troy.allen@amerimedpharmacy.com'),
    7 => array('address_1' => '1933 N. Pinella Avenue', 'address_2' => '', 'city' => 'Tarpon Springs', 'state' => 'FL', 'zip' => '34689', 'phone' => '727 944-5800', 'npi' => '1346565645', 'email' => 'Anclotepharma@gmail.com'),
    8 => array('address_1' => '90 Plaza Dr', 'address_2' => '', 'city' => 'Lawrenceburg', 'state' => 'KY', 'zip' => '40342', 'phone' => '859-498-1004', 'npi' => '1508287095', 'email' => 'jbarnettejr@yahoo.com'),
    9 => array('address_1' => '801 N. 2nd Street', 'address_2' => '', 'city' => 'Clarksville', 'state' => 'TN', 'zip' => '37040', 'phone' => '931 802-5389', 'npi' => '1023243672', 'email' => 'andyspharmacy@gmail.com'),
    10 => array('address_1' => '703 Giddings Ave, Ste L1', 'address_2' => '', 'city' => 'Annapolis', 'state' => 'MD', 'zip' => '21401', 'phone' => '410 263-7440', 'npi' => '1609272111', 'email' => 'annapolispharmacy@professionalpharmacygroup.com'),
    11 => array('address_1' => '125 Foxglove Dr', 'address_2' => '', 'city' => 'Mount Sterling', 'state' => 'KY', 'zip' => '40353', 'phone' => '859 4981004', 'npi' => '1861597676', 'email' => 'jbarnettejr@yahoo.com'),
    12 => array('address_1' => '1016 N. Mulberry St', 'address_2' => '', 'city' => 'Elizabethtown', 'state' => 'KY', 'zip' => '42701', 'phone' => '270 760-0303', 'npi' => '1881107985', 'email' => 'apothecare2@bbtel.com'),
    13 => array('address_1' => '908 Woodland Drive', 'address_2' => '', 'city' => 'Elizabethtown', 'state' => 'KY', 'zip' => '42701', 'phone' => 'usa 270 982-0303', 'npi' => '1942341391', 'email' => 'apothecare2@bbtel.com'),
    14 => array('address_1' => '404 N Fruitland Blvd', 'address_2' => '', 'city' => 'Salisbury', 'state' => 'MD', 'zip' => '21801', 'phone' => 'usa 410 7498401', 'npi' => '1861496812', 'email' => 'jeff@appledrugs.com'),
    15 => array('address_1' => '2201 Broadway', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10024', 'phone' => '212 877-3480', 'npi' => '1225070857', 'email' => 'staff@apthorprx.com'),
    16 => array('address_1' => '639 Irvin St', 'address_2' => '', 'city' => 'Cornelia', 'state' => 'GA', 'zip' => '30531', 'phone' => '706 778-4918', 'npi' => '1437139201', 'email' => 'rphmchugh@gmail.com'),
    17 => array('address_1' => '1460 Ritchie Highway', 'address_2' => 'Suite 103', 'city' => 'Arnold', 'state' => 'MD', 'zip' => '21012', 'phone' => 'usa 443 949-8373', 'npi' => '1861714677', 'email' => 'arnoldpharmacy@professionalpharmacygroup.com'),
    18 => array('address_1' => '883 9th Ave (at 57th Street)', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10019', 'phone' => '212 245-8469', 'npi' => '1134234867', 'email' => 'arrowpharmacy@gmail.com'),
    19 => array('address_1' => '10 Scheivert Ave, Ste 1', 'address_2' => '', 'city' => 'Aston', 'state' => 'PA', 'zip' => '19014', 'phone' => '610 494-1445', 'npi' => '1114354735', 'email' => 'mpmcneill@aphhc.net'),
    20 => array('address_1' => '3501 Poplar Level Rd', 'address_2' => '', 'city' => 'Louisville', 'state' => 'KY', 'zip' => '40213', 'phone' => '502 3711002', 'npi' => '1306943147', 'email' => 'audubonpharmacy@mw.twcbc.com'),
    21 => array('address_1' => '7 Second Ave', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10003', 'phone' => '212 260-3131', 'npi' => '1629305800', 'email' => 'avalonchemists@gmail.com'),
    22 => array('address_1' => '1578 HWY 44 Unit 1', 'address_2' => '', 'city' => 'Sheperdsville', 'state' => 'KY', 'zip' => '40165', 'phone' => '502 5438200', 'npi' => '1629464854', 'email' => 'bbpharmacy@gmail.com'),
    23 => array('address_1' => '607 Jefferson St', 'address_2' => '', 'city' => 'Whiteville', 'state' => 'NC', 'zip' => '28472', 'phone' => '910 642-8141', 'npi' => '1982701827', 'email' => 'baldwinwoods@intrstar.net'),
    24 => array('address_1' => '3994 E. Harbor RD', 'address_2' => '', 'city' => 'Port Clinton', 'state' => 'OH', 'zip' => '43452', 'phone' => 'USA 419 341-1498', 'npi' => '1376657262', 'email' => 'charlie@bassettsmarket.com'),
    25 => array('address_1' => '7800 Meany Ave, Ste B', 'address_2' => '', 'city' => 'Bakersfield', 'state' => 'CA', 'zip' => '93308', 'phone' => '661 679-7902', 'npi' => '1982629150', 'email' => 'bewell7800@gmail.com'),
    26 => array('address_1' => '29099 Hospital Rd #101', 'address_2' => '', 'city' => 'Lake Arrowhead', 'state' => 'CA', 'zip' => '92352', 'phone' => '909 337-0747', 'npi' => '1285736674', 'email' => 'beemansrx@aol.com'),
    27 => array('address_1' => '1616 Choto Markets Way', 'address_2' => '', 'city' => 'Knoxville', 'state' => 'TN', 'zip' => '37922', 'phone' => 'usa 865 766-4424', 'npi' => '1689183774', 'email' => 'dhbelew@belewdrugs.com'),
    28 => array('address_1' => '2021 N Broadway St', 'address_2' => '', 'city' => 'Knoxville', 'state' => 'TN', 'zip' => '37917', 'phone' => 'usa 865 406-4189', 'npi' => '1447332226', 'email' => 'dhbelew@belewdrugs.com'),
    29 => array('address_1' => '5908 Washington Pike, Ste 102', 'address_2' => '', 'city' => 'Knoxville', 'state' => 'TN', 'zip' => '37918', 'phone' => 'usa 865 525-4967', 'npi' => '1316215981', 'email' => 'dhbelew@belewdrugs.com'),
    30 => array('address_1' => '8222 Asheville Hwy', 'address_2' => '', 'city' => 'Knoxville', 'state' => 'TN', 'zip' => '37924', 'phone' => 'usa 865 933-3441', 'npi' => '1245383173', 'email' => 'dhbelew@belewdrugs.com'),
    31 => array('address_1' => '402 Richmond Rd N,', 'address_2' => '', 'city' => 'Berea', 'state' => 'KY', 'zip' => '40403', 'phone' => '859 986-4521', 'npi' => '1750308730', 'email' => 'bereadrug@yahoo.com'),
    32 => array('address_1' => '3708 North Main Street', 'address_2' => '', 'city' => 'Farmville', 'state' => 'NC', 'zip' => '27828', 'phone' => 'usa 252 753-2092', 'npi' => '1942201173', 'email' => 'sheryl@bestvaluedrug.com'),
    33 => array('address_1' => '432 N Bedford Dr', 'address_2' => '', 'city' => 'Beverly Hills', 'state' => 'CA', 'zip' => '90210', 'phone' => '310 741-4596', 'npi' => '1821454554', 'email' => 'info@beverlyhillsapothecary.com'),
    34 => array('address_1' => '8640 W. 3rd St #100', 'address_2' => '', 'city' => 'Los Angeles', 'state' => 'CA', 'zip' => '90048', 'phone' => '310 299-0808', 'npi' => '1023406428', 'email' => 'josephspharmacy@gmail.com'),
    35 => array('address_1' => '879 Black Oak Ridge Rd,', 'address_2' => '', 'city' => 'Wayne', 'state' => 'NJ', 'zip' => '07470', 'phone' => '973 837-8770', 'npi' => '1790295616', 'email' => 'pharmacy@blackoakrx.com'),
    36 => array('address_1' => '1128 N. Main Street', 'address_2' => '', 'city' => 'Madisonville', 'state' => 'KY', 'zip' => '42431', 'phone' => 'usa 270 871-3603', 'npi' => '1437158730', 'email' => 'grussell@bluegrasspharmacy.com'),
    37 => array('address_1' => '3901 S Atherton St, Ste 1', 'address_2' => '', 'city' => 'State College', 'state' => 'PA', 'zip' => '16801', 'phone' => '814 466-7936', 'npi' => '1053388470', 'email' => 'pharmacy@boalsburgapothecary.com'),
    38 => array('address_1' => '311 Bogle St', 'address_2' => '', 'city' => 'Somerset', 'state' => 'KY', 'zip' => '42503', 'phone' => '606 451-1200', 'npi' => '1811419492', 'email' => 'btoygtoy@hotmail.com'),
    39 => array('address_1' => '1320 Washington St', 'address_2' => '', 'city' => 'Watertown', 'state' => 'NY', 'zip' => '13601', 'phone' => '315 782-1992', 'npi' => '1134128762', 'email' => 'boltons2inc@yahoo.com'),
    40 => array('address_1' => '5208 Charlotte Pike', 'address_2' => '', 'city' => 'Nashville', 'state' => 'TN', 'zip' => '37209', 'phone' => '615 383-2741', 'npi' => '1235175654', 'email' => 'c.vallone@bradleyhealthservices.net'),
    41 => array('address_1' => '1035 Professional Park Dr', 'address_2' => '', 'city' => 'Brandon', 'state' => 'FL', 'zip' => '33511', 'phone' => '813 684-4444', 'npi' => '1013382662', 'email' => 'Brandonpharmacy@gmail.com'),
    42 => array('address_1' => '206 W Dampier St', 'address_2' => '', 'city' => 'Inverness', 'state' => 'FL', 'zip' => '34450', 'phone' => '352 6372079', 'npi' => '1710912662', 'email' => 'jesse@brashearspharmacy.com'),
    43 => array('address_1' => '471 N Dacie Pt', 'address_2' => '', 'city' => 'Lecanto', 'state' => 'FL', 'zip' => '34461', 'phone' => '352 7463420', 'npi' => '1679779185', 'email' => 'jesse@brashearspharmacy.com'),
    44 => array('address_1' => '23 Murphy Hwy, Ste B', 'address_2' => '', 'city' => 'Blairsville', 'state' => 'GA', 'zip' => '30512', 'phone' => 'usa 706 455-8123', 'npi' => '1730115619', 'email' => 'brasstown@gmail.com'),
    45 => array('address_1' => '4603 Okeechobee Blvd. Suite 118', 'address_2' => '', 'city' => 'West Palm Beach', 'state' => 'FL', 'zip' => '33417', 'phone' => '480 466-1878', 'npi' => '1417470816', 'email' => 'info@breakfreepharmacy.com'),
    46 => array('address_1' => '1017 S Medical Dr.', 'address_2' => '', 'city' => 'Brigham City', 'state' => 'UT', 'zip' => '84302', 'phone' => '435 723-5211', 'npi' => '1114083359', 'email' => 'brighampharmacy@gmail.com'),
    47 => array('address_1' => '8916 Brodie Lane Ste. 300', 'address_2' => '', 'city' => 'Austin', 'state' => 'TX', 'zip' => '78748', 'phone' => '512 362-8083', 'npi' => '1851818249', 'email' => 'aaron@brodielanepharmacy.com'),
    48 => array('address_1' => '1337 New Rd, Ste 1', 'address_2' => '', 'city' => 'Northfield', 'state' => 'NJ', 'zip' => '08225', 'phone' => '609 484-0026', 'npi' => '1124282868', 'email' => 'buntingfamilypharmacy@verizon.net'),
    49 => array('address_1' => '2106 Route 130', 'address_2' => '', 'city' => 'HARRISON CITY', 'state' => 'PA', 'zip' => '15636', 'phone' => '724 744-3300', 'npi' => '1811992001', 'email' => 'pharmacy@bushyrunrx.comcastbiz.net'),
    50 => array('address_1' => '115 E. Chestnut Street', 'address_2' => '', 'city' => 'Corydon', 'state' => 'IN', 'zip' => '47112', 'phone' => 'usa 812 738-3272', 'npi' => '1659364487', 'email' => 'katie@buttdrugs.com'),
    51 => array('address_1' => '104 S Eisenhower Dr', 'address_2' => '', 'city' => 'beckley', 'state' => 'WV', 'zip' => '25801', 'phone' => '3042562006', 'npi' => '1760793772', 'email' => 'RxpGC2@gmail.com'),
    52 => array('address_1' => '5151 MacCorkle Ave SW', 'address_2' => '', 'city' => 'South Charleston', 'state' => 'WV', 'zip' => '25309', 'phone' => '304 766-0900', 'npi' => '1700253861', 'email' => 'bypassrx4@gmail.com'),
    53 => array('address_1' => '104 S Eisenhower Dr', 'address_2' => '', 'city' => 'Beckley', 'state' => 'WV', 'zip' => '25801', 'phone' => '304 256-2006', 'npi' => '1760793772', 'email' => 'Bypassrx@gmail.com'),
    54 => array('address_1' => '227 S Main St', 'address_2' => '', 'city' => 'Cambridge Springs', 'state' => 'PA', 'zip' => '16403', 'phone' => '814 398-4600', 'npi' => '1174740278', 'email' => 'springsrx@verizon.net'),
    55 => array('address_1' => '75 W Central Ave', 'address_2' => '', 'city' => 'Camden', 'state' => 'OH', 'zip' => '45311', 'phone' => '937 4521263', 'npi' => '1871675678', 'email' => 'rachelmaleski@yahoo.com'),
    56 => array('address_1' => '521 E Plaza Dr', 'address_2' => '', 'city' => 'Mooresville', 'state' => 'NC', 'zip' => '28115', 'phone' => '704 658-9870', 'npi' => '1225412562', 'email' => 'pantherhealthllc@gmail.com'),
    57 => array('address_1' => '5235 S College Rd', 'address_2' => '', 'city' => 'Wilmington', 'state' => 'NC', 'zip' => '28412', 'phone' => 'usa 910 620-8429', 'npi' => '1629180633', 'email' => 'kdowning@capefearpharmacy.com'),
    58 => array('address_1' => '662 E Main Street', 'address_2' => '', 'city' => 'Frankfort', 'state' => 'KY', 'zip' => '40601', 'phone' => '502 223-2827', 'npi' => '1184600280', 'email' => 'aaron@mycapitalpharmacy.com'),
    59 => array('address_1' => '220 N Park Rd, Bldg #3', 'address_2' => '', 'city' => 'Wyomissing', 'state' => 'PA', 'zip' => '19610', 'phone' => '610 378-1396 ext 3', 'npi' => '1508110024', 'email' => 'scott@capstonecompounding.com'),
    60 => array('address_1' => '1320 N Michigan Ave, Ste 1', 'address_2' => '', 'city' => 'Saginaw', 'state' => 'MI', 'zip' => '48602', 'phone' => '989 392-7799', 'npi' => '1558513770', 'email' => 'care1pharmacy@att.net'),
    61 => array('address_1' => '95-02 Van Wyck Expwy', 'address_2' => '', 'city' => 'Jamaica', 'state' => 'NY', 'zip' => '11419', 'phone' => '718 688-5556', 'npi' => '1447786298', 'email' => 'caremart@gmail.com'),
    62 => array('address_1' => '150 Martin Luther King Jr Blvd', 'address_2' => '', 'city' => 'Monroe', 'state' => 'GA', 'zip' => '30655', 'phone' => '770 2674048', 'npi' => '1932217866', 'email' => 'wanda@carmichaeldrugs.com'),
    63 => array('address_1' => '4036 River Oaks Drive', 'address_2' => 'Unit B-1', 'city' => 'Myrtle Beach', 'state' => 'SC', 'zip' => '29579', 'phone' => '843 236-6500', 'npi' => '120532374', 'email' => 'carolinalforestpharmacy@gmail.com'),
    64 => array('address_1' => '1112 W. Seventh Street', 'address_2' => '', 'city' => 'Hopkinsville', 'state' => 'KY', 'zip' => '42241', 'phone' => '(270) 886-4466', 'npi' => '1609867290', 'email' => 'aroeder@caycespharmacy.com'),
    65 => array('address_1' => '1308 Ashley Cir', 'address_2' => '', 'city' => 'Bowling Green', 'state' => 'KY', 'zip' => '42104', 'phone' => '270 781-5661', 'npi' => '1073627055', 'email' => 'cds10pharmacybg@gmail.com'),
    66 => array('address_1' => '1207 2nd Ave', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10065', 'phone' => '212 758-1199', 'npi' => '1467867796', 'email' => 'mk@cedrapharmacy.com'),
    67 => array('address_1' => '2268 Broadway', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10024', 'phone' => '212 877-5000', 'npi' => '1396297024', 'email' => 'mk@cedrapharmacy.com'),
    68 => array('address_1' => '416 Clematis St', 'address_2' => '', 'city' => 'West Palm Beach', 'state' => 'FL', 'zip' => '33401', 'phone' => '561 8057135', 'npi' => '1962435156', 'email' => 'ccpharmacist@gmail.com'),
    69 => array('address_1' => '203 N 2nd St, Ste 1', 'address_2' => '', 'city' => 'Central City', 'state' => 'KY', 'zip' => '42330', 'phone' => '270 754-4300', 'npi' => '1679600823', 'email' => 'clinicpharmacycc@yahoo.com'),
    70 => array('address_1' => '790 N Dixie Ave, Ste 1100', 'address_2' => '', 'city' => 'Elizabethtown', 'state' => 'KY', 'zip' => '42701', 'phone' => '270 737-7880', 'npi' => '1508935453', 'email' => 'centurymedicinesetown@gmail.com'),
    71 => array('address_1' => '1608 Old Lebanon Rd', 'address_2' => '', 'city' => 'Campbellsville', 'state' => 'Ky', 'zip' => '42718', 'phone' => '270 789-4734', 'npi' => '1730257973', 'email' => 'centurymedicinesetown@gmail.com'),
    72 => array('address_1' => '937 Campbellsville Road', 'address_2' => '', 'city' => 'Columbia', 'state' => 'KY', 'zip' => '42728', 'phone' => '270 3859139', 'npi' => '1568480598', 'email' => 'kmiller@certacare.com'),
    73 => array('address_1' => '278 Lincoln Way E, Ste 1', 'address_2' => '', 'city' => 'Chambersburg', 'state' => 'PA', 'zip' => '17201', 'phone' => 'usa 717 263-0747', 'npi' => '1912256215', 'email' => 'chambersrxllc@yahoo.com'),
    74 => array('address_1' => '800 N Parish Ave', 'address_2' => '', 'city' => 'Adel', 'state' => 'GA', 'zip' => '31620', 'phone' => 'usa 229 794-3525', 'npi' => '1265437768', 'email' => 'guswalters@chancydrugs.com'),
    75 => array('address_1' => '101 N Main St', 'address_2' => '', 'city' => 'Moultrie', 'state' => 'GA', 'zip' => '31768', 'phone' => 'usa 229 794-3525', 'npi' => '1801931555', 'email' => 'guswalters@chancydrugs.com'),
    76 => array('address_1' => '2333 N Ashley St', 'address_2' => '', 'city' => 'Valdosta', 'state' => 'GA', 'zip' => '31602', 'phone' => 'usa 229 794-3525', 'npi' => '1669738605', 'email' => 'guswalters@chancydrugs.com'),
    77 => array('address_1' => '138A Amicks Ferry Rd', 'address_2' => '', 'city' => 'Chapin', 'state' => 'SC', 'zip' => '29036', 'phone' => '803 345-1114', 'npi' => '1467551390', 'email' => 'rphmchugh@gmail.com'),
    78 => array('address_1' => '1150 US Highway 41 NW, Ste 13', 'address_2' => '', 'city' => 'Jasper', 'state' => 'FL', 'zip' => '32052', 'phone' => '386 638-0101', 'npi' => '1275085243', 'email' => 'Matt@cheekandscott.com'),
    79 => array('address_1' => '161 SW Stonegate Ter, Ste 105', 'address_2' => '', 'city' => 'Lake City', 'state' => 'FL', 'zip' => '32024', 'phone' => '386 754-5377', 'npi' => '1114009875', 'email' => 'eric@cheekandscott.com'),
    80 => array('address_1' => '1520 Ohio Ave S', 'address_2' => '', 'city' => 'Live Oak', 'state' => 'FL', 'zip' => '32064', 'phone' => '386 362-2591', 'npi' => '1407941123', 'email' => 'jayb@cheekandscott.com'),
    81 => array('address_1' => '154 9th Ave', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10011', 'phone' => '212 255-8000', 'npi' => '1164736799', 'email' => 'Chelsearoyalcarepharmacy@gmail.com'),
    82 => array('address_1' => '18525 Highway 22', 'address_2' => '', 'city' => 'Maurepas', 'state' => 'LA', 'zip' => '70449', 'phone' => 'usa 225 698-6000', 'npi' => '1740437037', 'email' => 'info@genericstogo.com'),
    83 => array('address_1' => '833 W. Main Street', 'address_2' => '', 'city' => 'Homer', 'state' => 'LA', 'zip' => '71040', 'phone' => '318 927-3523', 'npi' => '1740560192', 'email' => 'claibornerx@gmail.com'),
    84 => array('address_1' => '716 Boone Ave', 'address_2' => '', 'city' => 'Winchester', 'state' => 'KY', 'zip' => '40391', 'phone' => '859 744-3350', 'npi' => '1194715359', 'email' => 'clarkcountypharmacy@gmail.com'),
    85 => array('address_1' => '141 Hospital Dr', 'address_2' => '', 'city' => 'Salem', 'state' => 'KY', 'zip' => '42078', 'phone' => '270 988-3230', 'npi' => '1669566170', 'email' => 'admin@clinicpharmacy.net'),
    86 => array('address_1' => '900 N Main St', 'address_2' => '', 'city' => 'Cloverdale', 'state' => 'IN', 'zip' => '46120', 'phone' => '765 795-4100', 'npi' => '1780788927', 'email' => 'amanda.leach@gmail.com'),
    87 => array('address_1' => '1016 16th St, Apt 1', 'address_2' => '', 'city' => 'Wellington', 'state' => 'TX', 'zip' => '79095', 'phone' => '806 447-1184', 'npi' => '1700807757', 'email' => 'craig_ouellette@yahoo.com'),
    88 => array('address_1' => '728 N John Young Parkway', 'address_2' => '', 'city' => 'Kissimmee', 'state' => 'FL', 'zip' => '34741', 'phone' => '407 344-5558', 'npi' => '1538145222', 'email' => 'colonialdrugs155@gmail.com'),
    89 => array('address_1' => '155 E New England Ave', 'address_2' => '', 'city' => 'Winter Park', 'state' => 'FL', 'zip' => '32789', 'phone' => '407 647-2311', 'npi' => '1972589679', 'email' => 'colonialdrugs155@gmail.com'),
    90 => array('address_1' => '9920-4th Avenue', 'address_2' => '', 'city' => 'Brooklyn', 'state' => 'NY', 'zip' => '11209', 'phone' => '718 238-1445', 'npi' => '1629285598', 'email' => 'narrowsrx@verizon.net'),
    91 => array('address_1' => '109 shult Dr.', 'address_2' => '', 'city' => 'Columbus', 'state' => 'TX', 'zip' => '78934', 'phone' => '979 500-4191', 'npi' => '1457875874', 'email' => 'columbuslocalpharmacy@gmail.com'),
    92 => array('address_1' => '1256 Pennsylvania Ave', 'address_2' => '', 'city' => 'Tyrone', 'state' => 'PA', 'zip' => '16686', 'phone' => '814 6840230', 'npi' => '1316015670', 'email' => 'faust.corina.l@gmail.com'),
    93 => array('address_1' => '4510 Brockton Ave,', 'address_2' => '', 'city' => 'Riverside', 'state' => 'CA', 'zip' => '92501', 'phone' => '951 683-1200', 'npi' => '1184736001', 'email' => 'keyesb@prodigy.net'),
    94 => array('address_1' => '12121 Shelbyville Rd', 'address_2' => '', 'city' => 'Louisville', 'state' => 'KY', 'zip' => '40243', 'phone' => '502 2446500', 'npi' => '1154474492', 'email' => 'cathy@compoundcarerx.com'),
    95 => array('address_1' => '117 Clintonian Plz', 'address_2' => '', 'city' => 'Breese', 'state' => 'IL', 'zip' => '62230', 'phone' => '618 526-8040', 'npi' => '1871504373', 'email' => 'marktimmermann58@gmail.com'),
    96 => array('address_1' => '28 Montcalm Ave', 'address_2' => '', 'city' => 'Plattsburgh', 'state' => 'NY', 'zip' => '12901', 'phone' => '518 563-3400', 'npi' => '1811985161', 'email' => 'info@condopharmacy.com'),
    97 => array('address_1' => '459 State Rd.', 'address_2' => '', 'city' => 'West Tisbury', 'state' => 'MA', 'zip' => '02575', 'phone' => '508 693-7070', 'npi' => '1639239346', 'email' => 'tamara536@comcast.net'),
    98 => array('address_1' => '1909 Memorial Hwy', 'address_2' => '', 'city' => 'Shavertown', 'state' => 'PA', 'zip' => '18708', 'phone' => '570 1191', 'npi' => '1851396675', 'email' => 'cooksrx@aol.com'),
    99 => array('address_1' => '830 Main St', 'address_2' => '', 'city' => 'Charlestown', 'state' => 'IN', 'zip' => '47111', 'phone' => '812 256-2500', 'npi' => '1306820246', 'email' => 'cooperdrugs@yahoo.com'),
    100 => array('address_1' => '2087 E Florida Ave', 'address_2' => '', 'city' => 'Hemet', 'state' => 'CA', 'zip' => '92544', 'phone' => '951 658-7207', 'npi' => '1053353748', 'email' => 'ketsi1127@gmail.com'),
    101 => array('address_1' => '205 W. Cedar Rock St', 'address_2' => '', 'city' => 'Pickens', 'state' => 'SC', 'zip' => '29671', 'phone' => 'usa 864 878-6357', 'npi' => '1750475067', 'email' => 'corner-drugstore@hotmail.com'),
    102 => array('address_1' => '1701 Alexandria Dr. Ste. E', 'address_2' => '', 'city' => 'Lexington', 'state' => 'KY', 'zip' => '40504', 'phone' => '859 3091230', 'npi' => '1912292210', 'email' => 'cornerpharmkatherine@aol.com'),
    103 => array('address_1' => '39575 Washington St, Ste 103', 'address_2' => '', 'city' => 'Palm Desert', 'state' => 'CA', 'zip' => '92211', 'phone' => '760 200-0220', 'npi' => '144778114', 'email' => 'gtcollins@gmail.com'),
    104 => array('address_1' => '112 N Lebanon St', 'address_2' => '', 'city' => 'Lebanon', 'state' => 'IN', 'zip' => '46052', 'phone' => '765 482-1081', 'npi' => '1487755781', 'email' => 'cowandrugs@yahoo.com'),
    105 => array('address_1' => '9843 3rd Street Rd', 'address_2' => '', 'city' => 'Louisville', 'state' => 'KY', 'zip' => '40272', 'phone' => '5029338444', 'npi' => '1164506291', 'email' => 'coxspharmacy#5@gmail.com'),
    106 => array('address_1' => '228 N Fairmont Ave', 'address_2' => '', 'city' => 'Morristown', 'state' => 'TN', 'zip' => '37814', 'phone' => '423 586-6263', 'npi' => '1225049893', 'email' => 'rkragel@crescentdrugs.com'),
    107 => array('address_1' => '203 E Jefferson Ave', 'address_2' => '', 'city' => 'Whitney', 'state' => 'TX', 'zip' => '76692', 'phone' => '580 2380177', 'npi' => '1104981216', 'email' => 'willdouglas@crimsoncarerx.com'),
    108 => array('address_1' => '710 Main Ave', 'address_2' => '', 'city' => 'Crivitz', 'state' => 'WI', 'zip' => '54114', 'phone' => '715 8547425', 'npi' => '1679542062', 'email' => 'megspharmacy@yahoo.com'),
    109 => array('address_1' => '2609 N High St', 'address_2' => '', 'city' => 'Columbus', 'state' => 'OH', 'zip' => '43202', 'phone' => '614 263-9424', 'npi' => '1346237146', 'email' => 'schoen@crosbysdrugs.com'),
    110 => array('address_1' => '209 E Pat Rady Way', 'address_2' => '', 'city' => 'Bainbridge', 'state' => 'IN', 'zip' => '46105', 'phone' => '765 522-4300', 'npi' => '1003332495', 'email' => 'amanda.leach@gmail.com'),
    111 => array('address_1' => '1756 Anderson Hwy', 'address_2' => '', 'city' => 'Cumberland', 'state' => 'VA', 'zip' => '23040', 'phone' => 'usa 804 822-0245', 'npi' => '1669601324', 'email' => 'kim@cumberlandrx.com'),
    112 => array('address_1' => '353 Crooks Ave', 'address_2' => '', 'city' => 'Clifton', 'state' => 'NJ', 'zip' => '07011', 'phone' => '862 225-9432', 'npi' => '1477979649', 'email' => 'ghada@curemedpharmacy.com'),
    113 => array('address_1' => '30226 US Highway 19 N', 'address_2' => '', 'city' => 'Clearwater', 'state' => 'FL', 'zip' => '33761', 'phone' => '727 869-3784', 'npi' => '1740456417', 'email' => 'curlewpharmacy@gmail.com'),
    114 => array('address_1' => '124 Market Pl', 'address_2' => '', 'city' => 'San Ramon', 'state' => 'CA', 'zip' => '94583', 'phone' => '925 830-4631', 'npi' => '1245241827', 'email' => 'alcostarx@gmail.com'),
    115 => array('address_1' => '6005 W. 71st St', 'address_2' => '', 'city' => 'Indianapolis', 'state' => 'IN', 'zip' => '46278', 'phone' => 'usa 317- 803-3436', 'npi' => '1497963540', 'email' => 'jeff@custommed.com'),
    116 => array('address_1' => '482 W Navajo St, Ste A', 'address_2' => '', 'city' => 'West Lafayette', 'state' => 'IN', 'zip' => '47906', 'phone' => '765 4632600', 'npi' => '1396011888', 'email' => 'pharmacy@customplusrx.com'),
    117 => array('address_1' => '418 Garrison Road', 'address_2' => 'Suite 100', 'city' => 'Stafford', 'state' => 'VA', 'zip' => '22554', 'phone' => 'usa 540 657-0006', 'npi' => '5406570006', 'email' => 'rx@danscare.com'),
    118 => array('address_1' => '178 Wren St', 'address_2' => '', 'city' => 'Barnwell', 'state' => 'SC', 'zip' => '29812', 'phone' => '803 259-1234', 'npi' => '1427146059', 'email' => 'rphmchugh@gmail.com'),
    119 => array('address_1' => '19354 Solomon Blatt Ave N', 'address_2' => '', 'city' => 'Blackville', 'state' => 'SC', 'zip' => '29817', 'phone' => '803 284-3372', 'npi' => '1821186461', 'email' => 'rphmchugh@gmail.com'),
    120 => array('address_1' => '1229 North Way', 'address_2' => '', 'city' => 'Darien', 'state' => 'GA', 'zip' => '31305', 'phone' => '912 437-3784', 'npi' => '1467496653', 'email' => 'darienrx437@gmail.com'),
    121 => array('address_1' => '300 S Perry St', 'address_2' => '', 'city' => 'Attica', 'state' => 'IN', 'zip' => '47918', 'phone' => '765 762-3287', 'npi' => '1902883994', 'email' => 'nlbdavis@yahoo.com'),
    122 => array('address_1' => '309 S. Texas St', 'address_2' => '', 'city' => 'De Leon', 'state' => 'TX', 'zip' => '76444', 'phone' => '254 2146059706', 'npi' => '1194829168', 'email' => 'deleonpharmacy@gmail.com'),
    123 => array('address_1' => '124 NE 5th Ave', 'address_2' => '', 'city' => 'Delray Beach', 'state' => 'FL', 'zip' => '33483', 'phone' => '561 272-2124', 'npi' => '1912086687', 'email' => 'delrayshorepharmacy@gmail.com'),
    124 => array('address_1' => '776 Deltona Blvd', 'address_2' => '', 'city' => 'Deltona', 'state' => 'FL', 'zip' => '32725', 'phone' => '386 574-7600', 'npi' => '1164408860', 'email' => 'colonialdrugs155@gmail.com'),
    125 => array('address_1' => '617 3rd St.', 'address_2' => '', 'city' => 'Dunmore', 'state' => 'PA', 'zip' => '18512', 'phone' => '570 209-7440', 'npi' => '1720358344', 'email' => 'tom@depietropharmacy.com'),
    126 => array('address_1' => '104 HWY 70 East', 'address_2' => '', 'city' => 'Dickson', 'state' => 'TN', 'zip' => '37055', 'phone' => 'usa 615 446-5585', 'npi' => '1659381093', 'email' => 'sshep2@comcast.net'),
    127 => array('address_1' => '200 W Harrison St, Ste A', 'address_2' => '', 'city' => 'Dillon', 'state' => 'SC', 'zip' => '29536', 'phone' => '843 7744749', 'npi' => '1386066132', 'email' => 'dilloncommunitypharmacy@hotmail.com'),
    128 => array('address_1' => '1311 Ring Rd, Ste 107', 'address_2' => '', 'city' => 'Elizabethtown', 'state' => 'KY', 'zip' => '42701', 'phone' => '270 872-0588', 'npi' => '1326409475', 'email' => 'dixiepharmacy2@gmail.com'),
    129 => array('address_1' => '2151 S Alternate A1A, Ste 1500', 'address_2' => '', 'city' => 'Jupiter', 'state' => 'FL', 'zip' => '33477', 'phone' => '561 7411191', 'npi' => '1912900226', 'email' => 'rick@palmbeachcompounding.com'),
    130 => array('address_1' => '127 East North St', 'address_2' => '', 'city' => 'Madisonville', 'state' => 'KY', 'zip' => '42431', 'phone' => 'usa 270 821-5440', 'npi' => '1235273467', 'email' => 'annette.127@bellsouth.net'),
    131 => array('address_1' => '101 Darby Sq', 'address_2' => '', 'city' => 'Elverson', 'state' => 'PA', 'zip' => '19520', 'phone' => '610 286-0496', 'npi' => '1235267196', 'email' => 'dougspharm@aol.com'),
    132 => array('address_1' => '1307 Donelson Parkway', 'address_2' => '', 'city' => 'Dover', 'state' => 'TN', 'zip' => '37058', 'phone' => '931 232-0123', 'npi' => '1205872751', 'email' => 'blanemgt@att.net'),
    133 => array('address_1' => '7320 E 82nd St, Ste A', 'address_2' => '', 'city' => 'Indianapolis', 'state' => 'IN', 'zip' => '46256', 'phone' => '317 842-5771', 'npi' => '1609940816', 'email' => 'kurt@drazizrx.com'),
    134 => array('address_1' => '1401 E Franklin Blvd', 'address_2' => '', 'city' => 'Gastonia', 'state' => 'NC', 'zip' => '28054', 'phone' => '980 320-1532', 'npi' => '1932586351', 'email' => 'info@dsdpharmacy.org'),
    135 => array('address_1' => '620 Dunlop Ln, Ste 111', 'address_2' => '', 'city' => 'Clarksville', 'state' => 'TN', 'zip' => '37040', 'phone' => '931 278-6422', 'npi' => '1881140127', 'email' => 'dunlopllc@gmail.com'),
    136 => array('address_1' => '6473 Highway 44, Ste 101', 'address_2' => '', 'city' => 'Gonzales', 'state' => 'LA', 'zip' => '70737', 'phone' => '225 257-1009', 'npi' => '1417211764', 'email' => 'ralphsrx3@gmail.com'),
    137 => array('address_1' => '1815 Central Ave NW', 'address_2' => '', 'city' => 'Alburquerque', 'state' => 'NM', 'zip' => '87104', 'phone' => 'usa  505 247-4141', 'npi' => '1497825970', 'email' => 'monaghattas@me.com'),
    138 => array('address_1' => '100 E. Cumberland St.', 'address_2' => '', 'city' => 'Albany', 'state' => 'KY', 'zip' => '42602', 'phone' => '606 387-6444', 'npi' => '1497754923', 'email' => 'arica702@hotmail.com'),
    139 => array('address_1' => '9010 Crawfordsville Rd', 'address_2' => '', 'city' => 'Indianapolis', 'state' => 'IN', 'zip' => '46234', 'phone' => '317 299-3771', 'npi' => '1427114651', 'email' => 'eaglehighlandpharmacy@yahoo.com'),
    140 => array('address_1' => '524 Andrew Johnson Hwy', 'address_2' => '', 'city' => 'Strawberry Plains', 'state' => 'TN', 'zip' => '37871', 'phone' => '865 933-4149', 'npi' => '1831108471', 'email' => 'carms0989@gmail.com'),
    141 => array('address_1' => '9650 E Washington St', 'address_2' => '', 'city' => 'Indianapolis', 'state' => 'IN', 'zip' => '46229', 'phone' => '317 591-9393', 'npi' => '1124567565', 'email' => 'shaukatyousaf69@gmail.com'),
    142 => array('address_1' => '56 Echo Ave.', 'address_2' => '', 'city' => 'Miller Place', 'state' => 'NY', 'zip' => '11764', 'phone' => '631 6428175', 'npi' => '1386741213', 'email' => 'info@echopharmacy.com'),
    143 => array('address_1' => '139, MANATEE AVE WEST', 'address_2' => '', 'city' => 'Bradenton', 'state' => 'FL', 'zip' => '34209', 'phone' => 'USA 941 538-7122', 'npi' => '1316205271', 'email' => 'bradenton@myeckerds.com'),
    144 => array('address_1' => '1011 N Main St', 'address_2' => '', 'city' => 'Edgerton', 'state' => 'WI', 'zip' => '53534', 'phone' => 'usa 608 884-3308', 'npi' => '1538159116', 'email' => 'jenna@edgertonpharmacy.com'),
    145 => array('address_1' => '23665 Moulton Parkway, Ste A', 'address_2' => '', 'city' => 'Laguna Hills', 'state' => 'CA', 'zip' => '92653', 'phone' => '949 586-3664', 'npi' => '1124122841', 'email' => 'stewart@eltoropharmacy.com'),
    146 => array('address_1' => '111 S Stuart Ave', 'address_2' => '', 'city' => 'Elkton', 'state' => 'VA', 'zip' => '22827', 'phone' => '540 2989090', 'npi' => '1700159696', 'email' => 'bryant.randy76@gmail.com'),
    147 => array('address_1' => '508 E 10th St', 'address_2' => '', 'city' => 'Sheridan', 'state' => 'IN', 'zip' => '46069', 'phone' => '317 7584121', 'npi' => '1134645146', 'email' => 'lindsey@ellapharmacy.com'),
    148 => array('address_1' => '9338 Baltimore Nation Pike, Unit 11', 'address_2' => '', 'city' => 'Ellicott City', 'state' => 'MD', 'zip' => '21029', 'phone' => '410 750-1951', 'npi' => '1497041768', 'email' => 'ellicottcitypharmacy@gmail.com'),
    149 => array('address_1' => '415 Happy Valley Rd.', 'address_2' => '', 'city' => 'Glascow', 'state' => 'KY', 'zip' => '42141', 'phone' => '270-651-8359', 'npi' => '1306878988', 'email' => 'shanebuie@elydrugs.biz'),
    150 => array('address_1' => '200 Hospital Dr, Ste 107', 'address_2' => '', 'city' => 'Glen Burnie', 'state' => 'MD', 'zip' => '21061', 'phone' => '410 787-0030', 'npi' => '1194752667', 'email' => 'empirepharmacy@professionalpharmacygroup.com'),
    151 => array('address_1' => '1111 48th Ave N, Ste 115', 'address_2' => '', 'city' => 'Myrtle Beach', 'state' => 'SC', 'zip' => '29577', 'phone' => '843 712-1897', 'npi' => '1548475783', 'email' => 'contact@enhealthmatters.com'),
    152 => array('address_1' => '914 No. Dixie Ave Suite 103', 'address_2' => '', 'city' => 'Elizabethtown', 'state' => 'KY', 'zip' => '42701', 'phone' => 'usa 270 900-1583', 'npi' => '1295020741', 'email' => 'etownpharmacy@gmail.com'),
    153 => array('address_1' => '1505 W Reynolds St', 'address_2' => '', 'city' => 'Plant City', 'state' => 'FL', 'zip' => '33563', 'phone' => 'usa 813 659-9777', 'npi' => '1932271590', 'email' => 'familycarepharmacy@yahoo.com'),
    154 => array('address_1' => '3644 Webber St', 'address_2' => '', 'city' => 'Sarasota', 'state' => 'FL', 'zip' => '34232', 'phone' => '941 921-6645', 'npi' => '1184666984', 'email' => 'license@familypharmacy.org'),
    155 => array('address_1' => '335 Main St', 'address_2' => '', 'city' => 'Duryea', 'state' => 'PA', 'zip' => '18642', 'phone' => '570 4576789', 'npi' => '1659379287', 'email' => 'familyrx335@yahoo.com'),
    156 => array('address_1' => '14821 Dayton Pike STE 115', 'address_2' => '', 'city' => 'Sale Creek', 'state' => 'TN', 'zip' => '37373', 'phone' => '423 486-9404', 'npi' => '1184977282', 'email' => 'salecreekpharmacy@gmail.com'),
    157 => array('address_1' => '4000 4th St', 'address_2' => '', 'city' => 'Marrero', 'state' => 'LA', 'zip' => '70072', 'phone' => '504 341-2711', 'npi' => '1356453203', 'email' => 'folserx2@bellsouth.net'),
    158 => array('address_1' => '425 E Dupont Rd', 'address_2' => '', 'city' => 'Fort Wayne', 'state' => 'IN', 'zip' => '46825', 'phone' => '260 490-3447', 'npi' => '1841347481', 'email' => 'retail@fwcustomrx.com'),
    159 => array('address_1' => '1928 Cumberland Ave,', 'address_2' => '', 'city' => 'Middlesboro', 'state' => 'KY', 'zip' => '40965', 'phone' => '606 2481052', 'npi' => '1700885001', 'email' => 'Hikingdawg@gmail.com'),
    160 => array('address_1' => '11100 Warner Ave, Ste 1', 'address_2' => '', 'city' => 'Fountain Valley', 'state' => 'CA', 'zip' => '92708', 'phone' => '714 979-9600', 'npi' => '1336290527', 'email' => 'fountainvalleyrx@outlook.com'),
    161 => array('address_1' => '30 S Water St Suite B', 'address_2' => '', 'city' => 'Franklin', 'state' => 'IN', 'zip' => '46131', 'phone' => '317 739-3257', 'npi' => '1871986596', 'email' => 'jamiefranklinrx@gmail.com'),
    162 => array('address_1' => '7963 Heritage Village Plz', 'address_2' => '', 'city' => 'Gainesville', 'state' => 'VA', 'zip' => '20155', 'phone' => '703 743-5603', 'npi' => '1700300506', 'email' => 'dpskahlon@gmail.com'),
    163 => array('address_1' => '58 Physicians Drive', 'address_2' => 'Suite 5', 'city' => 'Supply', 'state' => 'NC', 'zip' => '28462', 'phone' => '910-754-7200', 'npi' => '1366488389', 'email' => 'gallowaysands@atmc.net'),
    164 => array('address_1' => '1513 N. Howe St', 'address_2' => 'Suite 8', 'city' => 'Southport', 'state' => 'NC', 'zip' => '28461', 'phone' => 'usa 910 454-9090', 'npi' => '1255762134', 'email' => 'gallowaysands2@bizec.rr.com'),
    165 => array('address_1' => '325 S Main St', 'address_2' => '', 'city' => 'Fortville', 'state' => 'IN', 'zip' => '46040', 'phone' => 'usa 317 485-5555', 'npi' => '1598158016', 'email' => 'garstrx@gmail.com'),
    166 => array('address_1' => '1118 Mack St', 'address_2' => '', 'city' => 'Gaston', 'state' => 'SC', 'zip' => '29053', 'phone' => '803 939-8489', 'npi' => '1740370725', 'email' => 'rphmchugh@gmail.com'),
    167 => array('address_1' => '1024 Philadelphia St', 'address_2' => '', 'city' => 'Indiana', 'state' => 'PA', 'zip' => '15701', 'phone' => '724 349-4200', 'npi' => '1033223573', 'email' => 'smithcooney@gattirx.com'),
    168 => array('address_1' => '348 Pennsylvania Ave W,', 'address_2' => '', 'city' => 'Warren', 'state' => 'PA', 'zip' => '16365', 'phone' => '814 723-2840', 'npi' => '1013940600', 'email' => 'gaughns@gmail.com'),
    169 => array('address_1' => '228 W Main St', 'address_2' => '', 'city' => 'Genoa', 'state' => 'IL', 'zip' => '60135', 'phone' => '815 784-2122', 'npi' => '1386078830', 'email' => 'parag@doserx.com'),
    170 => array('address_1' => '1198 State Rd 46 E', 'address_2' => '', 'city' => 'Batesville', 'state' => 'IN', 'zip' => '47006', 'phone' => '812 932-6251', 'npi' => '1801289673', 'email' => 'ejschoett@yahoo.com'),
    171 => array('address_1' => '1002 Lexington Rd Suite 7', 'address_2' => '', 'city' => 'Georgetown', 'state' => 'KY', 'zip' => '40324', 'phone' => '502 3704336', 'npi' => '1407291982', 'email' => 'jbarnettejr@yahoo.com'),
    172 => array('address_1' => '173 Old Tappan Rd', 'address_2' => '', 'city' => 'Old Tappan', 'state' => 'NJ', 'zip' => '07675', 'phone' => '201 767-0095', 'npi' => '1346304235', 'email' => 'mail@getrxhelp.com'),
    173 => array('address_1' => '44523 Marietta Rd', 'address_2' => '', 'city' => 'Caldwell', 'state' => 'OH', 'zip' => '43724', 'phone' => '740 732-2356', 'npi' => '1336181296', 'email' => 'scottb@bradenmed.com'),
    174 => array('address_1' => '352 Righters Mill Rd', 'address_2' => '', 'city' => 'Gladwyne', 'state' => 'PA', 'zip' => '19035', 'phone' => '610 649-1100', 'npi' => '1467559054', 'email' => 'bjzaslow@gladwynepharmacy.com'),
    175 => array('address_1' => '615 S. L Rogers Wells Blvd.', 'address_2' => '', 'city' => 'Glasgow', 'state' => 'KY', 'zip' => '42141', 'phone' => '270 670-7676', 'npi' => '1326193236', 'email' => 'robertoliver@glasgowrx.com'),
    176 => array('address_1' => '500 S Grant Ave', 'address_2' => '', 'city' => 'Fowler', 'state' => 'IN', 'zip' => '47944', 'phone' => '765 884-1520', 'npi' => '184126793', 'email' => 'glotzbachpharmacy@gmail.com'),
    177 => array('address_1' => '46950 Community Plz, Ste 112', 'address_2' => '', 'city' => 'Sterling', 'state' => 'VA', 'zip' => '20164', 'phone' => '703 430-8883', 'npi' => '1114304268', 'email' => 'elsalam@goldenhealthpharmacy.com'),
    178 => array('address_1' => '60 Cassidy Ave #3', 'address_2' => '', 'city' => 'Danville', 'state' => 'KY', 'zip' => '40422', 'phone' => '859 936-1222', 'npi' => '1902970304', 'email' => 'pharmr@bellsouth.net'),
    179 => array('address_1' => '3725 W 4100 S', 'address_2' => '', 'city' => 'West Valley City', 'state' => 'UT', 'zip' => '84120', 'phone' => '801 965-3639', 'npi' => '1922154756', 'email' => 'info@grangerpharmacy.com'),
    180 => array('address_1' => '316 N Main St,', 'address_2' => '', 'city' => 'Granite', 'state' => 'OK', 'zip' => '73547', 'phone' => '580 535-2130', 'npi' => '1508249814', 'email' => 'katy@granitedrug.com'),
    181 => array('address_1' => '414 SW 6th St', 'address_2' => '', 'city' => 'Grants Pass', 'state' => 'OR', 'zip' => '97526', 'phone' => 'usa 541 476-4262', 'npi' => '1457411779', 'email' => 'michele@grantspasspharmacy.com'),
    182 => array('address_1' => '230 N Front St', 'address_2' => '', 'city' => 'Philipsburg', 'state' => 'PA', 'zip' => '16866', 'phone' => '814 342-2020', 'npi' => '1144329020', 'email' => 'anthony@grattanspharmacy.com'),
    183 => array('address_1' => '10154 Brooks School Rd', 'address_2' => '', 'city' => 'Fishers', 'state' => 'IN', 'zip' => '46037', 'phone' => '317 436-8366', 'npi' => '1659706745', 'email' => 'info@mygreenleafrx.com'),
    184 => array('address_1' => '117 N Main St', 'address_2' => '', 'city' => 'Greenville', 'state' => 'KY', 'zip' => '42345', 'phone' => 'usa 270 476-3600', 'npi' => '1386731537', 'email' => 'john_gentry@bellsouth.net'),
    185 => array('address_1' => '310 S. Main St', 'address_2' => 'Suite K', 'city' => 'Yeagerton', 'state' => 'PA', 'zip' => '17099', 'phone' => 'usa 717 953-9534', 'npi' => '1386988160', 'email' => 'jeffbonjo@aol.com'),
    186 => array('address_1' => '3801 Lake Mary Blvd, Ste 127', 'address_2' => '', 'city' => 'Lake Mary', 'state' => 'FL', 'zip' => '32746', 'phone' => '407 915-6505', 'npi' => '1366868911', 'email' => 'greenwoodrx@gmail.com'),
    187 => array('address_1' => '12555 Garden Grove Blvd, Ste 102', 'address_2' => '', 'city' => 'Garden Grove', 'state' => 'CA', 'zip' => '92843', 'phone' => '714 636-0593', 'npi' => '1649713777', 'email' => 'groveharborpharmacy@gmail.com'),
    188 => array('address_1' => '340 W 23rd St, Ste D2', 'address_2' => '', 'city' => 'Panama City', 'state' => 'FL', 'zip' => '32405', 'phone' => '850 6151000', 'npi' => '1164742334', 'email' => 'gulfpharmacy@yahoo.com'),
    189 => array('address_1' => '521 W Commerce St', 'address_2' => '', 'city' => 'Lewisburg', 'state' => 'TN', 'zip' => '37091', 'phone' => '931 359-2534', 'npi' => '1548395791', 'email' => 'hsl@handspharmacy.com'),
    190 => array('address_1' => '304 N Texana St', 'address_2' => '', 'city' => 'Hallettsville', 'state' => 'TX', 'zip' => '77964', 'phone' => 'usa 361 7722920', 'npi' => '1043382849', 'email' => 'paula@hallettsvillepharmacy.com'),
    191 => array('address_1' => '262 N State St', 'address_2' => '', 'city' => 'Hampshire', 'state' => 'IL', 'zip' => '60140', 'phone' => '847 683-2244', 'npi' => '1275827032', 'email' => 'parag@doserx.com'),
    192 => array('address_1' => '450 Fulton St.', 'address_2' => 'Suite 300', 'city' => 'Hannibal', 'state' => 'NY', 'zip' => '13074', 'phone' => '315 5646464', 'npi' => '1770876567', 'email' => 'hannibalpharmacy@gmail.com'),
    193 => array('address_1' => '594 N Main St', 'address_2' => '', 'city' => 'Mooresville', 'state' => 'NC', 'zip' => '28115', 'phone' => '704 7996870', 'npi' => '1336222801', 'email' => 'gavin@mooresvillepharmacy.com'),
    194 => array('address_1' => '1410 Eagle Dr', 'address_2' => '', 'city' => 'Ashland', 'state' => 'KY', 'zip' => '41102', 'phone' => '606 738-4042', 'npi' => '1588723688', 'email' => 'dickersonjbd@yahoo.com'),
    195 => array('address_1' => '3000 Far Hills Ave', 'address_2' => '', 'city' => 'Dayton', 'state' => 'OH', 'zip' => '45429', 'phone' => '937 7239075', 'npi' => '1811233430', 'email' => 'drghussin@yahoo.com'),
    196 => array('address_1' => '5551 S Main St', 'address_2' => '', 'city' => 'Eminence', 'state' => 'KY', 'zip' => '40019', 'phone' => '8594981004', 'npi' => '1952722449', 'email' => 'jbarnettejr@yahoo.com'),
    197 => array('address_1' => '207 N Dixon Rd', 'address_2' => '', 'city' => 'Kokomo', 'state' => 'IN', 'zip' => '46901', 'phone' => '765 4529000', 'npi' => '1295519563', 'email' => 'heidi@herbstpharmacy.com'),
    198 => array('address_1' => '741 E Landis Ave,', 'address_2' => '', 'city' => 'Vineland', 'state' => 'NJ', 'zip' => '08360', 'phone' => '856 691-3784', 'npi' => '1649535105', 'email' => 'nando16@comcast.net'),
    199 => array('address_1' => '116 N Park Ave', 'address_2' => '', 'city' => 'Herrin', 'state' => 'IL', 'zip' => '62948', 'phone' => '618 6186944494', 'npi' => '1417947870', 'email' => 'herrindrug@yahoo.com'),
    200 => array('address_1' => '735 Main St N', 'address_2' => '', 'city' => 'New Ellenton', 'state' => 'SC', 'zip' => '29809', 'phone' => 'usa 803 646-5036', 'npi' => '1205226495', 'email' => 'hibbittsland@gmail.com'),
    201 => array('address_1' => '35 Hidenwood Shopping Ctr', 'address_2' => '', 'city' => 'Newport News', 'state' => 'VA', 'zip' => '23606', 'phone' => '757 595-1151', 'npi' => '1720004435', 'email' => 'Hidenwoodrx@verizon.net'),
    202 => array('address_1' => '818 E Warrington Ave', 'address_2' => '', 'city' => 'Pittsburgh', 'state' => 'PA', 'zip' => '15210', 'phone' => '412 4315766', 'npi' => '1285851725', 'email' => 'hilltoppharm818@gmail.com'),
    203 => array('address_1' => '1340 KY HWY 185', 'address_2' => '', 'city' => 'Bowling Green', 'state' => 'KY', 'zip' => '42101', 'phone' => 'USA 270 842-4341', 'npi' => '1245323997', 'email' => 'hinespharmacy@hotmail.com'),
    204 => array('address_1' => '165 Narchez Trace Ave Suite 101', 'address_2' => '', 'city' => 'Bowling Gren', 'state' => 'KY', 'zip' => '42104', 'phone' => 'usa 270 796-1818', 'npi' => '1982875514', 'email' => 'hinespharmacy@gmail.com'),
    205 => array('address_1' => '3449 Fall Hill Ave', 'address_2' => '', 'city' => 'Fredericksburg', 'state' => 'VA', 'zip' => '22401', 'phone' => '540 358-8188', 'npi' => '1225574536', 'email' => 'hnrpharmacy@gmail.com'),
    206 => array('address_1' => '900 Main Street', 'address_2' => '', 'city' => 'Limon', 'state' => 'CO', 'zip' => '80828', 'phone' => '719 740-1993', 'npi' => '1548365992', 'email' => 'ryerx23@yahoo.com'),
    207 => array('address_1' => '359 W Walnut St', 'address_2' => '', 'city' => 'Frankfort', 'state' => 'IN', 'zip' => '46041', 'phone' => '765 654-4300', 'npi' => '1821453341', 'email' => 'holdenpharmacy@gmail.com'),
    208 => array('address_1' => '1134 US HWY 27 S', 'address_2' => '', 'city' => 'Cynthiana', 'state' => 'KY', 'zip' => '41031', 'phone' => '859-234-56', 'npi' => '1275058513', 'email' => 'singram@kih.net'),
    209 => array('address_1' => '6201 Vogel Rd', 'address_2' => '', 'city' => 'Evansville', 'state' => 'IN', 'zip' => '47715', 'phone' => '812 476-6194', 'npi' => '1558577601', 'email' => 'john@hooksrx.com'),
    210 => array('address_1' => '4227 S Highland Dr', 'address_2' => '', 'city' => 'Holladay', 'state' => 'UT', 'zip' => '84124', 'phone' => '877 401-4317', 'npi' => '1417400797', 'email' => 'hrxpharmacy@gmail.com'),
    211 => array('address_1' => '112 N. Liberty Street', 'address_2' => '', 'city' => 'New Castle', 'state' => 'PA', 'zip' => '16102', 'phone' => 'usa 724 652-1451', 'npi' => '1548371602', 'email' => 'astefanis@hydedrugstore.com'),
    212 => array('address_1' => '1001 W Kingshighway', 'address_2' => '', 'city' => 'Paragould', 'state' => 'AR', 'zip' => '72450', 'phone' => '870 239-4036', 'npi' => '1487675310', 'email' => 'hyderx@hotmail.com'),
    213 => array('address_1' => '2115 14th St, Ste 201', 'address_2' => '', 'city' => 'Auburn', 'state' => 'NE', 'zip' => '68305', 'phone' => '402 274-5225', 'npi' => '1457711988', 'email' => 'familyvaluepharmacy@gmail.com'),
    214 => array('address_1' => '8395 E 116th St', 'address_2' => '', 'city' => 'Fishers', 'state' => 'IN', 'zip' => '46038', 'phone' => '317 288-0400', 'npi' => '1093117426', 'email' => 'aaly@ippindy.com'),
    215 => array('address_1' => '4300 E. Indian River road', 'address_2' => '', 'city' => 'Chesapeake', 'state' => 'VA', 'zip' => '23325', 'phone' => '757 4208418', 'npi' => '1124052550', 'email' => 'irwinspharmacy@gmail.com'),
    216 => array('address_1' => '23010 Highway 5', 'address_2' => '', 'city' => 'West Blocton', 'state' => 'AL', 'zip' => '35184', 'phone' => '205 9389588', 'npi' => '1093822884', 'email' => 'nwhitch@hotmail.com'),
    217 => array('address_1' => '200 E Jackson St', 'address_2' => '', 'city' => 'Mexico', 'state' => 'MO', 'zip' => '65265', 'phone' => '573 581-7561', 'npi' => '1912082538', 'email' => 'jacksonstreetdrug@gmail.com'),
    218 => array('address_1' => '257 Florida Ave SE', 'address_2' => '', 'city' => 'Denham Springs', 'state' => 'LA', 'zip' => '70726', 'phone' => 'usa 225 665-5186', 'npi' => '1902969520', 'email' => 'hjamesds@yahoo.com'),
    219 => array('address_1' => '2415 Ring Road', 'address_2' => '', 'city' => 'elizabethtown', 'state' => 'KY', 'zip' => '42701', 'phone' => '270 765-2157', 'npi' => '1447330675', 'email' => 'jeffsrx@comcast.net'),
    220 => array('address_1' => '194 Turkeysag Trl, Ste B', 'address_2' => '', 'city' => 'Palmyra', 'state' => 'VA', 'zip' => '22963', 'phone' => '434 589-7902', 'npi' => '1871731448', 'email' => 'dwilliams@jeffersondrug.com'),
    221 => array('address_1' => '455 Broadway', 'address_2' => '', 'city' => 'Bayonne', 'state' => 'NJ', 'zip' => '07002', 'phone' => '201 3391992', 'npi' => '1639102940', 'email' => 'michaeljds@hotmail.com'),
    222 => array('address_1' => '3110 Promenade Blvd', 'address_2' => '', 'city' => 'Fair Lawn', 'state' => 'NJ', 'zip' => '07410', 'phone' => '201 590-2884', 'npi' => '1396126413', 'email' => 'compounding@jiffyrx.com'),
    223 => array('address_1' => '29148 S Montpelier Rd', 'address_2' => '', 'city' => 'Albany', 'state' => 'LA', 'zip' => '70711', 'phone' => 'usa 225 567-1921', 'npi' => '1912178294', 'email' => 'info@genericstogo.com'),
    224 => array('address_1' => '1101 Poplar St', 'address_2' => '', 'city' => 'Terre Haute', 'state' => 'IN', 'zip' => '47807', 'phone' => '812 235-7373', 'npi' => '1275637225', 'email' => 'golden.becky@gmail.com'),
    225 => array('address_1' => '12085 Somerset Ave', 'address_2' => '', 'city' => 'Princess Anne', 'state' => 'MD', 'zip' => '21853', 'phone' => '410 651-3980', 'npi' => '1316993389', 'email' => 'karemorepharmacy@hotmail.com'),
    226 => array('address_1' => '95 Mahalani St #10', 'address_2' => '', 'city' => 'Wailuku', 'state' => 'HI', 'zip' => '96793', 'phone' => '808 276-0198', 'npi' => '1861867558', 'email' => 'cory.lehano@gmail.com'),
    227 => array('address_1' => '2 W.Main', 'address_2' => '', 'city' => 'Herington', 'state' => 'KS', 'zip' => '67449', 'phone' => '785 258-3703', 'npi' => '7386793958', 'email' => 'kayspharmacy@gmail.com'),
    228 => array('address_1' => '320 S Main St', 'address_2' => '', 'city' => 'Marion', 'state' => 'KY', 'zip' => '42064', 'phone' => '270 967-9007', 'npi' => '1023398443', 'email' => 'brad@kbpharmacy.com'),
    229 => array('address_1' => '150 Main Ave W', 'address_2' => '', 'city' => 'Winsted', 'state' => 'MN', 'zip' => '55395', 'phone' => 'usa 612 791-6687', 'npi' => '1114071800', 'email' => 'deberah-keaveny@gmail.com'),
    230 => array('address_1' => '4852 Route 81', 'address_2' => '', 'city' => 'Greenville', 'state' => 'NY', 'zip' => '12083', 'phone' => '518 966-4800', 'npi' => '1891063012', 'email' => 'marty@kellyspharmacyinc.com'),
    231 => array('address_1' => '34 Hope Plz', 'address_2' => '', 'city' => 'West Coxsackie', 'state' => 'NY', 'zip' => '12192', 'phone' => '518 731-4800', 'npi' => '1902215213', 'email' => 'marty@kellyspharmacyinc.com'),
    232 => array('address_1' => '641 N Dupont Blvd', 'address_2' => '', 'city' => 'Milford', 'state' => 'DE', 'zip' => '19963', 'phone' => '302 4916886', 'npi' => '1285088633', 'email' => 'kentpharmacymilford@gmail.com'),
    233 => array('address_1' => '26500 Agoura Rd, Ste 111', 'address_2' => '', 'city' => 'Calabasas', 'state' => 'CA', 'zip' => '91302', 'phone' => '818 880-8816', 'npi' => '1063925493', 'email' => 'youlzik@gmail.com'),
    234 => array('address_1' => '31201 US HWY 19N, STE 1', 'address_2' => '', 'city' => 'Palm Harbor', 'state' => 'FL', 'zip' => '34684', 'phone' => '727 772-6868', 'npi' => '1740525708', 'email' => 'kingpharmacy@live.com'),
    235 => array('address_1' => '900 Morton Blvd', 'address_2' => '', 'city' => 'Hazard', 'state' => 'Ky', 'zip' => '41701', 'phone' => 'usa 606 341-0618', 'npi' => '1003858812', 'email' => 'scott.king@king-pharmacy.com'),
    236 => array('address_1' => '111 W. Kingston Springs Road', 'address_2' => '', 'city' => 'Kingston Springs', 'state' => 'TN', 'zip' => '37082', 'phone' => '615 952-3690', 'npi' => '1629177555', 'email' => 'sshep2@comcast.net'),
    237 => array('address_1' => '191 Glades Rd.', 'address_2' => '', 'city' => 'Berea', 'state' => 'KY', 'zip' => '40403', 'phone' => '859 986-0500', 'npi' => '1538236898', 'email' => 'eknightj@gmail.com'),
    238 => array('address_1' => '1411 W American Blvd', 'address_2' => '', 'city' => 'Muleshoe', 'state' => 'TX', 'zip' => '79347', 'phone' => 'usa 806 272-7511', 'npi' => '1215229844', 'email' => 'kkmuleshoe@gmail.com'),
    239 => array('address_1' => '1033 Andre St', 'address_2' => '', 'city' => 'New Iberia', 'state' => 'LA', 'zip' => '70563', 'phone' => '337 365-1411', 'npi' => '1518054709', 'email' => 'rahulpatel@lmpharmacy.com'),
    240 => array('address_1' => '526 Hwy 52 Bypass W.', 'address_2' => '', 'city' => 'Lafayette', 'state' => 'TN', 'zip' => '37083', 'phone' => '615 666-4444', 'npi' => '1962914234', 'email' => 'lafayettepharmacy@gmail.com'),
    241 => array('address_1' => '1110 Commerce Dr, Ste 110', 'address_2' => '', 'city' => 'Greensboro', 'state' => 'GA', 'zip' => '30642', 'phone' => 'usa 770 655-7206', 'npi' => '1336323567', 'email' => 'mypharmacist@lakecountrypharmacy.com'),
    242 => array('address_1' => '2431 Lakeway Dr', 'address_2' => '', 'city' => 'Russell Springs', 'state' => 'KY', 'zip' => '42642', 'phone' => '270 858-6400', 'npi' => '1912217589', 'email' => 'jonathan.grider@uky.edu'),
    243 => array('address_1' => '221 Latitude Lane, Ste 109', 'address_2' => '', 'city' => 'Lake Wylie', 'state' => 'SC', 'zip' => '29710', 'phone' => '803 831-2044', 'npi' => '1336424167', 'email' => 'larry@lakewylierx.com'),
    244 => array('address_1' => '42 W Main St', 'address_2' => '', 'city' => 'Lakeland', 'state' => 'Ga', 'zip' => '31635', 'phone' => 'usa 229 482-3677', 'npi' => '1811924756', 'email' => 'lakelanddrug.ga@gmail.com'),
    245 => array('address_1' => '1550 S. Pioneer Way', 'address_2' => 'STE 105', 'city' => 'Moses Lake', 'state' => 'WA', 'zip' => '98837', 'phone' => '509 765-8891', 'npi' => '1447611280', 'email' => 'laketownpharmacy@gmail.com'),
    246 => array('address_1' => '516 Monument Square', 'address_2' => '', 'city' => 'Racine', 'state' => 'WI', 'zip' => '53403', 'phone' => 'usa 262 497-0521', 'npi' => '1154325199', 'email' => 'pete@lakeviewpharmacy.com'),
    247 => array('address_1' => '3500 Ranch Rd 620 S, Ste A-100', 'address_2' => '', 'city' => 'Bee Cave', 'state' => 'TX', 'zip' => '78738', 'phone' => '512 502-5161', 'npi' => '1275021818', 'email' => 'achoezirim@gmail.com'),
    248 => array('address_1' => '5015 Main St', 'address_2' => '', 'city' => 'Stephens City', 'state' => 'VA', 'zip' => '22655', 'phone' => '540 869-1660', 'npi' => '1013433242', 'email' => 'afields20@hotmail.com'),
    249 => array('address_1' => '3507 Jaime Zapata Memorial HWY', 'address_2' => 'SUITE 4', 'city' => 'Laredo', 'state' => 'TX', 'zip' => '78043', 'phone' => 'usa 956 729-9993', 'npi' => '1972807014', 'email' => 'kelleyrwalters@gmail.com'),
    250 => array('address_1' => '5540 N Farmer Branch Rd', 'address_2' => '', 'city' => 'Ozark', 'state' => 'MO', 'zip' => '65721', 'phone' => 'usa 417 299-2859', 'npi' => '1285059089', 'email' => 'nathan@lawrencedrug.com'),
    251 => array('address_1' => '1156 George Washington Hwy N', 'address_2' => '', 'city' => 'Chesapeake', 'state' => 'VA', 'zip' => '23323', 'phone' => '757 487-3458', 'npi' => '1740388784', 'email' => 'lawrencepharmacy1@yahoo.com'),
    252 => array('address_1' => '508 N Main St', 'address_2' => '', 'city' => 'Richfield', 'state' => 'UT', 'zip' => '84701', 'phone' => '435 896-6000', 'npi' => '1053689158', 'email' => 'info@lennysrichfieldpharmacy.com'),
    253 => array('address_1' => '6715 Shallowford Rd', 'address_2' => '', 'city' => 'Lewisville', 'state' => 'NC', 'zip' => '27023', 'phone' => '336 9469220', 'npi' => '1710084652', 'email' => 'keithvance@lewisvilledrug.com'),
    254 => array('address_1' => '603 Main Street', 'address_2' => '', 'city' => 'Emlenton', 'state' => 'PA', 'zip' => '16303', 'phone' => 'usa 724 867-2400', 'npi' => '1508810417', 'email' => 'linmasdrugs@embarqmail.com'),
    255 => array('address_1' => '60 A St NW', 'address_2' => '', 'city' => 'Linton', 'state' => 'IN', 'zip' => '47441', 'phone' => '812 381-0482', 'npi' => '1528016136', 'email' => 'Jeff@LintonRx.com'),
    256 => array('address_1' => '1601 South Congress Ave', 'address_2' => '', 'city' => 'Delray Beach', 'state' => 'FL', 'zip' => '33445', 'phone' => '561 272-3059', 'npi' => '1609845502', 'email' => 'lintonsqpharmacy@bellsouth.net'),
    257 => array('address_1' => '1722 Utica Ave', 'address_2' => '', 'city' => 'Brooklyn', 'state' => 'NY', 'zip' => '11234', 'phone' => '718 9681600', 'npi' => '1326564584', 'email' => 'livewellutica@gmail.com'),
    258 => array('address_1' => '91 E Mount Pleasant Ave', 'address_2' => '', 'city' => 'Livingston', 'state' => 'NJ', 'zip' => '07039', 'phone' => '973 579-1200', 'npi' => '1699010785', 'email' => 'livingstonRX1@gmail.com'),
    259 => array('address_1' => '8315 Sheldon Rd', 'address_2' => '', 'city' => 'Tampa', 'state' => 'FL', 'zip' => '33615', 'phone' => '813 886-2800', 'npi' => '1659767515', 'email' => 'logospaharmacy@verizon.net'),
    260 => array('address_1' => '170 W Park Ave', 'address_2' => '', 'city' => 'Long Beach', 'state' => 'NY', 'zip' => '11561', 'phone' => '516 431-0611', 'npi' => '1548354657', 'email' => 'lbcrx@nyrph.com'),
    261 => array('address_1' => '6216 Jericho Turnpike', 'address_2' => '', 'city' => 'Commack', 'state' => 'NY', 'zip' => '11725', 'phone' => '631 486-9172', 'npi' => '1437661501', 'email' => 'info@longislandapothecary.com'),
    262 => array('address_1' => '785 Chickamauga Ave', 'address_2' => '', 'city' => 'Rossville', 'state' => 'GA', 'zip' => '30741', 'phone' => '706 866-1220', 'npi' => '1235668328', 'email' => 'longleyspharmacy@gmail.com'),
    263 => array('address_1' => '750 Hartness Road', 'address_2' => '', 'city' => 'Statesville', 'state' => 'NC', 'zip' => '28677', 'phone' => 'usa 704 873-2247', 'npi' => '1881815181', 'email' => 'fred@lowrydrug.com'),
    264 => array('address_1' => '101 S Main St', 'address_2' => '', 'city' => 'Hailey', 'state' => 'ID', 'zip' => '83333', 'phone' => '208 7884970', 'npi' => '1134373376', 'email' => 'analia@lukespharmacy.com'),
    265 => array('address_1' => '227 W National Ave', 'address_2' => '', 'city' => 'Brazil', 'state' => 'IN', 'zip' => '47834', 'phone' => '812 446-2381', 'npi' => '1922160605', 'email' => 'ldhostetler@frontier.com'),
    266 => array('address_1' => '115 W Front St', 'address_2' => '', 'city' => 'Lonoke', 'state' => 'AR', 'zip' => '72086', 'phone' => '501 676-2247', 'npi' => '1487764510', 'email' => 'rickpenn@sbcglobal.net'),
    267 => array('address_1' => '643 Edgemoor Rd', 'address_2' => '', 'city' => 'Powell', 'state' => 'TN', 'zip' => '37849', 'phone' => 'usa 865 945-3333', 'npi' => '1982621959', 'email' => 'jon@macspharmacy.com'),
    268 => array('address_1' => '140 S. Main St.', 'address_2' => '', 'city' => 'Monticello', 'state' => 'UT', 'zip' => '84535', 'phone' => '435 5872302', 'npi' => '1215144308', 'email' => 'mymsd@icloud.com'),
    269 => array('address_1' => '2117 Boston Ave', 'address_2' => '', 'city' => 'Bridgeport', 'state' => 'CT', 'zip' => '06610', 'phone' => 'usa 914 843-1881', 'npi' => '1245560853', 'email' => 'jean@mymspax.com'),
    270 => array('address_1' => '1581 N Main St', 'address_2' => '', 'city' => 'Marion', 'state' => 'VA', 'zip' => '24354', 'phone' => '276 7837284', 'npi' => '1891892634', 'email' => 'mfpharmacy@yahoo.com'),
    271 => array('address_1' => '1612 Market Street', 'address_2' => '', 'city' => 'Wilmington', 'state' => 'NC', 'zip' => '28401', 'phone' => 'usa  910 736-0845', 'npi' => '1992817902', 'email' => 'linkrx@aol.com'),
    272 => array('address_1' => '1621 Charlestown Road', 'address_2' => '', 'city' => 'New Albany', 'state' => 'IN', 'zip' => '47150', 'phone' => '812 944-3612', 'npi' => '1922036300', 'email' => 'mathesrx@aol.com'),
    273 => array('address_1' => '260 Boggs Ln', 'address_2' => '', 'city' => 'Richmond', 'state' => 'KY', 'zip' => '40475', 'phone' => '859 623-4216', 'npi' => '1730548728', 'email' => 'mccayspharmacy@gmail.com'),
    274 => array('address_1' => '1503 W Front St', 'address_2' => '', 'city' => 'Goldthwaite', 'state' => 'TX', 'zip' => '76844', 'phone' => '325 6482484', 'npi' => '1043313174 ', 'email' => 'blake@markethubco.com'),
    275 => array('address_1' => '45 Stephens Branch Rd', 'address_2' => '', 'city' => 'Martin', 'state' => 'KY', 'zip' => '41649', 'phone' => '6069491349', 'npi' => '1316307580', 'email' => 'jbarnettejr@yahoo.com'),
    276 => array('address_1' => '208 Stanford Street', 'address_2' => '', 'city' => 'Lancaster', 'state' => 'KY', 'zip' => '40444', 'phone' => '8597921697', 'npi' => '1528517034', 'email' => 'jbarnettejr@yahoo.com'),
    277 => array('address_1' => '102 Prince Royal Dr, Ste 2', 'address_2' => '', 'city' => 'Berea', 'state' => 'KY', 'zip' => '40403', 'phone' => '8594981004', 'npi' => '1730546409', 'email' => 'jbarnettejr@yahoo.com'),
    278 => array('address_1' => '114 Fairfield Hill Rd', 'address_2' => '', 'city' => 'Bloomfield', 'state' => 'KY', 'zip' => '40008', 'phone' => '502 2528242', 'npi' => '1952712465', 'email' => 'medicarerx3@gmail.com'),
    279 => array('address_1' => '202 West Stephen Foster Ave', 'address_2' => '', 'city' => 'Bardstown', 'state' => 'KY', 'zip' => '40004', 'phone' => '502 3486623', 'npi' => '1407873235', 'email' => 'medicarx@gmail.com'),
    280 => array('address_1' => '100 W Depot St', 'address_2' => '', 'city' => 'Springfield', 'state' => 'KY', 'zip' => '40069', 'phone' => '859 4817100', 'npi' => '1780139949', 'email' => 'medicarx@gmail.com'),
    281 => array('address_1' => '1220 N Race St', 'address_2' => '', 'city' => 'Glasgow', 'state' => 'KY', 'zip' => '42141', 'phone' => '270 6517030', 'npi' => '1629163142', 'email' => 'vannhealthcare@glasgow-ky.com'),
    282 => array('address_1' => '949 Sixth Ave', 'address_2' => '', 'city' => 'Huntington', 'state' => 'WV', 'zip' => '25701', 'phone' => 'usa 304 529-7141', 'npi' => '1477599736', 'email' => 'medrx@aol.com'),
    283 => array('address_1' => '4417 Bee Ridge Road', 'address_2' => '', 'city' => 'Sarosota', 'state' => 'FL', 'zip' => '34233', 'phone' => '941 387-5096', 'npi' => '1508090572', 'email' => 'raumi_joseph@hotmail.com'),
    284 => array('address_1' => '1740 South St, Ste 501', 'address_2' => '', 'city' => 'Philadelphia', 'state' => 'PA', 'zip' => '19146', 'phone' => '215 496-9595', 'npi' => '1538380431', 'email' => 'jerryhf@verizon.net'),
    285 => array('address_1' => '751 E Roosevelt Blvd', 'address_2' => '', 'city' => 'Monroe', 'state' => 'NC', 'zip' => '28112', 'phone' => '704 291-7070', 'npi' => '1023173440', 'email' => 'medicap8160@gmail.com'),
    286 => array('address_1' => '956 J Clyde Morris Blvd', 'address_2' => '', 'city' => 'Newport News', 'state' => 'VA', 'zip' => '23601', 'phone' => '757 599-9643', 'npi' => '1588866206', 'email' => '8400@medicap.com'),
    287 => array('address_1' => '6675 Falls of Neuse Road ste 101', 'address_2' => '', 'city' => 'Raleigh', 'state' => 'NC', 'zip' => '27615', 'phone' => 'usa 919 676-6161', 'npi' => '110422078', 'email' => 'kbarbrey@gmail.com'),
    288 => array('address_1' => '10 S Main St.', 'address_2' => '', 'city' => 'Liberty', 'state' => 'IN', 'zip' => '47353', 'phone' => '765 223-2121', 'npi' => '1609205681', 'email' => 'drokosz@medicenterpharmacy.com'),
    289 => array('address_1' => '100 N Foote St', 'address_2' => '', 'city' => 'Cambridge City', 'state' => 'IN', 'zip' => '47327', 'phone' => '765 334-8331', 'npi' => '1447438239', 'email' => 'knewton@medicenterpharmacy.com'),
    290 => array('address_1' => '1475 IN-44', 'address_2' => '', 'city' => 'Connersville', 'state' => 'IN', 'zip' => '47331', 'phone' => '765 827-1934', 'npi' => '1750753471', 'email' => 'lgetchius@medicenterpharmacy.com'),
    291 => array('address_1' => '204 S. Washington Street', 'address_2' => '', 'city' => 'DuQuoin', 'state' => 'IL', 'zip' => '62832', 'phone' => '618 625-8400', 'npi' => '1285081224', 'email' => 'bgalli@medicenterpharmacy.com'),
    292 => array('address_1' => '818 US 31W Byp', 'address_2' => '', 'city' => 'Bowling Green', 'state' => 'KY', 'zip' => '42101', 'phone' => '270 843-3203', 'npi' => '1730325127', 'email' => 'rxpharmgrl@gmail.com'),
    293 => array('address_1' => '900 Hustonville Rd', 'address_2' => '', 'city' => 'Danville', 'state' => 'KY', 'zip' => '40422', 'phone' => '859 238-0002', 'npi' => '1306918198', 'email' => '1503@medicineshoppe.com'),
    294 => array('address_1' => '2339 Broadway St', 'address_2' => '', 'city' => 'Mount Vernon', 'state' => 'IL', 'zip' => '62864', 'phone' => '618 2428776', 'npi' => '1417923014', 'email' => 'eblackrph@gmail.com'),
    295 => array('address_1' => '901 E Edwardsville Rd', 'address_2' => '', 'city' => 'Wood River', 'state' => 'IL', 'zip' => '62095', 'phone' => 'usa 314 276-5438', 'npi' => '1396759445', 'email' => '0062@medicineshoppe.com'),
    296 => array('address_1' => '1717 Union St STE 2', 'address_2' => '', 'city' => 'Opelousas', 'state' => 'LA', 'zip' => '70570', 'phone' => '337 948-7703', 'npi' => '1669584652', 'email' => '1198@medicineshoppe.com'),
    297 => array('address_1' => '2002 Medical Parkway, Ste 170', 'address_2' => '', 'city' => 'Annapolis', 'state' => 'MD', 'zip' => '21401', 'phone' => '410 573-6900', 'npi' => '1164828679', 'email' => 'medpark@professionalpharmacygroup.com'),
    298 => array('address_1' => '400 12th St N.W', 'address_2' => '', 'city' => 'Canton', 'state' => 'OH', 'zip' => '44703', 'phone' => 'usa 234 410-3366', 'npi' => '1407202914', 'email' => 'medtimerx@gmail.com'),
    299 => array('address_1' => '115 E Stockton St.', 'address_2' => '', 'city' => 'Edmonton', 'state' => 'KY', 'zip' => '42129', 'phone' => '270 432-3051', 'npi' => '1972503126', 'email' => 'jamey@metcalfedrugs.com'),
    300 => array('address_1' => '274 N Broad St', 'address_2' => '', 'city' => 'Carlinville', 'state' => 'IL', 'zip' => '62626', 'phone' => 'usa 217 825-3639', 'npi' => '1306953013', 'email' => 'michelle@michellespharmacy.com'),
    301 => array('address_1' => '753 Arthur Godfrey Road', 'address_2' => '', 'city' => 'Miami Beach', 'state' => 'FL', 'zip' => '33140', 'phone' => '305 531-5816', 'npi' => '1699847137', 'email' => 'ranrph@aol.com'),
    302 => array('address_1' => '500 Main Street', 'address_2' => '', 'city' => 'Beaver Dam', 'state' => 'KY', 'zip' => '42320', 'phone' => 'usa 270 775-2771', 'npi' => '1932442266', 'email' => 'john@midtownpharmacyexpress.com'),
    303 => array('address_1' => '3370 Tamiami Trl', 'address_2' => '', 'city' => 'Port Charlotte', 'state' => 'FL', 'zip' => '33952', 'phone' => '941 447-0230', 'npi' => '1639455447', 'email' => 'ykadibhai7@gmail.com'),
    304 => array('address_1' => '740 Main St', 'address_2' => '', 'city' => 'Tracy City', 'state' => 'TN', 'zip' => '37387', 'phone' => '931 592-9190', 'npi' => '1730259722', 'email' => 'mikeyarworth@yahoo.com'),
    305 => array('address_1' => '207 River St', 'address_2' => '', 'city' => 'Superior', 'state' => 'MT', 'zip' => '59872', 'phone' => '406 822-4681', 'npi' => '1871695213', 'email' => 'info@mineraldrug.com'),
    306 => array('address_1' => '976 S George St', 'address_2' => '', 'city' => 'York', 'state' => 'PA', 'zip' => '17403', 'phone' => '717 848-2312', 'npi' => '1255329231', 'email' => 'dshultz@minnichspharmacy.com'),
    307 => array('address_1' => '270 Copperfield Blvd NE, Ste 101', 'address_2' => '', 'city' => 'Concord', 'state' => 'NC', 'zip' => '28025', 'phone' => '704 784-9613', 'npi' => '1043317027', 'email' => 'brandi@moosepharmacy.com'),
    308 => array('address_1' => '1113 N Main St', 'address_2' => '', 'city' => 'Kannapolis', 'state' => 'NC', 'zip' => '28081', 'phone' => '704 932-9111', 'npi' => '1194862177', 'email' => 'james@moosepharmacy.com'),
    309 => array('address_1' => '8374 W Franklin St', 'address_2' => '', 'city' => 'Mount Pleasant', 'state' => 'NC', 'zip' => '28124', 'phone' => 'usa 704 995-9928', 'npi' => '1932219888', 'email' => 'whit@moosepharmacy.com'),
    310 => array('address_1' => '1408 W Innes St', 'address_2' => '', 'city' => 'Salisbury', 'state' => 'NC', 'zip' => '28144', 'phone' => '704 6366340', 'npi' => '1669626164', 'email' => 'kyle@moosepharmacy.com'),
    311 => array('address_1' => '1704 W Manchester Ave, Ste 100', 'address_2' => '', 'city' => 'Los Angeles', 'state' => 'CA', 'zip' => '90047', 'phone' => '323 753-1333', 'npi' => '1982748430', 'email' => 'remypharm@gmail.com'),
    312 => array('address_1' => '117 Beach Rd', 'address_2' => '', 'city' => 'Vineyard Haven', 'state' => 'MA', 'zip' => '02568', 'phone' => '508 693-7979', 'npi' => '1699847210', 'email' => 'bubrx@aol.com'),
    313 => array('address_1' => '412 Elden St', 'address_2' => '', 'city' => 'Herndon', 'state' => 'VA', 'zip' => '20170', 'phone' => '703 657-0303', 'npi' => '1629426648', 'email' => 'oshoheiber@mydrsrx.com'),
    314 => array('address_1' => '3000 Alvey Park Dr West', 'address_2' => '', 'city' => 'Owensboro', 'state' => 'KY', 'zip' => '42303', 'phone' => '270 926-4080', 'npi' => '1659435618', 'email' => 'nations54@nationsmedicines.com'),
    315 => array('address_1' => '11134 Kingston Pike', 'address_2' => '', 'city' => 'Knoxville', 'state' => 'TN', 'zip' => '37934', 'phone' => '703 371-8131', 'npi' => '1548632169', 'email' => 'jim@nationalrx.com'),
    // 316 => array('address_1' => '140B Estate St. George', 'address_2' => '', 'city' => 'Frederiksted', 'state' => 'USVI', 'zip' => '840', 'phone' => '404 3177350', 'npi' => '1134435951', 'email' => 'Not provided'),
    317 => array('address_1' => '698 Amsterdam Ave', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10025', 'phone' => '212 865-9700', 'npi' => '1033133558', 'email' => 'newamsterdamdrugmart@gmail.com'),
    318 => array('address_1' => '15 BAKER RD', 'address_2' => 'SUITE 2', 'city' => 'NEWNAN', 'state' => 'GA', 'zip' => '30265', 'phone' => '770 7706836771', 'npi' => '1871832592', 'email' => 'newnanpharmacy@numail.org'),
    319 => array('address_1' => '220 E Elkhorn St', 'address_2' => '', 'city' => 'Elkhorn City', 'state' => 'KY', 'zip' => '41522', 'phone' => '606 7545076', 'npi' => '1811987696', 'email' => 'jbarnettejr@yahoo.com'),
    320 => array('address_1' => '12878 US Highway 301', 'address_2' => '', 'city' => 'Dade City', 'state' => 'FL', 'zip' => '33525', 'phone' => '352 4375985', 'npi' => '1053729418', 'email' => 'nimohrx@gmail.com'),
    321 => array('address_1' => '12 St. Paul Drive. Ste 105', 'address_2' => '', 'city' => 'Chambersburg', 'state' => 'PA', 'zip' => '17201', 'phone' => '717 217-2790', 'npi' => '1699708768', 'email' => 'roles@norlandrx.com'),
    322 => array('address_1' => '3058 Campbellsville RD', 'address_2' => '', 'city' => 'Columbia', 'state' => 'KY', 'zip' => '42728', 'phone' => 'usa 270 380-1230', 'npi' => '1417364662', 'email' => 'northcenturypharmacy@duo-county.com'),
    323 => array('address_1' => '246 9th Ave', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10001', 'phone' => '212 462-2525', 'npi' => '1366771099', 'email' => 'Rob@MyNucare.com'),
    324 => array('address_1' => '216 S State St', 'address_2' => '', 'city' => 'Belvidere', 'state' => 'IL', 'zip' => '61008', 'phone' => '815- 544- 3433', 'npi' => '1194762674', 'email' => 'parag@doserx.com'),
    325 => array('address_1' => '3535 Galt Ocean Dr, Ste 1', 'address_2' => '', 'city' => 'Fort Lauderdale', 'state' => 'FL', 'zip' => '33308', 'phone' => '954 329-2691', 'npi' => '1831625136', 'email' => 'info@oceanchemist.net'),
    326 => array('address_1' => '1050 Rutledge Pike', 'address_2' => '', 'city' => 'Blaine', 'state' => 'TN', 'zip' => '37709', 'phone' => '865 776-6482', 'npi' => '1932206877', 'email' => 'okiesrx@bellsouth.net'),
    327 => array('address_1' => '100 Chapel Dr, Ste A', 'address_2' => '', 'city' => 'Monett', 'state' => 'MO', 'zip' => '65708', 'phone' => '417 2095799', 'npi' => '1164976999', 'email' => 'shanebecker@oldtownpharmacy.com'),
    328 => array('address_1' => '4854 Longhill Rd, Ste 5', 'address_2' => '', 'city' => 'Williamsburg', 'state' => 'VA', 'zip' => '23188', 'phone' => '757 220-8764', 'npi' => '1316990203', 'email' => 'oldetownerx@aol.com'),
    329 => array('address_1' => '130 W. Main St.', 'address_2' => '', 'city' => 'Orange', 'state' => 'VA', 'zip' => '22960', 'phone' => '540 6615006', 'npi' => '1801993738', 'email' => 's_hoffman9614@yahoo.com'),
    330 => array('address_1' => '133-40 79th Street, Unit I', 'address_2' => '', 'city' => 'Howard Beach', 'state' => 'NY', 'zip' => '11414', 'phone' => '718 487-3930', 'npi' => '1376052522', 'email' => 'organicrxjuice@gmail.com'),
    331 => array('address_1' => '2515 Castroville Rd,', 'address_2' => '', 'city' => 'San Antonio', 'state' => 'TX', 'zip' => '78237', 'phone' => '210 432-2361', 'npi' => '1508180951', 'email' => 'aortiz@ortizpharmacy.com'),
    332 => array('address_1' => '136 Jessica Lane Suite-E', 'address_2' => '', 'city' => 'Olive Hill', 'state' => 'KY', 'zip' => '41164', 'phone' => '859-498-1004', 'npi' => '1093895435', 'email' => 'jbarnettejr@yahoo.com'),
    333 => array('address_1' => '720 W Byers Ave', 'address_2' => '', 'city' => 'Owensboro', 'state' => 'KY', 'zip' => '42303', 'phone' => '270 683-2400', 'npi' => '1861857617', 'email' => 'owensborofamilypharmacy@gmail.com'),
    334 => array('address_1' => '2711 E. Atlantic Blvd.', 'address_2' => '', 'city' => 'Pompano Beach', 'state' => 'FL', 'zip' => '33062', 'phone' => '954 532-7940', 'npi' => '1477823474', 'email' => 'pharmacy@palmrxs.com'),
    335 => array('address_1' => '800 Magnolia Ave, Ste 116', 'address_2' => '', 'city' => 'Corona', 'state' => 'CA', 'zip' => '92879', 'phone' => '951 737-3511', 'npi' => '1306099395', 'email' => 'drsmali34@gmail.com'),
    336 => array('address_1' => '1564 E Lancaster Ave', 'address_2' => '', 'city' => 'Paoli', 'state' => 'PA', 'zip' => '19301', 'phone' => '610 644-3880', 'npi' => '1003867045', 'email' => 'pharmacist@paolipharmacy.com'),
    337 => array('address_1' => '707 Lamar Ave, Ste B', 'address_2' => '', 'city' => 'Paris', 'state' => 'TX', 'zip' => '75460', 'phone' => '903 785-4208', 'npi' => '1225176837', 'email' => 'leslie@paris-apothecary.com'),
    338 => array('address_1' => '220 Park Ave', 'address_2' => '', 'city' => 'Chambersburg', 'state' => 'PA', 'zip' => '17201', 'phone' => '717 264-7312', 'npi' => '1134124274', 'email' => 'sjhopple@comcast.net'),
    339 => array('address_1' => '1388 Hertel Ave', 'address_2' => '', 'city' => 'Buffalo', 'state' => 'NY', 'zip' => '14216', 'phone' => '716 725-0887', 'npi' => '1023319258', 'email' => 'barbrx@parkerpharmacy.com'),
    340 => array('address_1' => '2200 Ferry St', 'address_2' => '', 'city' => 'Lafayette', 'state' => 'IN', 'zip' => '47904', 'phone' => '765 447-1000', 'npi' => '1063465904', 'email' => 'parksidepharmacyinc@gmail.com'),
    341 => array('address_1' => '5571 The Square', 'address_2' => '', 'city' => 'Crozet', 'state' => 'VA', 'zip' => '22932', 'phone' => '434 8236337', 'npi' => '1952346322', 'email' => 'paul@parkwaypharm.net'),
    342 => array('address_1' => '30 N Bryn Mawr Ave', 'address_2' => '', 'city' => 'Bryn Mawr', 'state' => 'PA', 'zip' => '19010', 'phone' => '610 525-6664', 'npi' => '1336169895', 'email' => 'mdcrx@aol.com'),
    343 => array('address_1' => '53 E 34th St,', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10016', 'phone' => '212 683-3838', 'npi' => '1265440937', 'email' => 'pastpharm@aol.com'),
    344 => array('address_1' => '2047 West St', 'address_2' => '', 'city' => 'Annapolis', 'state' => 'MD', 'zip' => '21401', 'phone' => '443 716-6038', 'npi' => '1134587371', 'email' => 'vpatel6239@gmail.com'),
    345 => array('address_1' => '143 Thomas Green Blvd', 'address_2' => '', 'city' => 'Clemson', 'state' => 'SC', 'zip' => '29631', 'phone' => 'usa 864 999-2900', 'npi' => '1881117695', 'email' => 'kpsmith@patricksquare-rx.com'),
    346 => array('address_1' => '222 Oakridge Cmns', 'address_2' => '', 'city' => 'South Salem', 'state' => 'NY', 'zip' => '10590', 'phone' => '914 533-5679', 'npi' => '1669577433', 'email' => 'paroldan@aol.com'),
    347 => array('address_1' => '4901 Gary Ave', 'address_2' => '', 'city' => 'Fairfield', 'state' => 'AL', 'zip' => '35064', 'phone' => '205 7854343', 'npi' => '1215084561', 'email' => 'boydennisjr@mypaylessdrugs.com'),
    348 => array('address_1' => '285 W Turn Table Rd', 'address_2' => '', 'city' => 'Sparta', 'state' => 'TN', 'zip' => '38583', 'phone' => '931 8363187', 'npi' => '1821192881', 'email' => 'jenutpharmd@hotmail.com'),
    349 => array('address_1' => '4100 Eldorado Parkway, Ste 200', 'address_2' => '', 'city' => 'McKinney', 'state' => 'TX', 'zip' => '75070', 'phone' => '469 301-7621', 'npi' => '1568985034', 'email' => 'pharma1mckinney@gmail.com'),
    350 => array('address_1' => '86-032 Farrington Hwy, Ste 100', 'address_2' => '', 'city' => 'Waianae', 'state' => 'HI', 'zip' => '96792', 'phone' => '808 696-0005', 'npi' => '1740504109', 'email' => 'sradpay@pharmacarehawaii.com'),
    351 => array('address_1' => '934 Oldham St', 'address_2' => 'Suite 100 A', 'city' => 'Nolensville', 'state' => 'TN', 'zip' => '37135', 'phone' => 'usa 615 283-8035', 'npi' => '1427304617', 'email' => 'tim@pctn.net'),
    352 => array('address_1' => '2323 N Marr Rd', 'address_2' => '', 'city' => 'Columbus', 'state' => 'IN', 'zip' => '47203', 'phone' => '812 376-9650', 'npi' => '1134226970', 'email' => 'info@pharmacycaresolutions.com'),
    353 => array('address_1' => '508 N Main St', 'address_2' => '', 'city' => 'Carrollton', 'state' => 'IL', 'zip' => '62016', 'phone' => '217 942-3427', 'npi' => '1568557593', 'email' => 'bberry@rxplusinc.com'),
    354 => array('address_1' => '305 Mount Cross Rd', 'address_2' => '', 'city' => 'Danville', 'state' => 'VA', 'zip' => '24540', 'phone' => '434 791-3784', 'npi' => '1508134628', 'email' => 'vancekiser@yahoo.com'),
    355 => array('address_1' => '634 Pine Ridge Dr, Ste A', 'address_2' => '', 'city' => 'West Columbia', 'state' => 'SC', 'zip' => '29172', 'phone' => '803 955-3404', 'npi' => '1750527834', 'email' => 'rphmchugh@gmail.com'),
    356 => array('address_1' => '776 Daniel Ellis Dr, Ste 2C', 'address_2' => '', 'city' => 'Charleston', 'state' => 'SC', 'zip' => '29412', 'phone' => '843 795-2660', 'npi' => '1255416616', 'email' => 'plantationpharmacy@yahoo.com'),
    357 => array('address_1' => '531 Wappoo Rd', 'address_2' => '', 'city' => 'Charleston', 'state' => 'SC', 'zip' => '29407', 'phone' => '843 795-9554', 'npi' => '1154588101', 'email' => 'plantationpharmacy@yahoo.com'),
    358 => array('address_1' => '731 N Laurel Rd', 'address_2' => '', 'city' => 'London', 'state' => 'KY', 'zip' => '40741', 'phone' => '606 657-5245', 'npi' => '1770904989', 'email' => 'plazadrugoflondon@gmail.com'),
    359 => array('address_1' => '955 Catalina Blvd, Ste 102a', 'address_2' => '', 'city' => 'San Diego', 'state' => 'CA', 'zip' => '92106', 'phone' => '619 630-2710', 'npi' => '1053646265', 'email' => 'plsi@drug.sdcoxmail.com'),
    360 => array('address_1' => '1105 Rosecrans St', 'address_2' => '', 'city' => 'San Diego', 'state' => 'CA', 'zip' => '92106', 'phone' => '619 223-7171', 'npi' => '1518072545', 'email' => 'plsi@drug.sdcoxmail.com'),
    361 => array('address_1' => '990 Pine Barren Rd, Ste 102', 'address_2' => '', 'city' => 'Pooler', 'state' => 'GA', 'zip' => '31322', 'phone' => '912 3484420', 'npi' => '1770941007', 'email' => 'poolerpharmacy@gmail.com'),
    362 => array('address_1' => '54-56 N Main St', 'address_2' => '', 'city' => 'Port Allegany', 'state' => 'PA', 'zip' => '16743', 'phone' => '814 642-2871', 'npi' => '1679614507', 'email' => 'portpharm@gmail.com'),
    363 => array('address_1' => '7966 Lovers Ln', 'address_2' => '', 'city' => 'Portage', 'state' => 'MI', 'zip' => '49002', 'phone' => '269 327-0033', 'npi' => '1992735666', 'email' => 'lcurtis@portagepharmacy.com'),
    364 => array('address_1' => '705 S Broadway St', 'address_2' => '', 'city' => 'Portland', 'state' => 'TN', 'zip' => '37148', 'phone' => '615 3235052', 'npi' => '1043612880', 'email' => 'mistinnett@aol.com'),
    365 => array('address_1' => '1024 Middle Creek Rd, Ste 1', 'address_2' => '', 'city' => 'Sevierville', 'state' => 'TN', 'zip' => '37862', 'phone' => '865 3661770', 'npi' => '1740647338', 'email' => 'snyderitaville@charter.net'),
    366 => array('address_1' => '6586 E Grant Rd', 'address_2' => '', 'city' => 'Tucson', 'state' => 'AZ', 'zip' => '85715', 'phone' => '520 886-1035', 'npi' => '1129213640', 'email' => 'rxlabloretta@gmail.com'),
    367 => array('address_1' => '813 Hospital Dr', 'address_2' => '', 'city' => 'Andrews', 'state' => 'TX', 'zip' => '79714', 'phone' => '432 523-4861', 'npi' => '1790760056', 'email' => 'prescriptionshopandrews@gmail.com'),
    368 => array('address_1' => '5101 Lee Hwy', 'address_2' => '', 'city' => 'Arlington', 'state' => 'VA', 'zip' => '22207', 'phone' => '703 552-3412', 'npi' => '1104362227', 'email' => 'jorge@prestonspharmacy.com'),
    369 => array('address_1' => '27 Heckel Rd, Ste 110', 'address_2' => '', 'city' => 'Mc Kees Rocks', 'state' => 'PA', 'zip' => '15136', 'phone' => '412 771-2149', 'npi' => '1407184807', 'email' => 'anthonybertola@primarycarepharmacysvcs.com'),
    370 => array('address_1' => '931 Main St', 'address_2' => '', 'city' => 'Pennsburg', 'state' => 'PA', 'zip' => '18073', 'phone' => '2156799700', 'npi' => '1861495772', 'email' => 'kourtneychic@verizon.net'),
    371 => array('address_1' => '920 N Charlotte St', 'address_2' => '', 'city' => 'Pottstown', 'state' => 'PA', 'zip' => '19464', 'phone' => '610 323-2115', 'npi' => '1811992142', 'email' => 'dawn@professionalpharmacy.com'),
    372 => array('address_1' => '140 Roxboro Rd', 'address_2' => '', 'city' => 'Oxford', 'state' => 'NC', 'zip' => '27565', 'phone' => '919 6938555', 'npi' => '1720044720', 'email' => 'professionalpharmacy@embarqmail.com'),
    373 => array('address_1' => '8644 Sudley Rd.', 'address_2' => '', 'city' => 'Manassas', 'state' => 'VA', 'zip' => '20110', 'phone' => '703 384-5180', 'npi' => '1801035548', 'email' => 'vinod@prosperitypharmacy.com'),
    374 => array('address_1' => '306 Lynne Pl', 'address_2' => '', 'city' => 'Chester Springs', 'state' => 'PA', 'zip' => '19425', 'phone' => '610 321-3668', 'npi' => '1356744312', 'email' => 'puremeridian@gmail.com'),
    375 => array('address_1' => '2349 S. Kihei Rd.', 'address_2' => '', 'city' => 'Kihei', 'state' => 'HI', 'zip' => '96753', 'phone' => '808-879-9924', 'npi' => '1780996579', 'email' => 'cory.lehano@gmail.com'),
    376 => array('address_1' => '3541 Randolph Rd, Ste 104', 'address_2' => '', 'city' => 'Charlotte', 'state' => 'NC', 'zip' => '28211', 'phone' => '704 3650707', 'npi' => '1083739254', 'email' => 'amye.rmp@gmail.com'),
    377 => array('address_1' => '377 Main St', 'address_2' => '', 'city' => 'Harleysville', 'state' => 'PA', 'zip' => '19438', 'phone' => '215 256-4146', 'npi' => '1497852339', 'email' => 'rannpharmacy@yahoo.com'),
    378 => array('address_1' => '18181 Old Jefferson Hwy Ste #101', 'address_2' => '', 'city' => 'Baton Rough', 'state' => 'LA', 'zip' => '70817', 'phone' => 'usa 225 276-2602', 'npi' => '1245777366', 'email' => 'info@genericstogo.com'),
    379 => array('address_1' => '790 E Main St', 'address_2' => '', 'city' => 'Hyrum', 'state' => 'UT', 'zip' => '84319', 'phone' => '435 245-3784', 'npi' => '1821182957', 'email' => 'reedscompounding@gmial.com'),
    380 => array('address_1' => '7115 3rd Ave', 'address_2' => '', 'city' => 'Brooklyn', 'state' => 'NY', 'zip' => '11209', 'phone' => '718 238-7488', 'npi' => '1508954462', 'email' => 't.k@karnaby.com'),
    381 => array('address_1' => '207 E Main St', 'address_2' => '', 'city' => 'Remington', 'state' => 'VA', 'zip' => '22734', 'phone' => '540 439-3247', 'npi' => '1588659569', 'email' => 'info@remingtondrug.com'),
    382 => array('address_1' => '189 N Plano Rd, Ste 120', 'address_2' => '', 'city' => 'Richardson', 'state' => 'TX', 'zip' => '75081', 'phone' => '972 479-9798', 'npi' => '1558812206', 'email' => 'samibahta@yahoo.com'),
    383 => array('address_1' => '486 N Hwy 25W', 'address_2' => '', 'city' => 'Williamsburg', 'state' => 'KY', 'zip' => '40769', 'phone' => '606 515-6134', 'npi' => '1073865408', 'email' => 'james.rickett@yahoo.com'),
    384 => array('address_1' => '300 Pine St', 'address_2' => '', 'city' => 'Rison', 'state' => 'AR', 'zip' => '71665', 'phone' => 'usa 870 550-5633', 'npi' => '1942645569', 'email' => 'risonpharmacy@gmail.com'),
    385 => array('address_1' => '575 Rivergate, Unit 111', 'address_2' => '', 'city' => 'Durango', 'state' => 'CO', 'zip' => '81301', 'phone' => '970 3757711', 'npi' => '1194049494', 'email' => 'lori@rivergatepharmacy.com'),
    386 => array('address_1' => '1802 N Monroe St', 'address_2' => '', 'city' => 'Spokane', 'state' => 'WA', 'zip' => '99205', 'phone' => '509 343-6252', 'npi' => '1083759856', 'email' => 'chudek@riverpointrx.com'),
    387 => array('address_1' => '1406 McGavock Pike, Ste A', 'address_2' => '', 'city' => 'Nashville', 'state' => 'TN', 'zip' => '37216', 'phone' => 'USA 615 650-4444', 'npi' => '1336429018', 'email' => 'retailrvp@gmail.com'),
    388 => array('address_1' => '19118 Alberta St', 'address_2' => '', 'city' => 'Oneida', 'state' => 'TN', 'zip' => '37841', 'phone' => '423 569-9000', 'npi' => '1164596706', 'email' => 'roarksrx@highland.net'),
    389 => array('address_1' => '54 E King St', 'address_2' => '', 'city' => 'Shippensburg', 'state' => 'PA', 'zip' => '17257', 'phone' => '717 532-5812', 'npi' => '1750317608', 'email' => 'rthenrypharmacy@aol.com'),
    390 => array('address_1' => '250 Long Hollow Pike', 'address_2' => '', 'city' => 'Goodlettsville', 'state' => 'TN', 'zip' => '37072', 'phone' => '615 859-8999', 'npi' => '1972984961', 'email' => 'communitydrug@bellsouth.net'),
    391 => array('address_1' => '9101 Mendenhall Mall Rd', 'address_2' => '', 'city' => 'Juneau', 'state' => 'AK', 'zip' => '99801', 'phone' => 'usa 907 321-5065', 'npi' => '1912012931', 'email' => 'watts@acsalaska.net'),
    392 => array('address_1' => '1215 Mamaroneck Ave', 'address_2' => '', 'city' => 'White Plains', 'state' => 'NY', 'zip' => '10605', 'phone' => '1 914 948-4818', 'npi' => '1316095318', 'email' => 'mkleinrph@aol.com'),
    393 => array('address_1' => '127 N Main St', 'address_2' => '', 'city' => 'Sylvania', 'state' => 'GA', 'zip' => '30467', 'phone' => '912 5647002', 'npi' => '1487759635', 'email' => 'rossdruginc@gmail.com'),
    394 => array('address_1' => '6010 E W T Harris Blvd', 'address_2' => '', 'city' => 'Charlotte', 'state' => 'NC', 'zip' => '28215', 'phone' => '704 537-0909', 'npi' => '1235375742', 'email' => 'ahmed@rxclinicpharmacy.com'),
    395 => array('address_1' => '8614 W. 3rd St', 'address_2' => '', 'city' => 'Los Angelas', 'state' => 'CA', 'zip' => '90048', 'phone' => '310 860-9809', 'npi' => '1114378619', 'email' => 'salpharmacyrx@gmail.com'),
    396 => array('address_1' => '195-B Sheffield Dr.', 'address_2' => '', 'city' => 'Delmont', 'state' => 'PA', 'zip' => '15626', 'phone' => '724 468-5565', 'npi' => '1386723054', 'email' => 'tjsrx@comcast.net'),
    397 => array('address_1' => '1630 E High St, Bldg 2', 'address_2' => '', 'city' => 'Pottstown', 'state' => 'PA', 'zip' => '19464', 'phone' => '484 949-8505', 'npi' => '1922555697', 'email' => 'phillipsebrell@sanatogapharmacy.com'),
    398 => array('address_1' => '4 Regency Dr', 'address_2' => '', 'city' => 'Wylie', 'state' => 'TX', 'zip' => '75098', 'phone' => '972 5352020', 'npi' => '1962838466', 'email' => 'mikesands@sandsrx.com'),
    399 => array('address_1' => '8620 S. Tamiami Trail, Suite N-P', 'address_2' => '', 'city' => 'Sarasota', 'state' => 'FL', 'zip' => '34238', 'phone' => '941 218-4090', 'npi' => '1275070716', 'email' => 'sarasotaapothecary@gmail.com'),
    400 => array('address_1' => '110 N Lime Ave', 'address_2' => '', 'city' => 'Sarasota', 'state' => 'FL', 'zip' => '34237', 'phone' => '941 444-6888', 'npi' => '1114384187', 'email' => 'sarasotadiscountpharmacy@gmail.com'),
    401 => array('address_1' => '455 O\'Connor Drive', 'address_2' => 'Suite 190', 'city' => 'San Jose', 'state' => 'CA', 'zip' => '95128', 'phone' => 'usa 408 298-6190', 'npi' => '1952442477', 'email' => 'savcorx@gmail.com'),
    402 => array('address_1' => '3479 N Broadway St', 'address_2' => '', 'city' => 'Chicago', 'state' => 'IL', 'zip' => '60657', 'phone' => '773 5250766', 'npi' => '1295861334', 'email' => 'saveritepharmacy@sbcglobal.net'),
    403 => array('address_1' => '1999 N Pennsylvania St', 'address_2' => '', 'city' => 'Denver', 'state' => 'CO', 'zip' => '80203', 'phone' => '303 974-5424', 'npi' => '1861819856', 'email' => 'dan@scalespharmacy.com'),
    404 => array('address_1' => '707 Meyer St', 'address_2' => '', 'city' => 'Sealy', 'state' => 'TX', 'zip' => '77474', 'phone' => '979 256-3045', 'npi' => '1164894507', 'email' => 'sealypharmacy@gmail.com'),
    405 => array('address_1' => '10227 Beach Dr SW', 'address_2' => '', 'city' => 'Calabash', 'state' => 'NC', 'zip' => '28467', 'phone' => '910 579-3200', 'npi' => '1629123807', 'email' => 'edthomasrx@gmail.com'),
    406 => array('address_1' => '122 Mac Dougall Drive ', 'address_2' => '', 'city' => 'West End', 'state' => 'NC', 'zip' => '27376', 'phone' => 'usa  910 673-7467', 'npi' => '1215942834', 'email' => 'barrettpharmd@gmail.com'),
    407 => array('address_1' => '10721 Chapman Hwy, Ste 6', 'address_2' => '', 'city' => 'Seymour', 'state' => 'TN', 'zip' => '37865', 'phone' => 'usa 856 604-5186', 'npi' => '1063549863', 'email' => 'seymourpharmacy@gmail.com'),
    408 => array('address_1' => '346 E Main St', 'address_2' => '', 'city' => 'Jasonville', 'state' => 'IN', 'zip' => '47438', 'phone' => '812 665-9760', 'npi' => '1477815868', 'email' => 'shakamak.pharmacy@gmail.com'),
    409 => array('address_1' => '321 Wycoff Ave', 'address_2' => '', 'city' => 'Ridgewood', 'state' => 'NY', 'zip' => '11385', 'phone' => 'usa 718 417-0280', 'npi' => '1154404507', 'email' => 'shawnpharmacy@hotmail.com'),
    410 => array('address_1' => '539 Linden St', 'address_2' => '', 'city' => 'Scranton', 'state' => 'PA', 'zip' => '18503', 'phone' => '570 342-8936', 'npi' => '1073519906', 'email' => 'sheeleysdruginc@aol.com'),
    411 => array('address_1' => '182 Frankfort Rd', 'address_2' => '', 'city' => 'Shelbyville', 'state' => 'KY', 'zip' => '40065', 'phone' => '502 437-3008', 'npi' => '1063897015', 'email' => 'jason.underwood@shelbyvillepharmacist.com'),
    412 => array('address_1' => '843 Fairview Ave', 'address_2' => '', 'city' => 'Bowling Green', 'state' => 'KY', 'zip' => '42101', 'phone' => '270 8429511', 'npi' => '1770680514', 'email' => 'mlsnodgrass@sheldonsrx.com'),
    413 => array('address_1' => '760 Campbell Ln, Ste 121', 'address_2' => '', 'city' => 'Bowling Green', 'state' => 'KY', 'zip' => '42104', 'phone' => '270 782-6337', 'npi' => '1538330063', 'email' => 'ksheldon@sheldonsrx.com'),
    414 => array('address_1' => '212 South Main Street', 'address_2' => '', 'city' => 'Franklin', 'state' => 'KY', 'zip' => '42134', 'phone' => '270 842-4515', 'npi' => '1154420271', 'email' => 'ksheldon@sheldonsrx.com'),
    415 => array('address_1' => '5575 Scottsville Road', 'address_2' => 'Suite 101', 'city' => 'Bowling Green', 'state' => 'KY', 'zip' => '42104', 'phone' => 'usa 270 715-0650', 'npi' => '1336547579', 'email' => 'ksheldon@sheldonsrx.com'),
    416 => array('address_1' => '350 S Van Buren St, Ste F', 'address_2' => '', 'city' => 'Shipshewana', 'state' => 'IN', 'zip' => '46565', 'phone' => '260 768-4433', 'npi' => '1891199295', 'email' => 'shipshewanapharmacy@yahoo.com'),
    417 => array('address_1' => '9738 S Virginia St, Ste G', 'address_2' => '', 'city' => 'Reno', 'state' => 'NV', 'zip' => '89511', 'phone' => '775 853-3500', 'npi' => '1912155094', 'email' => 'david@sierrafamilyrx.com'),
    418 => array('address_1' => '16465 Sierra Lakes Parkway Ste #110', 'address_2' => '', 'city' => 'Fontana', 'state' => 'CA', 'zip' => '92336', 'phone' => '909 574-9620', 'npi' => '1275627671', 'email' => 'sierrasanantonio@gmail.com'),
    419 => array('address_1' => '1251 W. Columbia Ave', 'address_2' => '', 'city' => 'Monticello', 'state' => 'KY', 'zip' => '42633', 'phone' => '606 343-0101', 'npi' => '1992104442', 'email' => 'silverhtpharm@outlook.com'),
    420 => array('address_1' => '1 N West End Blvd', 'address_2' => '', 'city' => 'Quakertown', 'state' => 'PA', 'zip' => '18951', 'phone' => '215 538-8800', 'npi' => '1760851265', 'email' => 'smalltownrx@gmail.com'),
    421 => array('address_1' => '25 W Main St', 'address_2' => '', 'city' => 'Maple Shade', 'state' => 'NJ', 'zip' => '08052', 'phone' => '856 779-8300', 'npi' => '1568569481', 'email' => 'smithbrothersdrugs@gmail.com'),
    422 => array('address_1' => '215 Treuhaft Blvd, Ste 1', 'address_2' => '', 'city' => 'Barbourville', 'state' => 'KY', 'zip' => '40906', 'phone' => '6062770041', 'npi' => '1386175610', 'email' => 'smithfamilypharmacy@gmail.com'),
    423 => array('address_1' => '1390 Railroad Ave', 'address_2' => '', 'city' => 'Saint Helena', 'state' => 'CA', 'zip' => '94574', 'phone' => '707 963-2794', 'npi' => '1356498406', 'email' => 'smithspharmacy@comcast.net'),
    424 => array('address_1' => '13830 Sawyer Ranch Rd, Ste 104', 'address_2' => '', 'city' => 'Dripping Springs', 'state' => 'TX', 'zip' => '78620', 'phone' => '512 382-9381', 'npi' => '1447591102', 'email' => 'jamesmonty2@gmail.com'),
    425 => array('address_1' => '102 South Broadway', 'address_2' => '', 'city' => 'Carlisle', 'state' => 'KY', 'zip' => '40311', 'phone' => '8592898501', 'npi' => '1093878613', 'email' => 'jbarnettejr@yahoo.com'),
    426 => array('address_1' => '1401 Albright Rd', 'address_2' => '', 'city' => 'Rock Hill', 'state' => 'SC', 'zip' => '29730', 'phone' => '803 366-3784', 'npi' => '1477892644', 'email' => 'dunlaptim@hotmail.com'),
    427 => array('address_1' => '414 S Main St', 'address_2' => '', 'city' => 'Moorefield', 'state' => 'WV', 'zip' => '26836', 'phone' => '304 5301044', 'npi' => '1104248269', 'email' => 'pharmacy@southforkpharmacy.com'),
    428 => array('address_1' => '4075 E First St', 'address_2' => '', 'city' => 'Blue Ridge', 'state' => 'GA', 'zip' => '30513', 'phone' => '706 6324448', 'npi' => '1831602077', 'email' => 'sdavenport1177@gmail.com'),
    429 => array('address_1' => '511 Memorial Blvd', 'address_2' => '', 'city' => 'Springfield', 'state' => 'TN', 'zip' => '37172', 'phone' => '615 384-4561', 'npi' => '1124040050', 'email' => 'springfielddrugs@hotmail.com'),
    430 => array('address_1' => '3120 Latrobe Dr, Ste 220', 'address_2' => '', 'city' => 'Charlotte', 'state' => 'NC', 'zip' => '28211', 'phone' => 'usa  704 661-8206', 'npi' => '1265696199', 'email' => 'doug@stanleyrx.com'),
    431 => array('address_1' => '7200 Ridge Rd Ste 106,', 'address_2' => '', 'city' => 'Port Richey', 'state' => 'FL', 'zip' => '34668', 'phone' => '717 312-4888', 'npi' => '1629514864', 'email' => 'starcare17@gmail.com'),
    432 => array('address_1' => '1220 Master St, Ste 5', 'address_2' => '', 'city' => 'Corbin', 'state' => 'KY', 'zip' => '40701', 'phone' => '606 261-7877', 'npi' => '1467739805', 'email' => 'stephaniesdownhomepharm@hotmail.com'),
    433 => array('address_1' => '53 Miller Dr', 'address_2' => '', 'city' => 'Owingsville', 'state' => 'KY', 'zip' => '40360', 'phone' => '859-498-1004', 'npi' => '1952481483', 'email' => 'jbarnettejr@yahoo.com'),
    434 => array('address_1' => '1 Main St', 'address_2' => '', 'city' => 'Lake Luzerne', 'state' => 'NY', 'zip' => '12846', 'phone' => '518 696-3214', 'npi' => '1699746297', 'email' => 'stonespharmacy@frontiernet.net'),
    435 => array('address_1' => '202 S Court St', 'address_2' => '', 'city' => 'Scottsville', 'state' => 'KY', 'zip' => '42164', 'phone' => '270 237-5402', 'npi' => '1255463287', 'email' => 'lafayettepharmacy@gmail.com'),
    436 => array('address_1' => '2670 New Holt Rd, Suite D', 'address_2' => '', 'city' => 'Paducah', 'state' => 'KY', 'zip' => '42001', 'phone' => '270 444-7070', 'npi' => '1932426020', 'email' => 'leigh@strawberryhillspharmacy.com'),
    437 => array('address_1' => '267 N Main St,', 'address_2' => '', 'city' => 'Liberty', 'state' => 'NY', 'zip' => '12754', 'phone' => '845 295-5456', 'npi' => '1194810770', 'email' => 'ishan.trivedi@gmai.com'),
    438 => array('address_1' => '104 N Commonwealth Ave', 'address_2' => '', 'city' => 'Polk City', 'state' => 'FL', 'zip' => '33868', 'phone' => '863 874-4834', 'npi' => '1245479161', 'email' => 'polkcitysunshinepharmacy@gmail.com'),
    439 => array('address_1' => 'ave-noel estra da #80', 'address_2' => '', 'city' => 'ISABELA', 'state' => 'PUERTO RICO', 'zip' => '662', 'phone' => 'usa 787 308-1826', 'npi' => '1881681047', 'email' => 'dalilabu@aol.com'),
    440 => array('address_1' => '32362 Long Neck Rd, Unit 5', 'address_2' => '', 'city' => 'Millsboro', 'state' => 'DE', 'zip' => '19966', 'phone' => '302 9470333', 'npi' => '1629457734', 'email' => 'sussexpharmacylongneck@gmail.com'),
    441 => array('address_1' => '1952 Long Grove Dr, Ste 1', 'address_2' => '', 'city' => 'Mount Pleasant', 'state' => 'SC', 'zip' => '29464', 'phone' => '843 6544013', 'npi' => '1669889374', 'email' => 'staff@sweetgrasspharmacy.com'),
    442 => array('address_1' => '1741 Gold Hill Rd, Ste 106', 'address_2' => '', 'city' => 'Fort Mill', 'state' => 'SC', 'zip' => '29708', 'phone' => '803 547-6100', 'npi' => '1548374242', 'email' => 'lisa@tegacaypharmacy.com'),
    443 => array('address_1' => '30 E 40th St, Frnt 2', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10016', 'phone' => '212 684-5125', 'npi' => '1093993297', 'email' => 'thechemistshop@gmail.com'),
    444 => array('address_1' => '709 Ligonier St', 'address_2' => '', 'city' => 'Latrobe', 'state' => 'PA', 'zip' => '15650', 'phone' => '724 539-4565', 'npi' => '1104822402', 'email' => 'scsrx@aol.com'),
    445 => array('address_1' => '33 E Simpson St', 'address_2' => '', 'city' => 'Mechanicsburg', 'state' => 'PA', 'zip' => '17055', 'phone' => '717 697-0551', 'npi' => 'N/A', 'email' => '0952@medicineshoppe.com'),
    446 => array('address_1' => '101 West Lancaster Ave', 'address_2' => '', 'city' => 'Shillington', 'state' => 'PA', 'zip' => '19607', 'phone' => 'usa 610 777-2313', 'npi' => '1912012931', 'email' => 'watts@acsalaska.net'),
    447 => array('address_1' => '1230 Main Street', 'address_2' => '', 'city' => 'Altavista', 'state' => 'VA', 'zip' => '24517', 'phone' => 'usa 434 369-5257', 'npi' => '1093800914', 'email' => 'daltonjl@fairpoint.net'),
    448 => array('address_1' => '500 N James St', 'address_2' => '', 'city' => 'Grayling', 'state' => 'MI', 'zip' => '49738', 'phone' => '989 348-2000', 'npi' => '1467564468', 'email' => '1675@medicineshoppe.com'),
    449 => array('address_1' => '206 S. Martin St', 'address_2' => '', 'city' => 'Titus', 'state' => 'PA', 'zip' => '16354', 'phone' => 'usa  814 827-1849', 'npi' => '1720452824', 'email' => '2016@medicineshoppe.com'),
    450 => array('address_1' => '9004 Havensight Shopp Ctr, Ste D', 'address_2' => '', 'city' => 'St Thomas', 'state' => 'VI', 'zip' => '802', 'phone' => '340 7761235', 'npi' => '1669684270', 'email' => 'msp.usvi@gmail.com'),
    451 => array('address_1' => '465 Keene Centre Dr', 'address_2' => '', 'city' => 'Nicholasville', 'state' => 'KY', 'zip' => '40356', 'phone' => '859 887-2841', 'npi' => '1649382594', 'email' => 'mgdown2@gmail.com'),
    452 => array('address_1' => '1215 W Whittier Blvd', 'address_2' => '', 'city' => 'Montebello', 'state' => 'CA', 'zip' => '90640', 'phone' => '323 728-8127', 'npi' => '1245309962', 'email' => 'shushmapatel@aol.com'),
    453 => array('address_1' => '614A Highway 76', 'address_2' => '', 'city' => 'White House', 'state' => 'TN', 'zip' => '37188', 'phone' => '615 581-0930', 'npi' => '1699283598', 'email' => 'theprescriptionshoppe37188@gmail.com'),
    454 => array('address_1' => '1025 Third Ave', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10065', 'phone' => '212 230-1700', 'npi' => '1598279036', 'email' => 'andrew@thirdaveapothecary.com'),
    455 => array('address_1' => '600 E Chestnut Ave', 'address_2' => '', 'city' => 'Altoona', 'state' => 'PA', 'zip' => '16601', 'phone' => '814 944-6139', 'npi' => '1003857228', 'email' => 'billjr@thompsonpharmacy.com'),
    456 => array('address_1' => '4350 Bayou Blvd, Ste 5', 'address_2' => '', 'city' => 'Pensacola', 'state' => 'FL', 'zip' => '32503', 'phone' => 'usa 850 572-8869', 'npi' => '1386611077', 'email' => 'dmiller@mythriftdrugs.com'),
    457 => array('address_1' => '5032 Ooltewah- Ringgold Road', 'address_2' => 'Suite 100', 'city' => 'Ooltewah', 'state' => 'TN', 'zip' => '37363', 'phone' => '423 396-6963', 'npi' => '1902948003', 'email' => 'julie@thriftymedplus.com'),
    458 => array('address_1' => '127 E Main St', 'address_2' => '', 'city' => 'Providence', 'state' => 'KY', 'zip' => '42450', 'phone' => '270 667-2295', 'npi' => '1255478434', 'email' => 'christi.robinson14@gmail.com'),
    459 => array('address_1' => '308 Main St. S', 'address_2' => '', 'city' => 'Tifton', 'state' => 'GA', 'zip' => '31794', 'phone' => '229 396-5552', 'npi' => '1922344498', 'email' => 'troy.allen@amerimedpharmacy.com'),
    460 => array('address_1' => '226 E High St', 'address_2' => '', 'city' => 'Jefferson City', 'state' => 'MO', 'zip' => '65101', 'phone' => '417 2095799', 'npi' => '1174531347', 'email' => 'shanebecker@oldtownpharmacy.com'),
    461 => array('address_1' => '815 Country Club Ln', 'address_2' => '', 'city' => 'Hopkinsville', 'state' => 'KY', 'zip' => '42240', 'phone' => '2708851524', 'npi' => '1275548802', 'email' => 'jpnixon@me.com'),
    462 => array('address_1' => '844 N 4th St', 'address_2' => '', 'city' => 'Tomahawk', 'state' => 'WI', 'zip' => '54487', 'phone' => '715 453-6600', 'npi' => '1285045377', 'email' => 'tyler@tomahawkpharmacy.com'),
    463 => array('address_1' => '200 N. Crawford St', 'address_2' => '', 'city' => 'Tompkinsville', 'state' => 'KY', 'zip' => '42167', 'phone' => '270 487-6155', 'npi' => '1952419046', 'email' => 'rlcmanagement@hotmail.com'),
    464 => array('address_1' => '3333 US Highway 9', 'address_2' => '', 'city' => 'Freehold', 'state' => 'NJ', 'zip' => '07728', 'phone' => '732 308-3627', 'npi' => '1982753323', 'email' => 'ablasio@verizon.net'),
    465 => array('address_1' => '101 N Main St', 'address_2' => '', 'city' => 'Topeka', 'state' => 'IN', 'zip' => '46571', 'phone' => '260 593-2252', 'npi' => '1609941467', 'email' => 'trevor@topekapharmacy.net'),
    466 => array('address_1' => '1051 S Riverside Dr', 'address_2' => '', 'city' => 'Clarksville', 'state' => 'TN', 'zip' => '37040', 'phone' => '931 645-2494', 'npi' => '1427101575', 'email' => 'zaverrx@gmail.com'),
    467 => array('address_1' => '2745 N Grandview Ave', 'address_2' => '', 'city' => 'Odessa', 'state' => 'TX', 'zip' => '79762', 'phone' => '432 366-2868', 'npi' => '1801880356', 'email' => 'wfg98@aol.com'),
    468 => array('address_1' => '2195 RT 442 Hwy', 'address_2' => '', 'city' => 'Muncy', 'state' => 'PA', 'zip' => '17756', 'phone' => '570 546-8272', 'npi' => '1588682587', 'email' => 'garympeck@gmail.com'),
    469 => array('address_1' => '396 E Burwell St', 'address_2' => '', 'city' => 'Salem', 'state' => 'VA', 'zip' => '24153', 'phone' => 'usa 540 537-5472', 'npi' => '1265729826', 'email' => 'kirteshpatel@gmail.com'),
    470 => array('address_1' => '901 2nd Ave', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10017', 'phone' => '212 752-5151', 'npi' => '1760403729', 'email' => 'turtlebaychemist@aol.com'),
    471 => array('address_1' => '2490 Honolulu Ave, Ste 110', 'address_2' => '', 'city' => 'Montrose', 'state' => 'CA', 'zip' => '91020', 'phone' => '818 330-7031', 'npi' => '1407252166', 'email' => 'edwins-2@hotmail.com'),
    472 => array('address_1' => '2302 S. Union Avenue', 'address_2' => '', 'city' => 'Tacoma', 'state' => 'WA', 'zip' => '98332', 'phone' => '253 752-1705', 'npi' => '1023347960', 'email' => 'kim@unionavenuerx.com'),
    473 => array('address_1' => '16395 Route 8', 'address_2' => '', 'city' => 'Union City', 'state' => 'PA', 'zip' => '16438', 'phone' => '814 438-7570', 'npi' => '1760596159', 'email' => 'treich6110@gmail.com'),
    474 => array('address_1' => '2929 Telegraph Ave', 'address_2' => '', 'city' => 'Berkeley', 'state' => 'CA', 'zip' => '94705', 'phone' => '510 843-3201', 'npi' => '1437339736', 'email' => 'drpam@consultingwithdrpam.com'),
    475 => array('address_1' => '3908 Kensington Ave,', 'address_2' => '', 'city' => 'Philadelphia', 'state' => 'PA', 'zip' => '19124', 'phone' => '215 245-2020', 'npi' => '1851824254', 'email' => 'universalpharmacy@hotmail.com'),
    476 => array('address_1' => '1809 S Main St', 'address_2' => '', 'city' => 'Upland', 'state' => 'IN', 'zip' => '46989', 'phone' => '765 998-8072', 'npi' => '1609241629', 'email' => 'uplandrx46989@gmail.com'),
    477 => array('address_1' => '137 N Levisa Rd', 'address_2' => '', 'city' => 'Mouthcard', 'state' => 'KY', 'zip' => '41548', 'phone' => '606 8354991', 'npi' => '1376648659', 'email' => 'jbarnettejr@yahoo.com'),
    478 => array('address_1' => '240 Green Valley Rd', 'address_2' => '', 'city' => 'Freedom', 'state' => 'CA', 'zip' => '95019', 'phone' => '831 728-2239', 'npi' => '1700924180', 'email' => 'Arthur.presser@huhs.edu'),
    479 => array('address_1' => '2101 Roosevelt Rd', 'address_2' => '', 'city' => 'Valparaiso', 'state' => 'IN', 'zip' => '46383', 'phone' => '219 462-1484', 'npi' => '1184736027', 'email' => 'laurivt@valporx.com'),
    480 => array('address_1' => '150 Maple Ave W', 'address_2' => '', 'city' => 'Vienna', 'state' => 'VA', 'zip' => '22180', 'phone' => '938 5242', 'npi' => '1932206778', 'email' => 'viennadrug@aol.com'),
    481 => array('address_1' => '590 Main St', 'address_2' => '', 'city' => 'Lynnfield', 'state' => 'MA', 'zip' => '01940', 'phone' => '781 334-3133', 'npi' => '1407880008', 'email' => 'rxalol@verizon.net'),
    482 => array('address_1' => '1629 Pegasus Way', 'address_2' => '', 'city' => 'San Marcos', 'state' => 'CA', 'zip' => '92069', 'phone' => '760 889-5193', 'npi' => '1437245412', 'email' => 'pavilionelm@yahoo.com'),
    483 => array('address_1' => '2559 Willow Point Way', 'address_2' => '', 'city' => 'Knoxville', 'state' => 'TN', 'zip' => '37931', 'phone' => '865 5600135', 'npi' => '1063815470', 'email' => 'camilla@volunteerpharmacy.com'),
    484 => array('address_1' => '8845 Kennedy Ave', 'address_2' => '', 'city' => 'Highland', 'state' => 'IN', 'zip' => '46322', 'phone' => 'usa 219 614-9440', 'npi' => '1801934898', 'email' => 'nathan@vytospharmacy.com'),
    485 => array('address_1' => '604 S 12th St, Ste A', 'address_2' => '', 'city' => 'Murray', 'state' => 'KY', 'zip' => '42071', 'phone' => '270 4447070', 'npi' => '1043670352', 'email' => 'leigh@strawberryhillspharmacy.com'),
    486 => array('address_1' => '222 S. Main Street', 'address_2' => '', 'city' => 'Wauconda', 'state' => 'IL', 'zip' => '60084', 'phone' => '847 526-2591', 'npi' => '1831274513', 'email' => 'tbruner@ameritech.net'),
    487 => array('address_1' => '112 Auderer Blvd', 'address_2' => '', 'city' => 'Waveland', 'state' => 'MS', 'zip' => '39576', 'phone' => '228 463-1055', 'npi' => '1679507644', 'email' => 'wavelandpharmacy@hotmail.com'),
    488 => array('address_1' => '1481 Route 23 S', 'address_2' => '', 'city' => 'Butler', 'state' => 'NJ', 'zip' => '07405', 'phone' => '973 492-5400', 'npi' => '1417107848', 'email' => 'rxinvest@aol.com'),
    489 => array('address_1' => '1101 W Monroe St', 'address_2' => '', 'city' => 'Mexico', 'state' => 'MO', 'zip' => '65265', 'phone' => '573 581-6930', 'npi' => '1841284379', 'email' => 'justin_webber@sbcglobal.net'),
    490 => array('address_1' => '3405 White Horse Rd, Ste F', 'address_2' => '', 'city' => 'Greenville', 'state' => 'SC', 'zip' => '29611', 'phone' => '864 6710300', 'npi' => '1700324100', 'email' => 'welcomefamilypharmacy@gmail.com'),
    491 => array('address_1' => '3225 West Gordon Ave #2', 'address_2' => '', 'city' => 'Cayton', 'state' => 'UT', 'zip' => '84041', 'phone' => 'usa  801 544-7979', 'npi' => '1528464336', 'email' => 'info@westgordonpharmacy.com'),
    492 => array('address_1' => '14161 N US Highway 25 E', 'address_2' => '', 'city' => 'Corbin', 'state' => 'KY', 'zip' => '40701', 'phone' => '606 2581111', 'npi' => '1578937694', 'email' => 'jbarnettejr@yahoo.com'),
    493 => array('address_1' => '66 Main St,', 'address_2' => '', 'city' => 'Yonkers', 'state' => 'NY', 'zip' => '10701', 'phone' => '347 247-9255', 'npi' => '1598252769', 'email' => 'mediservrx@gmail.com'),
    494 => array('address_1' => '1619 W Market St', 'address_2' => '', 'city' => 'Johnson City', 'state' => 'TN', 'zip' => '37604', 'phone' => '423 926-9137', 'npi' => '1700955861', 'email' => 'taylor.peoples@anandaprofessional.com'),
    495 => array('address_1' => '106 East Main Street', 'address_2' => '', 'city' => 'Springerville', 'state' => 'AZ', 'zip' => '85938', 'phone' => 'usa 928 333-2916', 'npi' => '1285749200', 'email' => 'fharper@frontiernet.net'),
    496 => array('address_1' => '103 S. Union St', 'address_2' => '', 'city' => 'Westfield', 'state' => 'IN', 'zip' => '46074', 'phone' => 'usa 317 896-9378', 'npi' => '1679090195', 'email' => 'ella1finance@gmail.com'),
    497 => array('address_1' => '255 Columbus Ave', 'address_2' => '', 'city' => 'New York', 'state' => 'NY', 'zip' => '10023', 'phone' => '212 362-9170', 'npi' => '1578608105', 'email' => 'westsidepharmacy255@yahoo.com'),
    498 => array('address_1' => '327 Romany Rd', 'address_2' => '', 'city' => 'Lexington', 'state' => 'KY', 'zip' => '40502', 'phone' => '859 5542716', 'npi' => '1275894123', 'email' => 'claire@wheelercompounding.com'),
    499 => array('address_1' => '814 Greenville Hwy', 'address_2' => '', 'city' => 'Hendersonville', 'state' => 'NC', 'zip' => '28792', 'phone' => 'usa  828 692-4236', 'npi' => '1225130789', 'email' => 'flip@whitleydrugs.com'),
    500 => array('address_1' => '9209 Colima Rd, Ste 1100', 'address_2' => '', 'city' => 'Whittier', 'state' => 'CA', 'zip' => '90605', 'phone' => '562 789-5852', 'npi' => '1053312439', 'email' => 'odokhalil@yahoo.com'),
    501 => array('address_1' => '574 S Landmark Ave', 'address_2' => '', 'city' => 'Bloomington', 'state' => 'IN', 'zip' => '47403', 'phone' => '812 335-0000', 'npi' => '1487621553', 'email' => 'chadh@wbhcp.com'),
    502 => array('address_1' => '101 W Brumfield Ave', 'address_2' => '', 'city' => 'Princeton', 'state' => 'IN', 'zip' => '47670', 'phone' => '812 386-5194', 'npi' => '1437127305', 'email' => 'chadh@wbhcp.com'),
    503 => array('address_1' => '10 Williams Brothers Dr', 'address_2' => '', 'city' => 'Washington', 'state' => 'IN', 'zip' => '47501', 'phone' => '812 254-2497', 'npi' => '1497732028', 'email' => 'sarac@wbhcp.com'),
    504 => array('address_1' => '240 McLaws', 'address_2' => '', 'city' => 'Williamsburg', 'state' => 'VA', 'zip' => '23185', 'phone' => '757 2291041', 'npi' => '1184721920', 'email' => 'twtaylor@williamsburgdrug.com'),
    505 => array('address_1' => '1302 Mt. Vernon Ave.', 'address_2' => '', 'city' => 'Williamsburg', 'state' => 'VA', 'zip' => '23185', 'phone' => '757 2291041', 'npi' => '145744433', 'email' => 'twtaylor@williamsburgdrug.com'),
    506 => array('address_1' => '199 Brook St', 'address_2' => '', 'city' => 'Scarsdale', 'state' => 'NY', 'zip' => '10583', 'phone' => '914 7251827', 'npi' => '1831310945', 'email' => 'wilmontpharmacy@gmail.com'),
    507 => array('address_1' => '319 E. Main Street', 'address_2' => '', 'city' => 'Wilmore', 'state' => 'KY', 'zip' => '40390', 'phone' => '8594981004', 'npi' => '1750487260', 'email' => 'jbarnettejr@yahoo.com'),
    508 => array('address_1' => '8920 Wilshire Blvd ste 108', 'address_2' => '', 'city' => 'Beverly Hills', 'state' => 'CA', 'zip' => '90211', 'phone' => '310 657-0750', 'npi' => '1558537910', 'email' => 'elliotzan@aol.com'),
    509 => array('address_1' => '265 E Main St', 'address_2' => '', 'city' => 'Newport', 'state' => 'TN', 'zip' => '37821', 'phone' => '423 623-3456', 'npi' => '1205948213', 'email' => 'savmordrug@yahoo.com'),
    510 => array('address_1' => '1216 Washington Ave', 'address_2' => '', 'city' => 'Vincennes', 'state' => 'IN', 'zip' => '47591', 'phone' => '812 882-1800', 'npi' => '1366475133', 'email' => 'sarac@wbhcp.com'),
    511 => array('address_1' => '797 Ky 15 S', 'address_2' => '', 'city' => 'Campton', 'state' => 'KY', 'zip' => '41301', 'phone' => '606-668-2273', 'npi' => '1912282187', 'email' => 'jbarnettejr@yahoo.com'),
    512 => array('address_1' => '400 E Arcadia Ave', 'address_2' => '', 'city' => 'Dawson Springs', 'state' => 'KY', 'zip' => '42408', 'phone' => '270 797-2761', 'npi' => '1063507648', 'email' => 'woodburnpharmacy@bellsouth.net'),
    513 => array('address_1' => '432 Hopkinsville Road', 'address_2' => '', 'city' => 'Russellville', 'state' => 'KY', 'zip' => '42276', 'phone' => 'usa 270 725-7054', 'npi' => '1740615491', 'email' => 'yatespharmacy@gmail.com'),
    514 => array('address_1' => '3708 Freemansburg Ave,', 'address_2' => '', 'city' => 'Bethlehem', 'state' => 'PA', 'zip' => '18020', 'phone' => '610 866-4552', 'npi' => '1467471698', 'email' => 'jyoung@myyoungspharmacy.com'),
    515 => array('address_1' => '400 W Gordon Ave, Ste D', 'address_2' => '', 'city' => 'Gordonsville', 'state' => 'VA', 'zip' => '22942', 'phone' => '540 832-0000', 'npi' => '1033633474', 'email' => 'zacgvillephcy@gmail.com'),
    516 => array('address_1' => '3106 Buffalo Rd', 'address_2' => '', 'city' => 'Erie', 'state' => 'PA', 'zip' => '16510', 'phone' => '814 898-2086', 'npi' => '1053597070', 'email' => 'jeffsedelmyer@gmail.com'),
);
*/

add_action('init', 'runOnInit', 10, 0);
function runOnInit() { 
    /*
    if($_GET['xero'] == '1') {

        $contact_manager = new WC_XR_Contact_Manager(new WC_XR_Settings());

        $response = $contact_manager->get_all_contacts();

        // var_dump($response->Contacts);

        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="text.xml"');

        echo $response;
        exit('');
    }
    */

    
    // if ($_GET['customers'] == '1') {
    //     $cnt = 0;
    //     global $customers_array;
    //     foreach($customers_array as $customer) {
    //         $user_id = wp_insert_user([
    //             'user_login' => $customer['email'],
    //             'user_pass' => strtolower($customer['email']),
    //             'user_email' => $customer['email']
    //         ]);
    //         var_dump( 'User: ' . $customer['email'] . ' / ' . strtolower($customer['email']));
    //         if (!is_wp_error($user_id)) {
    //             update_user_meta( $user_id, 'already_bought', '1' );
    //             update_user_meta( $user_id, 'has_salesforce_checked', '1');
    //             update_user_meta( $user_id, 'npi_id', sanitize_text_field( $customer['npi'] ) );
    //             update_user_meta( $user_id, 'billing_address_1', sanitize_text_field( $customer['address_1'] ));
    //             update_user_meta( $user_id, 'billing_address_2', sanitize_text_field( $customer['address_2'] ));
    //             update_user_meta( $user_id, 'billing_city', sanitize_text_field( $customer['city'] ));
    //             update_user_meta( $user_id, 'billing_state', sanitize_text_field( $customer['state'] ));
    //             update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $customer['phone'] ));
    //             update_user_meta( $user_id, 'billing_postcode', sanitize_text_field( $customer['zip'] ));
    //             update_user_meta( $user_id, 'billing_country', 'US');
    //             echo 'user_created: '. $user_id . ' : '. $customer['email'] . '<br/>';
    //             $cnt ++;
    //         } else {
    //             echo 'user_failed: '. $customer['email'] . '<br/>';
    //         }
    //     }
    //     exit('total created: '. $cnt);
    // }
    // if ($_GET['customers'] == '1') {
    //     update_user_meta( 736, 'already_bought', '1' );
    //     update_user_meta( 736, 'has_salesforce_checked', '1');
    //     exit('test: ok');
    // }
    

    if (is_user_logged_in()) {
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
    }
}

