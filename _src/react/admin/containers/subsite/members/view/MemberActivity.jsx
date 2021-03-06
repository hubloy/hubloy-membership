import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class MemberActivity extends Component {

	constructor(props) {
		super(props);

		this.state = {
			pager: {},
            items: [],
			id : this.props.id,
			loading : true,
			error : false
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	notify(type, message) {
		toast[type](message, {toastId: 'members-activity-toast'});
	}

	async componentDidMount() {
		this.loadPage();
	}

	loadPage = async () => {
        const page = parseInt( location.hash.split("/").pop() ) || 1;
        if ( page !== this.state.pager.current ) {
            this.fetchWP.get( 'activity/list?page=' + page + '&ref_id=' + this.state.id + '&ref_type=member' )
                .then( (json) => this.setState({
                    items : json.items,
                    pager : json.pager,
                    loading : false,
				    error : false,
                }), (err) => {
					this.setState({ loading : false, error : true });
					this.notify( self.props.hubloy_membership.error, 'error' );
				}
            );
        }
	}

	memberActivities() {
		const { pager, items } = this.state;
        var strings = this.props.hubloy_membership.strings;
		return (
			<React.Fragment>
				<div>
					{items.length <= 0 ? (
						<h3 className="uk-text-center">{strings.edit.activities.not_found}</h3>
					) : (
						<ul className="uk-list uk-list-striped">
							{items.map(item =>
								<li key={item.ref_id}>
									<div className="uk-width-expand">
										<p className="uk-text-meta uk-margin-remove-bottom uk-text-small">
											<time dateTime={item.date}>{item.date}</time>
										</p>
										<h6 className="uk-margin-remove-top uk-margin-remove-bottom">
											{item.action} {':'} <span dangerouslySetInnerHTML={{ __html: item.description }} /> {'('} <span className="uk-text-small" dangerouslySetInnerHTML={{ __html: item.author }} /> {')'}
										</h6>
									</div>
								</li>
							)}
						</ul>
					)}
					<PaginationUI pager={pager}/>
				</div>
			</React.Fragment>
		)
	}

	render() {
		
		if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{this.props.hubloy_membership.error}</h3>
				)
			} else {
				return this.memberActivities();
			}
		}
	}
}

MemberActivity.propTypes = {
	hubloy_membership: PropTypes.object
};