<?php
/**
 * Account plan roe
 * This view is used as a row for transactions
 *
 * This template can be overridden by copying it to yourtheme/hammock/account/transaction/list/view-transaction.php.
 *
 * @package Hammock/Templates/Account/Transaction/List/View
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<td class="hammock-account-transaction--id"><a href="<?php echo esc_url( hammock_get_account_page_links( 'view-transaction', $transaction->invoice_id ) ); ?>"><?php echo $transaction->invoice_id; ?></a></td>
<td class="hammock-account-transaction--status"><?php echo $transaction->status_name; ?></td>
<td class="hammock-account-transaction--gateway"><?php echo $transaction->gateway_name; ?></td>
<td class="hammock-account-transaction--amount"><?php echo $transaction->amount_formated; ?></td>
<td class="hammock-account-transaction--date"><?php echo $transaction->date_created; ?></td>
