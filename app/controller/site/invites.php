<?php
namespace Hammock\Controller\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Model\Settings;
use Hammock\Services\Codes;

/**
 * Invites controller
 *
 * Manages all invites
 *
 * @since 1.0.0
 */
class Invites extends Controller {

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
	const MENU_SLUG = 'invites';

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
	 * Setting object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $setting = null;

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Invites
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
	 * @return Invites
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Initialize controller
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$settings      = Settings::instance();
		$this->setting = $settings->get_addon_setting( 'invitation' );
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
		$enabled        = isset( $this->setting['enabled'] ) ? $this->setting['enabled'] : false;
		$this->_page_id = $slug . '-' . self::MENU_SLUG;
		$this->_cap     = $cap;
		if ( $enabled ) {
			add_submenu_page(
				$slug,
				__( 'Invitation Codes', 'hammock' ),
				__( 'Invitation Codes', 'hammock' ),
				$this->_cap,
				$this->_page_id,
				array( $this, 'render' )
			);
		}
	}

	/**
	 * Register a page
	 * 
	 * @since 1.0.0
	 */
	public function register_page() {
		$enabled = isset( $this->setting['enabled'] ) ? $this->setting['enabled'] : false;
		if ( $enabled ) {
			$this->add_admin_page( array(
				'id'       => self::MENU_SLUG,
				'name'     => __( 'Invitation Codes', 'hammock' ),
				'icon'     => 'tag',
				'callback' => array( $this, 'render' )
			) );
		}
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
		if ( $this->is_page( 'invites' ) ) {
			$vars['common']['string']['title'] = __( 'Invitation Codes', 'hammock' );
			$vars['active_page']               = 'invites';
			$vars['strings']                   = $this->get_strings();
			$vars['page_strings']              = array(
				'status' => Codes::get_code_statuses(),
				'types'  => Codes::get_code_amount_types(),
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
			$this->strings = include HAMMOCK_LOCALE_DIR . '/site/codes.php';
		}
		return $this->strings;
	}

	/**
	 * Load controller specific scripts
	 *
	 * @since 1.0.0
	 */
	public function controller_scripts() {
		wp_enqueue_script( 'hammock-invites-react' );
	}

	/**
	 * Render view
	 *
	 * @return String
	 */
	public function render() {

		?>
		<div id="hammock-invites-container"></div>
		<?php
	}
}
?>
