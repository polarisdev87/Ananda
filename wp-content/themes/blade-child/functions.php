<?php

/**
 * Child Theme
 * 
 */
 
function grve_blade_child_theme_setup() {
	
}
add_action( 'after_setup_theme', 'grve_blade_child_theme_setup' );

add_filter( 'wpcf7_validate_configuration', '__return_false' );

function woocommerce_shipstation_export_custom_field_3($meta_key) {
    return 'shipstation_note';
}
add_filter( 'woocommerce_shipstation_export_custom_field_3', 'woocommerce_shipstation_export_custom_field_3');


function is_user_has_role( $role, $user_id = null ) {
    if ( is_numeric( $user_id ) ) {
        $user = get_userdata( $user_id );
    }
    else {
        $user = wp_get_current_user();
    }
    if ( !empty( $user ) ) {
        return in_array( $role, (array) $user->roles );
    }
    else
    {
        return false;
    }
}

add_action( 'woocommerce_new_order', 'create_fpn_note_for_wc_order',  1, 1  );
function create_fpn_note_for_wc_order($order_id) {
    if (is_user_logged_in() && !is_reorder() && is_user_has_role('fpn')) {
        update_post_meta($order_id, 'shipstation_note', 'This is first FPN order.');
        $order = wc_get_order($order_id);
        $order->add_product(wc_get_product(11532), 1, ['subtotal' => 0, 'total' => 0]);
    }
}


// add_action( 'woocommerce_new_order', 'update_order_if_is_on_behalf_of_customer',  1, 1  );
// function update_order_if_is_on_behalf_of_customer($order_id) {
//     if (is_user_logged_in()) {
//         $flag = false;
//         foreach ($_COOKIE as $key => $value) {
//             if(strpos($key, 'wordpress_user_sw_') !== false) $flag = true;
//             if(strpos($key, 'wordpress_user_sw_olduser_') !== false) $flag = true;
//         }
//         if (!$flag) return;
//         update_post_meta($order_id, '_placed_on_behalf_of_customer', '1');
//         exit('');
//     }
// }

function is_user_switched() {
    $flag = false;
    if (is_user_logged_in()) {
        foreach ($_COOKIE as $key => $value) {
            if(strpos($key, 'wordpress_user_sw_') !== false) {
                $flag = true;
                break;
            }
        }
    }
    return $flag;
}

add_action('woocommerce_checkout_create_order', 'before_checkout_create_order', 20, 2);
function before_checkout_create_order( $order, $data ) {
    if (is_user_switched()) {
        $order->update_meta_data( '_placed_on_behalf_of_customer', '1' );

        $old_user = user_switching::get_old_user();
        if ( $old_user instanceof WP_User ) {
            $order->update_meta_data( '_placed_on_behalf_of_customer_by', $old_user->ID );
        }
    }
}

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
function wooc_extra_register_fields_npi() {
    ?>

    <p class="form-row form-row-wide">
    <label for="reg_npi_id"><?php _e( 'NPI #', 'woocommerce' ); ?> <abbr class="required" title="required">*</abbr></label>
    <input type="text" class="input-text" name="npi_id" id="reg_npi_id" value="<?php if ( ! empty( $_POST['npi_id'] ) ) esc_attr_e( $_POST['npi_id'] ); ?>" />
    </p>

    <?php
}
function wooc_extra_register_fields_multistore() {
    ?>

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
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields_npi' );
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields_multistore' );

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

    if ( isset( $_POST['fpn'] ) && $_POST['fpn'] == 1) {
        $user = new WP_User( $customer_id );
        $user->add_role('fpn');
    }
    if ( isset( $_POST['cpc'] ) && $_POST['cpc'] == 1) {
        $user = new WP_User( $customer_id );
        $user->add_role('cpc');
    }
    if ( isset( $_POST['tcg'] ) && $_POST['tcg'] == 1) {
        $user = new WP_User( $customer_id );
        $user->add_role('tcg');
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

function check_if_valid_states() {
    global $woocommerce;
    $msg_states_thc = array( 'OK', 'MS', 'KS' );
    // var_dump(WC()->customer->get_shipping_state());
    $msg_states_all = array( 'OH', 'SD', 'ID', 'NE', 'IA', 'WV', 'AL', 'PR' );

    $items = $woocommerce->cart->get_cart();

    // 10226 - THC Free POS
    // 10251 - THC Free Ticture
    $product_thc = false;
    foreach($items as $item => $values) { 
        $product_id = $values['product_id'];
        // var_dump($product_id);
        if ($product_id == '10251' || $product_id == '10226') {
            $product_thc = true;
            break;
        }
    }
    // var_dump($product_thc);
    if( in_array( WC()->customer->get_shipping_state(), $msg_states_all ) || in_array( WC()->customer->get_billing_state(), $msg_states_all ) || (!$product_thc && in_array( WC()->customer->get_shipping_state(), $msg_states_thc )) || (!$product_thc && in_array( WC()->customer->get_billing_state(), $msg_states_thc )) ) {
        return false;
    }
    return true;
}
  
function show_checkout_notice() {
    if ( !check_if_valid_states() ) {
?>
    <p class="checkout_notice" style="color:red">Thank you for your interest in Ananda Professional.  Although Ananda Professional CBD products are federally legal in all states, due to regulations which exist in your state concerning hemp-derived CBD, we have chosen not to sell our products in your state at this time.  Legislation regarding hemp-derived CBD constantly evolves and we welcome the opportunity to follow up with you once the laws are favorable in your state.</p>
<?php
    }
}

// add_action('woocommerce_checkout_cart_items', 'checkout_cart_items_to_confirm_valid_states');
add_action('woocommerce_checkout_process', 'checkout_cart_items_to_confirm_valid_states');

function checkout_cart_items_to_confirm_valid_states() {
    if ( !check_if_valid_states() ) {
        wc_add_notice( 'Invalid state', 'error');
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


function is_user_switchable() {
    $user = wp_get_current_user();
    return !wp_doing_ajax() && $user && in_array( 'pharmacy_manager', (array) $user->roles );
}


function wpb_woo_my_account_items() {
    $items = [
        'dashboard'          => __( 'Dashboard', 'woocommerce' ),
        'orders'             => __( 'Orders', 'woocommerce' ),
        'edit-address'       => __( 'Addresses', 'woocommerce' ),
        'edit-account'       => __( 'Manage account', 'woocommerce' ),
        // 'downloads'          => __( 'Download MP4s', 'woocommerce' ),
        // 'payment-methods'    => __( 'Payment Methods', 'woocommerce' ),
    ];

    if (is_user_switchable()) {
        $items['login-as-customer'] = __( 'Login as customer', 'woocommerce' );
    }

    $items['customer-logout'] = __( 'Logout', 'woocommerce' );
    return $items;
}
add_filter ( 'woocommerce_account_menu_items', 'wpb_woo_my_account_items' );

function anandap_add_endpoint() {
    add_rewrite_endpoint( 'login-as-customer', EP_PAGES );
}
add_action( 'init', 'anandap_add_endpoint' );

function anandap_login_as_customer_endpoint_content() {
    $customer_args = [ 'role' => 'customer' ];
    $customers = get_users($customer_args);

    $user_switching = user_switching::get_instance();
    ?>
        <style type="text/css">
            .customers_list {}
            .customers_list tbody {
                font-size: 0.9em;
            }
            .customers_list td {
                word-break: break-all;
            }
            .customers_list tbody tr.displayNone {
                display: none;
            }
            .customer_search_box {
                display: flex;
                align-items: center;
                margin-bottom: 1em;
            }
            .customer_search_box span {
                margin-right: 1em;
            }
            #search_customer {
                margin-bottom: 0 !important;
            }
        </style>
        <div class="customer_search_box"><span>Search: </span><input type="text" id="search_customer" placeholder="Search by Company, Name, Email, NPI ..." /></div>
        <table class="customers_list">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>NPI</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($customers as $customer) {

                $display_name = $customer->display_name;
                if ($customer->first_name || $customer->last_name) {
                    $display_name = $customer->first_name . ' ' . $customer->last_name;
                }
                $switch_link = $user_switching->maybe_switch_url($customer);
                ?>
                <tr class="displayNone">
                    <td><?php echo $customer->billing_company; ?></td>
                    <td><a href="<?php echo $switch_link ? $switch_link : '#'; ?>"><?php echo $display_name; ?></a></td>
                    <td style=""><?php echo $customer->user_email; ?></td>
                    <td><?php echo $customer->npi_id; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                var textInput = document.getElementById('search_customer');
                var timeout = null;
                var all_customers = jQuery('.customers_list tbody tr');
                textInput.onkeyup = function (e) {
                    clearTimeout(timeout);
                    timeout = setTimeout(function () {
                        var keyword = textInput.value;
                        all_customers.addClass('displayNone').filter(function (item) {
                            if (!keyword) return false;
                            return jQuery(this).text().toLowerCase().includes(keyword.toLowerCase());
                        }).removeClass('displayNone');
                    }, 500);
                };
            });
        </script>
    <?php
}
add_action( 'woocommerce_account_login-as-customer_endpoint', 'anandap_login_as_customer_endpoint_content' );


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
                    <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account/edit-account/"><span class="grve-item">Manage Account</span></a></li>';

            $user = wp_get_current_user();
            if ( !wp_doing_ajax() && in_array( 'pharmacy_manager', (array) $user->roles ) ) {
                $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account/login-as-customer/"><span class="grve-item">Login as Customer</span></a></li>';
            }

            $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="' . wp_logout_url( home_url() ) . '"><span class="grve-item">Logout</span></a></li>
                </ul></li>';
        } else {
            $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account"><span class="grve-item">Register</span></a></li>';
        }
        $items .= '<li id="menu-item-8499" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8499"><a href="https://anandaprofessional.com/contact/"><span class="grve-item">Contact</span></a></li>';

        if (is_user_switched()) {
            $customer = new WC_Customer(get_current_user_id());
            $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><div class="switched-onto"><span>Logged in as:</span><span style="font-weight: bold;">' . $customer->get_first_name() . ' ' . $customer->get_last_name() . '</span><span style="font-weight: bold;">' . $customer->get_email() . '</span></div></li>';
        }
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

    return is_user_logged_in() && (count( $customer_orders ) >= $loyal_count || $user_already_bought=='1');
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
    if ( !wp_doing_ajax() && in_array( 'pharmacy_manager', (array) $user->roles ) && !in_array('administrator', (array)$user->roles) ) {
        ?>
            <style type="text/css">
                #adminmenu>li {
                    display: none;
                }
                /*#adminmenu>.toplevel_page_asl-plugin, */#adminmenu>.toplevel_page_woocommerce, #adminmenu>.menu-icon-users {
                    display: block;
                }
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li, #adminmenu>.toplevel_page_woocommerce>ul.wp-submenu>li,  #adminmenu>.menu-icon-users>ul.wp-submenu>li {
                    display: none;
                }
                /*#adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li:nth-child(1),
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li:nth-child(2),
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li:nth-child(3),
                #adminmenu>.toplevel_page_asl-plugin>ul.wp-submenu>li:nth-child(6),*/
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
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('body').on('update_checkout', function() {
                jQuery('.checkout_notice').remove();
                jQuery('.grve-woo-error').remove();
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
    if (!is_user_switched()) {
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
        });
    </script>
<?php
    } else {
?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#tax_cert').val('YES');
            jQuery('#tax_cert_field').hide();
        });
    </script>
<?php
    }
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
// $certcapture_client_id = '82190';  // new Ananda Hemp Ecomm credentials
// $certcapture_client_key = 'JRBPBrwrXFPFYhCT';
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

function get_full_state_name($state) {
    $states = ['AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming'];

    return $states[$state];
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

            if (is_user_switched()) return;

            global $certcapture_client_id, $certcapture_client_key, $certcapture_username, $certcapture_password;

            $npi_id = get_user_meta(get_current_user_id(), 'npi_id', true); // cert capture - customer number


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
                    'state' => ['name' => get_full_state_name($post_data['billing_state'])],
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
                        customer.state = '<?php echo get_full_state_name($post_data['billing_state']); ?>';
                        customer.country = 'United States';
                        // customer.country = '<?php echo $post_data['billing_country']; ?>';
                        customer.phone = '<?php echo $post_data['billing_phone']; ?>';
                        customer.zip = '<?php echo $post_data['billing_postcode']; ?>';
                        GenCert.setCustomerData(customer);
                        GenCert.setShipZone('<?php echo get_full_state_name($post_data['billing_state']); ?>');
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
            if (!in_array($product->get_id(), [11525, 11526, 12100, 12102, 12106, 12107])) {
                $tax_class = 'Zero Rate';
            }
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

// define the woocommerce_get_discounted_price callback 
function filter_woocommerce_get_discounted_price( $price, $values, $instance ) { 
    if (is_cart()) {
        return $price;
    }

    $discount_amounts= [];

    // ach discount - 2%
    $chosen_payment_method = WC()->session->get('chosen_payment_method');
    $payment_method = 'cheque';
    if( $payment_method == $chosen_payment_method ){
        $percent = 2; // for ach discount
        $discount_amounts[] = $values['line_subtotal'] / 100 * $percent;
    }

    // tcg, cpc discount - 10% initial, 5% reorder
    if (is_user_has_role('tcg') || is_user_has_role('cpc')) {
        if (!is_reorder()) {
            $discount_amounts[] = $values['line_subtotal'] / 100 * 10;
        } else {
            $discount_amounts[] = $values['line_subtotal'] / 100 * 5;
        }
    }

    $total_discount = 0;
    foreach ($discount_amounts as $discount) {
        $total_discount += $discount;
    }

    return $price - $total_discount;
};

// add the filter 
add_filter( 'woocommerce_get_discounted_price', 'filter_woocommerce_get_discounted_price', 10, 3 ); 

/*
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
        

        // $cart_object->add_discount( 'ach' );
    }
}
*/

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

function add_ach_discount_message() {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    global $woocommerce;
    $cart_object = $woocommerce->cart;

    $chosen_payment_method = WC()->session->get('chosen_payment_method');
    $payment_method = 'cheque';
    if( $payment_method == $chosen_payment_method ){
        $percent = 2; // 15%
        $cart_total = $cart_object->subtotal_ex_tax;
        ?>
            <tr class="cart-discount">
                <th>ACH discount 2%</th>
                <td><?php echo wc_price(-($cart_total / 100) * $percent); ?></td>
            </tr>
        <?php
    }

    if (is_user_has_role('tcg') && !is_reorder()) {
        ?>
            <tr class="cart-discount">
                <th>TCG Initial 10%</th>
                <td><?php echo wc_price(-($cart_total / 100) * 10); ?></td>
            </tr>
        <?php
    }
    if (is_user_has_role('cpc') && !is_reorder()) {
        ?>
            <tr class="cart-discount">
                <th>CPC Initial 10%</th>
                <td><?php echo wc_price(-($cart_total / 100) * 10); ?></td>
            </tr>
        <?php
    }

    if (is_user_has_role('tcg') && is_reorder()) {
        ?>
            <tr class="cart-discount">
                <th>TCG Reorder 5%</th>
                <td><?php echo wc_price(-($cart_total / 100) * 5); ?></td>
            </tr>
        <?php
    }
    if (is_user_has_role('cpc') && is_reorder()) {
        ?>
            <tr class="cart-discount">
                <th>CPC Reorder 5%</th>
                <td><?php echo wc_price(-($cart_total / 100) * 5); ?></td>
            </tr>
        <?php
    }
}
// add_action( 'woocommerce_cart_totals_before_order_total', 'add_ach_discount_message');
add_action( 'woocommerce_review_order_before_order_total', 'add_ach_discount_message');

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


add_action('init', 'runOnInit', 10, 0);
function runOnInit() {
    
    remove_action( 'woocommerce_before_checkout_form', array( $GLOBALS['wcms']->checkout, 'before_checkout_form' ) );

    if (isset($_GET['customers'])) {
        echo '<div style="width: 100%; height: 100%; position: relative; margin: 0; display: flex; align-items: center; justify-content: center; flex-direction: column;">';
        if (isset($_POST['submit']) && $_POST['submit'] == 'Enable') {
            $user = get_user_by('email', $_POST['email']);
            if ($user) {
                update_user_meta( $user->ID, 'already_bought', '1' );
                update_user_meta( $user->ID, 'has_salesforce_checked', '1');
                ?>
                    <div style="margin-bottom: 18px; color: green; font-size: 13px;">Successfully add "Reorder" ability to the use with this email &lt; <?php echo $_POST['email'] ?> &gt;</div>
                <?php
            } else {
                ?>
                    <div style="margin-bottom: 18px; color: red; font-size: 13px;">User with this email &lt; <?php echo $_POST['email'] ?> &gt; does not exist</div>
                <?php
            }
        }
        if($_GET['customers'] == 'enable_reorder') {
            ?>
                <form action="/?customers=enable_reorder" method="post">
                    <fieldset style="padding: 25px; margin-top: 12px;" >
                        <legend>Enable Reorder Feature</legend>
                        <span>Enter email address: </span><input type="email" name="email" size="35" required />
                        <input type="submit" name="submit" value="Enable" />
                    </fieldset>
                </form>
            <?php
        }
        echo '</div>';
        exit('');
    }

    if (isset($_GET['salesforce'])) {
        $salesforce = new SalesforceSDK(isset($_GET['env']) && $_GET['env']=='sandbox', isset($_GET['debug']) && $_GET['debug']==1);

        switch ($_GET['salesforce']) {
            case 'auth':
                $auth = $salesforce->get_auth();
                echo '<pre>', var_dump($auth), '</pre>';
                break;
            case 'token':
                $token = $salesforce->get_token();
                echo '<pre>', var_dump($token), '</pre>';
                break;
            case 'describe':
                $response = $salesforce->describe($_GET['table']);
                echo '<pre>', var_dump($response), '</pre>';
                break;
            case 'get_invoice':
                $response = $salesforce->get_invoice_by_id($_GET['ID']);
                echo '<pre>', var_dump($response), '</pre>';
                break;
            case 'get_invoices':
                $invoices = $salesforce->get_all_invoices('INV-', 2017);
                echo '<pre>', var_dump($invoices), '</pre>';
                break;
            case 'get_salesforce_invoices':
                $invoices = $salesforce->get_all_salesforce_invoices($_GET['ID']);
                echo '<pre>', var_dump($invoices), '</pre>';
                break;
            case 'create_contact':
                $response = $salesforce->get_account_from_external_xero_contact_id($_GET['ID']);
                echo '<pre>', var_dump($response), '</pre>';
                break;
            case 'create_account_from_xero_contact_id':
                $response = $salesforce->create_account_from_xero_contact_id($_GET['ID']);
                echo '<pre>', var_dump($response), '</pre>';
                break;
            case 'get_contact':
                $response = $salesforce->get_contact_by_id($_GET['ID']);
                echo '<pre>', var_dump($response), '</pre>';
                break;
            case 'get_contact_by_email':
                $response = $salesforce->get_contact_by_email($_GET['email']);
                echo '<pre>', var_dump($response), '</pre>';
                break;
            case 'migrate_contacts':
                $salesforce->migrate_contacts();
                break;
            case 'migrate_invoices':
                $salesforce->migrate_invoices($_GET['ID'] ?: '');
                break;
            case 'update_invoices':
                $salesforce->migrate_invoices('', '2018');
                // $salesforce->migrate_invoices('INV-', '2017');
                // $salesforce->migrate_invoices('CN-', '2017');
                // $salesforce->migrate_invoices('AE-', '2017');
                break;
            case 'create_invoice_from_quote':
                $salesforce->create_invoice_from_quote($_GET['ID'] ?: '');
                break;
            case 'get_accounts':
                $response = $salesforce->get_all_accounts();
                echo '<pre>', var_dump($response), '</pre>';
                break;
            case 'get_stores':
                $response = $salesforce->get_all_stores();
                echo '<pre>', var_dump($response), '</pre>';
                break;
            case 'migrate_stores':
                $salesforce->migrate_stores();
                break;
            case 'optin_store':
                $response = $salesforce->upsert_store_with_salesforce($_REQUEST);
                echo '<script type="text/javascript">window.close();</script>';
                // echo $response;
                break;
            case 'optout_store':
                $response = $salesforce->delete_store($_REQUEST);
                echo '<script type="text/javascript">window.close();</script>';
                // echo $response;
                break;
            case 'confirm_payment':
                $response = $salesforce->confirm_payment($_REQUEST);
                echo '<script type="text/javascript">window.close();</script>';
                // echo $response;
                break;
            case 'update_accounts':
                $salesforce->update_accounts();
                break;
            case 'update_account_test':
                $salesforce->update_account_test();
                break;
            case 'drop_invalid_accounts':
                $salesforce->drop_invalid_accounts();
                break;
            case 'pull_xero_contacts_in_table_format':
                $salesforce->pull_xero_contacts_in_table_format();
                break;
            case 'update_xero_contacts_name':
                $salesforce->update_xero_contacts_name();
                break;
            case 'update_salesforce_accounts_name':
                $salesforce->update_salesforce_accounts_name();
                break;
            case 'reset_salesforce_store_id':
                $salesforce->reset_salesforce_store_id();
                break;
            case 'check_missing_xero_invoices':
                $salesforce->check_missing_xero_invoices();
                break;
            case 'migrate_trackings':
                $salesforce->migrate_trackings();
                break;
            default:
                var_dump('no actions');
                break;
        }

        exit('');
    }

    if (isset($_GET['action_name'])) {
        switch ($_GET['action_name']) {
            case 'subscribe_red_states':
                update_user_meta( $_POST['customer_id'], 'subscribe_red_states', '1' );
                echo 'subscribe_red_states - updated for #' . $_POST['customer_id'];
                break;
            case 'show_red_states_subscribers':
                $users = get_users(['meta_key' => 'subscribe_red_states', 'meta_value' => '1']);
                echo '<table border=1 width="100%"><thead><tr><th>Company</th><th>Contact Name</th><th>Address</th><th>Email</th><th>Registered</th></tr></thead><tbody>';
                foreach ($users as $user) {
                    $customer = new WC_Customer($user->data->ID);
                    echo '<tr>';
                    echo '<td>'. $customer->get_billing_company() .'</td>';
                    echo '<td>'. $customer->get_first_name() . ' ' . $customer->get_last_name() . '(' . $customer->get_username() . ')</td>';
                    echo '<td>'. $customer->get_shipping_address() . ', ' . $customer->get_shipping_city() . ', ' . $customer->get_shipping_state() . ' ' . $customer->get_shipping_postcode() .'</td>';
                    echo '<td>'. $customer->get_email() .'</td>';
                    echo '<td>'. $user->data->user_registered .'</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
                break;
            default:
                echo 'no action';
                break;
        }
        exit('');
    }
    

    if (is_user_logged_in()) {
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
    }
}



add_filter( 'cron_schedules', 'schedule_salesforce_migration_interval' ); 
function schedule_salesforce_migration_interval( $schedules ) {
    $schedules['salesforce_migration_interval'] = array(
        'interval' => 60 * 60 * 2, // seconds
        'display'  => esc_html__( 'Every 2 Hours' ),
    );
    return $schedules;
}

if (! wp_next_scheduled ( 'salesforce_invoice_migration_hook' )) {
    wp_schedule_event(time(), 'hourly', 'salesforce_invoice_migration_hook');
}

add_action('salesforce_invoice_migration_hook', 'salesforce_invoice_migration_exec');
function salesforce_invoice_migration_exec() {
    $salesforce = new SalesforceSDK();
    $salesforce->migrate_invoices('WP-', '2018');
    $salesforce->migrate_invoices('INV-', '2018');
    $salesforce->migrate_invoices('CN-', '2018');
    $salesforce->migrate_invoices('AE-', '2018');
}



if (! wp_next_scheduled ( 'salesforce_account_name_npi_hook' )) {
    wp_schedule_event(time(), 'hourly', 'salesforce_account_name_npi_hook');
}

add_action('salesforce_account_name_npi_hook', 'salesforce_account_name_npi_exec');
function salesforce_account_name_npi_exec() {
    // $salesforce = new SalesforceSDK();
    // $salesforce->update_salesforce_accounts_name();
}



if (! wp_next_scheduled ( 'salesforce_billing_address_update_hook' )) {
    wp_schedule_event(time(), 'quarterdaily', 'salesforce_billing_address_update_hook');
}

add_action('salesforce_billing_address_update_hook', 'salesforce_billing_address_update_exec');
function salesforce_billing_address_update_exec() {
    // $salesforce = new SalesforceSDK();
    // $salesforce->update_accounts();
}


add_action( 'wp_enqueue_scripts', 'anandap_frontend_scripts' );
function anandap_frontend_scripts() {
    $grve_ver = BLADE_GRVE_THEME_MAJOR_VERSION . '.' . BLADE_GRVE_THEME_MINOR_VERSION . '.' . BLADE_GRVE_THEME_HOTFIX_VERSION;
    wp_enqueue_style( 'anandap-slick-carousel-css', get_template_directory_uri() . '/css/slick.css', array(), esc_attr( $grve_ver ) );
    // wp_enqueue_style( 'anandap-slick-carousel-theme-css', get_template_directory_uri() . '/css/slick-theme.css', array(), esc_attr( $grve_ver ) );
    wp_enqueue_script( 'anandap-slick-carousel-js', get_template_directory_uri() . '/js/slick.min.js', array( 'jquery' ), esc_attr( $grve_ver ), true );
}

function my_custom_wc_free_shipping_notice() {
    if ( ! is_cart() ) {
        return;
    }

    $cart_total = WC()->cart->get_displayed_subtotal();
    if ( WC()->cart->display_prices_including_tax() ) {
        $cart_total = round( $cart_total - ( WC()->cart->get_discount_total() + WC()->cart->get_discount_tax() ), wc_get_price_decimals() );
    } else {
        $cart_total = round( $cart_total - WC()->cart->get_discount_total(), wc_get_price_decimals() );
    }
    $min_amount = 500;
    if ( !empty( $min_amount ) && $cart_total < $min_amount ) {
        $remaining = $min_amount - $cart_total;
        wc_add_notice( sprintf( '<div class="free-shipping-notice">Add %s more to get free shipping!</div>', wc_price( $remaining ) ) );
    }
}
add_action( 'wp', 'my_custom_wc_free_shipping_notice' );


add_action( 'wp_head', 'hide_marketing_materials' );
function hide_marketing_materials() {
    if (is_reorder()) return;
    ?>
        <style type="text/css">
            #menu-item-11530 { display: none }
        </style>
    <?php
}

add_filter( 'woocommerce_email_styles', 'anandap_woocommerce_email_styles' );
function anandap_woocommerce_email_styles( $css ) {
    $css .= ".customer-new-template #template_header_image img {height: 40px;} .customer-new-template #header_wrapper h1 {color:white; text-align:center;}";
    return $css;
}
