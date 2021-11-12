<?php

namespace PMPro_Pods\PMPro;

/**
 * Helper functionality for interfacing with PMPro.
 *
 * @since   1.0.0
 * @package PMPro_Pods
 */
class Helper {

	/**
	 * Get the list of all level names keyed by ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] List of all level names keyed by ID.
	 */
	public static function get_all_level_names_by_id() {
		$all_levels = pmpro_getAllLevels();

		$levels = [];

		foreach ( $all_levels as $level ) {
			$levels[ $level->id ] = $level->name;
		}

		return $levels;
	}

}
