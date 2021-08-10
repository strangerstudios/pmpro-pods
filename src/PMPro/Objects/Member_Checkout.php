<?php

namespace PMPro_Pods\PMPro\Objects;

use MemberOrder;
use WP_User;

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

	private static $saved = false;

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

		add_action( 'pmpro_paypalexpress_session_vars', [ $this, 'pmpro_paypalexpress_session_vars' ] );
		add_action( 'pmpro_after_checkout', [ $this, 'pmpro_after_checkout' ], 10, 2 );

		add_action( 'pmpro_before_send_to_paypal_standard', [ $this, 'save_checkout_data' ], 10, 2 );
		add_action( 'pmpro_before_send_to_twocheckout', [ $this, 'save_checkout_data' ], 20, 2 );
		add_action( 'pmpro_before_send_to_gourl', [ $this, 'save_checkout_data' ], 20, 2 );
		add_action( 'pmpro_before_send_to_payfast', [ $this, 'save_checkout_data' ], 20, 2 );

		add_filter( 'pmpro_registration_checks', [ $this, 'pmpro_registration_checks' ] );
	}

	/**
	 * Remove hooks from class.
	 *
	 * @since TBD
	 */
	public function unhook() {
		remove_action( 'pmpro_checkout_after_level_cost', [ $this, 'pmpro_checkout_after_level_cost' ] );
		remove_action( 'pmpro_checkout_after_pricing_fields', [ $this, 'pmpro_checkout_after_pricing_fields' ] );
		remove_action( 'pmpro_checkout_after_username', [ $this, 'pmpro_checkout_after_username' ] );
		remove_action( 'pmpro_checkout_after_password', [ $this, 'pmpro_checkout_after_password' ] );
		remove_action( 'pmpro_checkout_after_email', [ $this, 'pmpro_checkout_after_email' ] );
		remove_action( 'pmpro_checkout_after_billing_fields', [ $this, 'pmpro_checkout_after_billing_fields' ] );
		remove_action( 'pmpro_checkout_after_payment_information_fields', [ $this, 'pmpro_checkout_after_payment_information_fields' ] );
		remove_action( 'pmpro_checkout_after_tos_fields', [ $this, 'pmpro_checkout_after_tos_fields' ] );
		remove_action( 'pmpro_checkout_after_captcha', [ $this, 'pmpro_checkout_after_captcha' ] );
		remove_action( 'pmpro_checkout_before_submit_button', [ $this, 'pmpro_checkout_before_submit_button' ] );

		remove_action( 'pmpro_paypalexpress_session_vars', [ $this, 'pmpro_paypalexpress_session_vars' ] );
		remove_action( 'pmpro_after_checkout', [ $this, 'pmpro_after_checkout' ], 10, 2 );

		remove_action( 'pmpro_before_send_to_paypal_standard', [ $this, 'save_checkout_data' ], 10, 2 );
		remove_action( 'pmpro_before_send_to_twocheckout', [ $this, 'save_checkout_data' ], 20, 2 );
		remove_action( 'pmpro_before_send_to_gourl', [ $this, 'save_checkout_data' ], 20, 2 );
		remove_action( 'pmpro_before_send_to_payfast', [ $this, 'save_checkout_data' ], 20, 2 );

		remove_filter( 'pmpro_registration_checks', [ $this, 'pmpro_registration_checks' ] );
	}

	/**
	 * Render the fields for the checkout page in the after level cost section.
	 *
	 * @since TBD
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_checkout_after_level_cost( $user ) {
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

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
		if ( ! empty( $GLOBALS['pmpro_review'] ) ) {
			return;
		}

		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_checkout',
			'section'       => 'before_submit_button',
			'render'        => 'div-rows',
			'heading'       => 'h3',
		] );
	}

	/**
	 * Handle validation of fields.
	 *
	 * @since TBD
	 *
	 * @param bool $is_valid Whether the registration validation passed.
	 *
	 * @return bool Whether the registration validation passed.
	 */
	public function pmpro_registration_checks( $check ) {
		// Bail if we are not already valid.
		if ( false === $check ) {
			return $check;
		}

		$is_valid = pods_form_validate_submitted_fields( 'pmpro_membership_user' );

		// Check if it passes validation.
		if ( true === $is_valid ) {
			return true;
		}

		global $pmpro_msg, $pmpro_msgt;

		$pmpro_msgt = 'pmpro_error';

		$error_messages = [];

		foreach ( $is_valid as $error ) {
			$error_messages[] = $error->get_error_message();
		}

		$error_messages = implode( '<br />', $error_messages );

		if ( ! empty( $pmpro_msg ) ) {
			$pmpro_msg .= '<br /><br />' . $error_messages;
		} else {
			$pmpro_msg = $error_messages;
		}

		// There were validation errors.
		return false;
	}

	/**
	 * Handle setting session variables for PayPal Express payments based on field values.
	 *
	 * @since TBD
	 */
	public function pmpro_paypalexpress_session_vars() {
		// Map the $_POST submitted fields to $_SESSION.
		if ( empty( $_POST ) ) {
			return;
		}

		$fields = pods_form_get_submitted_fields( 'pmpro_membership_user' );

		foreach ( $fields as $field ) {
			$field_name = $field['name'];

			if ( ! isset( $_POST[ 'pods_meta_' . $field_name ] ) ) {
				continue;
			}

			$_SESSION[ 'pods_meta_' . $field_name ] = pods_form_get_submitted_field_value( $field_name );
		}
	}

	/**
	 * Handle session variables from PayPal Express payments and move them back to $_POST data.
	 *
	 * @since TBD
	 *
	 * @param int         $user_id The user ID.
	 * @param MemberOrder $morder  The member order object.
	 */
	public function pmpro_after_checkout( $user_id, $morder ) {
		if ( self::$saved ) {
			return;
		}

		// Map the $_SESSION submitted fields to $_POST and then clean up.
		if ( ! empty( $_SESSION ) ) {
			$fields = pods_form_get_submitted_fields( 'pmpro_membership_user' );

			foreach ( $fields as $field ) {
				$field_name = $field['name'];

				$_POST[ 'pods_meta_' . $field_name ] = pods_form_get_submitted_field_value( $field_name, 'session' );

				if ( ! isset( $_SESSION[ 'pods_meta_' . $field_name ] ) ) {
					unset( $_SESSION[ 'pods_meta_' . $field_name ] );
				}
			}
		}

		$this->save_checkout_data( $user_id, $morder );
	}

	/**
	 * Handle saving checkout from PayPal Standard payments and move them back to $_POST data.
	 *
	 * @since TBD
	 *
	 * @param int              $user_id The user ID.
	 * @param null|MemberOrder $morder  The member order object.
	 */
	public function save_checkout_data( $user_id, $morder = null ) {
		if ( self::$saved ) {
			return;
		}

		pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id );

		self::$saved = true;
	}

}
