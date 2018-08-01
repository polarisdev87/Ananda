<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility with `woocommerce-customer-order-csv-export`.
 */
class WC_MS_Customer_Order_Csv_Export {

	/**
	 * Plugin reference.
	 *
	 * @var WC_Ship_Multiple
	 */
	private $wcms;

	/**
	 * Constructor
	 */
	public function __construct( WC_Ship_Multiple $wcms ) {
		$this->wcms = $wcms;

		add_filter( 'wc_customer_order_csv_export_order_headers', array( $this, 'modify_column_headers' ), 10, 1 );
		add_filter( 'wc_customer_order_csv_export_order_row', array( $this, 'modify_row_data' ), 10, 3 );
	}

	/**
	 * Method for adding an additional header for S2MA.
	 *
	 * @param  array $column_headers
	 * @return array
	 *
	 * @since 3.6.0
	 */
	public function modify_column_headers( $column_headers ) {
		$column_headers['wcms'] = __( 'Multiple Shipping', 'wc_shipping_multiple_address' );
		return $column_headers;
	}

	/**
	 * Method for adding an additional row data for S2MA.
	 *
	 * @param array $order_data
	 * @param \WC_Order $order WC Order object
	 *
	 * @since 3.6.0
	 */
	public function modify_row_data( $order_data, $order, $csv_generator ) {
		$order_id           = WC_MS_Compatibility::get_order_prop( $order, 'id' );
		$shipping_addresses = get_post_meta( $order_id, '_shipping_addresses' );

		if ( empty( $shipping_addresses[0] ) ) {
			return $order_data;
		}

		$packages  = get_post_meta( $order_id, '_wcms_packages', true );

		$addresses = array_map( function( $package ) {
			$retval  = '';
			$address = wcms_get_address( $package['destination'] );

			$address = array_map( function( $key, $value ) {
				return sprintf( '%s:%s', $key, $value );
			}, array_keys( $address ), $address );

			return implode( '|', $address );
		}, $packages );

		$addresses = implode( ';', $addresses );

		$custom_data      = array(
			'wcms' => $addresses,
		);

		$new_order_data   = array();

		if ( $this->is_one_row( $csv_generator ) ) {
			foreach ( $order_data as $data ) {
				$new_order_data[] = array_merge( (array) $data, $custom_data );
			}
		} else {
			$new_order_data = array_merge( $order_data, $custom_data );
		}

		return $new_order_data;
	}

	/**
	 * Helper function to check the export format
	 *
	 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator the generator instance
	 * @return bool - true if this is a one row per item format
	 *
	 * @since 3.6.0
	 */
	public function is_one_row( $csv_generator ) {
		$one_row_per_item = false;
		if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {
			// pre 4.0 compatibility
			$one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );
		} elseif ( isset( $csv_generator->format_definition ) ) {
			// post 4.0 (requires 4.0.3+)
			$one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
		}
		return $one_row_per_item;
	}
}
