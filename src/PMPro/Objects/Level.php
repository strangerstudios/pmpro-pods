<?php

namespace PMPro_Pods\PMPro\Objects;

/**
 * PMPro Membership Level object hooks.
 *
 * Admin field locations:
 *        do_action( 'pmpro_membership_level_after_billing_details_settings', $level );
 *        do_action( 'pmpro_membership_level_after_other_settings', $level );
 *        do_action( 'pmpro_membership_level_after_content_settings', $level );
 *
 * Markup:
 *        <hr />
 *        <h2>Group label</h2>
 *        <table class="form-table">
 *
 * Save hook:
 *        do_action( 'pmpro_save_membership_level', $level_id );
 *
 * Meta:
 *        get_pmpro_membership_level_meta( $level_id, 'meta_key', $single );
 *        add_pmpro_membership_level_meta( $level_id, 'meta_key', 'value' );
 *        update_pmpro_membership_level_meta( $level_id, 'meta_key', 'value' );
 *        delete_pmpro_membership_level_meta( $level_id, 'meta_key' );
 *
 * @since   1.0.0
 * @package PMPro_Pods
 */
class Level {

	/**
	 * Add hooks for class.
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		add_action( 'pmpro_membership_level_after_billing_details_settings', [ $this, 'pmpro_membership_level_after_billing_details_settings' ] );
		add_action( 'pmpro_membership_level_after_other_settings', [ $this, 'pmpro_membership_level_after_other_settings' ] );
		add_action( 'pmpro_membership_level_after_content_settings', [ $this, 'pmpro_membership_level_after_content_settings' ] );
		add_action( 'pmpro_save_membership_level', [ $this, 'pmpro_save_membership_level' ] );
	}

	/**
	 * Remove hooks from class.
	 *
	 * @since 1.0.0
	 */
	public function unhook() {
		remove_action( 'pmpro_membership_level_after_billing_details_settings', [ $this, 'pmpro_membership_level_after_billing_details_settings' ] );
		remove_action( 'pmpro_membership_level_after_other_settings', [ $this, 'pmpro_membership_level_after_other_settings' ] );
		remove_action( 'pmpro_membership_level_after_content_settings', [ $this, 'pmpro_membership_level_after_content_settings' ] );
		remove_action( 'pmpro_save_membership_level', [ $this, 'pmpro_save_membership_level' ] );
	}

	/**
	 * Render the fields after billing details settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param object $level The membership level object.
	 */
	public function pmpro_membership_level_after_billing_details_settings( $level ) {
		pods_form_render_fields( 'pmpro_membership_level', $level->id, [
			'section_field' => 'pmpro_section',
			'section'       => 'after_billing_details_settings',
		] );
	}

	/**
	 * Render the fields after other settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param object $level The membership level object.
	 */
	public function pmpro_membership_level_after_other_settings( $level ) {
		pods_form_render_fields( 'pmpro_membership_level', $level->id, [
			'section_field' => 'pmpro_section',
			'section'       => 'after_other_settings',
		] );
	}

	/**
	 * Render the fields after content settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param object $level The membership level object.
	 */
	public function pmpro_membership_level_after_content_settings( $level ) {
		pods_form_render_fields( 'pmpro_membership_level', $level->id, [
			'section_field' => 'pmpro_section',
			'section'       => 'after_content_settings',
		] );
	}

	/**
	 * Handle saving the submitted fields for the object.
	 *
	 * @since 1.0.0
	 *
	 * @param int $level_id The level ID.
	 */
	public function pmpro_save_membership_level( $level_id ) {
		pods_form_save_submitted_fields( 'pmpro_membership_level', $level_id );
	}

}
