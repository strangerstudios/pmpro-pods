<?php
/**
 * Member Profile Fields
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
 *		<h3>Group label</h3>
 *		<table class="form-table">
 *
 * Markup (pmpro_show_user_profile):
 *		<hr />
 *		<h3>Group label</h3>
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
 */

add_action( 'pmpro_show_user_profile', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_member_profile',
		'section'       => 'show_on_front',
		'render'        => 'div-rows',
		'heading'       => 'h3',
		'separator'     => 'off',
	] );
} );

add_action( 'pmpro_add_member_fields', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field'     => 'pmpro_section_member_profile',
		'section'           => 'show_on_front',
		'render'            => 'table-separated',
		'heading'           => 'h3',
		/*
		 * If we add fields then it separates from the rest of the fields and we need a heading to
		 * make it clear what the next fields are.
		 */
		'separated_heading' => __( 'Order Information', 'pmpro-pods' ),
		'separator'         => 'off',
	] );
} );

add_action( 'show_user_profile', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_member_profile',
		'section'       => 'show_on_admin',
		'heading'       => 'h3',
	] );
} );

add_action( 'edit_user_profile', static function( $user ) {
	pods_form_render_fields( 'pmpro_membership_user', $user->ID, [
		'section_field' => 'pmpro_section_member_profile',
		'section'       => 'show_on_admin',
		'heading'       => 'h3',
	] );
} );

add_action( 'personal_options_update', static function( $user_id ) {
	pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id, false, [
		'section_field' => 'pmpro_section_member_profile',
		'section'       => 'show_on_admin',
	] );
} );

add_action( 'edit_user_profile_update', static function( $user_id ) {
	pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id, false, [
		'section_field' => 'pmpro_section_member_profile',
		'section'       => 'show_on_admin',
	] );
} );

add_action( 'pmpro_personal_options_update', static function( $user_id ) {
	pods_form_save_submitted_fields( 'pmpro_membership_user', $user_id, false, [
		'section_field' => 'pmpro_section_member_profile',
		'section'       => 'show_on_front',
	] );
} );

add_action( 'pmpro_add_member_added', static function( $check ) { var_dump( current_filter(), $check ); return $check; } );
add_filter( 'pmpro_registration_checks', static function( $check ) { var_dump( current_filter() ); return $check; } );
