<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;

/**
 * Memberships controller
 * Manages all memberships
 *
 * @since 1.0.0
 */
class Memberships extends Controller {

	/**
	 * Page id
	 * Used to create the sub pages
	 *
	 * @var string
	 */
	private $_page_id = '';


	/**
	 * Cap
	 * Current page cap
	 *
	 * @var string
	 */
	private $_cap = '';

	/**
	 * Plugin Menu slug.
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	const MENU_SLUG = 'memberships';

	/**
	 * If is a sub page
	 * Always defaults to true
	 * 
	 * @since 1.0.0
	 */
	protected $is_sub_page = true;

	/**
	 * Set to true if content page
	 * 
	 * @since 1.0.0
	 * 
	 * @var bool
	 */
	protected $content_page = true;


	/**
	 * String translations
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $strings = array();

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create the menu page
	 *
	 * @param string $slug - the parent menu slug
	 * @param string $cap - the menu capabilities
	 *
	 * @since 1.0.0
	 */
	public function menu_page( $slug, $cap ) {
		$this->_page_id = $slug . '-' . self::MENU_SLUG;
		$this->_cap     = $cap;
		add_submenu_page(
			$slug,
			__( 'Memberships', 'hammock' ),
			__( 'Memberships', 'hammock' ),
			$this->_cap,
			$this->_page_id,
			array( $this, 'render' )
		);
	}

	/**
	 * Register a page
	 * 
	 * @since 1.0.0
	 */
	public function register_page() {
		$this->add_admin_page( array(
			'id'       => self::MENU_SLUG,
			'name'     => __( 'Memberships', 'hammock' ),
			'icon'     => 'lock',
			'callback' => array( $this, 'render' )
		) );
	}

	/**
	 * Set up admin js variables
	 *
	 * @param array $vars
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function admin_js_vars( $vars ) {
		if ( $this->is_page( 'memberships' ) ) {
			$vars['common']['string']['title'] = __( 'Memberships', 'hammock' );
			$vars['active_page']               = 'memberships';
			$vars['strings']                   = $this->get_strings();
			$vars['page_strings']              = array(
				'type'         => \Hammock\Services\Memberships::payment_types(),
				'duration'     => \Hammock\Services\Memberships::payment_durations(),
				'trial_period' => \Hammock\Services\Memberships::trial_duration(),
			);
		}
		return $vars;
	}


	/**
	 * Get the strings
	 * This sets the strings if not defined
	 *
	 * @since 1.0.0
	 */
	private function get_strings() {
		if ( empty( $this->strings ) ) {
			$this->strings = include HAMMOCK_LOCALE_DIR . '/site/memberships.php';
		}
		return $this->strings;
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hammock-memberships-react' );
		wp_enqueue_editor();
	}

	/**
	 * Render view
	 *
	 * @return string
	 */
	public function render() {

		?>
		<div id="hammock-memberships-container"></div>
		<?php
	}
}
?>
