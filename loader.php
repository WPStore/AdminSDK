<?php
/*
Plugin Name: AdminSDK
Description: Requires AdminSDK/*.php files
Version:     0.0.7-dev
 */

function AdminSDK_require_files() {

	$path   = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
	$folder = 'AdminSDK' . DIRECTORY_SEPARATOR;

	if ( file_exists( $path . 'PageAPI.php') ) {

		/** PageAPI: Create admin pages */
		require_once $path . 'PageAPI.php';

		/** SettingsAPI: Create admin settings pages - Extends PageAPI*/
		require_once $path . 'SettingsAPI.php';

		/** Utils: Collection of utility functions */
		require_once $path . 'Utils.php';

	} elseif ( file_exists( $path . $folder . 'PageAPI.php') ) {

		/** PageAPI: Create admin pages */
		require_once $path . $folder . 'PageAPI.php';

		/** SettingsAPI: Create admin settings pages - Extends PageAPI*/
		require_once $path . $folder . 'SettingsAPI.php';

		/** Utils: Collection of utility functions */
		require_once $path . $folder . 'Utils.php';

	}

} // END AdminSDK_require_files()

add_action( 'plugins_loaded', 'AdminSDK_require_files', 1 );
