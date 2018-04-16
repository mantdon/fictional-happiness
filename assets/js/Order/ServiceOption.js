import React from 'react';
import {render} from 'react-dom';
import { PropTypes } from 'react'

export default class ServiceOption extends React.Component {

    constructor(props) {
        super(props);
        this.clickHandle = this.clickHandle.bind(this);
    }

    clickHandle()
    {
        this.props.onClick(this.props.service);
    }

    render(){
       return <div className={'Option'} onClick={this.clickHandle}>
                    <div className={'serviceName'}> {this.props.service.name} </div>
                    <div className={'servicePrice'}>{this.props.service.price}</div>
                </div>;
    }
}