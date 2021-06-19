<?php
/**
 * Orders
 *
 * Admin field locations:
 *		do_action( 'pmpro_after_order_settings_table', $order );
 *
 * Markup:
 *		<h2>Group label</h2>
 *		<table class="form-table">
 *
 * Save hook:
 *		do_action( 'pmpro_updated_order', $order );
 *		do_action( 'pmpro_added_order', $order );
 *
 * Meta:
 *		get_pmpro_membership_order_meta( $order_id, 'meta_key', $single );
 *		add_pmpro_membership_order_meta( $order_id, 'meta_key', 'value' );
 *		update_pmpro_membership_order_meta( $order_id, 'meta_key', 'value' );
 *		delete_pmpro_membership_order_meta( $order_id, 'meta_key' );
 */

add_action( 'pmpro_after_order_settings_table', static function( $order ) {
	pods_form_render_fields( 'pmpro_membership_order', $order->id, [
		//'section' => 'after_order_settings_table',
	] );
} );

add_action( 'pmpro_added_order', static function( $order ) {
	pods_form_save_submitted_fields( 'pmpro_membership_order', $order->id );
} );

add_action( 'pmpro_updated_order', static function( $order ) {
	pods_form_save_submitted_fields( 'pmpro_membership_order', $order->id );
} );
