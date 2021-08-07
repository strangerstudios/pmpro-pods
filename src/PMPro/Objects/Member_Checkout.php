<?php

namespace PMPro_Pods\PMPro\Objects;

/**
 * PMPro Member object hooks for Checkout.
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
 *        <hr />
 *        <h3>Group label</h3>
 *        <table class="form-table">
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
 *
 * @since   TBD
 * @package PMPro_Pods
 */
class Member_Checkout {

	/**
	 * Add hooks for class.
	 *
	 * @since TBD
	 */
	public function hook() {
		add_action( 'pmpro_checkout_after_level_cost', [ $this, 'pmpro_checkout_after_level_cost' ] );
		add_action( 'pmpro_checkout_after_pricing_fields', [ $this, 'pmpro_checkout_after_pricing_fields' ] );
		add_action( 'pmpro_checkout_after_username', [ $this, 'pmpro_checkout_after_username' ] );
		add_action( 'pmpro_checkout_after_password', [ $this, 'pmpro_checkout_after_password' ] );
		add_action( 'pmpro_checkout_after_email', [ $this, 'pmpro_checkout_after_email' ] );
		add_action( 'pmpro_checkout_after_billing_fields', [ $this, 'pmpro_checkout_after_billing_fields' ] );
		add_action( 'pmpro_checkout_after_payment_information_fields', [ $this, 'pmpro_checkout_after_payment_information_fields' ] );
		add_action( 'pmpro_checkout_after_tos_fields', [ $this, 'pmpro_checkout_after_tos_fields' ] );
		add_action( 'pmpro_checkout_after_captcha', [ $this, 'pmpro_checkout_after_captcha' ] );
		add_action( 'pmpro_checkout_before_submit_button', [ $this, 'pmpro_checkout_before_submit_button' ] );

		add_action( 'TBD_TBD_TBD', [ $this, 'TBD_TBD_TBD' ] );

		// @todo Debug this.
		add_action( 'pmpro_paypalexpress_session_vars', [ $this, 'pmpro_paypalexpress_session_vars_for_user_fields' ] );
		add_action( 'pmpro_before_send_to_twocheckout', [ $this, 'pmpro_paypalexpress_session_vars_for_user_fields' ], 10, 0 );
	}

	/**
	 * Remove hooks from class.
	 *
	 * @since TBD
	 */
	public function unhook() {
	}

	/**
	 * Render the fields for the checkout page in the after level cost section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_level_cost( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_level_cost',
			'render'        => 'div-rows',
			'heading'       => 'h3',
		] );
	}

	/**
	 * Render the fields for the checkout page in the after pricing fields section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_pricing_fields( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_pricing_fields',
			'render'        => 'div-rows',
			'heading'       => 'h3',
		] );
	}

	/**
	 * Render the fields for the checkout page in the after username section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_username( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_username',
			'render'        => 'div-rows',
			// No heading to show here.
			'heading'       => null,
		] );
	}

	/**
	 * Render the fields for the checkout page in the after password section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_password( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_password',
			'render'        => 'div-rows',
			// No heading to show here.
			'heading'       => null,
		] );
	}

	/**
	 * Render the fields for the checkout page in the after email section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_email( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_email',
			'render'        => 'div-rows',
			// No heading to show here.
			'heading'       => null,
		] );
	}

	/**
	 * Render the fields for the checkout page in the after billing fields section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_billing_fields( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_billing_fields',
			'render'        => 'div-rows',
			'heading'       => 'h3',
		] );
	}

	/**
	 * Render the fields for the checkout page in the after payment information fields section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_payment_information_fields( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_payment_information_fields',
			'render'        => 'div-rows',
			'heading'       => 'h3',
		] );
	}

	/**
	 * Render the fields for the checkout page in the after TOS fields section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_tos_fields( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_tos_fields',
			'render'        => 'div-rows',
			'heading'       => 'h3',
		] );
	}

	/**
	 * Render the fields for the checkout page in the after captcha section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_captcha( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'after_captcha',
			'render'        => 'div-rows',
			'heading'       => 'h3',
		] );
	}

	/**
	 * Render the fields for the checkout page in the before submit button section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_before_submit_button( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'before_submit_button',
			'render'        => 'div-rows',
			'heading'       => 'h3',
		] );
	}

	/**
	 * Handle saving the submitted fields for the object on the checkout.
	 *
	 * @since TBD
	 *
	 * @param int $user_id The user ID.
	 *
	 * @todo  Figure out the hook.
	 *
	 */
	public function TBD_TBD_TBD( $user_id ) {
		pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id );
	}

	/**
	 * Sessions vars for PayPal Express.
	 *
	 * @since TBD
	 * @todo  Figure this out.
	 *
	 */
	public function pmpro_paypalexpress_session_vars_for_user_fields() {
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
						$upload_dir = wp_upload_dir();
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

}
