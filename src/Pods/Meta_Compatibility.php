<?php

namespace PMPro_Pods\Pods;

/**
 * Meta compatibility for Users using the pmpro_membership_user meta type.
 *
 * We use this custom meta type to differentiate between PMPro config vs User config in Pods.
 *
 * @since   1.0.0
 * @package PMPro_Pods
 */
class Meta_Compatibility {

	/**
	 * Add hooks for class.
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		global $wpdb;

		// Set up table alias.
		$wpdb->pmpro_membership_usermeta = $wpdb->usermeta;

		add_filter( 'get_pmpro_membership_user_metadata', [ $this, 'get_metadata' ], 10, 4 );
		add_filter( 'add_pmpro_membership_user_metadata', [ $this, 'add_metadata' ], 10, 5 );
		add_filter( 'update_pmpro_membership_user_metadata', [ $this, 'update_metadata' ], 10, 5 );
		add_filter( 'delete_pmpro_membership_user_metadata', [ $this, 'delete_metadata' ], 10, 5 );
	}

	/**
	 * Remove hooks from class.
	 *
	 * @since 1.0.0
	 */
	public function unhook() {
		global $wpdb;

		// Remove table alias.
		unset( $wpdb->pmpro_membership_usermeta );

		remove_filter( 'get_pmpro_membership_user_metadata', [ $this, 'get_metadata' ], 10, 4 );
		remove_filter( 'add_pmpro_membership_user_metadata', [ $this, 'add_metadata' ], 10, 5 );
		remove_filter( 'update_pmpro_membership_user_metadata', [ $this, 'update_metadata' ], 10, 5 );
		remove_filter( 'delete_pmpro_membership_user_metadata', [ $this, 'delete_metadata' ], 10, 5 );
	}

	/**
	 * Handle get_metadata fallback for PMPro types.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value     The value to return, either a single metadata value or an array
	 *                          of values depending on the value of `$single`. Default null.
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key  Metadata key.
	 * @param bool   $single    Whether to return only the first value of the specified `$meta_key`.
	 *
	 * @return mixed The value to return, either a single metadata value or an array of depending on the value of
	 *               `$single`. Default null.
	 */
	public function get_metadata( $value, $object_id, $meta_key, $single ) {
		return get_user_meta( $object_id, $meta_key, $single );
	}

	/**
	 * Handle add_metadata fallback for PMPro types.
	 *
	 * @since 1.0.0
	 *
	 * @param null|bool $check      Whether to allow adding metadata for the given type.
	 * @param int       $object_id  ID of the object metadata is for.
	 * @param string    $meta_key   Metadata key.
	 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool      $unique     Whether the specified meta key should be unique for the object.
	 *
	 * @return null|bool Whether to allow adding metadata for the given type.
	 */
	public function add_metadata( $check, $object_id, $meta_key, $meta_value, $unique ) {
		return add_user_meta( $object_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Handle update_metadata fallback for PMPro types.
	 *
	 * @since 1.0.0
	 *
	 * @param null|bool $check      Whether to allow updating metadata for the given type.
	 * @param int       $object_id  ID of the object metadata is for.
	 * @param string    $meta_key   Metadata key.
	 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed     $prev_value Optional. Previous value to check before updating.
	 *                              If specified, only update existing metadata entries with
	 *                              this value. Otherwise, update all entries.
	 *
	 * @return null|bool Whether to allow updating metadata for the given type.
	 */
	public function update_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		return update_user_meta( $object_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Handle delete_metadata fallback for PMPro types.
	 *
	 * @since 1.0.0
	 *
	 * @param null|bool $delete     Whether to allow metadata deletion of the given type.
	 * @param int       $object_id  ID of the object metadata is for.
	 * @param string    $meta_key   Metadata key.
	 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool      $delete_all Whether to delete the matching metadata entries
	 *                              for all objects, ignoring the specified $object_id.
	 *                              Default false.
	 *
	 * @return null|bool Whether to allow metadata deletion of the given type.
	 */
	public function delete_metadata( $delete, $object_id, $meta_key, $meta_value, $delete_all ) {
		return delete_user_meta( $object_id, $meta_key, $meta_value, $delete_all );
	}

}
