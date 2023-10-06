<?php
/*
Plugin Name: Paid Memberships Pro - Pods Add On
Plugin URI: https://www.paidmembershipspro.com/add-ons/pods-integration/
Description: Integrates Paid Memberships Pro with the Pods Framework to add groups of fields to many areas of your membership site including Member Profiles, the Membership Checkout page, and admin screens including Membership Levels and Orders.
Version: 1.0.3
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com/
Text Domain: pmpro-pods
*/

namespace PMPro_Pods;

use PMPro_Pods\PMPro\Objects\Level;
use PMPro_Pods\PMPro\Objects\Member_Checkout;
use PMPro_Pods\PMPro\Objects\Member_Profile;
use PMPro_Pods\PMPro\Objects\Order;
use PMPro_Pods\Pods\Integration;
use PMPro_Pods\Pods\Meta_Compatibility;
use PMPro_Pods\Pods\Permissions;

define( 'PMPRO_PODS_VERSION', '1.0.3' );
define( 'PMPRO_PODS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoload the classes for our namespace.
 *
 * @since 1.0.0
 *
 * @param string $class The class name to load.
 */
function pmpro_pods_autoload( $class ) {
	// Check that the class starts with our namespace.
	if ( 0 !== strpos( $class, 'PMPro_Pods\\' ) ) {
		return;
	}

	// Replace \ with / for folders.
	$class_path = str_replace( '\\', DIRECTORY_SEPARATOR, $class );

	// Remove the namespace root.
	$class_path = str_replace( 'PMPro_Pods' . DIRECTORY_SEPARATOR, '', $class_path );

	// Build file path.
	$file = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class_path . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}
}

spl_autoload_register( '\PMPro_Pods\pmpro_pods_autoload' );

/**
 * Handle init of the plugin hooks.
 *
 * @since 1.0.0
 */
function pmpro_pods_init() {
	if ( ! defined( 'PMPRO_VERSION' ) || version_compare( PMPRO_VERSION, '2.6', '<=' ) ) {
		add_action( 'admin_notices', static function() {
			echo '<div class="notice notice-error"><p>';
			echo sprintf(
				esc_html__( 'The %1$s requires %2$s installed and activated to be used.', 'pmpro-pods' ),
				'<strong>Paid Memberships Pro - Pods Add On</strong>',
				'<a href="' . esc_url( admin_url( 'plugin-install.php?s=Paid+Memberships+Pro&tab=search&type=term' ) ) . '">Paid Memberships Pro</a> 2.6+'
			);
			echo '</p></div>';
		} );

		return;
	}

	if ( ! defined( 'PODS_VERSION' ) || version_compare( PODS_VERSION, '2.8-b-1', '<=' ) ) {
		add_action( 'admin_notices', static function() {
			echo '<div class="notice notice-error"><p>';
			echo sprintf(
				esc_html__( 'The %1$s requires %2$s installed and activated to be used.', 'pmpro-pods' ),
				'<strong>Paid Memberships Pro - Pods Add On</strong>',
				'<a href="' . esc_url( admin_url( 'plugin-install.php?s=Pods+Framework&tab=search&type=term' ) ) . '">Pods Framework</a> 2.8+'
			);
			echo '</p></div>';
		} );

		return;
	}

	/*
	 * Hook into Pods where it needs it to support our custom objects.
	 */
	$pods_integration = new Integration();
	$pods_integration->hook();

	/*
	 * Handle object permissions to show/hide based on our pod/group/field PMPro options.
	 */
	$pods_permissions = new Permissions();
	$pods_permissions->hook();

	/*
	 * PMPro Member / User meta compatibility.
	 */
	$pods_meta_compatibility = new Meta_Compatibility();
	$pods_meta_compatibility->hook();

	/*
	 * The object form/saving hooks.
	 */
	$level_object = new Level();
	$level_object->hook();

	$member_profile_object = new Member_Profile();
	$member_profile_object->hook();

	$member_checkout_object = new Member_Checkout();
	$member_checkout_object->hook();

	$order_object = new Order();
	$order_object->hook();
}

add_action( 'init', '\PMPro_Pods\pmpro_pods_init' );
