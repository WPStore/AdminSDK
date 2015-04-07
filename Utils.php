<?php
/**
 * @author    WP-Store.io <code@wp-store.io>
 * @copyright Copyright (c) 2014-2015, WP-Store.io
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL-2.0+
 * @package   WPStore\AdminSDK
 */

if ( ! class_exists( 'Utils' ) ) {

	/**
	 * Utils Class
	 *
	 * @todo desc
	 *
	 * @version 0.0.12-dev
	 */
	class Utils {

		/**
		 * @todo desc
		 *
		 * @since  0.0.10
		 * @param  string $type [class|function|plugin_path|plugin_file]
		 * @param  string|array $search
		 * @return string Status of the plugin [active|inactive|missing]
		 */
		public static function detect( $type, $search ) {

			if ( method_exists( __CLASS__, 'detect_' . $type ) ) {
				$status = forward_static_call( __CLASS__ . "::detect_{$type}" , $search );
			} else {
				// wp_die()?
				$status = false;
			}
			return $status;

		} // END detect()

		/**
		 * @todo desc
		 *
		 * @since  0.0.10
		 * @param  string $class_name
		 * @return string
		 */
		protected static function detect_class( $class_name ) {

			if ( class_exists( $class_name ) ) {
				return 'active';
			} else {
				return 'missing';
			}

		} // END detect_class()

		/**
		 * @todo desc
		 *
		 * @since  0.0.10
		 * @param  string|array $function_name
		 * @return string
		 */
		protected static function detect_function( $function_name ) {

			if ( is_array( $function_name ) ) {
				if ( method_exists( $function_name[0], $function_name[1] ) ) {
					return 'active';
				} else {
					return 'missing';
				}
			} else {
				if ( function_exists( $function_name ) ) {
					return 'active';
				} else {
					return 'missing';
				}
			}

		} // END detect_function()

		/**
		 * @todo desc
		 *
		 * @since  0.0.10
		 * @param  string $plugin_path
		 * @return string
		 */
		protected static function detect_plugin_path( $plugin_path ) {

			if ( is_plugin_active( $plugin_path ) ) {
				return 'active';
			} else {
				$plugins = get_plugins();

				foreach ( $plugins as $key => $value ) {
					if ( $key == $plugin_path ) {
						return 'inactive';
					}
				}

				return 'missing';
			}

		} // END detect_plugin_path()

		/**
		 * @todo desc
		 * @todo check get_plugins() keys for containing $plugin_file + check is_plugin_active for found plugin (by key)
		 *
		 * @since  0.0.10
		 * @param  string $plugin_file
		 * @return string
		 */
		protected static function detect_plugin_file( $plugin_file ) {

			// more complex
			// get_plugins() + search the array!
			return 'active';

		} // END detect_plugin_file(

	} // END class Utils

} // END if class_exists
