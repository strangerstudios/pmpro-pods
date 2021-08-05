<?php
/*
Plugin Name: Paid Memberships Pro - Pods Add On
Plugin URI: https://www.paidmembershipspro.com/add-ons/pods/
Description: Pods integration for Paid Memberships Pro
Version: 0.1
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com/
Text Domain: pmpro-pods
*/

require_once 'includes/metadata-compat.php';
require_once 'includes/pods-integration.php';
require_once 'includes/permissions.php';
require_once 'includes/objects/level.php';
require_once 'includes/objects/member-checkout.php';
require_once 'includes/objects/member-profile.php';
require_once 'includes/objects/order.php';

/**
 * Determine whether the Pod type is a PMPro integration type.
 *
 * @since TBD
 *
 * @param string $type The Pod type.
 *
 * @return bool Whether the Pod type is a PMPro integration type.
 */
function pmpro_pods_is_covered_pod_type( $type ) {
	$types = pmpro_pods_get_pod_types();

	return isset( $types[ $type ] );
}

/**
 * Get the list of PMPro integration types and their configurations.
 *
 * @since TBD
 *
 * @return array[] The list of PMPro integration types and their configurations.
 */
function pmpro_pods_get_pod_types() {
	global $wpdb;

	return [
		'pmpro_membership_user' => [
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
		'pmpro_membership_order'  => [
			'label'            => __( 'PMPro Order', 'pmpro-pods' ),
			'table'            => $wpdb->pmpro_membership_orders,
			'meta_table'       => $wpdb->pmpro_membership_ordermeta,
			'field_id'         => 'id',
			'field_index'      => 'code',
			'meta_field_id'    => 'pmpro_membership_order_id',
			'meta_field_index' => 'meta_key',
			'meta_field_value' => 'meta_value',
		],
		'pmpro_membership_level'  => [
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

/**
 * Get the list of all level names keyed by ID.
 *
 * @since TBD
 *
 * @return string[] List of all level names keyed by ID.
 */
function pmpro_pods_get_all_level_names_by_id() {
	if ( ! function_exists( 'pmpro_getAllLevels' ) ) {
		return [];
	}

	$all_levels = pmpro_getAllLevels();

	$levels = [];

	foreach ( $all_levels as $level ) {
		$levels[ $level->id ] = $level->name;
	}

	return $levels;
}

return;

/**
 * NOTE: The code below is for further reference and will be removed later.
 *
 * quu..__
 *  $$$b  `---.__
 *   "$$b        `--.                          ___.---uuudP
 *    `$$b           `.__.------.__     __.---'      $$$$"              .
 *      "$b          -'            `-.-'            $$$"              .'|
 *        ".                                       d$"             _.'  |
 *          `.   /                              ..."             .'     |
 *            `./                           ..::-'            _.'       |
 *             /                         .:::-'            .-'         .'
 *            :                          ::''\          _.'            |
 *           .' .-.             .-.           `.      .'               |
 *           : /'$$|           .@"$\           `.   .'              _.-'
 *          .'|$u$$|          |$$,$$|           |  <            _.-'
 *          | `:$$:'          :$$$$$:           `.  `.       .-'
 *          :                  `"--'             |    `-.     \
 *         :##.       ==             .###.       `.      `.    `\
 *         |##:                      :###:        |        >     >
 *         |#'     `..'`..'          `###'        x:      /     /
 *          \                                   xXX|     /    ./
 *           \                                xXXX'|    /   ./
 *           /`-.                                  `.  /   /
 *          :    `-  ...........,                   | /  .'
 *          |         ``:::::::'       .            |<    `.
 *          |             ```          |           x| \ `.:``.
 *          |                         .'    /'   xXX|  `:`M`M':.
 *          |    |                    ;    /:' xXXX'|  -'MMMMM:'
 *          `.  .'                   :    /:'       |-'MMMM.-'
 *           |  |                   .'   /'        .'MMM.-'
 *           `'`'                   :  ,'          |MMM<
 *             |                     `'            |tbap\
 *              \                                  :MM.-'
 *               \                 |              .''
 *                \.               `.            /
 *                 /     .:::::::.. :           /
 *                |     .:::::::::::`.         /
 *                |   .:::------------\       /
 *               /   .''               >::'  /
 *               `',:                 :    .'
 *
 * @link https://asciiart.website/index.php?art=video%20games/pokemon
 */

/**
 * Add a field to the PMProRH regisration fields global
 *
 *    $where refers to various hooks in the PMPro checkout page and can be:
 *    - after_username
 *    - after_password
 *    - after_email
 *    - after_captcha
 *    - checkout_boxes
 *    - after_billing_fields
 *    - before_submit_button
 *    - just_profile (make sure you set the profile attr of the field to true or admins)
 */

/**
 * Update the fields at checkout.
 */
function pmpro_pods_after_checkout_save_fields( $user_id ) {
	global $pmpro_user_fields;

	//any fields?
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

				if ( ! empty( $field->profile ) && ( $field->profile === 'only' || $field->profile === 'only_admin' ) ) {
					continue;    //wasn't shown at checkout
				}

				//assume no value
				$value = null;

				//where are we getting the value from?
				if ( isset( $_REQUEST[ $field->name ] ) ) {
					//request
					$value = $_REQUEST[ $field->name ];
				} elseif ( isset( $_REQUEST[ $field->name . '_checkbox' ] ) && $field->type == 'checkbox' ) {
					//unchecked checkbox
					$value = 0;
				} elseif ( ! empty( $_POST[ $field->name . '_checkbox' ] ) && in_array( $field->type, [
						'checkbox',
						'checkbox_grouped',
						'select2',
					] ) )    //handle unchecked checkboxes
				{
					//unchecked checkbox
					$value = [];
				} elseif ( isset( $_SESSION[ $field->name ] ) ) {
					//file or value?
					if ( is_array( $_SESSION[ $field->name ] ) && isset( $_SESSION[ $field->name ]['name'] ) ) {
						//add to files global
						$_FILES[ $field->name ] = $_SESSION[ $field->name ];

						//set value to name
						$value = $_SESSION[ $field->name ]['name'];
					} else {
						//session
						$value = $_SESSION[ $field->name ];
					}

					//unset
					unset( $_SESSION[ $field->name ] );
				} elseif ( isset( $_FILES[ $field->name ] ) ) {
					//file
					$value = $_FILES[ $field->name ]['name'];
				}

				//update user meta
				if ( isset( $value ) ) {
					if ( isset( $field->sanitize ) && true === $field->sanitize ) {
						$value = pmpro_sanitize( $value, $field );
					}

					//callback?
					if ( ! empty( $field->save_function ) ) {
						call_user_func( $field->save_function, $user_id, $field->name, $value );
					} else {
						update_user_meta( $user_id, $field->meta_key, $value );
					}
				}
			}
		}
	}
}

add_action( 'pmpro_after_checkout', 'pmpro_after_checkout_save_fields' );
add_action( 'pmpro_before_send_to_paypal_standard', 'pmpro_after_checkout_save_fields' );    //for paypal standard we need to do this just before sending the user to paypal
add_action( 'pmpro_before_send_to_twocheckout', 'pmpro_after_checkout_save_fields', 20 );    //for 2checkout we need to do this just before sending the user to 2checkout
add_action( 'pmpro_before_send_to_gourl', 'pmpro_after_checkout_save_fields', 20 );    //for the GoURL Bitcoin Gateway Add On
add_action( 'pmpro_before_send_to_payfast', 'pmpro_after_checkout_save_fields', 20 );    //for the Payfast Gateway Add On

/**
 * Require required fields.
 */
function pmpro_pods_registration_checks_for_user_fields( $okay ) {
	global $current_user;

	//arrays to store fields that were required and missed
	$required        = [];
	$required_labels = [];

	//any fields?
	global $pmpro_user_fields;
	if ( ! empty( $pmpro_user_fields ) ) {
		//cycle through groups
		foreach ( $pmpro_user_fields as $where => $fields ) {
			//cycle through fields
			foreach ( $fields as $field ) {
				//handle arrays
				$field->name = preg_replace( '/\[\]$/', '', $field->name );

				//if the field is not for this level, skip it
				if ( ! pmpro_is_field( $field ) ) {
					continue;
				}

				if ( ! pmpro_check_field_for_level( $field, 'profile', $user_id ) ) {
					continue;
				}

				if ( ! empty( $field->profile ) && ( $field->profile === 'only' || $field->profile === 'only_admin' ) ) {
					continue;    //wasn't shown at checkout
				}

				if ( isset( $_REQUEST[ $field->name ] ) ) {
					$value = $_REQUEST[ $field->name ];
				} elseif ( isset( $_FILES[ $field->name ] ) ) {
					$value = $_FILES[ $field->name ]['name'];

					//handle empty file but the user already has a file
					if ( empty( $value ) && ! empty( $_REQUEST[ $field->name . '_old' ] ) ) {
						$value = $_REQUEST[ $field->name . '_old' ];
					} elseif ( ! empty( $value ) ) {
						//check extension against allowed extensions
						$filetype = wp_check_filetype_and_ext( $_FILES[ $field->name ]['tmp_name'], $_FILES[ $field->name ]['name'] );
						if ( ( ! $filetype['type'] || ! $filetype['ext'] ) && ! current_user_can( 'unfiltered_upload' ) ) {
							if ( $okay )    //only want to update message if there is no previous error
							{
								pmpro_setMessage( sprintf( __( 'Sorry, the file type for %s is not permitted for security reasons.', 'pmpro-pods' ), $_FILES[ $field->name ]['name'] ), 'pmpro_error' );
							}

							return false;
						} else {
							//check for specific extensions anyway
							if ( ! empty( $field->ext ) && ! in_array( $filetype['ext'], $field->ext ) ) {
								if ( $okay )    //only want to update message if there is no previous error
								{
									pmpro_setMessage( sprintf( __( 'Sorry, the file type for %s is not permitted for security reasons.', 'pmpro-pods' ), $_FILES[ $field->name ]['name'] ), 'pmpro_error' );
								}

								return false;
							}
						}
					}
				} else {
					$value = false;
				}

				if ( ! $field->was_filled_if_needed() ) {
					$required[]        = $field->name;
					$required_labels[] = $field->label;
				}
			}
		}
	}

	if ( ! empty( $required ) ) {
		$required = array_unique( $required );

		//add them to error fields
		global $pmpro_error_fields;
		$pmpro_error_fields = array_merge( (array) $pmpro_error_fields, $required );

		if ( count( $required ) == 1 ) {
			$pmpro_msg  = sprintf( __( 'The %s field is required.', 'pmpro-register-helper' ), implode( ', ', $required_labels ) );
			$pmpro_msgt = 'pmpro_error';
		} else {
			$pmpro_msg  = sprintf( __( 'The %s fields are required.', 'pmpro-register-helper' ), implode( ', ', $required_labels ) );
			$pmpro_msgt = 'pmpro_error';
		}

		if ( $okay ) {
			pmpro_setMessage( $pmpro_msg, $pmpro_msgt );
		}

		return false;
	}

	//return whatever status was before
	return $okay;
}

add_filter( 'pmpro_registration_checks', 'pmpro_registration_checks_for_user_fields' );

/**
 * Show user fields in profile.
 */
function pmpro_pods_show_user_fields_in_profile( $user, $withlocations = false ) {
	global $pmpro_user_fields;

	//which fields are marked for the profile
	$profile_fields = pmpro_get_user_fields_for_profile( $user->ID, $withlocations );

	//show the fields
	if ( ! empty( $profile_fields ) && $withlocations ) {
		foreach ( $profile_fields as $where => $fields ) {
			$box = pmpro_get_field_group_by_name( $where );

			if ( ! empty( $box->label ) ) { ?>
				<h3><?php echo $box->label; ?></h3><?php
			} ?>

			<table class="form-table">
				<?php
				//cycle through groups
				foreach ( $fields as $field ) {
					if ( pmpro_is_field( $field ) ) {
						$field->displayInProfile( $user->ID );
					}
				}
				?>
			</table>
			<?php
		}
	} elseif ( ! empty( $profile_fields ) ) {
		?>
		<table class="form-table">
			<?php
			//cycle through groups
			foreach ( $profile_fields as $field ) {
				if ( pmpro_is_field( $field ) ) {
					$field->displayInProfile( $user->ID );
				}
			}
			?>
		</table>
		<?php
	}
}

function pmpro_pods_show_user_fields_in_profile_with_locations( $user ) {
	pmpro_pods_show_user_fields_in_profile( $user, true );
}

add_action( 'show_user_profile', 'pmpro_pods_show_user_fields_in_profile_with_locations' );
add_action( 'edit_user_profile', 'pmpro_pods_show_user_fields_in_profile_with_locations' );

/**
 * Show Profile fields on the frontend "Member Profile Edit" page.
 *
 * @since 2.3
 */
function pmpro_pods_show_user_fields_in_frontend_profile( $user, $withlocations = false ) {
	global $pmpro_user_fields;

	//which fields are marked for the profile
	$profile_fields = pmpro_get_user_fields_for_profile( $user->ID, $withlocations );

	//show the fields
	if ( ! empty( $profile_fields ) && $withlocations ) {
		foreach ( $profile_fields as $where => $fields ) {
			$box = pmpro_get_field_group_by_name( $where );

			// Only show on front-end if there are fields to be shown.
			$show_fields = false;
			foreach ( $fields as $key => $field ) {
				if ( $field->profile !== 'only_admin' ) {
					$show_fields = true;
				}
			}

			// Bail if there are no fields to show on the front-end profile.
			if ( ! $show_fields ) {
				return;
			}
			?>

			<div class="pmpro_checkout_box-<?php echo $where; ?>">
				<?php if ( ! empty( $box->label ) ) { ?>
					<h3><?php echo $box->label; ?></h3>
				<?php } ?>

				<div class="pmpro_member_profile_edit-fields">
					<?php if ( ! empty( $box->description ) ) { ?>
						<div class="pmpro_checkout_description"><?php echo $box->description; ?></div>
					<?php } ?>

					<?php
					// Cycle through groups.
					foreach ( $fields as $field ) {
						if ( pmpro_is_field( $field ) && $field->profile !== 'only_admin' ) {
							$field->displayAtCheckout( $user->ID );
						}
					}
					?>
				</div> <!-- end pmpro_member_profile_edit-fields -->
			</div> <!-- end pmpro_checkout_box_$where -->
			<?php
		}
	} elseif ( ! empty( $profile_fields ) ) { ?>
		<div class="pmpro_member_profile_edit-fields">
			<?php
			// Cycle through groups.
			foreach ( $fields as $field ) {
				if ( pmpro_is_field( $field ) && $field->profile !== 'only_admin' ) {
					$field->displayAtCheckout( $user->ID );
				}
			}
			?>
		</div> <!-- end pmpro_member_profile_edit-fields -->
		<?php
	}
}

function pmpro_pods_show_user_fields_in_frontend_profile_with_locations( $user ) {
	pmpro_show_user_fields_in_frontend_profile( $user, true );
}

add_action( 'pmpro_show_user_profile', 'pmpro_show_user_fields_in_frontend_profile_with_locations' );

/**
 * Show user fields on the Add Member form
 * when using the Add Member Admin Add On.
 */
// Add fields to form.
function pmpro_pods_add_member_admin_fields( $user = null, $user_id = null ) {
	global $pmpro_user_fields;

	$addmember_fields = [];
	if ( ! empty( $pmpro_user_fields ) ) {
		//cycle through groups
		foreach ( $pmpro_user_fields as $where => $fields ) {
			//cycle through fields
			foreach ( $fields as $field ) {
				if ( pmpro_is_field( $field ) && isset( $field->addmember ) && ! empty( $field->addmember ) && ( in_array( strtolower( $field->addmember ), [
							'true',
							'yes',
						] ) || true == $field->addmember ) ) {
					$addmember_fields[] = $field;
				}
			}
		}
	}

	//show the fields
	if ( ! empty( $addmember_fields ) ) {
		?>
		<?php
		//cycle through groups
		foreach ( $addmember_fields as $field ) {
			if ( empty( $user_id ) && ! empty( $user ) && ! empty( $user->ID ) ) {
				$user_id = $user->ID;
			}

			if ( pmpro_is_field( $field ) ) {
				$field->displayInProfile( $user_id );
			}
		}
		?>
		<?php
	}
}

add_action( 'pmpro_add_member_fields', 'pmpro_add_member_admin_fields', 10, 2 );

// Save the fields.
function pmpro_pods_add_member_admin_save_user_fields( $uid = null, $user = null ) {
	/**
	 * BUG: Incorrectly assumed that the user_login $_REQUEST[] variable exists
	 *
	 * @since 1.3
	 */
	if ( ! empty( $user ) && is_object( $user ) ) {
		$user_id = $user->ID;
	}

	if ( ! empty( $uid ) && ( empty( $user ) || ! is_object( $user ) ) ) {
		$user_id = $uid;
	}

	if ( empty( $uid ) && ( empty( $user ) || ! is_object( $user ) ) ) {
		$user_login = isset( $_REQUEST['user_login'] ) ? $_REQUEST['user_login'] : null;

		if ( ! empty( $user_login ) ) {
			$user_id = get_user_by( 'login', $_REQUEST['user_login'] )->ID;
		}
	}

	// check whether the user login variable contains something useful
	if ( empty( $user_id ) ) {
		global $pmpro_msgt;
		global $pmpro_msg;

		$pmpro_msg  = __( 'Unable to add/update PMPro Register Helper registration fields for this member', 'pmpro-pods' );
		$pmpro_msgt = 'pmpro_error';

		return false;
	}

	global $pmpro_user_fields;

	$addmember_fields = [];
	if ( ! empty( $pmpro_user_fields ) ) {
		//cycle through groups
		foreach ( $pmpro_user_fields as $where => $fields ) {
			//cycle through fields
			foreach ( $fields as $field ) {
				if ( pmpro_is_field( $field ) && isset( $field->addmember ) && ! empty( $field->addmember ) && ( in_array( strtolower( $field->addmember ), [
							'true',
							'yes',
						] ) || true == $field->addmember ) ) {
					$addmember_fields[] = $field;
				}
			}
		}
	}

	//save our added fields in session while the user goes off to PayPal
	if ( ! empty( $addmember_fields ) ) {
		//cycle through fields
		foreach ( $addmember_fields as $field ) {
			if ( pmpro_is_field( $field ) && isset( $_POST[ $field->name ] ) || isset( $_FILES[ $field->name ] ) ) {
				if ( isset( $field->sanitize ) && true === $field->sanitize ) {
					$value = pmpro_sanitize( $_POST[ $field->name ], $field );
				} elseif ( isset( $_POST[ $field->name ] ) ) {
					$value = $_POST[ $field->name ];
				} else {
					$value = $_FILES[ $field->name ];
				}

				//callback?
				if ( ! empty( $field->save_function ) ) {
					call_user_func( $field->save_function, $user_id, $field->name, $value );
				} else {
					update_user_meta( $user_id, $field->meta_key, $value );
				}
			} elseif ( pmpro_is_field( $field ) && ! empty( $_POST[ $field->name . '_checkbox' ] ) && $field->type == 'checkbox' )    //handle unchecked checkboxes
			{
				//callback?
				if ( ! empty( $field->save_function ) ) {
					call_user_func( $field->save_function, $user_id, $field->name, 0 );
				} else {
					update_user_meta( $user_id, $field->meta_key, 0 );
				}
			} elseif ( ! empty( $_POST[ $field->name . '_checkbox' ] ) && in_array( $field->type, [
					'checkbox',
					'checkbox_grouped',
					'select2',
				] ) )    //handle unchecked checkboxes
			{
				//callback?
				if ( ! empty( $field->save_function ) ) {
					call_user_func( $field->save_function, $user_id, $field->name, [] );
				} else {
					update_user_meta( $user_id, $field->meta_key, [] );
				}
			}
		}
	}
}

add_action( 'pmpro_add_member_added', 'pmpro_add_member_admin_save_user_fields', 10, 2 );

/**
 * Get RH fields which are set to showup in the Members List CSV Export.
 */
function pmpro_pods_get_user_fields_for_csv() {
	global $pmpro_user_fields;

	$csv_fields = [];
	if ( ! empty( $pmpro_user_fields ) ) {
		//cycle through groups
		foreach ( $pmpro_user_fields as $where => $fields ) {
			//cycle through fields
			foreach ( $fields as $field ) {
				if ( pmpro_is_field( $field ) && ! empty( $field->memberslistcsv ) && ( $field->memberslistcsv == 'true' ) ) {
					$csv_fields[] = $field;
				}
			}
		}
	}

	return $csv_fields;
}

/**
 * Get user fields which are marked to show in the profile.
 * If a $user_id is passed in, get fields based on the user's level.
 */
function pmpro_pods_get_user_fields_for_profile( $user_id, $withlocations = false ) {
	global $pmpro_user_fields;

	$profile_fields = [];
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

				if ( ! empty( $field->profile ) && ( $field->profile === 'admins' || $field->profile === 'admin' || $field->profile === 'only_admin' ) ) {
					if ( current_user_can( 'manage_options' ) || current_user_can( 'pmpro_membership_manager' ) ) {
						if ( $withlocations ) {
							$profile_fields[ $where ][] = $field;
						} else {
							$profile_fields[] = $field;
						}
					}
				} elseif ( ! empty( $field->profile ) ) {
					if ( $withlocations ) {
						$profile_fields[ $where ][] = $field;
					} else {
						$profile_fields[] = $field;
					}
				}
			}
		}
	}

	return $profile_fields;
}

/**
 * Change the enctype of the edit user form in case files need to be uploaded.
 */
function pmpro_pods_user_edit_form_tag() {
	echo ' enctype="multipart/form-data"';
}

add_action( 'user_edit_form_tag', 'pmpro_user_edit_form_tag' );

/**
 * Save profile fields.
 */
function pmpro_pods_save_user_fields_in_profile( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$profile_fields = pmpro_get_user_fields_for_profile( $user_id );

	//save our added fields in session while the user goes off to PayPal
	if ( ! empty( $profile_fields ) ) {
		//cycle through fields
		foreach ( $profile_fields as $field ) {
			if ( ! pmpro_is_field( $field ) ) {
				continue;
			}

			if ( isset( $_POST[ $field->name ] ) || isset( $_FILES[ $field->name ] ) ) {
				if ( isset( $_POST[ $field->name ] ) && isset( $field->sanitize ) && true === $field->sanitize ) {
					$value = pmpro_sanitize( $_POST[ $field->name ], $field );
				} elseif ( isset( $_POST[ $field->name ] ) ) {
					$value = $_POST[ $field->name ];
				} else {
					$value = $_FILES[ $field->name ];
				}

				//callback?
				if ( ! empty( $field->save_function ) ) {
					call_user_func( $field->save_function, $user_id, $field->name, $value );
				} else {
					update_user_meta( $user_id, $field->meta_key, $value );
				}
			} elseif ( ! empty( $_POST[ $field->name . '_checkbox' ] ) && $field->type == 'checkbox' )    //handle unchecked checkboxes
			{
				//callback?
				if ( ! empty( $field->save_function ) ) {
					call_user_func( $field->save_function, $user_id, $field->name, 0 );
				} else {
					update_user_meta( $user_id, $field->meta_key, 0 );
				}
			} elseif ( ! empty( $_POST[ $field->name . '_checkbox' ] ) && in_array( $field->type, [
					'checkbox',
					'checkbox_grouped',
					'select2',
				] ) )    //handle unchecked checkboxes
			{
				//callback?
				if ( ! empty( $field->save_function ) ) {
					call_user_func( $field->save_function, $user_id, $field->name, [] );
				} else {
					update_user_meta( $user_id, $field->meta_key, [] );
				}
			}
		}
	}
}

add_action( 'personal_options_update', 'pmpro_save_user_fields_in_profile' );
add_action( 'edit_user_profile_update', 'pmpro_save_user_fields_in_profile' );
add_action( 'pmpro_personal_options_update', 'pmpro_save_user_fields_in_profile' );

/**
 * Add user fields to confirmation email.
 */
function pmpro_pods_add_user_fields_to_email( $email ) {
	global $wpdb;

	//only update admin confirmation emails
	if ( ! empty( $email ) && strpos( $email->template, 'checkout' ) !== false && strpos( $email->template, 'admin' ) !== false ) {
		//get the user_id from the email
		$user_id = $wpdb->get_var( "SELECT ID FROM $wpdb->users WHERE user_email = '" . $email->data['user_email'] . "' LIMIT 1" );

		if ( ! empty( $user_id ) ) {
			//get meta fields
			$fields = pmpro_get_user_fields_for_profile( $user_id );

			//add to bottom of email
			if ( ! empty( $fields ) ) {
				$email->body .= '<p>' . __( 'Extra Fields:', 'paid-memberships-pro' ) . '<br />';
				foreach ( $fields as $field ) {
					if ( ! pmpro_is_field( $field ) ) {
						continue;
					}

					$email->body .= '- ' . $field->label . ': ';

					$value = get_user_meta( $user_id, $field->meta_key, true );
					if ( $field->type == 'file' && is_array( $value ) && ! empty( $value['fullurl'] ) ) {
						$email->body .= $value['fullurl'];
					} elseif ( is_array( $value ) ) {
						$email->body .= implode( ', ', $value );
					} else {
						$email->body .= $value;
					}

					$email->body .= '<br />';
				}
				$email->body .= '</p>';
			}
		}
	}

	return $email;
}

add_filter( 'pmpro_email_filter', 'pmpro_add_user_fields_to_email', 10, 2 );

/**
 * Add CSV fields to the Member's List CSV Export.
 */
function pmpro_pods_members_list_csv_extra_columns_for_user_fields( $columns ) {
	$csv_cols = pmpro_pods_get_user_fields_for_csv();
	foreach ( $csv_cols as $key => $value ) {
		$columns[ $value->meta_key ] = 'pmpro_csv_columns_for_user_fields';
	}

	return $columns;
}

add_filter( 'pmpro_members_list_csv_extra_columns', 'pmpro_members_list_csv_extra_columns_for_user_fields', 10 );

/**
 * Get user meta for the added CSV columns.
 */
function pmpro_pods_csv_columns_for_user_fields( $user, $column ) {
	if ( ! empty( $user->metavalues->{$column} ) ) {
		// check for multiple values
		$value = maybe_unserialize( $user->metavalues->{$column} );
		if ( is_array( $value ) ) {
			$value = join( ',', $value );
		}

		return $value;
	}

	return '';
}
