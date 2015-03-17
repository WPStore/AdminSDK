<?php
/**
 * Page API Class
 *
 * @todo desc
 *
 * @version 0.0.1
 */
abstract class PageAPI {

	/**
	 * Various information about the current settings page
	 *
	 * @since  0.0.1
	 * @var    array
	 * @access private
	 */
	private $_args;

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

	public abstract function body();

} // END class PageAPI
