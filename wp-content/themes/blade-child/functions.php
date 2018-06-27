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
    <label for="reg_npi_id"><?php _e( 'NPI #', 'woocommerce' ); ?> <!--<span class="required">*</span>--></label>
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
        // $npi_response = json_decode(file_get_contents('https://npiregistry.cms.hhs.gov/api/?number=' . $_POST['npi_id']));
        // if (!$npi_response->result_count) {
        //     $errors->add( 'npi_id_error', __( 'This NPI # does not exist!' . $npi_response->result_count, 'woocommerce' ) );
        // }
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
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields', 10);

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields( $fields ) {
     $fields['billing']['billing_city']['label'] = 'City / Town';
     $fields['shipping']['shipping_city']['label'] = 'City / Town';
     return $fields;
}


// Hook in
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

// Our hooked in function - $address_fields is passed via the filter!
function custom_override_default_address_fields( $address_fields ) {
     $address_fields['city']['label'] = 'City / Town';

     return $address_fields;
}

add_filter( 'wp_nav_menu_items', 'add_membership_only_links', 0, 2 );

function add_membership_only_links( $items, $args ) {

    if ( $args->theme_location != 'grve_header_nav' ) {
        return $items;
    }
    
    if (  wc_memberships_is_user_active_member( get_current_user_id(), 'pharmacist') ) {
        $items .= '<li><a href="/product/reorder-bundle-product/">' . __( 'Re-Order' ) . '</a></li>';
    } 

    if ( wc_memberships_is_user_active_member( get_current_user_id(), 'sales-rep') ) {
        $items .= '<li><a href="/sales-rep-samples/">' . __( 'Sales Rep Samples' ) . '</a></li>';
    }

    return $items;
}

add_action( 'woocommerce_review_order_before_cart_contents', 'show_checkout_notice', 12 );
  
function show_checkout_notice() {
    global $woocommerce;
    $msg_states = array( 'AR','IA','ID','KS','OK','PR' );
    ob_start();
    echo '<p class="checkout_notice" style="color:red">Thank you for your interest in Ananda Professional.  Although Ananda Professional CBD products are federally legal in all states, due to regulations which exist in your state concerning hemp-derived CBD, we have chosen not to sell our products in your state at this time.  Legislation regarding hemp-derived CBD constantly evolves and we welcome the opportunity to follow up with you once the laws are favorable in your state.</p>';
    if( in_array( WC()->customer->get_shipping_state(), $msg_states ) ) { 
        ob_end_flush();
    } else ob_end_clean();
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
    if (is_user_logged_in() && $args->theme_location == 'grve_header_nav') {
        $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account"><span class="grve-item">My Account</span></a></li>';
    }
    elseif (!is_user_logged_in() && $args->theme_location == 'grve_header_nav') {
        $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="/my-account"><span class="grve-item">Register</span></a></li>';
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

        ?>
            <h3><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>
        <?php

        foreach ($checkout->checkout_fields['billing'] as $key => $field) :
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
    function custom_validation()
    {
        global $woocommerce;
        $address = $_POST;

        foreach ($address as $key => $field) :

            // Validation: Required fields
            if(startsWith($key,'billing_'))
            {

                if($key == 'billing_country' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please select a country.', 'woocommerce' ) );
                }

                if($key == 'billing_first_name' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter first name.', 'woocommerce' ) );
                }

                if($key == 'billing_last_name' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter last name.', 'woocommerce' ) );
                }

                if($key == 'billing_address_1' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter address.', 'woocommerce' ) );
                }

                if($key == 'billing_city' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter city.', 'woocommerce' ) );
                }

                if($key == 'billing_state' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter state.', 'woocommerce' ) );
                }

                if($key == 'billing_postcode' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter a postcode.', 'woocommerce' ) );
                }

                if($key == 'billing_email' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter billing email address.', 'woocommerce' ) );
                }

                if($key == 'billing_phone' && $field == '')
                {
                    $woocommerce->add_error( '' . __( 'ERROR', 'woocommerce' ) . ': ' . __( 'Please enter phone number.', 'woocommerce' ) );
                }
            }

        endforeach;
    }
    add_action('register_post','custom_validation');

}
