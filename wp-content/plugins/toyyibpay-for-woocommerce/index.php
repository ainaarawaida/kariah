<?php
/**
 * Plugin Name: toyyibPay for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/toyyibpay-for-woocommerce/#installation
 * Description: Integrate your WooCommerce site with toyyibPay Payment Gateway.
 * Version: 1.3.0
 * Author: toyyibPay
 * Author URI: https://toyyibpay.com
 * tested up to: 5.5.1
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

# Include toyyibPay Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'toyyibpay_init', 0 );

function toyyibpay_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/toyyibpay.php' );

	add_filter( 'woocommerce_payment_gateways', 'add_toyyibpay_to_woocommerce' );
	function add_toyyibpay_to_woocommerce( $methods ) {
		$methods[] = 'toyyibPay';

		return $methods;
	}
}

# Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'toyyibpay_links' );

function toyyibpay_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=toyyibpay' ) . '">' . __( 'Settings', 'toyyibpay' ) . '</a>',
	);

	# Merge our new link with the default ones
	return array_merge( $plugin_links, $links );
}

function requery_toyyibpay($BillCode, $OrderId) {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}
	include_once( 'src/toyyibpay.php' );

	$toyyibpay = new toyyibpay();
	$toyyibpay->cron_requery($BillCode, $OrderId);
}

add_action( 'init', 'toyyibpay_check_response', 15 );

function toyyibpay_check_response() {
	# If the parent WC_Payment_Gateway class doesn't exist it means WooCommerce is not installed on the site, so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include_once( 'src/toyyibpay.php' );

	$toyyibpay = new toyyibpay();
	$toyyibpay->check_toyyibpay_response();
	$toyyibpay->check_toyyibpay_callback();
	
}
