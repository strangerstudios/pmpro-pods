<?php

namespace PMPro_Pods\Pods;

use Pods\Permissions as Pods_Permissions;
use Pods\Whatsit;

class Permissions {

	/**
	 * The name used for the Require Membership Level option on objects.
	 */
	const OPTION_RESTRICT_MEMBERSHIP_LEVEL = 'pmpro_require_membership';

	/**
	 * The name used for the Show on Checkout Membership Level on objects.
	 */
	const OPTION_CHECKOUT_MEMBERSHIP_LEVEL = 'pmpro_show_on_checkout_membership_level';

	/**
	 * Add hooks for class.
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		add_filter( 'pods_permissions_user_has_permission', [ $this, 'user_has_permission' ], 10, 3 );
		add_filter( 'pods_permissions_are_permissions_restricted', [ $this, 'are_permissions_restricted' ], 10, 2 );
	}

	/**
	 * Remove hooks from class.
	 *
	 * @since 1.0.0
	 */
	public function unhook() {
		remove_filter( 'pods_permissions_user_has_permission', [ $this, 'user_has_permission' ] );
		remove_filter( 'pods_permissions_are_permissions_restricted', [ $this, 'are_permissions_restricted' ] );
	}

	/**
	 * Filter whether a user has permission to an object based on the PMPro restrictions set.
	 *
	 * @since 1.0.0
	 *
	 * @param bool             $user_has_permission Whether a user has permission to an object.
	 * @param array|Whatsit    $object              The object data.
	 * @param null|int|WP_User $user                The user ID or object (default: current user).
	 *
	 * @return bool Whether a user has permission to an object.
	 */
	public function user_has_permission( $user_has_permission, $object, $user ) {
		if ( ! $user_has_permission ) {
			return $user_has_permission;
		}

		// Check if the checkout membership level is restricted.
		if ( $this->is_checkout_membership_level_restricted( $object ) ) {
			// Restrict if we are not on the correct membership level.
			return ! $this->is_checkout_membership_level_restricted_for_user( $object, $user );
		}

		// Restrict if they do not have access to the membership level.
		return ! $this->is_membership_level_restricted_for_user( $object, $user );
	}


	/**
	 * Filter whether permissions are restricted for an object based on PMPro restrictions set.
	 *
	 * @since 1.0.0
	 *
	 * @param bool          $are_permissions_restricted Whether the permissions are restricted for an object.
	 * @param array|Whatsit $object                     The object data.
	 *
	 * @return bool Whether the permissions are restricted for an object.
	 */
	public function are_permissions_restricted( $are_permissions_restricted, $object ) {
		if ( $are_permissions_restricted ) {
			return $are_permissions_restricted;
		}

		return (
			$this->get_required_membership_levels( $object )
			|| $this->is_checkout_membership_level_restricted( $object )
		);
	}

	/**
	 * Determine whether membership level is restricted for user on an object.
	 *
	 * @since 1.0.0
	 *
	 * @param array|Whatsit $object The object data.
	 * @param WP_User       $user   The user ID or object (default: current user).
	 *
	 * @return bool Whether membership level is restricted for user on an object.
	 */
	public function is_membership_level_restricted_for_user( $object, $user ) {
		$required_membership_levels = $this->get_required_membership_levels( $object );

		// Do not restrict if no restricted membership levels provided.
		if ( ! $required_membership_levels ) {
			return false;
		}

		// Determine if the user has specific membership levels.
		$has_membership_level = pmpro_hasMembershipLevel( $required_membership_levels, $user->ID );

		// Restrict if they do not have the membership level required.
		return ! $has_membership_level;
	}

	/**
	 * Get the list of required membership levels.
	 *
	 * @since 1.0.0
	 *
	 * @param array|Whatsit $object The object data.
	 *
	 * @return array|false The list of required membership levels or false if not restricted.
	 */
	public function get_required_membership_levels( $object ) {
		$required_membership_levels = pods_v( self::OPTION_RESTRICT_MEMBERSHIP_LEVEL, $object );
		$required_membership_levels = array_map( 'absint', (array) $required_membership_levels );
		$required_membership_levels = array_unique( array_filter( $required_membership_levels ) );

		if ( empty( $required_membership_levels ) ) {
			return false;
		}

		return $required_membership_levels;
	}

	/**
	 * Determine whether checkout membership level is restricted on an object.
	 *
	 * @since 1.0.0
	 *
	 * @param array|Whatsit $object The object data.
	 *
	 * @return bool Whether checkout membership level is restricted on an object.
	 */
	public function is_checkout_membership_level_restricted( $object ) {
		// Restrict if checkout levels are provided.
		return false !== $this->get_checkout_membership_levels( $object );
	}

	/**
	 * Determine whether checkout membership level is restricted for a user on an object.
	 *
	 * @since 1.0.0
	 *
	 * @param array|Whatsit $object The object data.
	 * @param WP_User       $user   The user ID or object (default: current user).
	 *
	 * @return bool Whether checkout membership level is restricted for a user on an object.
	 */
	public function is_checkout_membership_level_restricted_for_user( $object, $user ) {
		$checkout_membership_levels = $this->get_checkout_membership_levels( $object );

		// Do not restrict if no checkout or required membership levels are provided.
		if ( ! $checkout_membership_levels ) {
			return false;
		}

		// Restrict if not on the checkout page.
		if ( ! pmpro_is_checkout() ) {
			$pods_permissions = pods_container( Pods_Permissions::class );

			// Admins can see all available checkout fields on any non-checkout form like edit profile.
			if ( $pods_permissions->is_user_an_admin( null, $user ) ) {
				return false;
			}

			// Skip if the user already has unrestricted access for this membership level.
			if ( ! $this->is_membership_level_restricted_for_user( $object, $user ) ) {
				return false;
			}

			return true;
		}

		global $pmpro_level;

		// Maybe set up the level info on checkout.
		if ( empty( $pmpro_level ) ) {
			pmpro_getLevelAtCheckout();
		}

		// Restrict if level is not set on checkout.
		if ( empty( $pmpro_level ) ) {
			return true;
		}

		// Restrict if the checkout membership levels required do not match the checkout level.
		return ! in_array( (int) $pmpro_level->id, $checkout_membership_levels, true );
	}

	/**
	 * Get the list of checkout membership levels if restricted.
	 *
	 * @since 1.0.0
	 *
	 * @param array|Whatsit $object The object data.
	 *
	 * @return array|false The list of checkout membership levels or false if not restricted.
	 */
	public function get_checkout_membership_levels( $object ) {
		$show_on_checkout_membership_level = (int) pods_v( self::OPTION_CHECKOUT_MEMBERSHIP_LEVEL, $object );

		if ( 0 === $show_on_checkout_membership_level ) {
			return false;
		}

		return $this->get_required_membership_levels( $object );
	}

}
