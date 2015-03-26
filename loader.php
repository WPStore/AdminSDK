<?php
/*
Plugin Name: AdminSDK
Description: Requires AdminSDK/*.php files
Version:     0.0.7-dev
 */

function AdminSDK_require_files() {
	/** PageAPI: Create admin pages */
	require_once 'PageAPI.php';

	/** SettingsAPI: Create admin settings pages - Extends PageAPI*/
	require_once 'SettingsAPI.php';

	/** Utils: Collection of utility functions */
	require_once 'Utils.php';
}

add_action( 'plugins_loaded', 'AdminSDK_require_files', 1 );
