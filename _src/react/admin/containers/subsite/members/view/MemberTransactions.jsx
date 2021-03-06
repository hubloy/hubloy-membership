import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class MemberTransactions extends Component {

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
		toast[type](message, {toastId: 'members-transactions-toast'});
	}

	async componentDidMount() {
		this.loadPage();
	}

	loadPage = async () => {
        const page = parseInt( location.hash.split("/").pop() ) || 1;
        if ( page !== this.state.pager.current ) {
            this.fetchWP.get( 'transactions/list?page=' + page + '&member_id=' + this.state.id )
                .then( (json) => this.setState({
                    items : json.items,
                    pager : json.pager,
                    loading : false,
				    error : false,
                }), (err) => {
					this.setState({ loading : false, error : true });
					sethislf.notify( this.props.hubloy_membership.error, 'error' );
				}
            );
        }
	}

	memberTransactions() {
		const { pager, items } = this.state;
        var strings = this.props.hubloy_membership.strings;
		return (
			<React.Fragment>
				<div>
					{items.length <= 0 ? (
						<h3 className="uk-text-center">{strings.edit.transactions.not_found}</h3>
					) : (
						<table className="uk-table">
								<thead>
								<tr>
									<th>{strings.edit.transactions.id}</th>
									<th>{strings.edit.transactions.status}</th>
									<th>{strings.edit.transactions.gateway}</th>
									<th>{strings.edit.transactions.amount}</th>
									<th>{strings.edit.transactions.date}</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>{strings.edit.transactions.id}</th>
									<th>{strings.edit.transactions.status}</th>
									<th>{strings.edit.transactions.gateway}</th>
									<th>{strings.edit.transactions.amount}</th>
									<th>{strings.edit.transactions.date}</th>
								</tr>
							</tfoot>
							<tbody>
							{items.map(item =>
								<tr key={item.id}>
									<td>
										<a href={item.admin_edit_url} className="uk-text-primary" target="_blank">{item.invoice_id}</a>
									</td>
									<td>{item.status_name}</td>
									<td>{item.gateway_name}</td>
									<td><span dangerouslySetInnerHTML={{ __html: item.amount_formated }}></span></td>
									<td>{item.date_created}</td>
								</tr>
							)}
							</tbody>
						</table>
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
				return this.memberTransactions();
			}
		}
	}
}

MemberTransactions.propTypes = {
	hubloy_membership: PropTypes.object
};