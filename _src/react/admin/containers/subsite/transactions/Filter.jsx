import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import { InputUI, DropDownUI } from 'ui/admin/form';

export function Filter(props) {
    const { hubloy_membership } = props;

    return (
        <div className="uk-width-1-1 uk-child-width-expand@s uk-margin-small-top uk-flex-middle" uk-grid="">
            <div className="uk-width-1-3">
                <DropDownUI name={`gateway`} values={hubloy_membership.page_strings.gateways} class_name={`hubloy_membership-transaction-gateway`}/>
            </div>
            <div className="uk-width-auto">
                <button className="uk-button uk-button-default uk-background-default">{hubloy_membership.common.general.filter}</button>
            </div>
        </div>
    );
};