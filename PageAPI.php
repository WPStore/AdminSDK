<?php
/**
 * Page API Class
 *
 * @todo desc
 *
 * @version 0.0.2
 */
abstract class PageAPI {

	/**
	 * Various information about the current settings page
	 *
	 * @since  0.0.1
	 * @var    array
	 * @access private
	 */
	protected $_args;

	/**
	 * The current screen
	 *
	 * @since  0.0.1
	 * @var    object
	 * @access protected
	 */
	protected $screen;

	public function __construct( $instance_args = array() ) {

		$instance_args['id'] = sanitize_key( $instance_args['id'] );
		// str_replace('.', '_', $title);

		$args = wp_parse_args(
			$instance_args,
			array(
				// 'id' => $this->screen->id,
				// 'title'   => __( 'Title' ),
				'tabbed'  => false,
				'ajax'    => false,
				'sidebar' => false,
				'class'   => 'page',
			)
		);

		$this->_args = $args;

		if ( $args['ajax'] ) {
			add_action( 'admin_footer', array( $this, '_js_vars' ) );
		}

	} // END __construct()

//	public function __construct( $args = array() ) {
//		$this->args = $args;
//	}

	public function display() {
		?>
<div id="<?php echo $this->_args['id']; ?>" class="wrap <?php echo $this->_args['class']; ?>">
	<div class="page-header">
		<div class="header-right">
			<?php 
			do_action( 'page_header_right' );
			do_action( "page_header_right_{$this->_args['id']}" );
			?>
		</div>
		<h2><?php echo $this->_args['title']; ?></h2>
	</div>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-<?php echo $this->_args['sidebar'] ? '2' : '1'; ?>">
			<div id="post-body-content" style="position: relative;">
				<?php $this->body(); ?>
			</div><!-- #post-body-content -->
			<div id="postbox-container-1" class="postbox-container">
				<?php
				do_action( 'page_sidebar' );
				do_action( "page_sidebar_{$this->_args['id']}" );
				?>
			</div><!-- #postbox-container-1 .postbox-container -->
		</div>
		<div class="clear"></div>
	</div><!-- #poststuff -->
</div><!-- .wrap -->
<?php
	} // END display()

	/**
	 * @todo desc
	 */
	public abstract function body();

	/**
	 * @todo desc
	 * 
	 * @param type $tabs
	 * @return void
	 */
	public function add_tabs( $tabs ) {

		foreach ( (array) $tabs as $tab_id => $title  ) {

			$page_id = str_replace( '-', '_', $this->_args['id'] );

			add_action( "{$page_id}_tab_{$tab_id}", array( $this, 'tab_'.$tab_id ) );

		} // END foreach

	} // END add_tabs()

	/**
	 * @todo desc
	 *
	 * @param array $tabs
	 * @return string ID of the active tab
	 */
	protected function get_active_tab( $tabs ) {

		$first_tab = key( $tabs );
		$active_tab = isset( $_GET['tab'] ) ? esc_html( $_GET['tab'] ) : $first_tab ;

		return $active_tab;

	} // END get_active_tab()

	protected function tab_nav( $tabs ) {

		$page = esc_html( $_GET['page'] );
		$active_tab = $this->get_active_tab( $tabs );

		echo '<h2 class="nav-tab-wrapper" id="' . esc_attr( $this->_args['id'] ) . '" >';

			foreach ( (array) $tabs as $tab_id => $title  ) {

				$active_class = ( $active_tab == $tab_id ? ' nav-tab-active' : '' );
				echo "<a id='{$tab_id}' class='nav-tab{$active_class}' href='?page={$page}&tab={$tab_id}'>{$title}</a>"; // class='{$this->_args['id']}-tab

			} // END foreach

		echo '</h2><!-- .nav-tab-wrapper -->';

	} // END tab_nav()

	protected function tabs( $tabs ) {

		$active_tab = $this->get_active_tab( $tabs );

		foreach ( (array) $tabs as $tab_id => $title ) {

			$active = ( $active_tab == $tab_id ? true : false );

			$this->tab_content( $tab_id, $active );

		} // END foreach

	} // END tabs()

	protected function tab_content( $tab_id, $active = false ) {

		$page_id    = str_replace( '-', '_', $this->_args['id'] );
		$active_tab = $active ? 'display: block;' : 'display: none;';

		echo "<div id='section-{$page_id}' class='section' style='{$active_tab}'>";

			do_action( "{$page_id}_tab_{$tab_id}" );

		echo "</div>";

	} // END tab_content()

} // END class PageAPI
