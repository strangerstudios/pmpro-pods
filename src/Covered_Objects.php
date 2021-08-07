<?php

namespace PMPro_Pods;

/**
 * Functionality for covered PMPro object types.
 *
 * @since   TBD
 * @package PMPro_Pods
 */
class Covered_Objects {

	/**
	 * Get the list of PMPro integration types and their configurations.
	 *
	 * @since TBD
	 *
	 * @return array[] The list of PMPro integration types and their configurations.
	 */
	public static function get_pod_types() {
		global $wpdb;

		return [
			'pmpro_membership_user'  => [
				'label'            => __( 'PMPro Member', 'pmpro-pods' ),
				'table'            => $wpdb->users,
				'meta_table'       => $wpdb->usermeta,
				'field_id'         => 'ID',
				'field_index'      => 'display_name',
				'field_slug'       => 'user_nicename',
				'meta_field_id'    => 'user_id',
				'meta_field_index' => 'meta_key',
				'meta_field_value' => 'meta_value',
			],
			'pmpro_membership_order' => [
				'label'            => __( 'PMPro Order', 'pmpro-pods' ),
				'table'            => $wpdb->pmpro_membership_orders,
				'meta_table'       => $wpdb->pmpro_membership_ordermeta,
				'field_id'         => 'id',
				'field_index'      => 'code',
				'meta_field_id'    => 'pmpro_membership_order_id',
				'meta_field_index' => 'meta_key',
				'meta_field_value' => 'meta_value',
			],
			'pmpro_membership_level' => [
				'label'            => __( 'PMPro Membership Level', 'pmpro-pods' ),
				'table'            => $wpdb->pmpro_membership_levels,
				'meta_table'       => $wpdb->pmpro_membership_levelmeta,
				'field_id'         => 'id',
				'field_index'      => 'name',
				'meta_field_id'    => 'pmpro_membership_level_id',
				'meta_field_index' => 'meta_key',
				'meta_field_value' => 'meta_value',
			],
		];
	}

}