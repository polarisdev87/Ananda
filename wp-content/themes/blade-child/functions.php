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

    <p class="form-row form-row-wide">
        <label class="label" for="distributor">Do you have multiple stores? <abbr class="required" title="required">*</abbr></label>
        <div class="inline-group">
            <label class="radio"><input type="radio" name="distributor" value="0" checked /> <i>No</i></label>
            <label class="radio"><input type="radio" name="distributor" value="1" /> <i>Yes</i></label>
        </div>
        <div>&nbsp;</div>
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

    if ( isset($_POST['distributor']) && $_POST['distributor'] == 1) {

        // bail if Memberships isn't active
        if ( ! function_exists( 'wc_memberships' ) ) {
            return;
        }

        $plan = wc_memberships_get_membership_plan('distributor');

        if (!$plan) return;

        $args = array(
            'plan_id' => $plan->id,
            'user_id' => $customer_id,
        );

        wc_memberships_create_user_membership( $args );

        // Optional: get the new membership and add a note so we know how this was registered.
        $user_membership = wc_memberships_get_user_membership( $customer_id, $args['plan_id'] );
        $user_membership->add_note( 'Membership access granted automatically from registration.' );

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




function is_reorder() {
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types(),
        'post_status' => 'wc-completed', // array_keys( wc_get_order_statuses() ),
    ) );

    $loyal_count = 1;
    $user_already_bought = get_user_meta(get_current_user_id(), 'already_bought', true);

    return count( $customer_orders ) >= $loyal_count || $user_already_bought=='1';
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
                #adminmenu>.toplevel_page_asl-plugin, #adminmenu>.toplevel_page_woocommerce, #adminmenu>.menu-icon-users {
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
                #adminmenu>.toplevel_page_woocommerce>ul.wp-submenu>li:nth-child(4),
                #adminmenu>.menu-icon-users>ul.wp-submenu>li:nth-child(1),
                #adminmenu>.menu-icon-users>ul.wp-submenu>li:nth-child(2) {
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
    if (is_reorder()) {
        unset($fields['billing']['rep_name']);
    }

    // $limited_list = ['CA', 'FL', 'PA', 'KY'];
    // if (!in_array(WC()->customer->get_shipping_state(), $limited_list)) {
    //     unset($fields['billing']['tax_cert']);
    // }

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
    if (is_reorder()) return;

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
    <script src="https://app.certcapture.com/gencert2/js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#tax_cert_field').hide();
            function update_tax_cert_field(search_state) {
                var found = ['CA', 'FL', 'PA', 'KY'].find(function (el) {
                    return el == search_state;
                });
                if (found) {
                    jQuery('#tax_cert_field').show();
                } else {
                    jQuery('#tax_cert_field').hide();
                }
            }
            update_tax_cert_field(jQuery('#billing_state').val());
            jQuery('#billing_state').change(function() {
                update_tax_cert_field(jQuery(this).val());
            });
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
    <div id="cert_capture_form" style="display: none;"></div>
<?php
}

/* Test */
// $certcapture_client_id = '82587';
// $certcapture_client_key = 'GcJMnfB0CnYMoG5R';
// $certcapture_username = 'anandap_test';
// $certcapture_password = 'AnandaProfessional2018';
$certcapture_username = 'lance032017@gmail.com';
$certcapture_password = 'AnandaProfessional@2018';
/* PROD */
$certcapture_client_id = '82590';
$certcapture_client_key = '3Y8i6qngdaRLFY7t';
// $certcapture_username = 'anandap';
// $certcapture_password = 'AnandaProfessional2018';

function curl_certcapture($url, $customer_number, $post = false, $postData = '') {
    global $certcapture_client_id, $certcapture_client_key, $certcapture_username, $certcapture_password;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    if ($post) {
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "x-client-id: " . $certcapture_client_id,
        "Authorization: Basic " . base64_encode($certcapture_username . ':' . $certcapture_password),
        "x-customer-number: " . $customer_number,
        "x-customer-primary-key: customer_number",
        "Content-Type: application/x-www-form-urlencoded",
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response);
}

add_action( 'woocommerce_review_order_after_submit', 'custom_review_order_after_submit' );
function custom_review_order_after_submit() {
    if (is_ajax() && !empty( $_POST['post_data'] ) ) {
        parse_str( $_POST['post_data'], $post_data );
    }else {
        $post_data = $_POST;
    }

    /*if (in_array($post_data['shipping_state'], ['CA', 'FL', 'PA', 'KY'])) {
        ?><script type="text/javascript">jQuery('#tax_cert_field').show();</script><?php
    }*/

    if(!empty($post_data['tax_cert'])) {
        if ($post_data['tax_cert']!='NO') {

            global $certcapture_client_id, $certcapture_client_key, $certcapture_username, $certcapture_password;

            $npi_id = get_user_meta(get_current_user_id(), 'npi_id', true); // cert capture - customer number

            $states = ['AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming'];

            $token_response = curl_certcapture("https://api.certcapture.com/v2/auth/get-token", $npi_id, true);
            $token = $token_response->response->token;

            $customer_data = curl_certcapture("https://api.certcapture.com/v2/customers/" . $npi_id, $npi_id);
            if (isset($customer_data->success) && $customer_data->success === false) {
                $data = addslashes(urldecode(http_build_query([
                    'customer_number' => $npi_id,
                    'alternate_id' => $npi_id,
                    'name' => $post_data['billing_company'],
                    'attn_name' => $post_data['billing_first_name'] . ' ' . $post_data['billing_last_name'],
                    'contact_name' => $post_data['billing_first_name'] . ' ' . $post_data['billing_last_name'],
                    'address_line1' => $post_data['billing_address_1'],
                    'address_line2' => '',
                    'city' => $post_data['billing_city'],
                    'zip' => $post_data['billing_postcode'],
                    'phone_number' => $post_data['billing_phone'],
                    'email_address' => $post_data['billing_email'],
                    'country' => ['name' => 'United States'],
                    'state' => ['name' => $states[$post_data['billing_state']]],
                ])));
                $response_customer_created = curl_certcapture("https://api.certcapture.com/v2/customers", $npi_id, true, $data);
            }
            $customer_certificates = curl_certcapture("https://api.certcapture.com/v2/customers/" . $npi_id . "/certificates", $npi_id);

            if (count($customer_certificates) == 0) {
                ?>
                    <script type="text/javascript">
                        jQuery('#place_order').attr('disabled', 'disabled');
                        // alert('Please complete Tax form below');
                    </script>
                    <script type="text/javascript">
                        GenCert.init(document.getElementById("cert_capture_form"), {
                            // The token and zone must set to start the process!
                            token: '<?php echo $token; ?>',
                            // debug: true,
                            edit_purchaser: true,
                            // hide_sig: true,
                            fill_only: true,
                            // upload_only: true,
                            // submit_to_stack: true,

                            onCertSuccess: function() {
                                console.log('Certificate successfully generated with id:' + GenCert.certificateIds);
                                jQuery('#place_order').attr('disabled', '').removeAttr('disabled');
                                GenCert.hide();
                                // alert('Please proceed with your order');
                            },
                        }, '<?php echo $certcapture_client_id; ?>', '<?php echo $certcapture_client_key; ?>');

                        GenCert.setCustomerNumber('<?php echo $npi_id; ?>'); // create customer
                        var customer = new Object();
                        customer.name = '<?php echo $post_data['billing_first_name'] . ' ' . $post_data['billing_last_name']; ?>';
                        customer.address1 = '<?php echo $post_data['billing_address_1']; ?>';
                        customer.city = '<?php echo $post_data['billing_city']; ?>';
                        customer.state = '<?php echo $states[$post_data['billing_state']]; ?>';
                        customer.country = 'United States';
                        // customer.country = '<?php echo $post_data['billing_country']; ?>';
                        customer.phone = '<?php echo $post_data['billing_phone']; ?>';
                        customer.zip = '<?php echo $post_data['billing_postcode']; ?>';
                        GenCert.setCustomerData(customer);
                        GenCert.setShipZone('<?php echo $states[$post_data['billing_state']]; ?>');
                        GenCert.show();
                    </script>
                <?php
            }
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

add_filter( 'woocommerce_product_get_tax_class', 'custom_wc_zero_tax_for_certificate', 10, 3);
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

if( !is_admin() ) {
    function exclude_orders_filter_recipient( $recipient, $order ){

        if ($order->get_payment_method() === 'cheque' ) {
            return $recipient;
        }

        if (is_reorder()) {
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
}


add_filter( 'woocommerce_coupon_get_discount_amount', 'alter_shop_coupon_data', 20, 5 );
function alter_shop_coupon_data( $round, $discounting_amount, $cart_item, $single, $coupon ){

    // Related coupons codes to be defined in this array (you can set many)
    $coupon_codes = array('tcg', 'care');

    if ( $coupon->is_type('percent') && in_array( $coupon->get_code(), $coupon_codes ) ) {
        if (is_reorder()) {
            $discount = (float) $coupon->get_amount() * ( 0.5 * $discounting_amount / 100 );
            $round = round( min( $discount, $discounting_amount ), wc_get_rounding_precision() );
        }
    }
    return $round;
}

if (!is_admin() && !wc_memberships_is_user_active_member( get_current_user_id(), 'distributor')) {
    // $GLOBALS['wcms']);
    // var_dump('deactiveated');
    remove_action( 'init', array( $GLOBALS['wcms']->front, 'load_account_addresses' ) );
    remove_action( 'woocommerce_before_checkout_shipping_form', array( $GLOBALS['wcms']->checkout, 'render_user_addresses_dropdown' ) );
    // remove_action ( 'woocommerce_account_edit-address_endpoint', array( $GLOBALS['wcms']->front, 'add_address_button' ) );
    // remove_action ( 'wp_loaded', array( $GLOBALS['wcms']->front, 'delete_address_action' ) );
    // remove_action ( 'wp_loaded', array( $GLOBALS['wcms']->front, 'delete_address_action' ) );
    // var_dump ($GLOBALS['wcms']->front->load_account_addresses);
}


add_action( 'woocommerce_cart_calculate_fees','shipping_method_discount', 20, 1 );
function shipping_method_discount( $cart_object ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

    // HERE Define your targeted shipping method ID
    $payment_method = 'cheque';

    // The percent to apply
    $percent = 2; // 15%

    $cart_total = $cart_object->subtotal_ex_tax;
    $chosen_payment_method = WC()->session->get('chosen_payment_method');

    if( $payment_method == $chosen_payment_method ){
        $label_text = __( "Shipping discount " . $percent ."%" );
        // Calculation
        $discount = number_format(($cart_total / 100) * $percent, 2);
        // Add the discount
        $cart_object->add_fee( $label_text, -$discount, false );
    }
}

add_action( 'woocommerce_review_order_before_payment', 'refresh_payment_methods' );
function refresh_payment_methods(){
    // jQuery code
    ?>
    <script type="text/javascript">
        (function($){
            $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                $('body').trigger('update_checkout');
            });
        })(jQuery);
    </script>
    <?php
}

/* locking down the company fields for checkout page */
function custom_woocommerce_billing_fields( $fields ){
    if ( !is_checkout() ) return $fields; 
    $url_param_fields = array(
        'company',
    );
    foreach( $url_param_fields as $param ){
        $billing_key = 'billing_' . $param;
        if ( array_key_exists( $billing_key, $fields) ) {
            $fields[$billing_key]['type'] = 'hidden'; // let's change the type of this to hidden.
        }
    }
    return $fields;
}
add_filter( 'woocommerce_billing_fields', 'custom_woocommerce_billing_fields' );
function custom_woocommerce_shipping_fields( $fields ){
    if ( !is_checkout() ) return $fields; 
    $url_param_fields = array(
        'company',
    );
    foreach( $url_param_fields as $param ){
        $shipping_key = 'shipping_' . $param;
        if ( array_key_exists( $shipping_key, $fields) ) {
            $fields[$shipping_key]['type'] = 'hidden'; // let's change the type of this to hidden.
        }
    }
    return $fields;
}
add_filter( 'woocommerce_shipping_fields', 'custom_woocommerce_shipping_fields' );

function woocommerce_form_field_hidden( $field, $key, $args ){
    $field = '
        <p class="form-row address-field validate-required" id="'.esc_attr($key).'_field" data-priority="90">
            <label for="'.esc_attr($key).'" class="">'.esc_attr($args['label']).'&nbsp;'.($args['required']?'<abbr class="required" title="required">*</abbr>':'').'</label>
            <span class="woocommerce-input-wrapper"><strong class="'.esc_attr($key).'">'.get_user_meta(get_current_user_id(), $key, true).'</strong><input type="hidden" name="'.esc_attr($key).'" id="'.esc_attr($key).'" value="'.get_user_meta(get_current_user_id(), $key, true).'" autocomplete="'.esc_attr($args['autocomplete']).'" class="" readonly="readonly"></span>
        </p>
    ';
    return $field;
}
add_filter( 'woocommerce_form_field_hidden', 'woocommerce_form_field_hidden', 10, 3 );

// https://t.yctin.com/en/excel/to-php-array/
// $customers_array = array(
//     0 => array('address_1' => '212 MILLWELL DR', 'address_2' => 'SUITE A', 'city' => 'MARYLAND HEIGHTS', 'state' => 'MO', 'zip' => '63043-2512', 'phone' => '314-727-8787', 'npi' => '1790061596', 'email' => 'Mgraumenz@Legacydrug.com'),
// );

add_action('init', 'runOnInit', 10, 0);
function runOnInit() {

    remove_action( 'woocommerce_before_checkout_form', array( $GLOBALS['wcms']->checkout, 'before_checkout_form' ) );
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

    //To be run anandaprofessional.com/?customers=1 after update
    //This code block is use to enable reorder
    // if ($_GET['customers'] == '1') {
    //     update_user_meta( 115, 'already_bought', '1' );
    //     update_user_meta( 115, 'has_salesforce_checked', '1');
    //     exit('test: ok');
    // }

    // if ($_GET['customers'] == '1') {
    //     $customer_emails = ['Achilles@acerxpharmacy.com','add.drug@gmail.com','acarepharmacy@gmail.com','affordablepharmacyservices@gmail.com','kim@cumberlandrx.com','musiceye@aol.com','troy.allen@amerimedpharmacy.com','pgordon@yahoo.com','Anclotepharma@gmail.com','jbarnettejr@yahoo.com','andyspharmacy@gmail.com','annapolispharmacy@professionalpharmacygroup.com','jbarnettejr@yahoo.com','apothecare2@bbtel.com','apothecare2@bbtel.com','apothecare2@bbtel.com','apothecare2@bbtel.com','jeff@appledrugs.com','staff@apthorprx.com','rphmchugh@gmail.com','arnoldpharmacy@professionalpharmacygroup.com','arrowpharmacy@gmail.com','mpmcneill@aphhc.net','audubonpharmacy@mw.twcbc.com','avalonchemists@gmail.com','bbpharmacy@gmail.com','baldwinwoods@intrstar.net','charlie@bassettsmarket.com','bewell7800@gmail.com','beemansrx@aol.com','dhbelew@belewdrugs.com','dhbelew@belewdrugs.com','dhbelew@belewdrugs.com','dhbelew@belewdrugs.com','bereadrug@yahoo.com','sheryl@bestvaluedrug.com','info@beverlyhillsapothecary.com','josephspharmacy@gmail.com','pharmacy@blackoakrx.com','grussell@bluegrasspharmacy.com','pharmacy@boalsburgapothecary.com','btoygtoy@hotmail.com','boltons2inc@yahoo.com','c.vallone@bradleyhealthservices.net','Brandonpharmacy@gmail.com','jesse@brashearspharmacy.com','jesse@brashearspharmacy.com','brasstown@gmail.com','info@breakfreepharmacy.com','brighampharmacy@gmail.com','aaron@brodielanepharmacy.com','buntingfamilypharmacy@verizon.net','pharmacy@bushyrunrx.comcastbiz.net','katie@buttdrugs.com','RxpGC2@gmail.com','bypassrx4@gmail.com','Bypassrx@gmail.com','springsrx@verizon.net','rachelmaleski@yahoo.com','pantherhealthllc@gmail.com','kdowning@capefearpharmacy.com','aaron@mycapitalpharmacy.com','scott@capstonecompounding.com','care1pharmacy@att.net','caremart@gmail.com','wanda@carmichaeldrugs.com','carolinalforestpharmacy@gmail.com','aroeder@caycespharmacy.com','cds10pharmacybg@gmail.com','mk@cedrapharmacy.com','mk@cedrapharmacy.com','ccpharmacist@gmail.com','clinicpharmacycc@yahoo.com','centurymedicinesetown@gmail.com','centurymedicinesetown@gmail.com','kmiller@certacare.com','chambersrxllc@yahoo.com','guswalters@chancydrugs.com','guswalters@chancydrugs.com','guswalters@chancydrugs.com','rphmchugh@gmail.com','Matt@cheekandscott.com','eric@cheekandscott.com','jayb@cheekandscott.com','Chelsearoyalcarepharmacy@gmail.com','info@genericstogo.com','claibornerx@gmail.com','clarkcountypharmacy@gmail.com','admin@clinicpharmacy.net','amanda.leach@gmail.com','dhagedorn@coastalmedicine.com','craig_ouellette@yahoo.com','colonialdrugs155@gmail.com','colonialdrugs155@gmail.com','narrowsrx@verizon.net','columbuslocalpharmacy@gmail.com','faust.corina.l@gmail.com','keyesb@prodigy.net','cathy@compoundcarerx.com','marktimmermann58@gmail.com','info@condopharmacy.com','tamara536@comcast.net','cooksrx@aol.com','cooperdrugs@yahoo.com','ketsi1127@gmail.com','corner-drugstore@hotmail.com','cornerpharmkatherine@aol.com','gtcollins@gmail.com','cowandrugs@yahoo.com','coxspharmacy#5@gmail.com','rkragel@crescentdrugs.com','willdouglas@crimsoncarerx.com','megspharmacy@yahoo.com','schoen@crosbysdrugs.com','amanda.leach@gmail.com','kim@cumberlandrx.com','ghada@curemedpharmacy.com','curlewpharmacy@gmail.com','alcostarx@gmail.com','jeff@custommed.com','pharmacy@customplusrx.com','rx@danscare.com','rphmchugh@gmail.com','rphmchugh@gmail.com','darienrx437@gmail.com','nlbdavis@yahoo.com','deleonpharmacy@gmail.com','delrayshorepharmacy@gmail.com','colonialdrugs155@gmail.com','tom@depietropharmacy.com','rameshrx@gmail.com','sshep2@comcast.net','dilloncommunitypharmacy@hotmail.com','dixiepharmacy2@gmail.com','rick@palmbeachcompounding.com','annette.127@bellsouth.net','dougspharm@aol.com','blanemgt@att.net','kurt@drazizrx.com','info@dsdpharmacy.org','dunlopllc@gmail.com','ralphsrx3@gmail.com','monaghattas@me.com','arica702@hotmail.com','eaglehighlandpharmacy@yahoo.com','carms0989@gmail.com','shaukatyousaf69@gmail.com','info@echopharmacy.com','bradenton@myeckerds.com','jenna@edgertonpharmacy.com','stewart@eltoropharmacy.com','bryant.randy76@gmail.com','lindsey@ellapharmacy.com','ellicottcitypharmacy@gmail.com','shanebuie@elydrugs.biz','empirepharmacy@professionalpharmacygroup.com','contact@enhealthmatters.com','etownpharmacy@gmail.com','familycarepharmacy@yahoo.com','license@familypharmacy.org','familyrx335@yahoo.com','pamelsa@prtc.net','salecreekpharmacy@gmail.com','folserx2@bellsouth.net','retail@fwcustomrx.com','Hikingdawg@gmail.com','fountainvalleyrx@outlook.com','jamiefranklinrx@gmail.com','franklinpharmacy@gmail.com','dpskahlon@gmail.com','gallowaysands@atmc.net','gallowaysands2@bizec.rr.com','garstrx@gmail.com','rphmchugh@gmail.com','smithcooney@gattirx.com','gaughns@gmail.com','parag@doserx.com','ejschoett@yahoo.com','jbarnettejr@yahoo.com','mail@getrxhelp.com','scottb@bradenmed.com','bjzaslow@gladwynepharmacy.com','robertoliver@glasgowrx.com','glotzbachpharmacy@gmail.com','elsalam@goldenhealthpharmacy.com','pharmr@bellsouth.net','info@grangerpharmacy.com','katy@granitedrug.com','michele@grantspasspharmacy.com','anthony@grattanspharmacy.com','greenspharmacy@bellsouth.net','info@mygreenleafrx.com','john_gentry@bellsouth.net','jeffbonjo@aol.com','greenwoodrx@gmail.com','groveharborpharmacy@gmail.com','gulfpharmacy@yahoo.com','hsl@handspharmacy.com','paula@hallettsvillepharmacy.com','parag@doserx.com','hannibalpharmacy@gmail.com','gavin@mooresvillepharmacy.com','dickersonjbd@yahoo.com','drghussin@yahoo.com','jbarnettejr@yahoo.com','heidi@herbstpharmacy.com','nando16@comcast.net','herrindrug@yahoo.com','hibbittsland@gmail.com','Hidenwoodrx@verizon.net','hilltoppharm818@gmail.com','hinespharmacy@hotmail.com','hinespharmacy@gmail.com','hnrpharmacy@gmail.com','ryerx23@yahoo.com','holdenpharmacy@gmail.com','singram@kih.net','john@hooksrx.com','hrxpharmacy@gmail.com','astefanis@hydedrugstore.com','hyderx@hotmail.com','familyvaluepharmacy@gmail.com','aaly@ippindy.com','irwinspharmacy@gmail.com','nwhitch@hotmail.com','jacksonstreetdrug@gmail.com','hjamesds@yahoo.com','jeffsrx@comcast.net','dwilliams@jeffersondrug.com','michaeljds@hotmail.com','compounding@jiffyrx.com','info@genericstogo.com','golden.becky@gmail.com','karemorepharmacy@hotmail.com','cory.lehano@gmail.com','kayspharmacy@gmail.com','brad@kbpharmacy.com','deberah-keaveny@gmail.com','marty@kellyspharmacyinc.com','marty@kellyspharmacyinc.com','kentpharmacymilford@gmail.com','youlzik@gmail.com','kingpharmacy@live.com','scott.king@king-pharmacy.com','sshep2@comcast.net','eknightj@gmail.com','knoxpharmacy@gmail.com','kkmuleshoe@gmail.com','rahulpatel@lmpharmacy.com','lafayettepharmacy@gmail.com','mypharmacist@lakecountrypharmacy.com','jonathan.grider@uky.edu','larry@lakewylierx.com','lakelanddrug.ga@gmail.com','laketownpharmacy@gmail.com','pete@lakeviewpharmacy.com','achoezirim@gmail.com','afields20@hotmail.com','kelleyrwalters@gmail.com','nathan@lawrencedrug.com','lawrencepharmacy1@yahoo.com','info@lennysrichfieldpharmacy.com','keithvance@lewisvilledrug.com','linmasdrugs@embarqmail.com','Jeff@LintonRx.com','lintonsqpharmacy@bellsouth.net','livewellutica@gmail.com','livingstonRX1@gmail.com','logospaharmacy@verizon.net','lbcrx@nyrph.com','info@longislandapothecary.com','longleyspharmacy@gmail.com','fred@lowrydrug.com','analia@lukespharmacy.com','ldhostetler@frontier.com','rickpenn@sbcglobal.net','jon@macspharmacy.com','mymsd@icloud.com','jean@mymspax.com','mfpharmacy@yahoo.com','linkrx@aol.com','mathesrx@aol.com','mccayspharmacy@gmail.com','blake@markethubco.com','jbarnettejr@yahoo.com','jbarnettejr@yahoo.com','jbarnettejr@yahoo.com','medicarerx3@gmail.com','medicarx@gmail.com','medicarx@gmail.com','vannhealthcare@glasgow-ky.com','medrx@aol.com','raumi_joseph@hotmail.com','jerryhf@verizon.net','medicap8160@gmail.com','8400@medicap.com','kbarbrey@gmail.com','drokosz@medicenterpharmacy.com','knewton@medicenterpharmacy.com','lgetchius@medicenterpharmacy.com','bgalli@medicenterpharmacy.com','rxpharmgrl@gmail.com','1503@medicineshoppe.com','eblackrph@gmail.com','0062@medicineshoppe.com','1198@medicineshoppe.com','vendor@medozrx.com','medpark@professionalpharmacygroup.com','medtimerx@gmail.com','jamey@metcalfedrugs.com','michelle@michellespharmacy.com','ranrph@aol.com','john@midtownpharmacyexpress.com','ykadibhai7@gmail.com','mikeyarworth@yahoo.com','info@mineraldrug.com','dshultz@minnichspharmacy.com','brandi@moosepharmacy.com','james@moosepharmacy.com','whit@moosepharmacy.com','kyle@moosepharmacy.com','remypharm@gmail.com','bubrx@aol.com','oshoheiber@mydrsrx.com','nations54@nationsmedicines.com','jim@nationalrx.com','Not provided','newamsterdamdrugmart@gmail.com','newnanpharmacy@numail.org','jbarnettejr@yahoo.com','nimohrx@gmail.com','roles@norlandrx.com','northcenturypharmacy@duo-county.com','Rob@MyNucare.com','parag@doserx.com','info@oceanchemist.net','okiesrx@bellsouth.net','shanebecker@oldtownpharmacy.com','oldetownerx@aol.com','s_hoffman9614@yahoo.com','organicrxjuice@gmail.com','aortiz@ortizpharmacy.com','jbarnettejr@yahoo.com','owensborofamilypharmacy@gmail.com','support@pandmpharmacy.com','pharmacy@palmrxs.com','drsmali34@gmail.com','pharmacist@paolipharmacy.com','leslie@paris-apothecary.com','sjhopple@comcast.net','barbrx@parkerpharmacy.com','parksidepharmacyinc@gmail.com','paul@parkwaypharm.net','mdcrx@aol.com','pastpharm@aol.com','vpatel6239@gmail.com','kpsmith@patricksquare-rx.com','paroldan@aol.com','boydennisjr@mypaylessdrugs.com','jenutpharmd@hotmail.com','pharma1mckinney@gmail.com','sradpay@pharmacarehawaii.com','tim@pctn.net','info@pharmacycaresolutions.com','bberry@rxplusinc.com','vancekiser@yahoo.com','rphmchugh@gmail.com','gvassie@gmail.com','plantationpharmacy@yahoo.com','plantationpharmacy@yahoo.com','plazadrugoflondon@gmail.com','plsi@drug.sdcoxmail.com','plsi@drug.sdcoxmail.com','poolerpharmacy@gmail.com','portpharm@gmail.com','lcurtis@portagepharmacy.com','mistinnett@aol.com','powhatandrug@gmail.com','snyderitaville@charter.net','rxlabloretta@gmail.com','prescriptionshopandrews@gmail.com','jorge@prestonspharmacy.com','anthonybertola@primarycarepharmacysvcs.com','kourtneychic@verizon.net','dawn@professionalpharmacy.com','professionalpharmacy@embarqmail.com','vinod@prosperitypharmacy.com','puremeridian@gmail.com','cory.lehano@gmail.com','amye.rmp@gmail.com','rannpharmacy@yahoo.com','patrick.redipharmacy@gmail.com','info@genericstogo.com','reedscompounding@gmial.com','t.k@karnaby.com','info@remingtondrug.com','samibahta@yahoo.com','james.rickett@yahoo.com','risonpharmacy@gmail.com','lori@rivergatepharmacy.com','chudek@riverpointrx.com','retailrvp@gmail.com','roarksrx@highland.net','rthenrypharmacy@aol.com','communitydrug@bellsouth.net','watts@acsalaska.net','mkleinrph@aol.com','rossdruginc@gmail.com','ahmed@rxclinicpharmacy.com','salpharmacyrx@gmail.com','tjsrx@comcast.net','pgordon@yahoo.com','phillipsebrell@sanatogapharmacy.com','mikesands@sandsrx.com','sarasotaapothecary@gmail.com','sarasotadiscountpharmacy@gmail.com','savcorx@gmail.com','saveritepharmacy@sbcglobal.net','dan@scalespharmacy.com','sealypharmacy@gmail.com','edthomasrx@gmail.com','barrettpharmd@gmail.com','seymourpharmacy@gmail.com','shakamak.pharmacy@gmail.com','shawnpharmacy@hotmail.com','sheeleysdruginc@aol.com','jason.underwood@shelbyvillepharmacist.com','mlsnodgrass@sheldonsrx.com','ksheldon@sheldonsrx.com','ksheldon@sheldonsrx.com','ksheldon@sheldonsrx.com','shipshewanapharmacy@yahoo.com','david@sierrafamilyrx.com','sierrasanantonio@gmail.com','silverhtpharm@outlook.com','smalltownrx@gmail.com','smithbrothersdrugs@gmail.com','smithfamilypharmacy@gmail.com','smithspharmacy@comcast.net','jamesmonty2@gmail.com','jbarnettejr@yahoo.com','dunlaptim@hotmail.com','pharmacy@southforkpharmacy.com','sdavenport1177@gmail.com','springfielddrugs@hotmail.com','doug@stanleyrx.com','starcare17@gmail.com','stephaniesdownhomepharm@hotmail.com','jbarnettejr@yahoo.com','stonespharmacy@frontiernet.net','lafayettepharmacy@gmail.com','leigh@strawberryhillspharmacy.com','ishan.trivedi@gmai.com','sunrisevillagerx@gmail.com','polkcitysunshinepharmacy@gmail.com','dalilabu@aol.com','sussexpharmacylongneck@gmail.com','staff@sweetgrasspharmacy.com','lisa@tegacaypharmacy.com','thechemistshop@gmail.com','scsrx@aol.com','0952@medicineshoppe.com','watts@acsalaska.net','daltonjl@fairpoint.net','1675@medicineshoppe.com','2016@medicineshoppe.com','msp.usvi@gmail.com','mgdown2@gmail.com','shushmapatel@aol.com','theprescriptionshoppe37188@gmail.com','andrew@thirdaveapothecary.com','billjr@thompsonpharmacy.com','dmiller@mythriftdrugs.com','julie@thriftymedplus.com','christi.robinson14@gmail.com','troy.allen@amerimedpharmacy.com','shanebecker@oldtownpharmacy.com','jpnixon@me.com','tyler@tomahawkpharmacy.com','rlcmanagement@hotmail.com','ablasio@verizon.net','trevor@topekapharmacy.net','zaverrx@gmail.com','wfg98@aol.com','garympeck@gmail.com','kirteshpatel@gmail.com','turtlebaychemist@aol.com','usavewayne@gmail.com','edwins-2@hotmail.com','kim@unionavenuerx.com','treich6110@gmail.com','drpam@consultingwithdrpam.com','universalpharmacy@hotmail.com','uplandrx46989@gmail.com','jbarnettejr@yahoo.com','Arthur.presser@huhs.edu','laurivt@valporx.com','viennadrug@aol.com','rxalol@verizon.net','pavilionelm@yahoo.com','camilla@volunteerpharmacy.com','nathan@vytospharmacy.com','leigh@strawberryhillspharmacy.com','tbruner@ameritech.net','wavelandpharmacy@hotmail.com','rxinvest@aol.com','justin_webber@sbcglobal.net','welcomefamilypharmacy@gmail.com','info@westgordonpharmacy.com','jbarnettejr@yahoo.com','mediservrx@gmail.com','taylor.peoples@anandaprofessional.com','fharper@frontiernet.net','ella1finance@gmail.com','westsidepharmacy255@yahoo.com','claire@wheelercompounding.com','flip@whitleydrugs.com','odokhalil@yahoo.com','chadh@wbhcp.com','chadh@wbhcp.com','sarac@wbhcp.com','twtaylor@williamsburgdrug.com','twtaylor@williamsburgdrug.com','wilmontpharmacy@gmail.com','jbarnettejr@yahoo.com','elliotzan@aol.com','savmordrug@yahoo.com','sarac@wbhcp.com','jbarnettejr@yahoo.com','woodburnpharmacy@bellsouth.net','yatespharmacy@gmail.com','jyoung@myyoungspharmacy.com','zacgvillephcy@gmail.com','jeffsedelmyer@gmail.com'];
    //     foreach($customer_emails as $customer_email) {
    //         $user = get_user_by('email', $customer_email);
    //         if ($user) {
    //             echo $user->ID . '<br/>';
    //             update_user_meta( $user->ID, 'already_bought', '1' );
    //             update_user_meta( $user->ID, 'has_salesforce_checked', '1');
    //         }
    //     }
    //     echo 'DONE';
    //     exit ('');
    // }
    

    if (is_user_logged_in()) {
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
    }
}

