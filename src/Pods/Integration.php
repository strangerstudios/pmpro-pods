<?php

namespace PMPro_Pods\Pods;

use PMPro_Pods\Covered_Objects;
use PMPro_Pods\PMPro\Helper;
use PMPro_Pods\Pods\Permissions;
use Pods\Whatsit;

/**
 * Integration functionality for Pods.
 *
 * @since   1.0.0
 * @package PMPro_Pods
 */
class Integration {

	/**
	 * List of covered pod types.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	private $pod_types;

	/**
	 * Set up the object properties needed.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->pod_types = Covered_Objects::get_pod_types();
	}

	/**
	 * Add hooks for class.
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		add_action( 'pods_init', [ $this, 'pods_init' ] );

		add_filter( 'pods_admin_setup_add_extend_pod_type', [ $this, 'admin_setup_add_extend_pod_type' ] );

		add_filter( 'pods_api_pod_types', [ $this, 'api_pod_types' ] );
		add_filter( 'pods_api_save_pod_post_data', [ $this, 'api_save_pod_post_data' ], 10, 2 );
		add_filter( 'pods_api_save_pod_meta_data', [ $this, 'api_save_pod_meta_data' ], 10, 2 );
		add_filter( 'pods_meta_default_box_title', [ $this, 'meta_default_box_title' ], 10, 2 );

		add_action( 'pods_form_render_fields_group_pre', [ $this, 'form_render_fields_group_pre' ] );

		add_filter( 'pods_api_save_wp_object_for_custom_object_type', [
			$this,
			'api_save_wp_object_for_custom_object_type',
		], 10, 7 );
		add_filter( 'pods_api_save_wp_object_use_meta_fallback', [
			$this,
			'api_save_wp_object_use_meta_fallback',
		], 10, 2 );
		add_filter( 'pods_field_wp_object_use_meta_fallback', [
			$this,
			'api_save_wp_object_use_meta_fallback',
		], 10, 2 );
	}

	/**
	 * Remove hooks from class.
	 *
	 * @since 1.0.0
	 */
	public function unhook() {
		remove_action( 'pods_init', [ $this, 'pods_init' ] );

		remove_filter( 'pods_admin_setup_add_extend_pod_type', [ $this, 'admin_setup_add_extend_pod_type' ] );

		remove_filter( 'pods_api_pod_types', [ $this, 'api_pod_types' ] );
		remove_filter( 'pods_api_save_pod_post_data', [ $this, 'api_save_pod_post_data' ] );
		remove_filter( 'pods_api_save_pod_meta_data', [ $this, 'api_save_pod_meta_data' ] );
		remove_filter( 'pods_meta_default_box_title', [ $this, 'meta_default_box_title' ] );

		remove_action( 'pods_form_render_fields_group_pre', [ $this, 'form_render_fields_group_pre' ] );

		remove_filter( 'pods_api_save_wp_object_for_custom_object_type', [
			$this,
			'api_save_wp_object_for_custom_object_type',
		] );
		remove_filter( 'pods_api_save_wp_object_use_meta_fallback', [ $this, 'api_save_wp_object_use_meta_fallback' ] );
		remove_filter( 'pods_field_wp_object_use_meta_fallback', [ $this, 'api_save_wp_object_use_meta_fallback' ] );
	}

	/**
	 * Add our covered types to be extended on the Add New Pod Wizard screen.
	 *
	 * @since 1.0.0
	 *
	 * @param array $pod_types The list of available pod types to extend.
	 *
	 * @return array The list of available pod types to extend.
	 *
	 * @throws \Exception
	 */
	public function admin_setup_add_extend_pod_type( $pod_types ) {
		$all_pods = pods_api()->load_pods( [ 'key_names' => true ] );

		foreach ( $this->pod_types as $type => $config ) {
			if ( isset( $all_pods[ $type ] ) ) {
				continue;
			}

			$pod_types[ $type ] = $config['label'];
		}

		return $pod_types;
	}

	/**
	 * Override the Pod label for a covered type to always match the label we have defined.
	 *
	 * @since 1.0.0
	 *
	 * @param array         $post_data The post data to be saved for the Pod.
	 * @param array|Whatsit $pod       The Pod data.
	 *
	 * @return array The post data to be saved for the Pod.
	 */
	public function api_save_pod_post_data( $post_data, $pod ) {
		if ( empty( $pod ) ) {
			return $post_data;
		}

		$type = pods_v( $pod['name'], $this->pod_types );

		if ( empty( $type ) ) {
			return $post_data;
		}

		$post_data['post_title'] = $type['label'];

		return $post_data;
	}

	/**
	 * Override the Pod config for a covered type to always match what we have defined.
	 *
	 * @since 1.0.0
	 *
	 * @param array         $meta The post meta to be saved for the Pod.
	 * @param array|Whatsit $pod  The Pod data.
	 *
	 * @return array The post meta to be saved for the Pod.
	 */
	public function api_save_pod_meta_data( $meta, $pod ) {
		if ( empty( $pod ) ) {
			return $meta;
		}

		$type = pods_v( $pod['name'], $this->pod_types );

		if ( empty( $type ) ) {
			return $meta;
		}

		$meta['storage']                 = 'meta';
		$meta['label']                   = $type['label'];
		$meta['table_custom']            = $type['table'];
		$meta['meta_table_custom']       = $type['meta_table'];
		$meta['join_custom']             = ! empty( $type['join'] ) ? $type['join'] : '';
		$meta['orderby_custom']          = ! empty( $type['orderby'] ) ? $type['orderby'] : '';
		$meta['where_default_custom']    = ! empty( $type['where_default'] ) ? $type['where_default'] : '';
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

	/**
	 * Override the default group title for a covered type to always match what we have defined.
	 *
	 * @since 1.0.0
	 *
	 * @param string        $title The default group title.
	 * @param array|Whatsit $pod   The Pod data.
	 *
	 * @return string The default group title.
	 */
	public function meta_default_box_title( $title, $pod ) {
		if ( empty( $pod ) ) {
			return $title;
		}

		$type = pods_v( $pod['name'], $this->pod_types );

		if ( empty( $type ) ) {
			return $title;
		}

		return __( 'More Fields', 'pmpro-pods' );
	}

	/**
	 * @param $pod_types
	 *
	 * @return mixed
	 */
	public function api_pod_types( $pod_types ) {
		foreach ( $this->pod_types as $type => $config ) {
			$pod_types[ $type ] = $config['label'];
		}

		return $pod_types;
	}

	/**
	 * Add our custom PMPro Pods CSS on the PMPro screens.
	 *
	 * @since 1.0.0
	 */
	public function form_render_fields_group_pre() {
		wp_enqueue_style( 'pmpro-pods', PMPRO_PODS_URL . 'assets/pmpro-pods.css', [], PMPRO_PODS_VERSION );
	}

	/**
	 * Set up things after Pods has been initialized.
	 *
	 * @since 1.0.0
	 */
	public function pods_init() {
		pods_register_related_object( 'pmpro_membership_level', __( 'PMPro Membership Level', 'pmpro-pods' ), [
			'data_callback' => [ Helper::class, 'get_all_level_names_by_id' ],
		] );

		foreach ( $this->pod_types as $type => $config ) {
			add_filter( 'pods_admin_setup_edit_group_tabs_' . $type, [ $this, 'admin_setup_edit_group_tabs' ] );
			add_filter( 'pods_admin_setup_edit_group_options_' . $type, [ $this, 'admin_setup_edit_group_options' ], 10, 2 );
			add_filter( 'pods_admin_setup_edit_field_tabs_' . $type, [ $this, 'admin_setup_edit_field_tabs' ] );
			add_filter( 'pods_admin_setup_edit_field_options_' . $type, [ $this, 'admin_setup_edit_field_options' ], 10, 2 );
		}

		// @todo Core: Support this in the future better with CPT supports compatibility in PMPro.
		//add_filter( 'pods_admin_setup_edit_options_post_type', [ $this, 'admin_setup_edit_options_post_type' ] );
	}

	/**
	 * Allow hooking in to support saving for custom object types.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|false $object_id   The object ID after saving, false if not saved.
	 * @param string           $object_type The custom object type.
	 * @param array            $data        All object data to be saved
	 * @param array            $meta        Associative array of meta keys and values.
	 * @param bool             $strict      Decides whether the previous saved meta should be deleted or not.
	 * @param bool             $sanitized   Will unsanitize the data, should be passed if the data is sanitized before
	 *                                      sending.
	 * @param array            $fields      The array of fields and their options, for further processing with.
	 *
	 * @return int|string|false The object ID after saving, false if not saved.
	 */
	public function api_save_wp_object_for_custom_object_type( $object_id, $object_type, $data, $meta, $strict, $sanitized, $fields ) {
		$type = pods_v( $object_type, $this->pod_types );

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

				$saved_object_id = pmpro_insert_or_replace( $table, array_values( $data ), array_values( $formats ) );

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

	/**
	 * Allow hooking in to support saving meta using the meta fallback.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $use_meta_fallback Whether to support saving meta using the meta fallback.
	 * @param string $object_type       The custom object type.
	 *
	 * @return bool Whether to support saving meta using the meta fallback.
	 */
	public function api_save_wp_object_use_meta_fallback( $use_meta_fallback, $object_type ) {
		// Already using meta fallback.
		if ( $use_meta_fallback ) {
			return $use_meta_fallback;
		}

		// All types should enable meta fallback.
		return isset( $this->pod_types[ $object_type ] );
	}

	/**
	 * Add PMPro tab to groups.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs List of group tabs.
	 *
	 * @return array List of group tabs.
	 */
	public function admin_setup_edit_group_tabs( $tabs ) {
		$tabs['pmpro'] = __( 'PMPro Options', 'pmpro-pods' );

		return $tabs;
	}

	/**
	 * Add PMPro group options to the PMPro group tab.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $options The list of group options.
	 * @param Whatsit $pod     The Pod object.
	 *
	 * @return array The list of group options.
	 */
	public function admin_setup_edit_group_options( $options, $pod ) {
		$pod_name = $pod->get_name();

		$options['pmpro'] = [];

		if ( 'pmpro_membership_user' === $pod_name ) {
			$options['pmpro']['pmpro_section_member_profile'] = [
				'name'             => 'pmpro_section_member_profile',
				'label'            => __( 'Profile area(s) to show this group of fields on', 'pmpro-pods' ),
				// @todo Add a link to a doc page that shows the locations of each.
				'help'             => __( 'All available fields in this group will show in the sections chosen.', 'pmpro-pods' ),
				'type'             => 'pick',
				'data'             => [
					'show_on_account' => __( 'Display field values on Account Details Page', 'pmpro-pods' ),
					'show_on_front'   => __( 'Front-facing Profile Edit', 'pmpro-pods' ),
					'show_on_admin'   => __( 'WP Dashboard Profile Edit', 'pmpro-pods' ),
				],
				'default'          => [
					'show_on_account',
					'show_on_front',
					'show_on_admin',
				],
				'pick_format_type' => 'multi',
			];

			// @todo Fix the pick multi checkboxes dependency issue in Pods core, show_on_account isn't getting picked up here.
			$options['pmpro']['pmpro_account_display_group_label'] = [
				'name'       => 'pmpro_account_display_group_label',
				'label'      => __( 'Display Group Label above fields on Account Details', 'pmpro-pods' ),
				'help'       => __( 'If enabled, this will display the group label above the fields on Account Details. When disabled, only the field values will be displayed without separation.', 'pmpro-pods' ),
				'type'       => 'boolean',
				'default'    => 0,
				'depends-on' => [
					'pmpro_section_member_profile' => 'show_on_account',
				],
			];

			$options['pmpro']['pmpro_section_checkout'] = [
				'name'    => 'pmpro_section_checkout',
				'label'   => __( 'Checkout Section to show this group of fields on', 'pmpro-pods' ),
				// @todo Add a link to a doc page that shows the locations of each.
				'help'    => __( 'All available fields in this group will show in the sections chosen.', 'pmpro-pods' ),
				'type'    => 'pick',
				'data'    => [
					'disabled'                         => __( 'Do NOT show on Checkout', 'pmpro-pods' ),
					'after_level_cost'                 => __( 'After Level Cost / Before Discount', 'pmpro-pods' ),
					// After Level Cost / Before Discount [after_level_cost]
					'after_pricing_fields'             => __( 'After Pricing Fields', 'pmpro-pods' ),
					// After Pricing Fields [after_pricing_fields]
					'after_username'                   => __( 'After Username (no heading)', 'pmpro-pods' ),
					// After Username (no heading) [after_username]
					'after_password'                   => __( 'After Password (no heading)', 'pmpro-pods' ),
					// After Password (no heading) [after_password]
					'after_email'                      => __( 'After Email (no heading)', 'pmpro-pods' ),
					// After User
					'after_user_fields'                => __( 'After User Fields', 'pmpro-pods' ),
					// After Checkout Boxes [after_user_fields]
					'boxes'                            => __( 'Checkout Boxes', 'pmpro-pods' ),
					// After Email (no heading) [after_email]
					'after_billing_fields'             => __( 'After Billing Fields', 'pmpro-pods' ),
					// After Billing Fields [after_billing_fields]
					'after_payment_information_fields' => __( 'After Payment Information Fields', 'pmpro-pods' ),
					// After Payment Information Fields [after_payment_information_fields]
					'after_tos_fields'                 => __( 'After TOS Fields', 'pmpro-pods' ),
					// After TOS Fields [after_tos_fields]
					'after_captcha'                    => __( 'After Captcha', 'pmpro-pods' ),
					// After Captcha [after_captcha]
					'before_submit_button'             => __( 'Before Submit Button', 'pmpro-pods' ),
					// Before Submit Button [before_submit_button]
				],
				'default' => 'after_captcha',
			];
		} elseif ( 'pmpro_membership_level' === $pod_name ) {
			$options['pmpro']['pmpro_section'] = [
				'name'    => 'pmpro_section',
				'label'   => __( 'Sections to show this group of fields on', 'pmpro-pods' ),
				// @todo Add a link to a doc page that shows the locations of each.
				'help'    => __( 'All available fields in this group will show in the sections chosen.', 'pmpro-pods' ),
				'type'    => 'pick',
				'data'    => [
					'after_billing_details_settings' => __( 'After Billing Details', 'pmpro-pods' ),
					// After Billing Details [after_billing_details_settings]
					'after_other_settings'           => __( 'Before Content Settings', 'pmpro-pods' ),
					// Before Content Settings [after_other_settings]
					'after_content_settings'         => __( 'After Content Settings', 'pmpro-pods' ),
					// After Content Settings [after_content_settings]
				],
				'default' => 'after_content_settings',
			];
		}

		$options['pmpro'][ Permissions::OPTION_RESTRICT_MEMBERSHIP_LEVEL ] = [
			'name'             => Permissions::OPTION_RESTRICT_MEMBERSHIP_LEVEL,
			'label'            => __( 'Require Membership to see this group of fields', 'pmpro-pods' ),
			'help'             => __( 'All available fields in this group will show if the Member has the required membership level(s) chosen.', 'pmpro-pods' ),
			'type'             => 'pick',
			'pick_object'      => 'pmpro_membership_level',
			'default'          => 0,
			'pick_format_type' => 'multi',
		];

		if ( 'pmpro_membership_user' === $pod_name ) {
			$options['pmpro'][ Permissions::OPTION_CHECKOUT_MEMBERSHIP_LEVEL ] = [
				'name'              => Permissions::OPTION_CHECKOUT_MEMBERSHIP_LEVEL,
				'label'             => __( 'Show on checkout only for selected Required Membership Levels', 'pmpro-pods' ),
				'help'              => __( 'This will allow showing this field on checkout only for a specific checkout level as selected above. This will allow for the field to be able to be shown on checkout for the corresponding required level as well as when editing the profile field (if the group is set to show on front/admin profile).', 'pmpro-pods' ),
				'type'              => 'boolean',
				'default'           => 0,
				'boolean_yes_label' => '',
				'excludes-on'       => [
					Permissions::OPTION_RESTRICT_MEMBERSHIP_LEVEL => 0,
				],
			];
		}

		return $options;
	}

	/**
	 * Add PMPro tab to fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs List of field tabs.
	 *
	 * @return array List of field tabs.
	 */
	public function admin_setup_edit_field_tabs( $tabs ) {
		$tabs['pmpro'] = __( 'PMPro Options', 'pmpro-pods' );

		return $tabs;
	}

	/**
	 * Add PMPro field options to the PMPro field tab.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $options The list of field options.
	 * @param Whatsit $pod     The Pod object.
	 *
	 * @return array The list of field options.
	 */
	public function admin_setup_edit_field_options( $options, $pod ) {
		$pod_name = $pod->get_name();

		$options['pmpro'] = [];

		$options['pmpro'][ Permissions::OPTION_RESTRICT_MEMBERSHIP_LEVEL ] = [
			'name'             => Permissions::OPTION_RESTRICT_MEMBERSHIP_LEVEL,
			'label'            => __( 'Require Membership to update this field', 'pmpro-pods' ),
			'help'             => __( 'If this is a checkout field, then having this membership level already will be required to see this field.', 'pmpro-pods' ),
			'type'             => 'pick',
			'pick_object'      => 'pmpro_membership_level',
			'default'          => 0,
			'pick_format_type' => 'multi',
			'dependency'       => true,
		];

		if ( 'pmpro_membership_user' === $pod_name ) {
			$options['pmpro'][ Permissions::OPTION_CHECKOUT_MEMBERSHIP_LEVEL ] = [
				'name'              => Permissions::OPTION_CHECKOUT_MEMBERSHIP_LEVEL,
				'label'             => __( 'Show on checkout only for selected Required Membership Levels', 'pmpro-pods' ),
				'help'              => __( 'This will allow showing this field on checkout only for a specific checkout level as selected above. This will allow for the field to be able to be shown on checkout for the corresponding required level as well as when editing the profile field (if the group is set to show on front/admin profile).', 'pmpro-pods' ),
				'type'              => 'boolean',
				'default'           => 0,
				'boolean_yes_label' => '',
				'excludes-on'       => [
					Permissions::OPTION_RESTRICT_MEMBERSHIP_LEVEL => 0,
				],
			];
		}

		return $options;
	}

	/**
	 * Add PMPro Pod options to the PMPro Pod tab.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $options The list of Pod options.
	 * @param Whatsit $pod     The Pod object.
	 *
	 * @return array The list of Pod options.
	 */
	public function admin_setup_edit_options_post_type( $options ) {
		// @todo Core: Support this in the future better with CPT supports compatibility in PMPro.

		// @todo Add support for this.
		$options['advanced']['pmpro_enable_require_membership'] = [
			'name'              => 'pmpro_enable_require_membership',
			'label'             => __( 'PMPro: Enable Require Membership functionality', 'pmpro-pods' ),
			'type'              => 'boolean',
			'default'           => 0,
			'boolean_yes_label' => '',
		];

		return $options;
	}

}
