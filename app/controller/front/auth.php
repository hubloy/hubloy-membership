<?php
namespace Hammock\Controller\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Controller;
use Hammock\Model\Settings;
use Hammock\Services\Members;
use Hammock\Services\Memberships;

/**
 * Auth controller
 * This manages front end account authentication or creation processes
 *
 * @since 1.0.0
 */
class Auth extends Controller {

	/**
	 * The member service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $member_service = null;

	/**
	 * The membership service
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $membership_service = null;

	/**
	 * Setting object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $settings = null;

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Controller
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
	 * @return Controller
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
		$this->settings           = new Settings();
		$this->member_service     = new Members();
		$this->membership_service = new Memberships();

		$this->add_ajax_action( 'hammock_register', 'account_register', true, true );
		$this->add_ajax_action( 'hammock_reset', 'account_reset', true, true );
		$this->add_ajax_action( 'hammock_login', 'account_login', true, true );

		// Verification code check
		$this->add_action( 'wp_login', 'handle_verification_code', 10, 2 );

		// Handle messages on auth page
		$this->add_action( 'hammock_before_account_access', 'message_on_auth_page' );
	}

	/**
	 * Handle registration
	 * This handles the account creation process
	 *
	 * @since 1.0.0
	 *
	 * @return application/json
	 */
	public function account_register() {
		$this->verify_nonce( 'hammock_account_register_nonce' );

		$user_login    = sanitize_text_field( $_POST['user_login'] );
		$user_email    = sanitize_text_field( $_POST['user_email'] );
		$user_password = sanitize_text_field( $_POST['user_password'] );

		if ( ! is_email( $user_email ) ) {
			wp_send_json_error( __( 'Invalid email', 'hammock' ) );
		}

		/**
		 * Save user but do not save member
		 */
		$response = $this->member_service->save_new_user( $email, '', '', $user_login, $user_password, false );

		if ( $response['status'] ) {
			$user_id = $response['user_id'];
			$user    = get_user_by( 'ID', $user_id );
			if ( $user ) {
				do_action( 'signup_finished' );

				$type = \Hammock\Services\Emails::COMM_TYPE_REGISTRATION;
				/**
				 * Send the email
				 *
				 * @see \Hammock\Base\Email::send_email
				 *
				 * @since 1.0.0
				 */
				do_action( 'hammock_send_email_member-' . $type, array(), $user, $user_email, array(), array() );

				if ( $this->settings->get_general_setting( 'account_verification' ) === 1 ) {

					$verify_key = wp_generate_password( 20, false );
					// Flag the account
					update_user_meta( $user_id, '_hammock_activation_status', 2 );
					update_user_meta( $user_id, '_hammock_activation_key', $verify_key );

					$type = \Hammock\Services\Emails::COMM_TYPE_REGISTRATION_VERIFY;

					$user_object = (object) array(
						'user_login' => $user_login,
						'user_id'    => $user_id,
						'verify_key' => $verify_key,
					);
					// Send verification email
					do_action( 'hammock_send_email_member-' . $type, array(), $user_object, $user_email, array(), array() );

					wp_send_json_success( __( 'Registration successful. An email has been sent with a link to verify your account', 'hammock' ) );
				} else {
					if ( ! headers_sent() ) {
						$auth_user = wp_signon(
							array(
								'user_login'    => $user_login,
								'user_password' => $user_password,
								'remember'      => true,
							)
						);

						// Stop here in case the login failed.
						if ( is_wp_error( $auth_user ) ) {
							wp_send_json_error( sprintf( __( 'Error : %s', 'hammock' ), $auth_user->get_error_message() ) );
						}
					}

					wp_set_current_user( $user->ID );
					wp_set_auth_cookie( $user->ID );
					do_action( 'wp_login', $user->username, $user );

					wp_send_json_success(
						array(
							'message' => __( 'Registration successful', 'hammock' ),
							'reload'  => true,
						)
					);
				}
			} else {
				wp_send_json_error( __( 'There was an error creating your account', 'hammock' ) );
			}
		} else {
			wp_send_json_error( $response['message'] );
		}

	}

	/**
	 * Handle password reset
	 * This handles the account password reset process
	 *
	 * @since 1.0.0
	 *
	 * @return application/json
	 */
	public function account_reset() {
		$this->verify_nonce( 'hammock_account_reset_nonce' );
		$user_data = false;
		if ( empty( $_POST['user_login'] ) || ! is_string( $_POST['user_login'] ) ) {
			wp_send_json_error( __( 'Enter a username or email address', 'hammock' ) );
		} elseif ( is_email( $_POST['user_login'] ) ) {
			$user_data = get_user_by( 'email', sanitize_email( $_POST['user_login'] ) );
			if ( empty( $user_data ) ) {
				wp_send_json_error( __( 'There is no account with that username or email address.', 'hammock' ) );
			}
		} else {
			$login     = sanitize_text_field( $_POST['user_login'] );
			$user_data = get_user_by( 'login', $login );
		}

		if ( ! $user_data ) {
			wp_send_json_error( __( 'There is no account with that username or email address.', 'hammock' ) );
		}

		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		$key        = get_password_reset_key( $user_data );

		if ( is_wp_error( $key ) ) {
			wp_send_json_error( $key->get_error_message() );
		}

		if ( is_multisite() ) {
			$site_name = get_network()->site_name;
		} else {
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}
		$type         = \Hammock\Services\Emails::COMM_TYPE_RESETPASSWORD;
		$placeholders = array(
			'{reset_url}' => network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ),
		);

		/**
		 * Send the email
		 *
		 * @see \Hammock\Base\Email::send_email
		 *
		 * @since 1.0.0
		 */
		do_action( 'hammock_send_email_member-' . $type, $placeholders, $user_data, $user_email, array(), array() );

		wp_send_json_success( __( 'An email has been sent with a link to create a new password', 'hammock' ) );
	}

	/**
	 * Login action
	 *
	 * @since 1.0.0
	 */
	public function account_login() {
		$this->verify_nonce( 'hammock_account_login_nonce' );

		$rememberme = isset( $_POST['rememberme'] );
		$user_login = sanitize_text_field( $_POST['user_login'] );
		$user_pass  = sanitize_text_field( $_POST['user_pass'] );

		$info = array(
			'user_login'    => $user_login,
			'user_password' => $user_pass,
			'remember'      => $rememberme,
		);

		/**
		 * Check if the current user can login
		 *
		 * @param bool $can_login - true or false
		 * @param array $info - the user info
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 */
		$can_login = apply_filters( 'hammock_account_can_login', true, $info );
		if ( $can_login ) {
			$user_signon = wp_signon( $info );

			if ( is_wp_error( $user_signon ) ) {
				do_action( 'hammock_account_login_error', $user_signon );
				wp_send_json_error( $user_signon->get_error_message() );
			} else {
				if ( ! is_super_admin( $user_signon->ID ) ) {
					$user_activation_status = get_user_meta( $user_signon->ID, '_hammock_activation_status', true );
					if ( $user_activation_status && intval( $user_activation_status ) === 2 ) {
						do_action( 'hammock_verification_failed', $login, $user );
						wp_destroy_current_session();
						wp_clear_auth_cookie();
						wp_send_json_error( __( 'Account not validated. Please check your email for a verification link', 'hammock' ) );
					}
				}

				wp_send_json_success(
					array(
						'message' => __( 'Login successful', 'hammock' ),
						'reload'  => true,
					)
				);
				do_action( 'hammock_account_login_success', $user_signon );
			}
		} else {
			wp_send_json_error( __( 'Login is disabled for your account', 'hammock' ) );
		}
	}

	/**
	 * Check that the user is erified to log in
	 *
	 * @param string  $login - the user login
	 * @param WP_User $user - the user
	 *
	 * @since 1.0.0
	 */
	public function handle_verification_code( $login, $user ) {
		if ( $this->settings->get_general_setting( 'account_verification' ) === 1 ) {
			if ( ! is_super_admin( $user->ID ) ) {
				$user_activation_status = get_user_meta( $user->ID, '_hammock_activation_status', true );
				if ( $user_activation_status && intval( $user_activation_status ) === 2 ) {
					do_action( 'hammock_verification_failed', $login, $user );
					wp_destroy_current_session();
					wp_clear_auth_cookie();
					$login_url = hammock_get_account_url();
					$login_url = add_query_arg(
						array(
							'ver_error' => true,
						),
						$login_url
					);
					if ( ! defined( 'DOING_AJAX' ) ) {
						wp_redirect( $login_url );
						exit;
					} else {
						wp_send_json_error( __( 'Account not validated. Please check your email for a verification link', 'hammock' ) );
					}
				}
			}
		}
	}

	/**
	 * Prints specific messages on auth page
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function message_on_auth_page() {
		global $wp;
		if ( isset( $_REQUEST['ver_error'] ) ) {
			?>
			<div class="hammock-notification hammock-notification--error">
				<?php _e( 'Your account is not verified. Please check your email for a link to verify your account', 'hammock' ); ?>
			</div>
			<?php
		}
		if ( ! is_user_logged_in() ) {
			// Non logged in user message
			if ( isset( $wp->query_vars['view-plan'] ) ) {
				$plan_id    = $wp->query_vars['view-plan'];
				$membership = $this->membership_service->get_membership_by_membership_id( $plan_id );
				if ( $membership ) {
					?>
					<div class="hammock-notification hammock-notification--primary">
						<?php echo sprintf( __( 'Log in or create an account to join the %s plan', 'hammock' ), '<strong>' . $membership->name . '</strong>' ); ?>
					</div>
					<?php
				}
			}
		}
	}
}

?>
