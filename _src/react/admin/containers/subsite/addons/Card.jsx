import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';

import { toast } from 'react-toastify';

export default class Card extends PureComponent {

	constructor(props) {
		super(props);

		this.state = {
			settings : {},
			checked: undefined,
			active : undefined,
			loading : true,
			error : false,
		};
		this.onChange = this.onChange.bind(this);
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	notify(type, message) {
		toast[type](message, {toastId: 'addon-card-toast'});
	}

	onChange(e) {
		var $checked = !!e.target.checked;
		this.fetchWP.post( 'addons/toggle', { name: this.props.id, enabled : $checked } )
			.then( (json) => this.setState({
				checked : json.enabled
			}), (err) => this.notify( this.props.hubloy_membership.error, 'error' )
		);
	}

	async componentDidMount() {
		const id = this.props.id;
		this.fetchWP.get( 'addons/settings?name=' + id )
			.then( (json) => this.setState({
				settings : json.settings,
				checked : json.enabled,
				active : json.active,
				loading : false,
				error : false,
			}), (err) => {
				this.setState({ loading : false, error : true });
			}
		);
	}

	render() {
		var hubloy_membership = this.props.hubloy_membership;
		if ( this.state.loading) {
			return (
                <li>
					<div className={"uk-card uk-card-body uk-card-hover uk-padding-remove addon-card"}>
						<div className="uk-card-body uk-padding-remove-top">
							<span className="uk-text-center" uk-spinner="ratio: 3"></span>
						</div>
					</div>
                </li>
            )
		} else {
			if ( this.state.error) {
				return (
					<li>
						<div className={"uk-card uk-card-body uk-card-hover uk-padding-remove addon-card"}>
							<div className="uk-card-body uk-padding-remove-top">
							<h3 className="uk-text-center uk-text-danger">{hubloy_membership.error}</h3>
							</div>
						</div>
					</li>
				)
			} else {
				var checked = typeof this.state.checked === 'undefined' ? false : this.state.checked;
				if ( typeof this.state.active !== 'undefined' ) {
					if ( this.state.active !== true ) {
						checked = false;
					}
				}
				return(
					<li className={checked ? 'enabled' : 'disabled'} key={this.props.id}>
						<div className={"uk-card uk-card-hover uk-padding-remove addon-card " + ( checked ? 'uk-card-primary' : 'uk-card-default' )}>
							<h3 className="uk-text-small uk-card-title uk-padding uk-padding-remove-bottom">
								<span className={this.props.item.icon}></span> {this.props.item.name}
								
							</h3>
							<div className="uk-card-body uk-padding-remove-top">
								{this.props.item.description}
							</div>
							<div className="uk-card-footer">
								{ typeof this.props.item.settings !== 'undefined' && checked > 0 &&
									<div className="uk-position-bottom-left uk-padding-small">
										<a className="hubloy_membership-addon-toggle-setting" data-id={this.props.id} data-name={this.props.item.name} data-nonce={hubloy_membership.ajax_nonce}>{hubloy_membership.common.general.configure}</a>
									</div>
								}
								{ typeof this.props.item.message !== 'undefined' &&
									<div className="uk-position-bottom-left uk-padding-small uk-width-1-1">
										<span className="uk-text-center">{this.props.item.message}</span>
									</div>
								}
								{ typeof this.props.item.url !== 'undefined' && checked > 0 &&
									<div className="uk-position-bottom-left uk-padding-small">
										<a href={this.props.item.url}>{hubloy_membership.common.general.settings}</a>
									</div>
								}
								{ typeof this.state.active !== 'undefined' && this.state.active === true &&
									<div className="uk-position-bottom-right uk-padding-small">
										<div className="hubloy_membership-input">
											<section className="slider-checkbox">
												<input
													type="checkbox"
													name={this.props.id}
													value={`1`}
													checked={checked}
													onChange={this.onChange}
												/>
												<label className='label' htmlFor={this.props.id}>
													{checked ? hubloy_membership.common.status.enabled : hubloy_membership.common.status.disabled }
												</label>
											</section>
										</div>
									</div>
								}
							</div>
						</div>
					</li>
				)
			}
		}
		
	}
}