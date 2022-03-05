<?php
namespace Hammock\View\Backend\Rules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

/**
 * Access view
 * Manages rule access settings.
 * 
 * @since 1.0.0
 */
class Access extends View {

	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$rule        = $this->data['rule'];
		$id          = $this->data['id'];
		$item        = $this->data['item'];
		$memberships = $this->data['memberships'];
		ob_start();
		?>
		<div class="uk-flex" data-id="<?php echo esc_attr( $id ); ?>" data-item="<?php echo esc_attr( $item ); ?>">
			<select data-placeholder="<?php esc_html_e( 'Select membership', 'hammock' ); ?>" multiple class="hammock-chosen-select">
				<?php
					$rule_memberships = $rule ? $rule->memberships : array();
					foreach ( $memberships as $id => $name ) {
						$selected = in_array( $id, $rule_memberships, true );
						?><option value="<?php echo esc_attr( $id ); ?>" <?php echo $selected ? 'selected="selected"' : ''; ?>><?php echo esc_html( $name ); ?></option><?php
					}
				?>
			</select>
			<button class="uk-button uk-button-primary uk-button-small hammock-rule-update"><?php esc_html_e( 'Update', 'hammock' ); ?></button>
		</div>
		<?php
		return ob_get_clean();
	}
}