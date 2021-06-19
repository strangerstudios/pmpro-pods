<?php
/**
 * Add metadata compatibility for PMPro object types as they are needed by Pods.
 */

/**
 * Handle get_metadata fallback for PMPro types.
 *
 * @since TBD
 *
 * @param mixed  $value     The value to return, either a single metadata value or an array
 *                          of values depending on the value of `$single`. Default null.
 * @param int    $object_id ID of the object metadata is for.
 * @param string $meta_key  Metadata key.
 * @param bool   $single    Whether to return only the first value of the specified `$meta_key`.
 * @param string $meta_type Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
 *                          or any other object type with an associated meta table.
 */
function pmpro_pods_get_metadata( $value, $object_id, $meta_key, $single, $meta_type ) {
	// Handle integration for the supported meta type.
	if ( 'pmpro_membership_user' === $meta_type ) {
		return get_user_meta( $object_id, $meta_key, $single );
	}

	return $value;
}

add_filter( 'get_pmpro_membership_user_metadata', 'pmpro_pods_get_metadata', 10, 5 );

/**
 * Handle add_metadata fallback for PMPro types.
 *
 * @since TBD
 *
 * @param null|bool $check      Whether to allow adding metadata for the given type.
 * @param int       $object_id  ID of the object metadata is for.
 * @param string    $meta_key   Metadata key.
 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool      $unique     Whether the specified meta key should be unique for the object.
 * @param string    $meta_type  Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
 *                              or any other object type with an associated meta table.
 */
function pmpro_pods_add_metadata( $check, $object_id, $meta_key, $meta_value, $unique, $meta_type ) {
	// Handle integration for the supported meta type.
	if ( 'pmpro_membership_user' === $meta_type ) {
		return add_user_meta( $object_id, $meta_key, $meta_value, $unique );
	}

	return $check;
}

add_filter( 'add_pmpro_membership_user_metadata', static function() {
	// Get all arguments.
	$args = func_get_args();

	// Add the $meta_type.
	$args[] = 'pmpro_membership_user';

	// Pass the updated args to the final callback.
	return pmpro_pods_add_metadata( ...$args );
}, 10, 5 );

/**
 * Handle update_metadata fallback for PMPro types.
 *
 * @since TBD
 *
 * @param null|bool $check      Whether to allow updating metadata for the given type.
 * @param int       $object_id  ID of the object metadata is for.
 * @param string    $meta_key   Metadata key.
 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed     $prev_value Optional. Previous value to check before updating.
 *                              If specified, only update existing metadata entries with
 *                              this value. Otherwise, update all entries.
 * @param string    $meta_type  Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
 *                              or any other object type with an associated meta table.
 */
function pmpro_pods_update_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value, $meta_type ) {
	// Handle integration for the supported meta type.
	if ( 'pmpro_membership_user' === $meta_type ) {
		return update_user_meta( $object_id, $meta_key, $meta_value, $prev_value );
	}

	return $check;
}

add_filter( 'update_pmpro_membership_user_metadata', static function() {
	// Get all arguments.
	$args = func_get_args();

	// Add the $meta_type.
	$args[] = 'pmpro_membership_user';

	// Pass the updated args to the final callback.
	return pmpro_pods_update_metadata( ...$args );
}, 10, 5 );

/**
 * Handle delete_metadata fallback for PMPro types.
 *
 * @since TBD
 *
 * @param null|bool $delete     Whether to allow metadata deletion of the given type.
 * @param int       $object_id  ID of the object metadata is for.
 * @param string    $meta_key   Metadata key.
 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool      $delete_all Whether to delete the matching metadata entries
 *                              for all objects, ignoring the specified $object_id.
 *                              Default false.
 * @param string    $meta_type  Type of object metadata is for. Accepts 'post', 'comment', 'term', 'user',
 *                              or any other object type with an associated meta table.
 */
function pmpro_pods_delete_metadata( $delete, $object_id, $meta_key, $meta_value, $delete_all, $meta_type ) {
	// Handle integration for the supported meta type.
	if ( 'pmpro_membership_user' === $meta_type ) {
		return delete_user_meta( $object_id, $meta_key, $meta_value, $delete_all );
	}

	return $delete;
}

add_filter( 'delete_pmpro_membership_user_metadata', static function() {
	// Get all arguments.
	$args = func_get_args();

	// Add the $meta_type.
	$args[] = 'pmpro_membership_user';

	// Pass the updated args to the final callback.
	return pmpro_pods_delete_metadata( ...$args );
}, 10, 5 );

add_action( 'init', static function() {
	global $wpdb;

	// Set up table alias.
	$wpdb->pmpro_membership_usermeta = $wpdb->usermeta;
} );