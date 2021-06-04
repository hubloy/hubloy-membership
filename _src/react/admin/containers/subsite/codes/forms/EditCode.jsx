import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import { Link } from 'react-router-dom';
import fetchWP from 'utils/fetchWP';
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';

export default class EditCode extends Component {

	constructor(props) {
		super(props);

		this.coupon_edit_code = React.createRef();
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
		});
		
		this.state = {
            item: {},
            loading : true,
            error : false,
        };
	}

	async componentDidMount() {
		this.fetch_data();
		this.init_scripts()
	}
	
	async componentDidUpdate() {
		this.init_scripts()
	}


	fetch_data = async() => {
		const type = this.props.type;
		const id = this.props.match.params.id;
        this.fetchWP.get( 'codes/get/'+type+'?id=' + id )
            .then( (json) => this.setState({
                item : json,
                loading : false,
                error : false,
            }), (err) => this.setState({ loading : false, error : true, err : err })
        );
	}

	init_scripts() {
		window.hammock.helper.bind_date_range();
        jQuery('.hammock-email-tags').tagsInput({width:'98%',  defaultText : this.props.hammock.strings.select_email});
	}


	handleSubmit( event ) {
		event.preventDefault();
		const type = this.props.type;
		var self = this,
			$form = jQuery(self.coupon_edit_code.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize(),
			helper = window.hammock.helper;
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'codes/update/' + type, form, true )
			.then( (json) => {
				if ( json.status ) {
					helper.notify( json.message, 'success');
				} else {
					helper.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				helper.notify( self.props.hammock.error, 'error' );
			}
		);
	}

	renderCouponForm( strings, page_strings, code ) {
		
		return (
			<React.Fragment>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.code.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`coupon`} placeholder={strings.create.coupons.code.title} value={code.code} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.code.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.amount_type.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`amount_type`} values={page_strings.types} value={code.amount_type} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.amount.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`amount`} placeholder={`0`} required={false} value={code.amount}/>
						<p className="uk-text-meta">{strings.create.coupons.amount.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.status.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`status`} values={page_strings.status} value={code.status} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.expire.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`expire`} class_name={`hammock-from-date`} placeholder={``} value={typeof code.custom_data.expire !== 'undefined' ? code.custom_data.expire : ''} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.expire.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.restrict.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`restrict`} class_name={`hammock-email-tags`} placeholder={``} value={typeof code.custom_data.restrict !== 'undefined' ? code.custom_data.restrict : ''} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.restrict.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.usage.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`usage`} placeholder={``} required={false} value={typeof code.custom_data.usage !== 'undefined' ? code.custom_data.usage : ''}/>
						<p className="uk-text-meta">{strings.create.coupons.usage.description}</p>
					</div>
				</div>
			</React.Fragment>
		)
	}

	renderInviteForm( strings, page_strings, code ) {
		return (
			<React.Fragment>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.code.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`coupon`} value={code.code} placeholder={strings.create.invites.code.title} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.code.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.status.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`status`} values={page_strings.status} value={code.status} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.expire.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`expire`} class_name={`hammock-from-date`} placeholder={``} value={typeof code.custom_data.expire !== 'undefined' ? code.custom_data.expire : ''} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.expire.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.restrict.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`restrict`} class_name={`hammock-email-tags`} placeholder={``} value={typeof code.custom_data.restrict !== 'undefined' ? code.custom_data.restrict : ''} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.restrict.description}</p>
					</div>
				</div>
			</React.Fragment>
		)
	}

	render_form( hammock ) {
		const type = this.props.type,
			strings = hammock.strings,
			page_strings = hammock.page_strings,
			code = this.state.item;
		return (
			<Dashboard hammock={hammock}>
				<h2 className="uk-text-center uk-heading-divider">
					{type === 'coupons' ? (
						strings.edit.coupon
					) : ( 
						strings.edit.invite
					)}	
				</h2>
				<Link to={'/'} className="uk-button uk-button-primary uk-button-small">{hammock.common.buttons.back}</Link>
				<div className={"uk-background-default uk-padding-small uk-margin-small-top hammock-settings-" + type}>
					<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.coupon_edit_code}>
						<InputUI name={`type`} type={`hidden`} value={this.props.type}/>
						<InputUI name={`id`} type={`hidden`} value={code.id}/>
						{type === 'coupons' ? (
							this.renderCouponForm( strings, page_strings, code )
						) : (
							this.renderInviteForm( strings, page_strings, code )
						)}
						<div className="uk-margin">
							<button className="uk-button uk-button-primary save-button">{hammock.common.buttons.update}</button>
						</div>
					</form>
				</div>
			</Dashboard>
		)
	}

	render() {
		const hammock = this.props.hammock;
		if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				
				return (
					<h3 className="uk-text-center uk-text-danger">{hammock.error}</h3>
				)
			} else {
				return this.render_form( hammock );
			}
		}
	}

}
EditCode.propTypes = {
	hammock: PropTypes.object
};