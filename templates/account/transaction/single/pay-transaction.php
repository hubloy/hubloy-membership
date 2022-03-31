<?php
/**
 * Account transaction pay
 *
 * This template can be overridden by copying it to yourtheme/hubloy-membership/account/transaction/single/pay-transaction.php.
 *
 * @package HubloyMembership/Templates/Account/Transaction/Single/Pay
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hubloy-membership-account-transaction--pay-transaction hubloy-membership-account-transaction-<?php echo esc_attr( $invoice->id ); ?>">
	<form method="POST" class="hubloy-membership-checkout-form">
		<?php wp_nonce_field( 'hubloy-membership_purchase_subscription' ); ?>
		<input type="hidden" name="action" value="hubloy-membership_purchase_subscription" />
		<input type="hidden" name="invoice" value="<?php echo esc_attr( $invoice->id ); ?>" />
		<?php do_action( 'hubloy-membership_account_pay_single_transaction_before', $invoice ); ?>
		<table class="hubloy-membership-account-transaction--pay-transaction-details">
			<tr class="details">
				<td><?php esc_html_e( 'Details:', 'hubloy-membership' ); ?></td>
				<td>
					<?php
						hubloy-membership_get_template(
							'account/plan/single/plan-price.php',
							array(
								'plan' => $invoice->get_plan(),
							)
						);
					?>
				</td>
			</tr>
			<tr class="total">
				<td><?php esc_html_e( 'Total:', 'hubloy-membership' ); ?></td>
				<td><?php echo wp_kses_post( $invoice->get_amount_formated() ); ?></td>
			</tr>
			<?php if ( $invoice->is_owner() ) : ?>
			<tr class="gateway">
				<td><?php esc_html_e( 'Payment gateway:', 'hubloy-membership' ); ?></td>
				<td>
					<?php
						foreach( hubloy-membership_list_active_gateways() as $gateway_id => $gateway_name ) {
							hubloy-membership_get_template(
								'account/transaction/single/payment-method.php',
								array(
									'invoice'      => $invoice,
									'gateway_id'   => $gateway_id,
									'gateway_name' => $gateway_name,
								)
							);
						}
					?>
				</td>
			</tr>
			<?php endif; ?>
		</table>
		<?php do_action( 'hubloy-membership_account_pay_single_transaction_after', $invoice ); ?>
	</form>
</div>

