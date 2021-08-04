<?php
/**
 * Membership Levels
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
 */

add_action( 'pmpro_membership_level_after_billing_details_settings', static function ( $level ) {
	pods_form_render_fields( 'pmpro_membership_level', $level->id, [
		'section_field' => 'pmpro_section',
		'section'       => 'after_billing_details_settings',
	] );
} );

add_action( 'pmpro_membership_level_after_other_settings', static function ( $level ) {
	pods_form_render_fields( 'pmpro_membership_level', $level->id, [
		'section_field' => 'pmpro_section',
		'section'       => 'after_other_settings',
	] );
} );

add_action( 'pmpro_membership_level_after_content_settings', static function ( $level ) {
	pods_form_render_fields( 'pmpro_membership_level', $level->id, [
		'section_field' => 'pmpro_section',
		'section'       => 'after_content_settings',
	] );
} );

add_action( 'pmpro_save_membership_level', static function ( $level_id ) {
	pods_form_save_submitted_fields( 'pmpro_membership_level', $level_id );
} );
