<?php
/**
 * Member Checkout Fields
 *
 * Frontend field locations:
 *      do_action( 'pmpro_checkout_after_level_cost', $user );
 *      do_action( 'pmpro_checkout_after_pricing_fields', $user );
 *      do_action( 'pmpro_checkout_after_username', $user );
 *      do_action( 'pmpro_checkout_after_password', $user );
 *      do_action( 'pmpro_checkout_after_email', $user );
 *      do_action( 'pmpro_checkout_after_billing_fields', $user );
 *      do_action( 'pmpro_checkout_after_payment_information_fields', $user );
 *      do_action( 'pmpro_checkout_after_tos_fields', $user );
 *      do_action( 'pmpro_checkout_after_captcha', $user );
 *      do_action( 'pmpro_checkout_before_submit_button', $user );
 *
 * Markup:
 *		<hr />
 *		<h3>Group label</h3>
 *		<table class="form-table">
 *
 * Save hook:
 *      do_action( 'pmpro_after_checkout', $user_id );
 *      do_action( 'pmpro_before_send_to_paypal_standard', $user_id );
 *      do_action( 'pmpro_before_send_to_twocheckout', $user_id );
 *      do_action( 'pmpro_before_send_to_gourl', $user_id );
 *      do_action( 'pmpro_before_send_to_payfast', $user_id );
 *
 * Meta:
 *        get_user_meta( $user_id, 'meta_key', $single );
 *        add_user_meta( $user_id, 'meta_key', 'value' );
 *        update_user_meta( $user_id, 'meta_key', 'value' );
 *        delete_user_meta( $user_id, 'meta_key' );
 */

add_action( 'pmpro_checkout_after_level_cost', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_level_cost',
		'render'        => 'div-rows',
		'heading'       => 'h3',
	] );
} );

add_action( 'pmpro_checkout_after_pricing_fields', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_pricing_fields',
		'render'        => 'div-rows',
		'heading'       => 'h3',
	] );
} );

add_action( 'pmpro_checkout_after_username', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_username',
		'render'        => 'div-rows',
		// No heading to show here.
		'heading'       => null,
	] );
} );

add_action( 'pmpro_checkout_after_password', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_password',
		'render'        => 'div-rows',
		// No heading to show here.
		'heading'       => null,
	] );
} );

add_action( 'pmpro_checkout_after_email', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_email',
		'render'        => 'div-rows',
		// No heading to show here.
		'heading'       => null,
	] );
} );

add_action( 'pmpro_checkout_after_billing_fields', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_billing_fields',
		'render'        => 'div-rows',
		'heading'       => 'h3',
	] );
} );

add_action( 'pmpro_checkout_after_payment_information_fields', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_payment_information_fields',
		'render'        => 'div-rows',
		'heading'       => 'h3',
	] );
} );

add_action( 'pmpro_checkout_after_tos_fields', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_tos_fields',
		'render'        => 'div-rows',
		'heading'       => 'h3',
	] );
} );

add_action( 'pmpro_checkout_after_captcha', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'after_captcha',
		'render'        => 'div-rows',
		'heading'       => 'h3',
	] );
} );

add_action( 'pmpro_checkout_before_submit_button', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_checkout',
		'section'       => 'before_submit_button',
		'render'        => 'div-rows',
		'heading'       => 'h3',
	] );
} );

add_action( 'TBD_TBD_TBD', static function( $user_id ) {
	// @todo Figure out the hook.
	pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id );
} );

/**
 * Sessions vars for PayPal Express
 */
function pmpro_pods_paypalexpress_session_vars_for_user_fields() {
	global $pmpro_user_fields;

	//save our added fields in session while the user goes off to PayPal
	if ( ! empty( $pmpro_user_fields ) ) {
		//cycle through groups
		foreach ( $pmpro_user_fields as $where => $fields ) {
			//cycle through fields
			foreach ( $fields as $field ) {
				if ( ! pmpro_is_field( $field ) ) {
					continue;
				}

				if ( ! pmpro_check_field_for_level( $field, 'profile', $user_id ) ) {
					continue;
				}

				if ( isset( $_REQUEST[ $field->name ] ) ) {
					$_SESSION[ $field->name ] = $_REQUEST[ $field->name ];
				} elseif ( isset( $_FILES[ $field->name ] ) ) {
					/*
						We need to save the file somewhere and save values in $_SESSION
					*/

					//check for a register helper directory in wp-content
					$upload_dir  = wp_upload_dir();
					$pmprorh_dir = $upload_dir['basedir'] . '/pmpro-register-helper/tmp/';

					//create the dir and subdir if needed
					if ( ! is_dir( $pmprorh_dir ) ) {
						wp_mkdir_p( $pmprorh_dir );
					}

					//move file
					$new_filename = $pmprorh_dir . basename( $_FILES[ $field->name ]['tmp_name'] );
					move_uploaded_file( $_FILES[ $field->name ]['tmp_name'], $new_filename );

					//update location of file
					$_FILES[ $field->name ]['tmp_name'] = $new_filename;

					//save file info in session
					$_SESSION[ $field->name ] = $_FILES[ $field->name ];
				}
			}
		}
	}
}

add_action( 'pmpro_paypalexpress_session_vars', 'pmpro_paypalexpress_session_vars_for_user_fields' );
add_action( 'pmpro_before_send_to_twocheckout', 'pmpro_paypalexpress_session_vars_for_user_fields', 10, 0 );
