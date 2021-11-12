<?php

namespace PMPro_Pods;

/**
 * Functionality for covered PMPro object types.
 *
 * @since   1.0.0
 * @package PMPro_Pods
 */
class Covered_Objects {

	/**
	 * Get the list of PMPro integration types and their configurations.
	 *
	 * @since 1.0.0
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
				'join'             => [
					'memberships_users' => "LEFT JOIN `{$wpdb->pmpro_memberships_users}` AS `memberships_users` ON `memberships_users`.`user_id` = `t`.`ID`",
					'membership_levels' => "LEFT JOIN `{$wpdb->pmpro_membership_levels}` AS `membership_levels` ON `membership_levels`.`id` = `memberships_users`.`membership_id`",
				],
				'orderby'          => '`t`.`display_name`',
				'where_default'    => '`memberships_users`.`status` = \'active\'',
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
				'join'             => [
					'users'             => "LEFT JOIN `{$wpdb->users}` AS `users` ON `users`.`ID` = `t`.`user_id`",
					'membership_levels' => "LEFT JOIN `{$wpdb->pmpro_membership_levels}` AS `membership_levels` ON `membership_levels`.`id` = `t`.`membership_id`",
				],
				'orderby'          => '`t`.`timestamp` DESC',
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
				'orderby'          => '`t`.`name`',
				'field_id'         => 'id',
				'field_index'      => 'name',
				'meta_field_id'    => 'pmpro_membership_level_id',
				'meta_field_index' => 'meta_key',
				'meta_field_value' => 'meta_value',
			],
		];
	}

}