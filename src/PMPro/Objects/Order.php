<?php

namespace PMPro_Pods\PMPro\Objects;

/**
 * PMPro Membership Order object hooks.
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
 *
 * @since   1.0.0
 * @package PMPro_Pods
 */
class Order {

	/**
	 * Add hooks for class.
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		add_action( 'pmpro_after_order_settings_table', [ $this, 'pmpro_after_order_settings_table' ] );
		add_action( 'pmpro_added_order', [ $this, 'pmpro_added_order' ] );
		add_action( 'pmpro_updated_order', [ $this, 'pmpro_updated_order' ] );
	}

	/**
	 * Remove hooks from class.
	 *
	 * @since 1.0.0
	 */
	public function unhook() {
		remove_action( 'pmpro_after_order_settings_table', [ $this, 'pmpro_after_order_settings_table' ] );
		remove_action( 'pmpro_added_order', [ $this, 'pmpro_added_order' ] );
		remove_action( 'pmpro_updated_order', [ $this, 'pmpro_updated_order' ] );
	}

	/**
	 * Render the fields for the order form.
	 *
	 * @since 1.0.0
	 *
	 * @param object $order The order object.
	 */
	public function pmpro_after_order_settings_table( $order ) {
		pods_form_render_fields( 'pmpro_membership_order', $order->id, [
			//'section' => 'after_order_settings_table',
		] );
	}

	/**
	 * Handle saving the submitted fields for the object when adding an order.
	 *
	 * @since 1.0.0
	 *
	 * @param object $order The order object.
	 */
	public function pmpro_added_order( $order ) {
		pods_form_save_submitted_fields( 'pmpro_membership_order', $order->id );
	}

	/**
	 * Handle saving the submitted fields for the object when editing an order.
	 *
	 * @since 1.0.0
	 *
	 * @param object $order The order object.
	 */
	public function pmpro_updated_order( $order ) {
		pods_form_save_submitted_fields( 'pmpro_membership_order', $order->id );
	}

}
