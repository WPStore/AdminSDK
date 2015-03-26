<?php

if ( ! class_exists( 'SettingsAPI' ) ) :

/**
 * Settings API Class
 *
 * Create a settings page easily, optionally with tabs and/or sidebar
 *
 * @version 0.0.10-dev
 */
class SettingsAPI extends \PageAPI {

	/**
	 * settings sections array
	 *
	 * @since 0.0.1
	 * @var   array
	 */
	protected $_sections = array();

	/**
	 * Settings fields array
	 *
	 * @since 0.0.1
	 * @var   array
	 */
	protected $_fields = array();

	/**
	 * @todo desc
	 *
	 * @since  0.0.1
	 * @param  array $instance_args
	 */
	public function __construct( $instance_args = array() ) {

		$instance_args['id'] = sanitize_key( $instance_args['id'] );

		$args = wp_parse_args(
			$instance_args,
			array(
				'title'   => __( 'Settings' ),
				'ajax'    => true,
				'sidebar' => true,
				'class'   => 'page settings-page',
			)
		);

		parent::__construct( $args );

	} // END __construct()

	/**
	 * @todo desc
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function register_settings() {

		if ( false == get_option( $this->_args['id'] ) ) {
			add_option( $this->_args['id'] );
		}

		foreach ( $this->get_sections() as $section => $values ) {
			$tab = ( isset( $values['tab'] ) ) ? $values['tab'] : 'nontab';
			$fields = $this->get_fields();

			$this->register_section( $tab, $section, $values );

			//register settings fields
			foreach ( $fields[ $section ] as $section_id => $section_values ) {
				$this->register_field( $tab, $section, $section_id, $section_values );
			}
		} // END foreach sections + fields

		// creates our settings in the options table
		/**
		 * @todo
		foreach ( $this->get_sections() as $section ) {
			register_setting( key( $section ), key( $section ), array( $this, 'sanitize_options' ) );
		}
		*/
	} // END register_settings()

	/**
	 * @todo desc
	 * @todo replace create_function() with anonymous functions
	 *
	 * @param string $tab
	 * @param string $section
	 * @param array  $values
	 */
	protected function register_section( $tab, $section, $values ) {

		if ( isset( $values['desc'] ) && ! empty( $values['desc'] ) ) {
			// $values['desc']	 = '<div class="inside">' . $values['desc'] . '</div>';
			$callback = create_function( '', 'echo "<p>' . str_replace( '"', '\"', $values['desc'] ) . '</p>";' );
		} else {
			$callback = '__return_false';
		}

		$page = $this->_args['id'] . '_' . $tab;

		add_settings_section( $section, $values['title'], $callback, $page );

	} // END register_section()

	/**
	 * @todo desc
	 *
	 * @since 0.0.1
	 * @param string $tab
	 * @param string $section
	 * @param string $field
	 * @param array $values
	 */
	protected function register_field( $tab, $section, $field, $values ) {

		if ( isset( $values['option'] ) ) {
			$option = $values['option'];
			if ( false == get_option( $values['option'] ) ) {
				add_option( $values['option'] );
			}
		} else {
			$option = $this->_args['id'];
		}

		$label = "<label for='{$option}[{$field}]'>" . $values['label'] . '</label>';
		$page = $this->_args['id'] . '_' . $tab;

		$args = array(
			'id'                => $field,
			'desc'              => isset( $values['desc'] ) ? $values['desc'] : '',
			'name'              => $values['label'],
			'section'           => $section,
			'size'              => isset( $values['size'] ) ? $values['size'] : 'regular', // null
			'options'           => isset( $values['options'] ) ? $values['options'] : '',
			'std'               => isset( $values['default'] ) ? $values['default'] : '',
			'sanitize_callback' => isset( $values['sanitize_callback'] ) ? $values['sanitize_callback'] : '',
			'option'            => $option, // (add_)option to be saved to
		);

		if ( isset( $values['type'] ) && method_exists( $this, 'field_' . $values['type'] ) ) {
			$type = $values['type'];
		}
		else {
			$type = 'debug';
			$args['type'] = $values['type'];
		}

		add_settings_field( $field, $label, array( $this, 'field_' . $type ), $page, $section, $args );

	} // END register_field()

	/**
	 * @todo desc
	 *
	 * @since 0.0.2
	 * @return string HTML output
	 */
	public function body( $tabs ) {

		if ( $tabs ) {
			$this->add_tabs( $tabs );
			$this->tab_nav( $tabs );
		}
		?>
		<form action="" method="post">
			<?php
			wp_nonce_field( "{$this->_args['id']}-settings-update", "{$this->_args['id']}-settings-nonce" ); // generate ids

			if ( $tabs ) {
				$this->tabs( $tabs );
			} else {
				$this->tab_content( $this->_args['id'], true );
			}

			$this->print_submit();
			?>
		</form>
		<?php do_action( "{$this->_args['id']}_post_form" );
	} // END body()

	/**
	 * Return filtered settings sections
	 *
	 * @sinec  0.0.1
	 * @return array settings sections
	 */
	public function get_sections() {
		return apply_filters( "sections_{$this->_args['id']}", $this->sections() ); // $this->_sections
	} // END get_sections()

	/**
	 * Set settings sections
	 *
	 * @since  0.0.1
	 * @param  array $sections setting sections
	 * @return \SettingsAPI
	 */
	public function set_sections( $sections ) {

		$this->_sections = $sections;

		return $this;

	} // END set_sections()

	function sections() {}

	/**
	 * Return filtered settings fields
	 *
	 * @since  0.0.1
	 * @return array settings fields
	 */
	public function get_fields() {
		return apply_filters( "fields_{$this->_args['id']}", $this->fields() ); // $this->_fields
	} // END get_fields()

	/**
	 * Set settings fields
	 *
	 * @since  0.0.1
	 * @param  array $fields settings fields array
	 * @return \SettingsAPI
	 */
	public function set_fields( $fields ) {

		$this->_fields = $fields;

		return $this;

	} // END set_fields()

	function fields() {}

	/**
	 * @todo desc
	 *
	 * @since  0.0.1
	 * @param  string $tab_id
	 * @param  bool $active
	 * @return string
	 */
	protected function tab_content( $tab_id, $active = false ) {

		$page_id    = str_replace( '-', '_', $this->_args['id'] ) . '-' . $tab_id;
		$page       = $this->_args['id'] . '_' . $tab_id;
		$active_tab = $active ? 'display: block;' : 'display: none;';

		echo "<div id='section-{$tab_id}' class='section' style='{$active_tab}'>";

		do_settings_sections( $page );
		do_settings_sections( $page_id ); // TEMP debug

		echo 'do_settings_sections: PAGE_ID: ' . $page_id; // TEMP debug
		echo '<br>do_settings_sections: PAGE: ' . $page; // TEMP debug

		echo '</div>';

	} // END tab_content()

	/**
	 * @todo desc
	 *
	 * @since  0.0.1
	 * @return string HTML submit button
	 */
	protected function print_submit() {
		submit_button();
	}

	/** Field Types ******************************************************/

	/**
	 * @todo COPIED + modified
	 * @param type $args
	 */
	public function field_text( $args ) {

		$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		// <input id="blogdescription" class="regular-text" type="text" value="Just another WP Trunk Sites site" name="blogdescription">

		$html = sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['option'], $args['id'], $value );
		$html .= sprintf( '<p class="description"> %s</p>', $args['desc'] );

		echo $html;

	} // END field_text()

	/**
	 * @todo COPIED + non-modified
	 * @param type $args
	 */
	public function field_checkbox( $args ) {

		$value = esc_attr( $this->get_option( $args['id'], $args['option'], $args['std'] ) );

		$html = sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
		$html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s />', $args['option'], $args['id'], $value, checked( $value, 'on', false ) );
		$html .= sprintf( '<label for="%1$s[%2$s]"> %3$s</label>', $args['section'], $args['id'], $args['desc'] );

		echo $html;

	} // END field_checkbox()

	/**
	 * @todo COPIED + non-modified
	 * @param type $args
	 */
	public function field_radio( $args ) {

		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );

		$html = '';
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s />', $args['option'], $args['id'], $key, checked( $value, $key, false ) );
			$html .= sprintf( '<label for="%1$s[%2$s][%4$s]"> %3$s</label><br>', $args['section'], $args['id'], $label, $key );
		}
		$html .= sprintf( '<span class="description"> %s</label>', $args['desc'] );

		echo $html;

	} // END field_radio()

	/**
	 * @todo COPIED + non-modified
	 * @param type $args
	 */
	public function field_textarea( $args ) {

		$value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		$html = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['option'], $args['id'], $value );
		$html .= sprintf( '<br><span class="description"> %s</span>', $args['desc'] );

		echo $html;

	}

	/**
	 * @todo COPIED + non-modified
	 * @param type $args
	 */
	public function field_select( $args ) {

		// multiselect option vs different callback??

		$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		$html = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['option'], $args['id'] );
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
		}
		$html .= sprintf( '</select>' );
		$html .= sprintf( '<p class="description"> %s</p>', $args['desc'] );

		echo $html;

	}

	public function field_debug( $args ) {

		var_dump( $args );

	} // END field_debug()


	/**
	 * Get the value of a settings field
	 *
	 * @todo account for option != section
	 *
	 * @param string  $option  settings field name
	 * @param string  $section the section name this field belongs to
	 * @param string  $default default text if it's not found
	 * @return mixed
	 */
	public function get_option( $option, $section, $default = '' ) {

		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;

	} // END get_option()

	/**
	 * @todo desc
	 *
	 * @since  0.0.1
	 * @return string HTML output
	 */
	public function _js_footer() { ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.nav-tab-wrapper').on('click','a.nav-tab', function(e){
		e.preventDefault();
		if ( ! $(this).hasClass('nav-tab-active') ) {
			$('.settings-section').hide();
			$('.nav-tab').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			$('#section-' + $(this).attr('id')).show();
		}
	});
});
jQuery(document).ready(function($) {
	// Switches option sections
	$('.group').hide();
	var activetab = '';
	if (typeof(localStorage) !== 'undefined' ) {
		activetab = localStorage.getItem("activetab");
	}
	if (activetab !== '' && $(activetab).length ) {
		$(activetab).fadeIn();
	} else {
		$('.group:first').fadeIn();
	}
	$('.group .collapsed').each(function(){
		$(this).find('input:checked').parent().parent().parent().nextAll().each(
		function(){
			if ($(this).hasClass('last')) {
				$(this).removeClass('hidden');
				return false;
			}
			$(this).filter('.hidden').removeClass('hidden');
		});
	});

	if (activetab !== '' && $(activetab + '-tab').length ) {
		$(activetab + '-tab').addClass('nav-tab-active');
	}
	else {
		$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
	}
	$('.nav-tab-wrapper a').click(function(evt) {
		$('.nav-tab-wrapper a').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active').blur();
		var clicked_group = $(this).attr('href');
		if (typeof(localStorage) != 'undefined' ) {
			localStorage.setItem("activetab", $(this).attr('href'));
		}
		$('.group').hide();
		$(clicked_group).fadeIn();
		evt.preventDefault();
	});
});
</script>
<?php

	} // END js_footer()

	/**
	 * Send required variables to JavaScript land
	 *
	 * @since  0.0.1
	 * @return string
	 */
	public function _js_vars() {
		/**

		$args = array(
			'class'  => get_class( $this ),
			'screen' => array(
				'id'   => $this->screen->id,
				'base' => $this->screen->base,
			)
		);

		printf( "<script type='text/javascript'>settings_args = %s;</script>\n", json_encode( $args ) );

		 */

	} // END _js_vars()

} // END class SettingsAPI

endif; // END if class_exists
