<?php
namespace HubloyMembership\View\Backend\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\View;

/**
 * Mailchimp Settings view
 *
 * @since 1.0.0
 */
class Mailchimp extends View {


	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$settings   = $this->data['settings'];
		$optin      = isset( $settings['double_optin'] ) ? $settings['double_optin'] : true;
		$apikey     = isset( $settings['apikey'] ) ? $settings['apikey'] : '';
		$reg_list   = isset( $settings['registered_list'] ) ? $settings['registered_list'] : '';
		$sub_list   = isset( $settings['subscriber_list'] ) ? $settings['subscriber_list'] : '';
		$unsub_list = isset( $settings['unsubscriber_list'] ) ? $settings['unsubscriber_list'] : '';
		ob_start();
		?>
		<div class="uk-margin">
			<label class="uk-form-label" for="apikey"><?php _e( 'MailChimp API Key', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls uk-grid-small" uk-grid>
				<div class="uk-width-3-4">
					<?php
						$this->ui->render(
							'input',
							array(
								'name'        => 'apikey',
								'class'       => 'uk-input uk-form-width-large',
								'value'       => sanitize_text_field( $apikey ),
								'placeholder' => '',
							)
						);
					?>
					<p class="uk-text-meta">
						<?php echo sprintf( __( 'Visit <a href="%s" target="_blank">your API dashboard</a> to create an API Key.', 'hubloy-membership' ), 'http://admin.mailchimp.com/account/api' ); ?>
					</p>
				</div>
				<div class="uk-width-1-4">
					<a class="uk-button uk-button-secondary uk-button-small"><?php _e( 'Validate', 'hubloy-membership' ); ?></a>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

