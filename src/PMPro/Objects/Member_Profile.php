<?php

namespace PMPro_Pods\PMPro\Objects;

/**
 * PMPro Member object hooks for Profile.
 *
 * Frontend field locations:
 *		do_action( 'pmpro_show_user_profile', $user );
 *		do_action( 'pmpro_add_member_fields', $user );
 *
 * Admin field locations:
 *		do_action( 'show_user_profile', $user );
 *		do_action( 'edit_user_profile', $user );
 *
 * Markup:
 *		<hr />
 *		<h2>Group label</h2>
 *		<table class="form-table">
 *
 * Markup (pmpro_show_user_profile):
 *		<hr />
 *		<h2>Group label</h2>
 *		<div ...>
 *
 * Save hook:
 *      do_action( 'personal_options_update', $user_id );
 *      do_action( 'edit_user_profile_update', $user_id );
 *      do_action( 'pmpro_personal_options_update', $user_id );
 *      do_action( 'pmpro_add_member_added', $user_id );
 *
 * Meta:
 *        get_user_meta( $user_id, 'meta_key', $single );
 *        add_user_meta( $user_id, 'meta_key', 'value' );
 *        update_user_meta( $user_id, 'meta_key', 'value' );
 *        delete_user_meta( $user_id, 'meta_key' );
 *
 * @since   1.0.0
 * @package PMPro_Pods
 */
class Member_Profile {

	/**
	 * Add hooks for class.
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		add_action( 'pmpro_show_user_profile', [ $this, 'pmpro_show_user_profile' ] );
		add_action( 'pmpro_account_bullets_bottom', [ $this, 'pmpro_account_bullets_bottom' ] );
		add_action( 'pmpro_add_member_fields', [ $this, 'pmpro_add_member_fields' ] );
		add_action( 'show_user_profile', [ $this, 'show_user_profile' ] );
		add_action( 'edit_user_profile', [ $this, 'edit_user_profile' ] );

		add_action( 'personal_options_update', [ $this, 'personal_options_update' ] );
		add_action( 'edit_user_profile_update', [ $this, 'edit_user_profile_update' ] );
		add_action( 'pmpro_personal_options_update', [ $this, 'pmpro_personal_options_update' ] );

		// @todo Debug this.
		//add_action( 'pmpro_add_member_added', [ $this, 'pmpro_add_member_added' ] );
	}

	/**
	 * Remove hooks from class.
	 *
	 * @since 1.0.0
	 */
	public function unhook() {
		remove_action( 'pmpro_show_user_profile', [ $this, 'pmpro_show_user_profile' ] );
		remove_action( 'pmpro_add_member_fields', [ $this, 'pmpro_add_member_fields' ] );
		remove_action( 'show_user_profile', [ $this, 'show_user_profile' ] );
		remove_action( 'edit_user_profile', [ $this, 'edit_user_profile' ] );

		remove_action( 'personal_options_update', [ $this, 'personal_options_update' ] );
		remove_action( 'edit_user_profile_update', [ $this, 'edit_user_profile_update' ] );
		remove_action( 'pmpro_personal_options_update', [ $this, 'pmpro_personal_options_update' ] );

		// @todo Debug this.
		//remove_action( 'pmpro_add_member_added', [ $this, 'pmpro_add_member_added' ] );
	}

	/**
	 * Render the fields for the frontend user profile form.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_show_user_profile( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_member_profile',
			'section'       => 'show_on_front',
			'render'        => 'div-rows',
			'heading'       => 'h2',
			'separator'     => 'off',
		] );
	}

	/**
	 * Render the fields for the frontend user profile form.
	 *
	 * @since TBD
	 */
	public function pmpro_account_bullets_bottom() {
		$item_id = get_current_user_id();

		$pod = pods( 'pmpro_membership_user', $item_id );

		// Check if Pod is valid and the item exists.
		if ( ! $pod->valid() || ! $pod->exists() ) {
			return;
		}

		$groups = pods_form_get_visible_objects( $pod, [
			'section_field' => 'pmpro_section_member_profile',
			'section'       => 'show_on_account',
			'return_type'   => 'group',
		] );

		foreach ( $groups as $group ) {
			$fields = $group->get_fields();

			if ( ! empty( $group['pmpro_account_display_group_label'] ) ) {
				printf(
					'</ul><p>%s</p><ul>',
					esc_html( $group['label'] )
				);
			}

			foreach ( $fields as $field ) {
				$value = $pod->display( $field['name'] );

				// Skip empty values.
				if ( empty( $value ) ) {
					continue;
				}

				printf(
					'<li><strong>%1$s:</strong> %2$s</li>',
					esc_html( $field['label'] ),
					$value
				);
			}
		}
	}

	/**
	 * Render the fields for the frontend user registration form.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User $user The user object.
	 */
	public function pmpro_add_member_fields( $user ) {
		/**
		 * If we add fields then it separates from the rest of the fields and we need a heading to
		 * make it clear what the next fields are.
		 */
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field'     => 'pmpro_section_member_profile',
			'section'           => 'show_on_front',
			'render'            => 'table-separated',
			'heading'           => 'h2',
			'separated_heading' => __( 'Order Information', 'pmpro-pods' ),
			'separator'         => 'off',
		] );
	}

	/**
	 * Render the fields for the admin user profile form.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User $user The user object.
	 */
	public function show_user_profile( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_member_profile',
			'section'       => 'show_on_admin',
			'heading'       => 'h2',
		] );
	}

	/**
	 * Render the fields for the admin user edit form.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User $user The user object.
	 */
	public function edit_user_profile( $user ) {
		pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
			'section_field' => 'pmpro_section_member_profile',
			'section'       => 'show_on_admin',
			'heading'       => 'h2',
		] );
	}

	/**
	 * Handle saving the submitted fields for the object on the admin member profile section when saving your own profile.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user ID.
	 */
	public function personal_options_update( $user_id ) {
		pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id, false, [
			'section_field' => 'pmpro_section_member_profile',
			'section'       => 'show_on_admin',
		] );
	}

	/**
	 * Handle saving the submitted fields for the object on the admin member profile section when editing another user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user ID.
	 */
	public function edit_user_profile_update( $user_id ) {
		pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id, false, [
			'section_field' => 'pmpro_section_member_profile',
			'section'       => 'show_on_admin',
		] );
	}

	/**
	 * Handle saving the submitted fields for the object on the front member profile section when saving you own profile.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The user ID.
	 */
	public function pmpro_personal_options_update( $user_id ) {
		pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id, false, [
			'section_field' => 'pmpro_section_member_profile',
			'section'       => 'show_on_front',
		] );
	}

	/**
	 * @todo Debug this.
	 *
	 * @param $check
	 *
	 * @return mixed
	 */
	public function pmpro_add_member_added( $check ) {
		return $check;
	}

}
