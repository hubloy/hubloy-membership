<?php
namespace Hammock\Emails\Member\Membership\Status;

use Hammock\Base\Email;

/**
 * User card expire
 */
class After extends Email {

	/**
	 * Singletone instance of the addon.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the addon.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set up variables
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$type                = \Hammock\Services\Emails::COMM_TYPE_AFTER_FINISHES;
		$this->id            = 'member-' . $type;
		$this->template_html = 'emails/membership/status/after.php';
		$this->placeholders  = array(
			'{membership_name}' => ''
		);
	}

	/**
	 * Register defaults
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_defaults() {
		return array(
			'title'       => __( 'Membership Finished', 'hammock' ),
			'description' => __( 'Sent to a member after the membership finishes', 'hammock' ),
			'heading'     => __( 'Membership Finished', 'hammock' ),
			'subject'     => sprintf( __( 'Reminder: your %s membership has ended', 'hammock' ), '{membership_name}' ),
			'recipient'   => '',
			'enabled'     => false,
		);
	}
}

