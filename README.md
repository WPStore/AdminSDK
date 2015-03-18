# AdminSDK
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/WPStore/AdminSDK/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/WPStore/AdminSDK/?branch=master)
[![GPL-2.0+](http://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](http://www.gnu.org/licenses/gpl-2.0.html)

## Usage

### Generate a settings page

### Add custom field type

```php
<?php
class Settings extends \WPStore\SettingsAPI\SettingsAPI {

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function __construct( $instance_args = array() ) {

		parent::__construct( $instance_args );

	} // END __construct()

	public function field_custom( $args ) {

        echo 'Custom Output: ' . $args['option'] . '_' . $args['id'];

    }

} // END class Settings

```

## License
__[GPLv2](http://www.gnu.org/licenses/gpl-2.0.html)__

    AdminSDK
    Copyright (C) 2015 WP-Store.io (http://www.wp-store.io)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
