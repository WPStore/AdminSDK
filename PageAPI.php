<?php
/**
 * @author    WP-Store.io <code@wp-store.io>
 * @copyright Copyright (c) 2014-2015, WP-Store.io
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL-2.0+
 * @package   WPStore\AdminSDK
 */

if ( ! class_exists( 'PageAPI' ) ) {

	/**
	 * Page API Class
	 *
	 * @todo desc
	 *
	 * @version 0.0.12-dev
	 */
	abstract class PageAPI {

		/**
		 * Various information about the current page
		 *
		 * @since  0.0.1
		 * @var    array
		 */
		protected $_args;

		/**
		 * @todo desc
		 *
		 * @since 0.0.3
		 * @var   array|bool
		 */
		protected $_tabs = false;

		/**
		 * The current screen
		 *
		 * @since  0.0.1
		 * @var    object
		 */
		protected $screen;

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
					'tabs'    => false,
					'ajax'    => false,
					'sidebar' => false,
					'class'   => 'page',
				)
			);

			$this->_args = $args;
			$this->_tabs = $args['tabs'];

			if ( $args['ajax'] ) {
				add_action( 'admin_footer', array( $this, '_js_vars' ) ); // @todo combine all footer js code
			}

			if ( $args['tabs'] ) {
				add_action( 'admin_footer', array( $this, 'tabs_js' ) ); // @todo combine all footer js code
			}

		} // END __construct()

		/**
		 * @todo desc
		 *
		 * @since  0.0.1
		 * @return sring HTML output
		 */
		public function display() { ?>
	<div id="<?php echo $this->_args['id']; ?>" class="wrap <?php echo $this->_args['class']; ?>">
		<?php $this->header(); ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-<?php echo $this->_args['sidebar'] ? '2' : '1'; ?>">
				<div id="post-body-content" style="position: relative;">
					<?php $this->body( $this->_args['tabs'] ); ?>
				</div><!-- #post-body-content -->
				<?php
				if ( $this->_args['sidebar'] ) { $this->sidebar(); }
				$this->footer();
				?>
			</div>
			<br class="clear">
		</div><!-- #poststuff -->
	</div><!-- .wrap -->
	<?php
		} // END display()

		/**
		 * @todo desc
		 * @todo action docu
		 *
		 * @since 0.0.7
		 */
		protected function header() { ?>
			<div class="page-header">
				<div class="header-right alignright">
					<?php do_action( 'page_header_right', $this->_args['id'] ); ?>
				</div>
				<h2><?php echo $this->_args['title']; ?></h2>
				<div class="clear"></div>
			</div>
		<?php
		} // END header()

		/**
		 * @todo desc
		 *
		 * @since 0.0.1
		 * @param array|bool $tabs
		 */
		public function body( $tabs ) {

			if ( $tabs && is_array( $tabs ) ) {

				$this->add_tabs( $tabs );
				$this->tab_nav( $tabs );
				$this->tabs( $tabs );

			} else {

				$this->content();

			} // END if/else

		} // END body()

		/**
		 * @todo desc
		 *
		 * @since 0.0.9
		 */
		protected function content() {
			/**
			 * HTML/PHP output
			 */
		} // END content()

		/**
		 * @todo desc
		 * @todo action docu
		 * @todo possible action alias
		 *       do_action( "page_sidebar_{$this->_args['id']}" );
		 *
		 * @since  0.0.5
		 * @return string HTML output of the sidebar
		 */
		protected function sidebar() { ?>
			<div id="postbox-container-1" class="postbox-container">
				<?php do_action( 'page_sidebar', $this->_args['id'] ); ?>
			</div><!-- #postbox-container-1 .postbox-container -->
		<?php
		} // END sidebar()

		/**
		 * @todo desc
		 * @todo action docu
		 * @todo possible action alias
		 *       do_action( "page_footer_{$this->_args['id']}" );
		 *<div id="postbox-container-2" class="postbox-container">
		 * @since 0.0.7
		 * @param type $wrap
		 */
		protected function footer( $wrap = true ) { ?>
			<div id="postbox-container-2" class="postbox-container">
				<?php do_action( 'page_footer', $this->_args['id'] ); ?>
			</div><!-- #postbox-container-1 .postbox-container -->
		<?php
		} // END footer()

		/**
		 * @todo desc
		 *
		 * @since  0.0.3
		 * @return array
		 */
		public function get_tabs() {
			return apply_filters( "tabs_{$this->_args['id']}", $this->_tabs );
		} // END get_tabs()

		/**
		 * @todo desc
		 *
		 * @since  0.0.2
		 * @param  type $tabs
		 * @return void
		 */
		public function add_tabs( $tabs ) {

			foreach ( (array) $tabs as $tab_id => $title  ) {

				$page_id = str_replace( '-', '_', $this->_args['id'] );

				add_action( "{$page_id}_tab_{$tab_id}", array( $this, "tab_{$tab_id}" ) );

			} // END foreach

		} // END add_tabs()

		/**
		 * @todo desc
		 *
		 * @since  0.0.2
		 * @param  array $tabs
		 * @return string ID of the active tab
		 */
		protected function get_active_tab( $tabs ) {

			$first_tab = key( $tabs );
			$active_tab = isset( $_GET['tab'] ) ? esc_html( $_GET['tab'] ) : $first_tab ;

			return $active_tab;

		} // END get_active_tab()

		/**
		 * @todo desc
		 *
		 * @since  0.0.2
		 * @param  array $tabs
		 * @return string HTML output
		 */
		protected function tab_nav( $tabs ) {

			$page       = esc_html( $_GET['page'] );
			$active_tab = $this->get_active_tab( $tabs );

			echo '<h2 class="nav-tab-wrapper" id="' . esc_attr( $this->_args['id'] ) . '" >';

			foreach ( (array) $tabs as $tab_id => $title  ) {

				$active_class = ( $active_tab == $tab_id ? ' nav-tab-active' : '' );

				echo "<a id='{$tab_id}' class='nav-tab{$active_class}' href='?page={$page}&tab={$tab_id}'>{$title}</a>"; // class='{$this->_args['id']}-tab

			} // END foreach

			echo '</h2><!-- .nav-tab-wrapper -->';

		} // END tab_nav()

		/**
		 * @todo desc
		 *
		 * @since  0.0.2
		 * @param  array $tabs
		 * @return void
		 */
		protected function tabs( $tabs ) {

			$active_tab = $this->get_active_tab( $tabs );

			foreach ( (array) $tabs as $tab_id => $title ) {

				$active = ( $active_tab == $tab_id ? true : false );

				$this->tab_content( $tab_id, $active );

			} // END foreach

		} // END tabs()

		/**
		 * @todo desc
		 *
		 * @since  0.0.2
		 * @param  string $tab_id
		 * @param  bool $active
		 * @return string HTML output
		 */
		protected function tab_content( $tab_id, $active = false ) {

			$page       = str_replace( '-', '_', $this->_args['id'] );
			$active_tab = $active ? 'display: block;' : 'display: none;';

			echo "<div id='section-{$tab_id}' class='section' style='{$active_tab}'>";

				do_action( "{$page}_tab_{$tab_id}" );

			echo '</div>';

		} // END tab_content()

		/**
		 * @todo desc
		 *
		 * @since  0.0.2
		 * @return string HTML output
		 */
		public function tabs_js() { ?>
			<script type="text/javascript">
			jQuery(document).ready(function($){
				$('.nav-tab-wrapper').on('click','a.nav-tab', function(e){
					e.preventDefault();
					if ( ! $(this).hasClass('nav-tab-active') ) {
						$('.section').hide();
						$('.nav-tab').removeClass('nav-tab-active');
						$(this).addClass('nav-tab-active');
						$('#section-' + $(this).attr('id')).show();
					}
				});
			});
			</script>
			<?php
		} // END js_footer()

	} // END class PageAPI

} // END if class_exists
