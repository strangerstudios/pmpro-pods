<?php
/**
 * Integration code for Pods.
 */

// Filter to add the available types to be extended on the Add New Pod Wizard screen.
function pmpro_pods_admin_setup_add_extend_pod_type( $pod_types ) {
	$types = pmpro_pods_get_pod_types();

	$all_pods = pods_api()->load_pods( [ 'key_names' => true ] );

	foreach ( $types as $type => $config ) {
		if ( isset( $all_pods[ $type ] ) ) {
			continue;
		}

		$pod_types[ $type ] = $config['label'];
	}

	return $pod_types;
}

add_filter( 'pods_admin_setup_add_extend_pod_type', 'pmpro_pods_admin_setup_add_extend_pod_type' );

function pmpro_pods_api_save_pod_post_data( $post_data, $pod ) {
	$types = pmpro_pods_get_pod_types();
	$type  = pods_v( $pod['name'], $types );

	if ( empty( $type ) ) {
		return $post_data;
	}

	$post_data['post_title'] = $type['label'];

	return $post_data;
}

add_filter( 'pods_api_save_pod_post_data', 'pmpro_pods_api_save_pod_post_data', 10, 2 );

function pmpro_pods_api_save_pod_meta_data( $meta, $pod ) {
	$types = pmpro_pods_get_pod_types();
	$type  = pods_v( $pod['name'], $types );

	if ( empty( $type ) ) {
		return $meta;
	}

	$meta['storage']                 = 'meta';
	$meta['table_custom']            = $type['table'];
	$meta['meta_table_custom']       = $type['meta_table'];
	$meta['field_id_custom']         = $type['field_id'];
	$meta['field_index_custom']      = $type['field_index'];
	$meta['meta_field_id_custom']    = $type['meta_field_id'];
	$meta['meta_field_index_custom'] = $type['meta_field_index'];
	$meta['meta_field_value_custom'] = $type['meta_field_value'];

	if ( ! empty( $type['field_slug'] ) ) {
		$meta['field_slug_custom'] = $type['field_slug'];
	}

	return $meta;
}

add_filter( 'pods_api_save_pod_meta_data', 'pmpro_pods_api_save_pod_meta_data', 10, 2 );

function pmpro_pods_meta_default_box_title( $title, $pod, $fields, $type, $name ) {
	if ( ! pmpro_pods_is_covered_pod_type( $pod['name'] ) ) {
		return $title;
	}

	return __( 'More Fields', 'pmpro-pods' );
}

add_filter( 'pods_meta_default_box_title', 'pmpro_pods_meta_default_box_title', 10, 5 );

function pmpro_pods_api_pod_types( $pod_types ) {
	$types = pmpro_pods_get_pod_types();

	foreach ( $types as $type => $config ) {
		$pod_types[ $type ] = $config['label'];
	}

	return $pod_types;
}

add_filter( 'pods_api_pod_types', 'pmpro_pods_api_pod_types' );

function pmpro_pods_form_render_fields_group_pre() {
	wp_enqueue_style( 'pmpro-pods', plugin_dir_url( __FILE__ ) . 'assets/pmpro-pods.css', [], '1.0' );
}

add_action( 'pods_form_render_fields_group_pre', 'pmpro_pods_form_render_fields_group_pre' );

function pmpro_pods_permissions_user_has_permission( $permission, $object, $user ) {
	// Permission already disabled.
	if ( ! $permission ) {
		return $permission;
	}

	// No membership levels required.
	if ( empty( $object['pmpro_require_membership'] ) ) {
		return $permission;
	}

	$object['pmpro_require_membership'] = array_map( 'absint', (array) $object['pmpro_require_membership'] );
	$object['pmpro_require_membership'] = array_filter( $object['pmpro_require_membership'] );

	// No valid membership levels required.
	if ( empty( $object['pmpro_require_membership'] ) ) {
		return $permission;
	}

	// Membership requires a valid user.
	if ( empty( $user ) ) {
		return false;
	}

	return pmpro_hasMembershipLevel( $object['pmpro_require_membership'], $user->ID );
}

add_filter( 'pods_permissions_user_has_permission', 'pmpro_pods_permissions_user_has_permission', 10, 3 );

function pmpro_pods_init() {
	if ( ! function_exists( 'pmpro_getAllLevels' ) ) {
		return;
	}

	pods_register_related_object( 'pmpro_membership_level', __( 'PMPro Membership Level', 'pmpro-pods' ), [
		'data_callback' => 'pmpro_pods_get_all_level_names_by_id',
	] );

	$types = pmpro_pods_get_pod_types();

	foreach ( $types as $type => $config ) {
		add_filter( 'pods_admin_setup_edit_group_tabs_' . $type, static function ( $tabs ) {
			$tabs['pmpro'] = __( 'PMPro Options', 'pmpro-pods' );

			return $tabs;
		} );

		add_filter( 'pods_admin_setup_edit_group_options_' . $type, static function ( $options, $pod ) {
			$options['pmpro'] = [];

			if ( 'pmpro_membership_user' === $pod->get_name() ) {
				$options['pmpro']['pmpro_section_member_profile'] = [
					'name'             => 'pmpro_section_member_profile',
					'label'            => __( 'Profile area(s) to show this group of fields on', 'pmpro-pods' ),
					// @todo Add a link to a doc page that shows the locations of each.
					'help'             => __( 'All available fields in this group will show in the sections chosen.', 'pmpro-pods' ),
					'type'             => 'pick',
					'data'             => [
						'show_on_front' => __( 'Front-facing Profile', 'pmpro-pods' ),
						'show_on_admin' => __( 'WP Dashboard Profile', 'pmpro-pods' ),
					],
					'default'          => [
						'show_on_front',
						'show_on_admin',
					],
					'pick_format_type' => 'multi',
				];

				$options['pmpro']['pmpro_section_checkout'] = [
					'name'             => 'pmpro_section_checkout',
					'label'            => __( 'Checkout Section to show this group of fields on', 'pmpro-pods' ),
					// @todo Add a link to a doc page that shows the locations of each.
					'help'             => __( 'All available fields in this group will show in the sections chosen.', 'pmpro-pods' ),
					'type'             => 'pick',
					'data' => [
						'disabled'                         => __( 'Do NOT show on Checkout', 'pmpro-pods' ),
						'after_level_cost'                 => __( 'After Level Cost / Before Discount', 'pmpro-pods' ), // After Level Cost / Before Discount [after_level_cost]
						'after_pricing_fields'             => __( 'After Pricing Fields', 'pmpro-pods' ), // After Pricing Fields [after_pricing_fields]
						'after_username'                   => __( 'After Username (no heading)', 'pmpro-pods' ), // After Username (no heading) [after_username]
						'after_password'                   => __( 'After Password (no heading)', 'pmpro-pods' ), // After Password (no heading) [after_password]
						'after_email'                      => __( 'After Email (no heading)', 'pmpro-pods' ), // After Email (no heading) [after_email]
						'after_billing_fields'             => __( 'After Billing Fields', 'pmpro-pods' ), // After Billing Fields [after_billing_fields]
						'after_payment_information_fields' => __( 'After Payment Information Fields', 'pmpro-pods' ), // After Payment Information Fields [after_payment_information_fields]
						'after_tos_fields'                 => __( 'After TOS Fields', 'pmpro-pods' ), // After TOS Fields [after_tos_fields]
						'after_captcha'                    => __( 'After Captcha', 'pmpro-pods' ), // After Captcha [after_captcha]
						'before_submit_button'             => __( 'Before Submit Button', 'pmpro-pods' ), // Before Submit Button [before_submit_button]
					],
					'default'          => 'after_captcha',
				];
			} elseif ( 'pmpro_membership_level' === $pod->get_name() ) {
				$options['pmpro']['pmpro_section'] = [
					'name'             => 'pmpro_section',
					'label'            => __( 'Sections to show this group of fields on', 'pmpro-pods' ),
					// @todo Add a link to a doc page that shows the locations of each.
					'help'             => __( 'All available fields in this group will show in the sections chosen.', 'pmpro-pods' ),
					'type'             => 'pick',
					'data'             => [
						'after_billing_details_settings' => __( 'After Billing Details', 'pmpro-pods' ), // After Billing Details [after_billing_details_settings]
						'after_other_settings'           => __( 'Before Content Settings', 'pmpro-pods' ), // Before Content Settings [after_other_settings]
						'after_content_settings'         => __( 'After Content Settings', 'pmpro-pods' ), // After Content Settings [after_content_settings]
					],
					'default'          => 'after_content_settings',
				];
			}

			$options['pmpro']['pmpro_require_membership'] = [
				'name'             => 'pmpro_require_membership',
				'label'            => __( 'Require Membership to see this group of fields', 'pmpro-pods' ),
				'help'             => __( 'All available fields in this group will show if the Member has the required membership level(s) chosen.', 'pmpro-pods' ),
				'type'             => 'pick',
				'pick_object'      => 'pmpro_membership_level',
				'default'          => 0,
				'pick_format_type' => 'multi',
			];

			return $options;
		}, 10, 2 );

		add_filter( 'pods_admin_setup_edit_field_tabs_' . $type, static function ( $tabs ) {
			$tabs['pmpro'] = __( 'PMPro Options', 'pmpro-pods' );

			return $tabs;
		} );

		add_filter( 'pods_admin_setup_edit_field_options_' . $type, static function ( $options ) {
			$options['pmpro'] = [];

			$options['pmpro']['pmpro_require_membership'] = [
				'name'             => 'pmpro_require_membership',
				'label'            => __( 'Require Membership to see this field', 'pmpro-pods' ),
				'type'             => 'pick',
				'pick_object'      => 'pmpro_membership_level',
				'default'          => 0,
				'pick_format_type' => 'multi',
			];

			$options['pmpro']['pmpro_show_on_membership_level'] = [
				'name'             => 'pmpro_show_on_membership_level',
				'label'            => __( 'Only show field on checkout for a specific Membership Level', 'pmpro-pods' ),
				'type'             => 'pick',
				'pick_object'      => 'pmpro_membership_level',
				'default'          => 0,
				'pick_format_type' => 'multi',
			];

			return $options;
		} );
	}

	// @todo Support this in the future better with CPT supports compatibility in PMPro.
	add_filter( 'pods_admin_setup_edit_options_post_type', static function ( $options ) {
		$options['advanced']['pmpro_enabled_require_membership'] = [
			'name'              => 'pmpro_enable_require_membership',
			'label'             => __( 'PMPro: Enable Require Membership functionality', 'pmpro-pods' ),
			'type'              => 'boolean',
			'default'           => 0,
			'boolean_yes_label' => '',
		];

		return $options;
	} );
}

add_action( 'pods_init', 'pmpro_pods_init' );

/**
 * Allow hooking in to support saving for custom object types.
 *
 * @since TBD
 *
 * @param int|string|false $object_id   The object ID after saving, false if not saved.
 * @param string           $object_type The custom object type.
 * @param array            $data        All object data to be saved
 * @param array            $meta        Associative array of meta keys and values.
 * @param bool             $strict      Decides whether the previous saved meta should be deleted or not.
 * @param bool             $sanitized   Will unsanitize the data, should be passed if the data is sanitized before sending.
 * @param array            $fields      The array of fields and their options, for further processing with.
 *
 * @return int|string|false The object ID after saving, false if not saved.
 */
function pmpro_pods_api_save_wp_object_for_custom_object_type( $object_id, $object_type, $data, $meta, $strict, $sanitized, $fields ) {
	$types = pmpro_pods_get_pod_types();
	$type  = pods_v( $object_type, $types );

	// Only save custom objects for our supported types.
	if ( ! $type ) {
		return $object_id;
	}

	// Handle saving the data.
	$existing_object_id = pods_v( $type['field_id'], $data );

	if ( empty( $existing_object_id ) ) {
		$existing_object_id = false;
	}

	global $wpdb;

	$table    = false;
	$defaults = [];
	$formats  = [];

	if ( 'pmpro_membership_user' === $object_type ) {
		if ( empty( $existing_object_id ) ) {
			// Users require minimal parameters set.
			if ( empty( $data['user_login'] ) || empty( $data['user_email'] ) ) {
				return false;
			}

			$object_id = wp_insert_user( $data );
		} elseif ( 1 < count( $data ) ) {
			// This is an existing item.
			$object_id = wp_update_user( $data );
		} else {
			// No data to save, just use the $existing_object_id.
			$object_id = $existing_object_id;
		}

		// There was an error saving this data.
		if ( is_wp_error( $object_id ) ) {
			return false;
		}
	} elseif ( 'pmpro_membership_level' === $object_type ) {
		$defaults = [
			'id'                => 0,
			'name'              => 'New level',
			'description'       => '',
			'confirmation'      => '',
			'initial_payment'   => '',
			'billing_amount'    => 0,
			'cycle_number'      => 0,
			'cycle_period'      => 0,
			'billing_limit'     => 0,
			'trial_amount'      => 0,
			'trial_limit'       => 0,
			'expiration_number' => 0,
			'expiration_period' => 0,
			'allow_signups'     => 0,
		];

		$formats = [
			'id'                => '%d',
			'name'              => '%s',
			'description'       => '%s',
			'confirmation'      => '%s',
			'initial_payment'   => '%f',
			'billing_amount'    => '%f',
			'cycle_number'      => '%d',
			'cycle_period'      => '%s',
			'billing_limit'     => '%d',
			'trial_amount'      => '%f',
			'trial_limit'       => '%d',
			'expiration_number' => '%d',
			'expiration_period' => '%s',
			'allow_signups'     => '%d',
		];

		$table = $wpdb->pmpro_membership_levels;
	} elseif ( 'pmpro_membership_order' === $object_type ) {
		$defaults = [
			'id'                          => '0',
			'code'                        => '',
			'session_id'                  => '',
			'user_id'                     => '0',
			'membership_id'               => '0',
			'paypal_token'                => '',
			'billing_name'                => '',
			'billing_street'              => '',
			'billing_city'                => '',
			'billing_state'               => '',
			'billing_zip'                 => '',
			'billing_country'             => '',
			'billing_phone'               => '',
			'subtotal'                    => '',
			'tax'                         => '',
			'couponamount'                => '',
			'checkout_id'                 => '0',
			'certificate_id'              => '0',
			'certificateamount'           => '',
			'total'                       => '',
			'payment_type'                => '',
			'cardtype'                    => '',
			'accountnumber'               => '',
			'expirationmonth'             => '',
			'expirationyear'              => '',
			'status'                      => '',
			'gateway'                     => '',
			'gateway_environment'         => '',
			'payment_transaction_id'      => '',
			'subscription_transaction_id' => '',
			'timestamp'                   => '',
			'affiliate_id'                => '',
			'affiliate_subid'             => '',
			'notes'                       => '',
		];

		$formats = [
			'id'                          => '%d',
			'code'                        => '%s',
			'session_id'                  => '%s',
			'user_id'                     => '%d',
			'membership_id'               => '%d',
			'paypal_token'                => '%s',
			'billing_name'                => '%s',
			'billing_street'              => '%s',
			'billing_city'                => '%s',
			'billing_state'               => '%s',
			'billing_zip'                 => '%s',
			'billing_country'             => '%s',
			'billing_phone'               => '%s',
			'subtotal'                    => '%s',
			'tax'                         => '%s',
			'couponamount'                => '%s',
			'checkout_id'                 => '%d',
			'certificate_id'              => '%d',
			'certificateamount'           => '%s',
			'total'                       => '%s',
			'payment_type'                => '%s',
			'cardtype'                    => '%s',
			'accountnumber'               => '%s',
			'expirationmonth'             => '%s',
			'expirationyear'              => '%s',
			'status'                      => '%s',
			'gateway'                     => '%s',
			'gateway_environment'         => '%s',
			'payment_transaction_id'      => '%s',
			'subscription_transaction_id' => '%s',
			'timestamp'                   => '%s',
			'affiliate_id'                => '%s',
			'affiliate_subid'             => '%s',
			'notes'                       => '%s',
		];

		$table = $wpdb->pmpro_membership_orders;
	}

	// Handle table interactions.
	if ( $table ) {
		// Use defaults if adding a new item.
		if ( empty( $existing_object_id ) ) {
			$data = array_merge( $defaults, $data );
		}

		if ( 1 < count( $data ) ) {
			// Get only the formats that we are saving for.
			$formats = array_intersect_key( $formats, $data );

			// Ensure both arrays match order precisely.
			ksort( $data );
			ksort( $formats );

			$saved_object_id = pmpro_insert_or_replace(
				$table,
				array_values( $data ),
				array_values( $formats )
			);

			// The item ID is still the same so return that.
			if ( ! empty( $existing_object_id ) ) {
				$object_id = $existing_object_id;
			} else {
				$object_id = $saved_object_id;
			}
		} else {
			// No data to save, just use the $existing_object_id.
			$object_id = $existing_object_id;
		}
	}

	return $object_id;
}

add_filter( 'pods_api_save_wp_object_for_custom_object_type', 'pmpro_pods_api_save_wp_object_for_custom_object_type', 10, 7 );

/**
 * Allow hooking in to support saving meta using the meta fallback.
 *
 * @since TBD
 *
 * @param bool   $use_meta_fallback Whether to support saving meta using the meta fallback.
 * @param string $object_type       The custom object type.
 *
 * @return bool Whether to support saving meta using the meta fallback.
 */
function pmpro_pods_api_save_wp_object_use_meta_fallback( $use_meta_fallback, $object_type ) {
	// Already using meta fallback.
	if ( $use_meta_fallback ) {
		return $use_meta_fallback;
	}

	$types = pmpro_pods_get_pod_types();

	// All types should enable meta fallback.
	return isset( $types[ $object_type ] );
}

add_filter( 'pods_api_save_wp_object_use_meta_fallback', 'pmpro_pods_api_save_wp_object_use_meta_fallback', 10, 2 );
add_filter( 'pods_field_wp_object_use_meta_fallback', 'pmpro_pods_api_save_wp_object_use_meta_fallback', 10, 2 );
