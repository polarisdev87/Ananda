<?php
/**
 * Greatives Redux Extension Loader
 * @version	1.0
 * @author		Greatives Team
 * @URI		http://greatives.eu
 * */

if(!function_exists('blade_grve_redux_register_custom_extension_loader')) :
    function blade_grve_redux_register_custom_extension_loader($ReduxFramework) {
        $path = get_template_directory() . '/includes/admin/extensions/';
            $folders = scandir( $path, 1 );
            foreach ( $folders as $folder ) {
                if ( $folder === '.' or $folder === '..' or ! is_dir( $path . $folder ) ) {
                    continue;
                }
                $extension_class = 'ReduxFramework_Extension_' . $folder;
                if ( ! class_exists( $extension_class ) ) {
                    $class_file = $path . $folder . '/extension_' . $folder . '.php';
                    $class_file = apply_filters( 'redux/extension/' . $ReduxFramework->args['opt_name'] . '/' . $folder, $class_file );
                    if ( $class_file ) {
                        require_once $class_file;
                    }
                }
                if ( ! isset( $ReduxFramework->extensions[ $folder ] ) ) {
                    $ReduxFramework->extensions[ $folder ] = new $extension_class( $ReduxFramework );
                }
            }
    }
    add_action("redux/extensions/grve_blade_options/before", 'blade_grve_redux_register_custom_extension_loader', 0);
	add_action("redux/extensions/um_options/before", 'blade_grve_redux_register_custom_extension_loader', 0);

endif;

function blade_grve_redux_scripts() {
	if ( !wp_script_is( 'select2-js', 'registered' ) ) {
		wp_register_script( 'select2-js', get_template_directory_uri() . '/includes/admin/extensions/vendor_support/vendor/select2/select2.js', array( 'jquery', 'redux-select2-sortable-js' ), time(), true );
		wp_register_style( 'select2-css', get_template_directory_uri() . '/includes/admin/extensions/vendor_support/vendor/select2/select2.css', array(), time(), 'all' );
		wp_enqueue_style( 'select2-css' );
	}
}
add_action( "redux/page/grve_blade_options/enqueue", 'blade_grve_redux_scripts' );

function blade_grve_cdn_fix( $args ) {
	$args['use_cdn'] = false;
	return $args;
}
add_filter("redux/options/um_options/args", 'blade_grve_cdn_fix', 11 );

//Omit closing PHP tag to avoid accidental whitespace output errors.
