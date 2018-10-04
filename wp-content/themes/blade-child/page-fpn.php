<?php
/**
 * Template Name: FPN Member
 */

?>

<?php
	if ( is_user_logged_in() ) {
		header("Location: /my-account");
	} else {
?>

<?php get_header(); ?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php remove_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields_multistore' ); ?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<style type="text/css">
	.u-columns {
		display: flex;
	}
	.u-column1.col-1 {
		margin-right: 30px;
	}
</style>

<div class="u-columns col2-set" id="customer_login">

	<div class="u-column1 col-1">
		<table>
			<thead>
				<tr><th>GPO</th></tr>
			</thead>
			<tbody>
				<tr><td>American Associated Pharmacies, Inc.</td></tr>
				<tr><td>American Pharmacies</td></tr>
				<tr><td>American Pharmacy Services Corp.</td></tr>
				<tr><td>Compliant Pharmacy Alliance</td></tr>
				<tr><td>EPIC Pharmacies</td></tr>
				<tr><td>Independent Pharmacy Alliance, Inc.</td></tr>
				<tr><td>Independent Pharmacy Buying Group, Inc.</td></tr>
				<tr><td>Independent Pharmacy Cooperative</td></tr>
				<tr><td>Keystone Pharmacy Purchasing Alliance, Inc.</td></tr>
				<tr><td>Northeast Pharmacy Services Corporation, Inc.</td></tr>
				<tr><td>PBA Health</td></tr>
				<tr><td>Pharmacy Franchisees and Owners Association</td></tr>
				<tr><td>Pharmacy Management Partners</td></tr>
				<tr><td>Pharmacy Providers of Oklahoma, Inc.</td></tr>
				<tr><td>PPSC</td></tr>
				<tr><td>Quality Care Pharmacies</td></tr>
				<tr><td>RxPlus Pharmacies, Inc.</td></tr>
				<tr><td>Smart-Fill Management Group</td></tr>
				<tr><td>Southern Pharmacy Cooperative</td></tr>
				<tr><td>Western States Pharmacy Coalition</td></tr>
				<tr><td>Sav-Mor Pharmacy Services</td></tr>
			</tbody>
		</table>
	</div>
	<div class="u-column1 col-2">

		<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>
		<p>FOR NEW CUSTOMERS.</p>

		<form method="post" class="register">

			<input type="hidden" name="fpn" value="1" />

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <!--<span class="required">*</span>--></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?>" />
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php _e( 'Email', 'woocommerce' ); ?> <!--<span class="required">*</span>--></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( $_POST['email'] ) : ''; ?>" />
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <!--<span class="required">*</span>--></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" />
				</p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="woocommerce-FormRow form-row">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<input type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>" />
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

<?php do_action( 'woocommerce_after_main_content' ); ?>
		
<?php get_footer(); ?>

<?php
	}
?>
