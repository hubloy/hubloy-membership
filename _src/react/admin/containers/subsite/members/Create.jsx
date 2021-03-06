import React, { PureComponent } from 'react';
import { InputUI } from 'ui/admin/form';
import {Users} from '../common/Users';
import fetchWP from 'utils/fetchWP';

import { toast } from 'react-toastify';

export class Create extends PureComponent {

	constructor(props) {
		super(props);
		this.save_existing_member = React.createRef();
		this.save_new_member = React.createRef();
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	notify(message, type) {
		toast[type](message, {toastId: 'members-create-toast'});
	}

	handleSubmitExisting(event) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.save_existing_member.current);
		this.processForm( $form, 'existing' );
	}

	handleSubmitNew(event) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.save_new_member.current);
		this.processForm( $form, 'new' );
	}

	processForm( $form, $type ) {
		var $button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize(),
			self = this;

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'members/save/' + $type, form, true )
			.then( (json) => {
				if ( json.status ) {
					self.notify( json.message, 'success' );
					setTimeout(function(){
						if ( typeof json.id !== 'undefined' ) {
							window.location.hash = "#/member/" + json.id;
						}
					}, 1000);
				} else {
					self.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( self.props.hubloy_membership.error, 'error' );
			}
		);
	}

	render() {
		const { hubloy_membership } = this.props;
		var strings = hubloy_membership.strings;
		return (
			<div id="hubloy_membership-add-member" uk-modal="">
				<div className="uk-modal-dialog">
					<button className="uk-modal-close-default" type="button" uk-close=""></button>
					<div className="uk-modal-header">
						<h2 className="uk-modal-title">{strings.dashboard.add_new.modal.title}</h2>
					</div>
					<div className="uk-modal-body">
						<div className="uk-margin">
							<select className="uk-select hubloy_membership-mode-select" data-target="member-type">
								<option value="existing">{strings.dashboard.add_new.modal.select.existing}</option>
								<option value="new">{strings.dashboard.add_new.modal.select.new}</option>
							</select>
						</div>
						<hr/>
						<div className="hubloy_membership-member-type member-type-new" style={{display : "none"}}>
							<form className="uk-form-stacked" onSubmit={this.handleSubmitNew.bind(this)} ref={this.save_new_member}>
								<div className="uk-margin">
									<legend className="uk-form-label">{strings.labels.email}</legend>
									<div className="uk-form-controls">
										<InputUI name={`email`} placeholder={strings.labels.email} required={true}/>
									</div>
								</div>
								<div className="uk-margin">
									<legend className="uk-form-label">{strings.labels.password}</legend>
									<div className="uk-form-controls">
										<InputUI type={`password`} name={`password`} placeholder={strings.labels.password}/>
									</div>
								</div>
								<div className="uk-margin">
									<legend className="uk-form-label">{strings.labels.firstname}</legend>
									<div className="uk-form-controls">
										<InputUI name={`firstname`} placeholder={strings.labels.firstname}/>
									</div>
								</div>
								<div className="uk-margin">
									<legend className="uk-form-label">{strings.labels.lastname}</legend>
									<div className="uk-form-controls">
										<InputUI name={`lastname`} placeholder={strings.labels.lastname}/>
									</div>
								</div>
								<div className="uk-margin ">
									<button className="uk-button uk-button-primary save-button">{hubloy_membership.common.buttons.save}</button>
								</div>
							</form>
						</div>
						<div className="hubloy_membership-member-type member-type-existing">
							<form className="uk-form-stacked" onSubmit={this.handleSubmitExisting.bind(this)} ref={this.save_existing_member}>
								<div className="uk-margin">
									<label className="uk-form-label">{strings.dashboard.add_new.modal.select_user}</label>
									<div className="uk-form-controls">
										<Users hubloy_membership={hubloy_membership}/>
									</div>
								</div>
								<div className="uk-margin ">
									<button className="uk-button uk-button-primary save-button">{hubloy_membership.common.buttons.save}</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		)
	}
}