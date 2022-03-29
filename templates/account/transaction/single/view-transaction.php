<?php
/**
 * Account transaction view
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/transaction/single/pay-transaction.php.
 *
 * @package Hammock/Templates/Account/Transaction/Single/View
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hammock-account-transaction--view-transaction hammock-account-transaction-<?php echo esc_attr( $invoice->id ); ?>">
	<?php do_action( 'hammock_account_view_single_transaction_before', $invoice ); ?>
	<ul class="hammock-account-transaction--view-transaction-details">
		<li class="status">
			<?php esc_html_e( 'Invoice status:', 'hammock' ); ?>
			<strong><?php echo esc_html( $invoice->get_status_name() ); ?></strong>
		</li>
		<li class="total">
			<?php esc_html_e( 'Total:', 'hammock' ); ?>
			<strong><?php echo wp_kses_post( $invoice->get_amount_formated() ); ?></strong>
		</li>
		<?php if ( $invoice->gateway ) : ?>
		<li class="method">
			<?php esc_html_e( 'Payment method:', 'hammock' ); ?>
			<strong><?php echo wp_kses_post( $invoice->gateway_name() ); ?></strong>
		</li>
		<?php endif; ?>
	</ul>
	<?php do_action( 'hammock_account_view_single_transaction_after', $invoice ); ?>
</div>
